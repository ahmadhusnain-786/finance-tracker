@php
    /** @var \App\Models\Category $category */
    $iconChoices = [
        'bi-tag', 'bi-tags', 'bi-cash-stack', 'bi-cash-coin', 'bi-piggy-bank',
        'bi-egg-fried', 'bi-cart', 'bi-bag', 'bi-house-door', 'bi-house',
        'bi-bus-front', 'bi-airplane', 'bi-fuel-pump', 'bi-lightning-charge',
        'bi-receipt', 'bi-heart-pulse', 'bi-book', 'bi-controller', 'bi-gift',
        'bi-shield-check', 'bi-laptop', 'bi-briefcase', 'bi-graph-up', 'bi-three-dots',
    ];
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name"
               value="{{ old('name', $category->name) }}"
               class="form-control @error('name') is-invalid @enderror"
               required maxlength="100" autofocus>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
        <select id="type" name="type"
                class="form-select @error('type') is-invalid @enderror" required>
            @foreach ($types as $t)
                <option value="{{ $t->value }}"
                    {{ old('type', $category->type?->value) === $t->value ? 'selected' : '' }}>
                    {{ $t->label() }}
                </option>
            @endforeach
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="icon" class="form-label">Icon</label>
        <select id="icon" name="icon"
                class="form-select @error('icon') is-invalid @enderror">
            <option value="">— None —</option>
            @foreach ($iconChoices as $icon)
                <option value="{{ $icon }}"
                    {{ old('icon', $category->icon) === $icon ? 'selected' : '' }}>
                    {{ $icon }}
                </option>
            @endforeach
        </select>
        @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Bootstrap Icons name (e.g. <code>bi-cart</code>).</div>
    </div>

    <div class="col-md-6">
        <label for="color" class="form-label">Color</label>
        <div class="d-flex gap-2 align-items-center">
            <input type="color" id="color" name="color"
                   value="{{ old('color', $category->color ?? '#2563eb') }}"
                   class="form-control form-control-color @error('color') is-invalid @enderror"
                   style="width:56px;height:38px;">
            <input type="text" class="form-control"
                   value="{{ old('color', $category->color ?? '#2563eb') }}"
                   disabled>
        </div>
        @error('color')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
</div>