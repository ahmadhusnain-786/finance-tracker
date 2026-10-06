@extends('admin.layouts.app')

@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('content')

<div class="alert alert-info border-0 shadow-sm">
    <i class="bi bi-info-circle me-2"></i>
    Settings here apply system-wide. Some (like currency) are only defaults — individual users can override them in their own profile.
</div>

<form method="POST" action="{{ route('admin.settings.update') }}" novalidate>
    @csrf
    @method('PUT')

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <span class="fw-semibold">Application Settings</span>
        </div>
        <div class="card-body">

            @foreach ($schema as $field)
                @php
                    $key     = $field['key'];
                    $value   = $current[$key] ?? $field['default'];
                    $label   = $field['label'];
                    $help    = $field['help'] ?? null;
                    $type    = $field['type'];
                @endphp

                <div class="mb-4">
                    <label for="{{ $key }}" class="form-label fw-semibold">{{ $label }}</label>

                    @if ($type === 'boolean')
                        <div class="form-check form-switch">
                            <input type="hidden" name="{{ $key }}" value="0">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   id="{{ $key }}" name="{{ $key }}" value="1"
                                   {{ old($key, $value) ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $key }}">
                                {{ $value ? 'Enabled' : 'Disabled' }}
                            </label>
                        </div>
                    @elseif ($type === 'select')
                        <select id="{{ $key }}" name="{{ $key }}" class="form-select">
                            @foreach ($field['options'] as $opt)
                                <option value="{{ $opt }}"
                                    {{ (string) old($key, $value) === (string) $opt ? 'selected' : '' }}>
                                    {{ $opt }}
                                </option>
                            @endforeach
                        </select>
                    @elseif ($type === 'integer')
                        <input type="number" id="{{ $key }}" name="{{ $key }}"
                               value="{{ old($key, $value) }}"
                               class="form-control">
                    @else
                        <input type="text" id="{{ $key }}" name="{{ $key }}"
                               value="{{ old($key, $value) }}"
                               class="form-control" maxlength="255">
                    @endif

                    @if ($help)
                        <div class="form-text">{{ $help }}</div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="card-footer bg-white d-flex justify-content-end">
            <button type="submit" class="btn btn-danger px-4">
                <i class="bi bi-save me-2"></i>Save Settings
            </button>
        </div>
    </div>
</form>

@endsection