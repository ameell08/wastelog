<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            /** @var \App\Models\Pengguna $user */
            $user = Auth::user(); // editor tahu ini class Pengguna
            return $this->redirectByRole($user->getRole());
        }

        return view('auth.login');
    }

    public function postlogin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->remember)) {
            /** @var \App\Models\Pengguna $user */
            $user = Auth::user();
            return $this->redirectByRole($user->getRole());
        }

        return redirect()->back()->withErrors([
            'login' => 'Email atau password salah.',
        ])->withInput($request->only('email'));
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    protected function redirectByRole($role)
    {
        switch ($role) {
            case 'SDM':
                return redirect('/dashboard');
            case 'ADM1':
                return redirect('/inputlimbahmasuk');
            case 'ADM2':
                return redirect('/inputlimbaholah');
            case 'PMP':
                return redirect('/dashboard');
            default:
                return redirect('/');
        }
    }
}
