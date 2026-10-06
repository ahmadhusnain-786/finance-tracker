<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function __construct(private SettingsService $settings)
    {
    }

    public function index(): View
    {
        return view('admin.settings.index', [
            'schema'   => $this->settings->schema(),
            'current'  => $this->settings->all(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $values = [];

        foreach ($this->settings->schema() as $field) {
            $key = $field['key'];

            $values[$key] = match ($field['type']) {
                'boolean' => $request->boolean($key),
                'integer' => (int) $request->input($key, 0),
                default   => (string) $request->input($key, ''),
            };
        }

        $this->settings->setMany($values);

        ActivityLogger::log('admin.settings.updated', null, 'Updated system settings');

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'System settings saved.');
    }
}