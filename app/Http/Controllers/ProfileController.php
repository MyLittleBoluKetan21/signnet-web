<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $userId = $user->getAuthIdentifier();
        $primaryKeyName = $user->getKeyName();

        // 1. Gunakan Validator manual agar aman untuk Fetch API / JSON
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique($user->getTable(), 'email')->ignore($userId, $primaryKeyName),
            ],
        ]);

        // 2. Jika validasi gagal, paksa kembalikan respon JSON 422
        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return Redirect::route('profile.edit')->withErrors($validator)->withInput();
        }

        // 3. Ambil data yang sudah tervalidasi
        $user->fill($validator->validated());

        // if ($user->isDirty('email')) {
        //     $user->email_verified_at = null;
        // }

        $user->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Perubahan konfigurasi akun berhasil disimpan.'
            ], 200);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}