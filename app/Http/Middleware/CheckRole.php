<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Jika user belum login, lempar ke halaman login
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Ubah role user ke huruf kecil agar aman dari perbedaan kapitalisasi (misal: ADMIN, Admin, admin)
        $userRole = strtolower(auth()->user()->role);

        // Admin utama (super admin) bebas akses ke mana saja
        if ($userRole === 'admin') {
            return $next($request);
        }

        // Ubah juga semua array roles yang dikirim dari route menjadi huruf kecil
        $roles = array_map('strtolower', $roles);

        // Cek apakah role user saat ini ada di dalam daftar izin middleware
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Jika tidak punya akses, kembalikan ke /home dengan pesan error
        return redirect('/home')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut!');
    }
}