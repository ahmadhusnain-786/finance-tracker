<?php

namespace App\Console\Commands;

use App\Services\RecurringService;
use Illuminate\Console\Command;

class ProcessRecurring extends Command
{
    protected $signature = 'recurring:process';

    protected $description = 'Create income/expense records from due recurring templates.';

    public function handle(RecurringService $recurring): int
    {
        $count = $recurring->processDue();

        if ($count === 0) {
            $this->info('No due recurring templates.');
            return self::SUCCESS;
        }

        $this->info("Processed {$count} recurring entr" . ($count === 1 ? 'y.' : 'ies.'));
        return self::SUCCESS;
    }
}