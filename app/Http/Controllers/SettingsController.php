<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(Request $request): View
    {
        $user    = $request->user();
        $profile = $user->profile ?? new Profile(['user_id' => $user->id]);

        return view('settings.edit', [
            'user'    => $user,
            'profile' => $profile,
            'currencies'  => ['PKR', 'USD', 'EUR', 'GBP', 'AED', 'SAR', 'CAD', 'AUD'],
            'dateFormats' => [
                'd/m/Y' => 'DD/MM/YYYY (24/09/2026)',
                'm/d/Y' => 'MM/DD/YYYY (09/24/2026)',
                'Y-m-d' => 'YYYY-MM-DD (2026-09-24)',
                'd-m-Y' => 'DD-MM-YYYY (24-09-2026)',
            ],
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $user    = $request->user();
        $profile = $user->profile ?? Profile::create(['user_id' => $user->id]);

        $profile->update($request->validated());

        return redirect()
            ->route('settings.edit')
            ->with('success', 'Settings updated successfully.');
    }
}