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
        // TAMBAHKAN BARIS INI. Ini akan mematikan proses redirect dan menampilkan isi asli role-nya di layar.
    dd("Role aslinya adalah: '" . $user->role . "'", "Panjang karakternya: " . strlen($user->role));
        switch ($user->role) {
            case 'admin':
                return redirect('/home');

            case 'hrd_ga':
                return redirect('/hrd-ga/karyawan');

            case 'ppic_warehouse':
                return redirect('/warehouse/finish-good');

            case 'development':
                return redirect('/development/dashboard');

            case 'produksi':
                return redirect('/produksi/daily-report');

            case 'STAFF_QUALITY':
                return redirect('/quality/dashboard-qa');

            case 'fat':
                return redirect('/finance/invoice');

            case 'sales_marketing':
                return redirect('/sales/dashboard');

            case 'supplier':
                return redirect('/capa-8d/supplier');

            // ---> INI DIA JALUR KHUSUS UNTUK MANAGER PLAN <---
            case 'manager_plan':
                return redirect('/purchasing/purchase-request');

            default:
                return redirect('/home');
        }
    }
}