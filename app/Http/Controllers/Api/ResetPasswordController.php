<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function passwordEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json(['message' => 'Password reset email sent successfully'], 200);
            } else {
                throw ValidationException::withMessages([
                    'email' => [__($status)],
                ]);
            }
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getCode(), $e->getTrace());
            return response()->json(['error' => 'There was an issue sending the password reset email. Please try again later.'], 500);
        }
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successful. You can now login.'], 200);
        } else {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ])->status(422); 
        }
    }
}
