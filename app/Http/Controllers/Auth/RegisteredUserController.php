<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $genderOptions = ['Laki-laki', 'Perempuan'];
        $majorOptions = ['RPL', 'TJKT', 'AKL', 'TF', 'MPLB', 'PM'];
        $classOptions = ['X', 'XI', 'XII'];

        return view('auth.register', compact('genderOptions', 'majorOptions', 'classOptions'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'numeric',],
            'address' => ['required', 'string'],
        ]);
        
        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
            ]);

            $user->studentDetail()->create([
                'nis' => $request->nis,
                'class' => $request->class,
                'major' => $request->major,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'address' => $request->address,
                'phone' => $request->phone,
            ]);
            return $user;
        });


        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
