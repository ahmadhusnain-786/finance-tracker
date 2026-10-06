<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethodType;
use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', PaymentMethod::class);

        $userId = $request->user()->id;

        $query = PaymentMethod::query()
            ->availableTo($userId)
            ->withCount(['income', 'expenses']);

        // Filter: all / mine / system
        if ($scope = $request->input('scope')) {
            match ($scope) {
                'mine'   => $query->where('user_id', $userId),
                'system' => $query->whereNull('user_id'),
                default  => null,
            };
        }

        // Filter by active status
        if ($request->input('status') === 'active') {
            $query->where('is_active', true);
        } elseif ($request->input('status') === 'inactive') {
            $query->where('is_active', false);
        }

        // Search by name
        if ($search = trim((string) $request->input('q'))) {
            $query->where('name', 'like', "%{$search}%");
        }

        $methods = $query
            ->orderByRaw('user_id IS NULL ASC')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('payment-methods.index', [
            'methods' => $methods,
            'filters' => [
                'scope'  => $request->input('scope'),
                'status' => $request->input('status'),
                'q'      => $request->input('q'),
            ],
            'types' => PaymentMethodType::cases(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', PaymentMethod::class);

        return view('payment-methods.create', [
            'method' => new PaymentMethod([
                'type'      => PaymentMethodType::Cash->value,
                'is_active' => true,
            ]),
            'types' => PaymentMethodType::cases(),
        ]);
    }

    public function store(StorePaymentMethodRequest $request): RedirectResponse
    {
        $this->authorize('create', PaymentMethod::class);

        $data = $request->validated();
        $data['user_id']    = $request->user()->id;
        $data['is_system']  = false;
        $data['is_active']  = $request->boolean('is_active', true);

        PaymentMethod::create($data);

        return redirect()
            ->route('payment-methods.index')
            ->with('success', 'Payment method created successfully.');
    }

    public function edit(PaymentMethod $payment_method): View
    {
        $this->authorize('update', $payment_method);

        return view('payment-methods.edit', [
            'method' => $payment_method,
            'types'  => PaymentMethodType::cases(),
        ]);
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $payment_method): RedirectResponse
    {
        $this->authorize('update', $payment_method);

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $payment_method->update($data);

        return redirect()
            ->route('payment-methods.index')
            ->with('success', 'Payment method updated successfully.');
    }

    public function destroy(PaymentMethod $payment_method): RedirectResponse
    {
        $this->authorize('delete', $payment_method);

        $payment_method->delete();

        return redirect()
            ->route('payment-methods.index')
            ->with('success', 'Payment method deleted successfully.');
    }
}