@php
    /** @var \App\Models\FinancialGoal $goal */
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label for="name" class="form-label">Goal Name <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name"
               value="{{ old('name', $goal->name) }}"
               class="form-control @error('name') is-invalid @enderror"
               required maxlength="150" autofocus
               placeholder="e.g. New Laptop">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="status" class="form-label">Status</label>
        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
            <option value="active"    {{ old('status', $goal->status?->value) === 'active'    ? 'selected' : '' }}>Active</option>
            <option value="completed" {{ old('status', $goal->status?->value) === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ old('status', $goal->status?->value) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="target_amount" class="form-label">Target Amount <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0.01"
               id="target_amount" name="target_amount"
               value="{{ old('target_amount', $goal->target_amount) }}"
               class="form-control @error('target_amount') is-invalid @enderror"
               required>
        @error('target_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="target_date" class="form-label">Target Date</label>
        <input type="date" id="target_date" name="target_date"
               value="{{ old('target_date', optional($goal->target_date)->toDateString()) }}"
               class="form-control @error('target_date') is-invalid @enderror">
        @error('target_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Optional. Used for deadline reminders.</div>
    </div>

    <div class="col-12">
        <label for="description" class="form-label">Description</label>
        <textarea id="description" name="description" rows="3"
                  class="form-control @error('description') is-invalid @enderror"
                  maxlength="2000"
                  placeholder="Optional notes about this goal">{{ old('description', $goal->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>