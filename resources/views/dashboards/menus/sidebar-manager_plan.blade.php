@php
    // 1. Menghitung jumlah dokumen PR yang butuh Approval
    $butuhApproval = \Illuminate\Support\Facades\DB::table('purchase_requests')
                        ->where('status_approval', 'Pending')
                        ->count();

    // 2. [BARU] Menghitung jumlah dokumen PO yang butuh Approval (Sesuai Jabatan)
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
        $notifPO = \Illuminate\Support\Facades\DB::table('purchase_orders')
                    ->where('approval_direktur', 'Approved')
                    ->where('approval_presdir', 'Menunggu')->count();
    } else {
        // Untuk Super Admin (melihat semua yang belum tuntas)
        $notifPO = \Illuminate\Support\Facades\DB::table('purchase_orders')
                    ->where('approval_manager_plan', 'Menunggu')
                    ->orWhere('approval_direktur', 'Menunggu')
                    ->orWhere('approval_presdir', 'Menunggu')->count();
    }
@endphp

<ul class="menu-list">
    <!-- Menu Dashboard -->
    <li>
        <a href="{{ url('/dashboard') }}" style="{{ request()->is('dashboard*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
            <i class="fas fa-th-large"></i> <span>Dashboard Utama</span>
        </a>
    </li>

    <!-- Pembatas Kategori -->
    <li style="padding: 15px 15px 5px 15px; font-size: 11px; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; pointer-events: none;">
        Tugas & Approval
    </li>

    <!-- Menu Approval PR -->
    <li>
        <a href="{{ url('/purchasing/purchase-request') }}" style="{{ request()->is('purchasing/purchase-request*') ? 'background-color: var(--sidebar-hover); color: #ffffff; font-weight: bold;' : '' }}">
            <i class="fas fa-file-signature" style="{{ request()->is('purchasing/purchase-request*') ? 'color: #ffffff;' : 'color: #10b981;' }}"></i> 
            <span>Approval PR Masuk</span>
            
            <!-- Notifikasi jika ada PR yang belum di-approve -->
            @if($butuhApproval > 0)
                <span style="background: #ef4444; color: white; padding: 2px 7px; border-radius: 12px; font-size: 11px; font-weight: bold; margin-left: auto; box-shadow: 0 2px 4px rgba(239,68,68,0.3); animation: pulseNotif 2s infinite;">
                    {{ $butuhApproval }}
                </span>
            @endif
        </a>
    </li>
    
    <!-- [DIPERBAIKI] Menu Approval PO -->
    <li>
        <a href="{{ url('/purchasing/approval-po') }}" style="{{ request()->is('purchasing/approval-po*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
            <i class="fas fa-stamp" style="{{ request()->is('purchasing/approval-po*') ? 'color: #3b82f6;' : 'color: #10b981;' }}"></i> 
            <span>Approval PO Keluar</span>

            <!-- [BARU] Notifikasi jika ada PO yang belum di-approve -->
            @if($notifPO > 0)
                <span style="background: #ef4444; color: white; padding: 2px 7px; border-radius: 12px; font-size: 11px; font-weight: bold; margin-left: auto; box-shadow: 0 2px 4px rgba(239,68,68,0.3); animation: pulseNotif 2s infinite;">
                    {{ $notifPO }}
                </span>
            @endif
        </a>
    </li>

    <!-- Pembatas Kategori -->
    <li style="padding: 15px 15px 5px 15px; font-size: 11px; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; pointer-events: none;">
        Monitoring (Pantauan)
    </li>

    <!-- Pantauan PPIC -->
    <li>
        <a href="{{ url('/ppic/mrp-dashboard') }}" style="{{ request()->is('ppic/mrp-dashboard*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
            <i class="fas fa-chart-line"></i> <span>Pantauan Hasil MRP</span>
        </a>
    </li>

    <!-- Pantauan Purchasing -->
    <li>
        <a href="{{ url('/purchasing/outstanding-po') }}" style="{{ request()->is('purchasing/outstanding-po*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
            <i class="fas fa-truck-loading" style="{{ request()->is('purchasing/outstanding-po*') ? 'color: #3b82f6;' : 'color: #f59e0b;' }}"></i> 
            <span>Outstanding PO</span>
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