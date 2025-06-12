<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        $remember = $request->has('remember'); // <- ambil nilai checkbox

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ]);
    }

    public function ubahPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password_lama' => 'required',
            'password_baru' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!Hash::check($request->password_lama, $user->password)) {
            $errorMsg = 'Password lama tidak sesuai.';

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $errorMsg
                ], 422);
            }

            return back()->withErrors(['password_lama' => $errorMsg]);
        }

        $user->password = Hash::make($request->password_baru);
        $user->save();

        $successMsg = 'Password berhasil diubah.';

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $successMsg
            ]);
        }

        return redirect()->back()->with('success', $successMsg);
    }

    // public function logout(Request $request)
    // {
    //     Auth::logout();
    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();
    //     return redirect('/login');
    // }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
