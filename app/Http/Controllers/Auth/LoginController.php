<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Redirection dinamis berdasarkan Role setelah user berhasil login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        switch ($user->role) {
            case 'admin':
                return redirect('/qualisync/home');

            case 'hrd_ga':
                return redirect('/qualisync/hrd-ga/karyawan');

            case 'ppic_warehouse':
                return redirect('/qualisync/warehouse/finish-good');

            case 'development':
                return redirect('/qualisync/development/dashboard');

            case 'produksi':
                return redirect('/qualisync/produksi/daily-report');

            case 'STAFF_QUALITY':
                return redirect('/qualisync/dashboard-qa');

            case 'fat':
                return redirect('/qualisync/finance/invoice');

            case 'sales_marketing':
                return redirect('/qualisync/sales/dashboard');

            case 'supplier':
                return redirect('/qualisync/capa-8d/supplier');

            // ---> INI DIA JALUR KHUSUS UNTUK MANAGER PLAN <---
            case 'manager_plan':
                return redirect('/qualisync/purchasing/purchase-request');

            default:
                return redirect('/qualisync/home');
        }
    }
}