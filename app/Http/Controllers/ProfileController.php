<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user    = $request->user();
        $profile = $user->profile ?? new Profile(['user_id' => $user->id]);

        return view('profile.edit', [
            'user'    => $user,
            'profile' => $profile,
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user    = $request->user();
        $profile = $user->profile ?? Profile::create(['user_id' => $user->id]);

        $data = $request->only(['full_name', 'phone']);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($profile->avatar && Storage::disk('public')->exists($profile->avatar)) {
                Storage::disk('public')->delete($profile->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $profile->update($data);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}