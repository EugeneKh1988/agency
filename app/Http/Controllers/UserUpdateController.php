<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class UserUpdateController extends Controller
{
    // update user data
    public function update(Request $request): JsonResponse {
        $request->validate([
            'name' => ['sometimes','required','string','min:3','max:255'],
            'email' => ['sometimes', 'required', 'lowercase','email', 'max:255', 'unique:'.User::class],
            'password' => ['sometimes','required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = $request->user();
        // change name
        if($user && $request->has('name')) {
            if($user->name != $request->name) {
                $user->name = $request->name;
            }
        }

        // change email
        if($user && $request->has('email')) {
            if($user->email != $request->email) {
                $user->email = $request->email;
                $user->email_verified_at = null;
                $request->user()->sendEmailVerificationNotification();
            }
        }

        // change password
        if($user && $request->has('password')) {
            $user->forceFill([
                    'password' => Hash::make($request->string('password')),
                    'remember_token' => Str::random(60),
            ]);

            event(new PasswordReset($user));
        }

        // save when changed and login
        if($user && $user->isDirty("password")) {
            $user->save();
            // login
            Auth::guard('web')->login($user);
        }
        if($user && $user->isDirty()) {
            $user->save();
        }

        return response()->json(['status' => 'User data was changed']);
    }
}
