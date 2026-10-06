@php
    /** @var \App\Models\Income $income */
    $frequencies = \App\Enums\RecurringFrequency::cases();
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" id="title" name="title"
               value="{{ old('title', $income->title) }}"
               class="form-control @error('title') is-invalid @enderror"
               required maxlength="150" autofocus
               placeholder="e.g. September Salary">
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="amount" class="form-label">Amount (Rs.) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0.01"
               id="amount" name="amount"
               value="{{ old('amount', $income->amount) }}"
               class="form-control @error('amount') is-invalid @enderror"
               required>
        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Use a positive number, up to 2 decimal places.</div>
    </div>

    <div class="col-md-6">
        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
        <select id="category_id" name="category_id"
                class="form-select @error('category_id') is-invalid @enderror" required>
            <option value="">— Select a category —</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    {{ (int) old('category_id', $income->category_id) === $category->id ? 'selected' : '' }}>
                    {{ $category->name }}{{ $category->is_system ? ' (System)' : '' }}
                </option>
            @endforeach
        </select>
        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="payment_method_id" class="form-label">Payment Method</label>
        <select id="payment_method_id" name="payment_method_id"
                class="form-select @error('payment_method_id') is-invalid @enderror">
            <option value="">— None —</option>
            @foreach ($paymentMethods as $method)
                <option value="{{ $method->id }}"
                    {{ (int) old('payment_method_id', $income->payment_method_id) === $method->id ? 'selected' : '' }}>
                    {{ $method->name }}{{ $method->is_system ? ' (System)' : '' }}
                </option>
            @endforeach
        </select>
        @error('payment_method_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
        <input type="date" id="date" name="date"
               value="{{ old('date', optional($income->date)->toDateString() ?? now()->toDateString()) }}"
               max="{{ now()->toDateString() }}"
               class="form-control @error('date') is-invalid @enderror"
               required>
        @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="recurring_frequency" class="form-label">Recurring</label>
        <div class="input-group">
            <div class="input-group-text">
                <input type="hidden" name="is_recurring" value="0">
                <input class="form-check-input mt-0" type="checkbox"
                       id="is_recurring" name="is_recurring" value="1"
                       {{ old('is_recurring', $income->is_recurring) ? 'checked' : '' }}>
            </div>
            <select id="recurring_frequency" name="recurring_frequency"
                    class="form-select @error('recurring_frequency') is-invalid @enderror">
                <option value="">— Not recurring —</option>
                @foreach ($frequencies as $freq)
                    <option value="{{ $freq->value }}"
                        {{ old('recurring_frequency', $income->recurring_frequency?->value) === $freq->value ? 'selected' : '' }}>
                        {{ $freq->label() }}
                    </option>
                @endforeach
            </select>
        </div>
        @error('recurring_frequency')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        <div class="form-text">Check the box + pick a frequency for automatic entries (coming in Phase 15).</div>
    </div>

    <div class="col-12">
        <label for="description" class="form-label">Description</label>
        <textarea id="description" name="description" rows="3"
                  class="form-control @error('description') is-invalid @enderror"
                  maxlength="2000"
                  placeholder="Optional notes">{{ old('description', $income->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>