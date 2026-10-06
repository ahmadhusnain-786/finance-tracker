@php
    /** @var \App\Models\PaymentMethod $method */
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name"
               value="{{ old('name', $method->name) }}"
               class="form-control @error('name') is-invalid @enderror"
               required maxlength="100" autofocus
               placeholder="e.g. My HBL Account">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
        <select id="type" name="type"
                class="form-select @error('type') is-invalid @enderror" required>
            @foreach ($types as $t)
                <option value="{{ $t->value }}"
                    {{ old('type', $method->type?->value) === $t->value ? 'selected' : '' }}>
                    {{ $t->label() }}
                </option>
            @endforeach
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">Description</label>
        <textarea id="description" name="description" rows="2"
                  class="form-control @error('description') is-invalid @enderror"
                  maxlength="500"
                  placeholder="Optional. E.g. Account # ending 4321">{{ old('description', $method->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <div class="form-check form-switch">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" role="switch"
                   id="is_active" name="is_active" value="1"
                   {{ old('is_active', $method->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
        <div class="form-text">Inactive methods are hidden from selection lists but historical records remain intact.</div>
    </div>
</div>