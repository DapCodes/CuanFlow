<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\ColorPalette;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:edit profil', only: ['edit']),
            new Middleware('permission:update profil', only: ['update']),
        ];
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user'           => $request->user(),
            'colorPalettes'  => ColorPalette::orderBy('sort_order')->get(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's color palette preference (AJAX – no page reload).
     */
    public function updateColorPalette(Request $request): JsonResponse
    {
        $request->validate([
            'color_palette_id' => ['required', 'exists:color_palettes,id'],
        ]);

        $user = $request->user();
        $user->color_palette_id = $request->color_palette_id;
        $user->save();

        $palette = ColorPalette::find($request->color_palette_id);

        return response()->json([
            'success' => true,
            'palette' => $palette->toTailwindColors(),
        ]);
    }
}
