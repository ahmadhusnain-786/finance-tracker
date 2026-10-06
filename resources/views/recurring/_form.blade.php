@php
    /** @var \App\Models\RecurringTransaction $template */
    $frequencies = \App\Enums\RecurringFrequency::cases();
    $types = \App\Enums\TransactionType::cases();
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" id="title" name="title"
               value="{{ old('title', $template->title) }}"
               class="form-control @error('title') is-invalid @enderror"
               required maxlength="150" autofocus
               placeholder="e.g. Monthly Rent">
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0.01"
               id="amount" name="amount"
               value="{{ old('amount', $template->amount) }}"
               class="form-control @error('amount') is-invalid @enderror"
               required>
        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
        <select id="type" name="type"
                class="form-select @error('type') is-invalid @enderror" required>
            @foreach ($types as $t)
                <option value="{{ $t->value }}"
                    {{ old('type', $template->type?->value) === $t->value ? 'selected' : '' }}>
                    {{ $t->label() }}
                </option>
            @endforeach
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
        <select id="category_id" name="category_id"
                class="form-select @error('category_id') is-invalid @enderror" required>
            <option value="">— Select —</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}"
                        data-type="{{ $cat->type->value }}"
                        {{ (int) old('category_id', $template->category_id) === $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }} ({{ $cat->type->label() }})
                </option>
            @endforeach
        </select>
        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Category must match the Type above.</div>
    </div>

    <div class="col-md-4">
        <label for="payment_method_id" class="form-label">Payment Method</label>
        <select id="payment_method_id" name="payment_method_id"
                class="form-select @error('payment_method_id') is-invalid @enderror">
            <option value="">— None —</option>
            @foreach ($paymentMethods as $m)
                <option value="{{ $m->id }}"
                    {{ (int) old('payment_method_id', $template->payment_method_id) === $m->id ? 'selected' : '' }}>
                    {{ $m->name }}
                </option>
            @endforeach
        </select>
        @error('payment_method_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="frequency" class="form-label">Frequency <span class="text-danger">*</span></label>
        <select id="frequency" name="frequency"
                class="form-select @error('frequency') is-invalid @enderror" required>
            @foreach ($frequencies as $f)
                <option value="{{ $f->value }}"
                    {{ old('frequency', $template->frequency?->value) === $f->value ? 'selected' : '' }}>
                    {{ $f->label() }}
                </option>
            @endforeach
        </select>
        @error('frequency')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
        <input type="date" id="start_date" name="start_date"
               value="{{ old('start_date', optional($template->start_date)->toDateString() ?? now()->toDateString()) }}"
               class="form-control @error('start_date') is-invalid @enderror"
               required>
        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="end_date" class="form-label">End Date</label>
        <input type="date" id="end_date" name="end_date"
               value="{{ old('end_date', optional($template->end_date)->toDateString()) }}"
               class="form-control @error('end_date') is-invalid @enderror">
        @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Optional. Leave empty for indefinite.</div>
    </div>

    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" role="switch"
                   id="is_active" name="is_active" value="1"
                   {{ old('is_active', $template->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>

    <div class="col-12">
        <label for="description" class="form-label">Description</label>
        <textarea id="description" name="description" rows="2"
                  class="form-control @error('description') is-invalid @enderror"
                  maxlength="2000"
                  placeholder="Optional">{{ old('description', $template->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>