@php
    // Menghitung jumlah dokumen PO yang butuh Approval (Sesuai Jabatan)
    $userRole = auth()->user()->role ?? '';
    $notifPO = 0;
    
    if ($userRole == 'manager_plan') {
        $notifPO = \Illuminate\Support\Facades\DB::table('purchase_orders')
                    ->where('approval_manager_plan', 'Menunggu')->count();
    } elseif ($userRole == 'direktur') {
        // Direktur hanya melihat PO yang sudah di-ACC Manager
        $notifPO = \Illuminate\Support\Facades\DB::table('purchase_orders')
                    ->where('approval_manager_plan', 'Approved')
                    ->where('approval_direktur', 'Menunggu')->count();
    } elseif ($userRole == 'presiden_direktur') {
        $notifPO = \Illuminate\Support\Facades\DB::table('purchase_orders')
                    ->where('approval_direktur', 'Approved')
                    ->where('approval_presdir', 'Menunggu')->count();
    } else {
        $notifPO = \Illuminate\Support\Facades\DB::table('purchase_orders')
                    ->where('approval_manager_plan', 'Menunggu')
                    ->orWhere('approval_direktur', 'Menunggu')
                    ->orWhere('approval_presdir', 'Menunggu')->count();
    }
@endphp

<ul class="menu-list">
    <li>
        <a href="{{ url('/dashboard') }}" style="{{ request()->is('dashboard*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
            <i class="fas fa-th-large"></i> <span>Dashboard Direktur</span>
        </a>
    </li>

    <li style="padding: 15px 15px 5px 15px; font-size: 11px; font-weight: bold; color: #94a3b8; text-transform: uppercase; pointer-events: none;">
        Tugas Eksekutif
    </li>

    <li>
        <a href="{{ url('/purchasing/approval-po') }}" style="{{ request()->is('purchasing/approval-po*') ? 'background-color: var(--sidebar-hover); color: #ffffff; font-weight: bold;' : '' }}">
            <i class="fas fa-file-signature" style="{{ request()->is('purchasing/approval-po*') ? 'color: #ffffff;' : 'color: #10b981;' }}"></i> 
            <span>Approval PO Keluar</span>
            
            <!-- [BARU] Notifikasi jika ada PO yang belum di-approve Direktur -->
            @if($notifPO > 0)
                <span style="background: #ef4444; color: white; padding: 2px 7px; border-radius: 12px; font-size: 11px; font-weight: bold; margin-left: auto; box-shadow: 0 2px 4px rgba(239,68,68,0.3); animation: pulseNotif 2s infinite;">
                    {{ $notifPO }}
                </span>
            @endif
        </a>
    </li>
</ul>

<!-- Animasi Kedap-kedip untuk Badge Notifikasi -->
<style>
    @keyframes pulseNotif {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
</style>