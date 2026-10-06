@php
    /** @var \App\Models\Budget $budget */
    $periods = [
        'weekly'  => 'Weekly',
        'monthly' => 'Monthly',
        'yearly'  => 'Yearly',
    ];
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="category_id" class="form-label">Expense Category <span class="text-danger">*</span></label>
        <select id="category_id" name="category_id"
                class="form-select @error('category_id') is-invalid @enderror" required>
            <option value="">— Select a category —</option>
            @foreach ($categories as $category)
                @php
                    $isUsed = in_array($category->id, $usedCategoryIds ?? [], true)
                              && (int) old('category_id', $budget->category_id) !== $category->id;
                @endphp
                <option value="{{ $category->id }}"
                        {{ $isUsed ? 'disabled' : '' }}
                        {{ (int) old('category_id', $budget->category_id) === $category->id ? 'selected' : '' }}>
                    {{ $category->name }}{{ $isUsed ? ' — already budgeted' : '' }}
                </option>
            @endforeach
        </select>
        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Only expense categories can have budgets.</div>
    </div>

    <div class="col-md-6">
        <label for="amount" class="form-label">Budget Limit <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0.01"
               id="amount" name="amount"
               value="{{ old('amount', $budget->amount) }}"
               class="form-control @error('amount') is-invalid @enderror"
               required>
        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="period" class="form-label">Period <span class="text-danger">*</span></label>
        <select id="period" name="period"
                class="form-select @error('period') is-invalid @enderror" required>
            @foreach ($periods as $val => $label)
                <option value="{{ $val }}"
                    {{ old('period', $budget->period ?? 'monthly') === $val ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('period')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Status</label>
        <div class="form-check form-switch mt-2">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" role="switch"
                   id="is_active" name="is_active" value="1"
                   {{ old('is_active', $budget->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>

    <div class="col-12">
        <label for="notes" class="form-label">Notes</label>
        <textarea id="notes" name="notes" rows="2"
                  class="form-control @error('notes') is-invalid @enderror"
                  maxlength="1000"
                  placeholder="Optional. E.g. Includes groceries and eating out.">{{ old('notes', $budget->notes) }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>