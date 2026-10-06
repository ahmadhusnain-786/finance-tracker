<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Expense;
use App\Models\Income;
use App\Models\RecurringTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecurringService
{
    /**
     * Run all due templates. Returns count of records created.
     * Uses a safeguard: only one record per (template, run date).
     */
    public function processDue(?Carbon $asOf = null): int
    {
        $asOf  = $asOf ?? now();
        $count = 0;

        $due = RecurringTransaction::query()
            ->due()
            ->get();

        foreach ($due as $template) {
            // Ensure we don't create duplicates: process each due day
            // until next_run_date is in the future.
            $safetyCounter = 0;
            while ($this->isDue($template, $asOf) && $safetyCounter < 366) {
                $this->runOne($template, $template->next_run_date->copy());
                $template->refresh();
                $safetyCounter++;
                $count++;
            }
        }

        return $count;
    }

    /**
     * Run a specific template once, on a specific date (defaults to its next_run_date).
     */
    public function runOne(RecurringTransaction $template, ?Carbon $onDate = null): void
    {
        $onDate = $onDate ?? $template->next_run_date ?? now();

        DB::transaction(function () use ($template, $onDate) {
            $payload = [
                'user_id'           => $template->user_id,
                'category_id'       => $template->category_id,
                'payment_method_id' => $template->payment_method_id,
                'title'             => $template->title . ' (auto)',
                'amount'            => $template->amount,
                'date'              => $onDate->toDateString(),
                'description'       => $template->description,
                'is_recurring'      => true,
                'recurring_frequency' => $template->frequency->value,
            ];

            if ($template->type === TransactionType::Income) {
                Income::create($payload);
            } else {
                Expense::create($payload);
            }

            $template->last_run_date = $onDate->toDateString();
            $template->advanceNextRunDate();

            // Deactivate if the end date has passed
            if ($template->end_date
                && $template->next_run_date->gt($template->end_date)) {
                $template->is_active = false;
                $template->save();
            }

            Log::info('Recurring template ran', [
                'template_id' => $template->id,
                'ran_on'      => $onDate->toDateString(),
                'next_run'    => $template->next_run_date?->toDateString(),
            ]);
        });
    }

    /**
     * Is the template due on a given date?
     */
    private function isDue(RecurringTransaction $template, Carbon $asOf): bool
    {
        if (! $template->is_active) {
            return false;
        }
        if (! $template->next_run_date) {
            return false;
        }
        if ($template->next_run_date->gt($asOf)) {
            return false;
        }
        if ($template->end_date && $template->next_run_date->gt($template->end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Suggest the initial next_run_date when creating a template.
     */
    public function suggestFirstRun(string $startDate): string
    {
        return $startDate;
    }
}