<?php
/**
 * Created by Beyond Plus <bplusmyanmar@hotmail.com>
 * User: Beyond Plus
 * Date: D/M/Y
 * Time: MM:HH PM
 */
namespace App\Http\Controllers\BpAdmin;
use Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Session;

class Main extends Controller
{

    public function login(Request $request)
    {
        auth()->guard('admins')->logout();
        // Post back to whatever URL this form was reached at (default or secret path).
        return view('auth/adminlogin', ['loginAction' => $request->url()]);
    }

    public function loginAdmin(Request $request)
    {
        $admin = auth()->guard('admins');

        // Rate limit login attempts per IP: 5 tries, then locked for the window.
        $throttleKey = 'admin-login:'.$request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return view('auth/adminlogin', [
                'match'       => "Too many login attempts. Try again in {$seconds} seconds.",
                'loginAction' => $request->url(),
            ]);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($throttleKey, 60);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $error = 'Username and Password does not match';
        $credentials = ['email' => $request->input('email'), 'password' => $request->input('password')];

        // Hardened login: if a secret path is set, the default bp-admin/login is a
        // decoy that never authenticates. We still run attempt() (for timing) then
        // log out, so the decoy response is indistinguishable from a real failure.
        $loginPath = trim((string) bp_option('admin_login_path', ''), '/');
        $isDecoy = $loginPath !== '' && preg_match('#(^|/)bp-admin/login$#', $request->path());

        if ($isDecoy) {
            $admin->attempt($credentials);
            $admin->logout();
            RateLimiter::hit($throttleKey, 60);
            return view('auth/adminlogin', ['match' => $error, 'loginAction' => $request->url()]);
        }

        if ($admin->attempt($credentials)) {
            RateLimiter::clear($throttleKey);
            return redirect()->intended('bp-admin');
        }

        RateLimiter::hit($throttleKey, 60);
        return view('auth/adminlogin', ['match' => $error, 'loginAction' => $request->url()]);
    }

    public function logout()
    {
        auth()->guard('admins')->logout();
        return redirect('/');
    }

}
