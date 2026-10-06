<?php

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Http\Requests\StoreRecurringRequest;
use App\Http\Requests\UpdateRecurringRequest;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\RecurringTransaction;
use App\Services\RecurringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecurringController extends Controller
{
    public function __construct(private RecurringService $recurring)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', RecurringTransaction::class);

        $userId = $request->user()->id;

        $query = RecurringTransaction::query()
            ->where('user_id', $userId)
            ->with(['category', 'paymentMethod']);

        if ($q = trim((string) $request->input('q'))) {
            $query->where('title', 'like', "%{$q}%");
        }

        $status = $request->input('status', 'all');
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $templates = $query
            ->orderByDesc('is_active')
            ->orderBy('next_run_date')
            ->paginate(15)
            ->withQueryString();

        return view('recurring.index', [
            'templates' => $templates,
            'filters'   => ['q' => $request->input('q'), 'status' => $status],
            'currency'  => $request->user()->profile->currency ?? 'PKR',
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', RecurringTransaction::class);

        return view('recurring.create', [
            'template'       => new RecurringTransaction([
                'type'          => 'expense',
                'frequency'     => 'monthly',
                'start_date'    => now()->toDateString(),
                'next_run_date' => now()->toDateString(),
                'is_active'     => true,
            ]),
            'categories'     => $this->availableCategories($request->user()->id),
            'paymentMethods' => $this->availablePaymentMethods($request->user()->id),
        ]);
    }

    public function store(StoreRecurringRequest $request): RedirectResponse
    {
        $this->authorize('create', RecurringTransaction::class);

        $data = $request->validated();
        $data['user_id']       = $request->user()->id;
        $data['is_active']     = $request->boolean('is_active', true);
        $data['next_run_date'] = $data['start_date'];

        RecurringTransaction::create($data);

        return redirect()
            ->route('recurring.index')
            ->with('success', 'Recurring template created successfully.');
    }

    public function edit(Request $request, RecurringTransaction $recurring): View
    {
        $this->authorize('update', $recurring);

        return view('recurring.edit', [
            'template'       => $recurring,
            'categories'     => $this->availableCategories($request->user()->id),
            'paymentMethods' => $this->availablePaymentMethods($request->user()->id),
        ]);
    }

    public function update(UpdateRecurringRequest $request, RecurringTransaction $recurring): RedirectResponse
    {
        $this->authorize('update', $recurring);

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        // If start_date changed, resync next_run_date only if template has never run
        if (! $recurring->last_run_date && $data['start_date'] !== optional($recurring->start_date)->toDateString()) {
            $data['next_run_date'] = $data['start_date'];
        }

        $recurring->update($data);

        return redirect()
            ->route('recurring.index')
            ->with('success', 'Recurring template updated successfully.');
    }

    public function destroy(RecurringTransaction $recurring): RedirectResponse
    {
        $this->authorize('delete', $recurring);

        $recurring->delete();

        return redirect()
            ->route('recurring.index')
            ->with('success', 'Recurring template deleted successfully.');
    }

    /**
     * Manual "Run due now" action for a specific user — no waiting for scheduler.
     */
    public function runNow(Request $request): RedirectResponse
    {
        $userId = $request->user()->id;

        // Only process this user's due templates
        $due = RecurringTransaction::query()
            ->where('user_id', $userId)
            ->due()
            ->get();

        $count = 0;
        foreach ($due as $template) {
            $safety = 0;
            while ($template->is_active
                   && $template->next_run_date
                   && $template->next_run_date->lte(now())
                   && $safety < 366) {

                if ($template->end_date && $template->next_run_date->gt($template->end_date)) {
                    $template->update(['is_active' => false]);
                    break;
                }

                $this->recurring->runOne($template);
                $template->refresh();
                $count++;
                $safety++;
            }
        }

        return redirect()
            ->route('recurring.index')
            ->with('success', $count > 0
                ? "Processed {$count} recurring entr" . ($count === 1 ? 'y.' : 'ies.')
                : 'No due recurring entries right now.');
    }

    private function availableCategories(int $userId)
    {
        return Category::query()
            ->availableTo($userId)
            ->orderBy('type')
            ->orderBy('name')
            ->get();
    }

    private function availablePaymentMethods(int $userId)
    {
        return PaymentMethod::query()
            ->availableTo($userId)
            ->orderBy('name')
            ->get();
    }
}