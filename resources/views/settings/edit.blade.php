@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-4">Preferences</h5>

                <form method="POST" action="{{ route('settings.update') }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="currency" class="form-label">Preferred Currency</label>
                        <select id="currency" name="currency"
                                class="form-select @error('currency') is-invalid @enderror">
                            @foreach ($currencies as $code)
                                <option value="{{ $code }}"
                                    {{ old('currency', $profile->currency) === $code ? 'selected' : '' }}>
                                    {{ $code }}
                                </option>
                            @endforeach
                        </select>
                        @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Used to display all amounts in the app.</div>
                    </div>

                    <div class="mb-3">
                        <label for="timezone" class="form-label">Timezone</label>
                        <input type="text" id="timezone" name="timezone"
                               value="{{ old('timezone', $profile->timezone) }}"
                               class="form-control @error('timezone') is-invalid @enderror"
                               placeholder="Asia/Karachi">
                        @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">PHP timezone identifier, e.g. <code>Asia/Karachi</code>, <code>UTC</code>, <code>America/New_York</code>.</div>
                    </div>

                    <div class="mb-4">
                        <label for="date_format" class="form-label">Date Format</label>
                        <select id="date_format" name="date_format"
                                class="form-select @error('date_format') is-invalid @enderror">
                            @foreach ($dateFormats as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('date_format', $profile->date_format) === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('date_format')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Save Settings
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection