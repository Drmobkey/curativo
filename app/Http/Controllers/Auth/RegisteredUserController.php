<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegisterMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'no_telp' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_telp' => $request->no_telp,
            'email_verified_at' => now(), 
        ]);

        $user->assignRole('user');

        event(new Registered($user));
        Auth::login($user);

        // Buat link verifikasi email manual (gunakan signed route jika perlu)
        // $link = URL::temporarySignedRoute(
        //     'verification.verify',
        //     now()->addMinutes(60),
        //     ['id' => $user->id, 'hash' => sha1($user->email)]
        // );

        // $link = "http://curativo_api.test/api/verification/". $user->id;
        // // Kirim email
        // Mail::to($user->email)->send(new RegisterMail($link));

        // return response()->json([
        //     'message' => 'User registered successfully. Please check your email for verification.'
        // ], 201);
    }
}

// return response()->noContent();

