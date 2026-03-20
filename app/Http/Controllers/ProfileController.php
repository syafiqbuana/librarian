<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $genderOptions = ['Laki-laki', 'Perempuan'];
        $majorOptions = ['RPL', 'TJKT', 'AKL', 'TF', 'MPLB', 'PM'];
        $classOptions = ['X', 'XI', 'XII'];
        $user = $request->user()->load('studentDetail');
        return view('profile.edit', [
            'user' => $user,
            'genderOptions' => $genderOptions,
            'majorOptions' => $majorOptions,
            'classOptions' => $classOptions,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // update tabel users
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // update tabel student_details
        $user->studentDetail()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $request->phone,
                'address' => $request->address,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'class' => $request->class,
                'major' => $request->major,
            ]
        );

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
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
