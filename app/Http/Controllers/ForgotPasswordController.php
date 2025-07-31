<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\ResetPasswordEmail;
use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\PasswordReset;

class ForgotPasswordController extends Controller
{
    public function mailSend(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => __('Email tidak ditemukan.')]);
        }

        $existingToken = PasswordResetToken::where('email', $request->email)->first();
        if ($existingToken) {
            if ($existingToken->is_used) {
                $existingToken->is_used = false;
                $existingToken->save();
            }
            $token = $existingToken->token;
            $expiredAt = $existingToken->expired_at;
        } else {
            $token = $user->id . '-' . now()->format('Ymd') . '-' . Str::random(10);
            $expiredAt = Carbon::now()->addHours(24);
            $createdAt = Carbon::now();

            PasswordResetToken::updateOrCreate(
                ['email' => $request->email],
                ['token' => $token, 'expired_at' => $expiredAt, 'created_at' => $createdAt]
            );
        }

        Mail::to($request->email)->send(new ResetPasswordEmail($token));

        return back()->with('success', 'Permintaan berhasil terkirim!');
    }

    public function showResetPasswordForm($token) {
        $email = PasswordResetToken::where('token', $token)->pluck('email')->first();

        $user = User::where('email', $email)->first();
    
        return view('auth.reset-password', ['token' => $token, 'email' => $email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $resetToken = PasswordResetToken::where('token', $request->token)
            ->where('email', $request->email)
            ->first();

        if (!$resetToken || $resetToken->is_used) {
            return redirect()->route('login')->with('error', 'Token reset password tidak valid.');
        }

        if ($resetToken->expired_at && $resetToken->expired_at < now()) {
            return redirect()->route('login')->with('error', 'Token reset password telah kadaluarsa.');
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User tidak ditemukan.']);
        }

        $user->password = bcrypt($request->password); // ✅ Pakai bcrypt
        $user->save();

        $resetToken->update([
            'is_used' => true,
            'used_at' => now(),
        ]);

        event(new PasswordReset($user));

        return redirect()->route('mahasiswa.login')->with('success', 'Kata sandi Anda berhasil diubah. Silakan masuk dengan kata sandi baru Anda.');
    }

}
