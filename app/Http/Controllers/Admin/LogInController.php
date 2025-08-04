<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogInController extends Controller
{
    public function showLoginForm() {
        
        if(Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('pages.admin.auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Error message & validates using Auth::attempt() to avoid SQL injection
        if (!Auth::attempt($validated)) {
           return back()->with('error', 'Invalid email or password');           
        }

        $user = Auth::user();

        // success message
        return redirect()->route('admin.dashboard')->with('success', "Login successful! Welcome, {$user->name}.");
    }

  
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin');
    }
}
