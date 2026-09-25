@php
    // Menghitung jumlah dokumen PO yang butuh Approval (Sesuai Jabatan)
    $userRole = auth()->user()->role ?? '';
    $notifPO = 0;
    
    if ($userRole == 'manager_plan') {
        $notifPO = \Illuminate\Support\Facades\DB::table('purchase_orders')
                    ->where('approval_manager_plan', 'Menunggu')->count();
    } elseif ($userRole == 'direktur') {
        $notifPO = \Illuminate\Support\Facades\DB::table('purchase_orders')
                    ->where('approval_manager_plan', 'Approved')
                    ->where('approval_direktur', 'Menunggu')->count();
    } elseif ($userRole == 'presiden_direktur') {
        // Presdir hanya melihat PO > 50 Juta yang sudah lolos dari Direktur
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
            <i class="fas fa-chart-pie"></i> <span>Dashboard Presdir</span>
        </a>
    </li>

    <li style="padding: 15px 15px 5px 15px; font-size: 11px; font-weight: bold; color: #94a3b8; text-transform: uppercase; pointer-events: none;">
        Tugas Eksekutif
    </li>

    <li>
        <a href="{{ url('/purchasing/approval-po') }}" style="{{ request()->is('purchasing/approval-po*') ? 'background-color: var(--sidebar-hover); color: #ffffff; font-weight: bold;' : '' }}">
            <i class="fas fa-stamp" style="{{ request()->is('purchasing/approval-po*') ? 'color: #ffffff;' : 'color: #f59e0b;' }}"></i> 
            <span>Persetujuan PO Khusus</span>
            
            <!-- [BARU] Notifikasi jika ada PO Khusus yang belum di-approve Presdir -->
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