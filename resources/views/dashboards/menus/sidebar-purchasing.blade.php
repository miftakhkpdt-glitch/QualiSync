@php
    // 1. Menghitung TOTAL seluruh PR yang 'Pending'
    $pendingPrCount = \Illuminate\Support\Facades\DB::table('purchase_requests')
                        ->where('status', 'Pending')
                        ->count();

    // [BARU] 2. Menghitung PR Pending PER DEPARTEMEN
    $pendingPpic = \Illuminate\Support\Facades\DB::table('purchase_requests')->where('status', 'Pending')->where('departemen', 'LIKE', '%ppic%')->count();
    $pendingEng  = \Illuminate\Support\Facades\DB::table('purchase_requests')->where('status', 'Pending')->where('departemen', 'LIKE', '%engineering%')->count();
    $pendingQc   = \Illuminate\Support\Facades\DB::table('purchase_requests')->where('status', 'Pending')->where('departemen', 'LIKE', '%quality%')->count();
    $pendingHrd  = \Illuminate\Support\Facades\DB::table('purchase_requests')->where('status', 'Pending')->where('departemen', 'LIKE', '%hrd_ga%')->count();
    $pendingDev  = \Illuminate\Support\Facades\DB::table('purchase_requests')->where('status', 'Pending')->where('departemen', 'LIKE', '%development%')->count();

    // 3. Menghitung jumlah Outstanding PO
    $outstandingPoCount = \Illuminate\Support\Facades\DB::table('purchase_orders')
                        ->where('status_barang', '!=', 'Sudah Diterima')
                        ->count();
                        
    // Logika deteksi menu Dropdown Aktif
    $isPrActive = request()->is('purchasing/purchase-request*');
@endphp

<!-- 1. Dashboard Utama Purchasing -->
<li>
    <a href="{{ url('/purchasing/dashboard') }}" style="{{ request()->is('purchasing/dashboard*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-th-large" style="width: 20px;"></i> Dashboard Utama
    </a>
</li>
<li>
    <a href="{{ route('purchasing.vendors.index') }}" style="{{ request()->routeIs('purchasing.vendors.*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-address-book" style="width: 20px;"></i> Daftar Supplier / Vendor
    </a>
</li>

<!-- MENU PR MASUK DENGAN DROPDOWN DEPARTEMEN & NOTIFIKASI -->
<li class="menu-item-has-children">
    <a href="#" onclick="toggleMenuLengkap(document.getElementById('prMasukSubmenu'), this.querySelector('i.fa-chevron-down')); return false;" style="display: flex; align-items: center; {{ $isPrActive ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-file-invoice" style="width: 20px;"></i> 
        <span>Dokumen PR Masuk</span>
        
        <!-- Wadah untuk Notifikasi Induk & Panah Dropdown -->
        <div style="margin-left: auto; display: flex; align-items: center; gap: 10px;">
            @if($pendingPrCount > 0)
                <span style="background: #ef4444; color: white; padding: 2px 7px; border-radius: 12px; font-size: 11px; font-weight: bold; box-shadow: 0 2px 4px rgba(239,68,68,0.3); animation: pulseNotif 2s infinite;">
                    {{ $pendingPrCount }}
                </span>
            @endif
            <i class="fas fa-chevron-down" style="font-size: 12px; transition: transform 0.3s ease; {{ $isPrActive ? 'transform: rotate(180deg);' : '' }}"></i>
        </div>
    </a>
    
    <!-- SUB-MENU DEPARTEMEN DENGAN BADGE ANGKA -->
    <ul class="sub-menu" id="prMasukSubmenu" style="display: {{ $isPrActive ? 'block' : 'none' }};">
        <li>
            <a href="{{ url('/purchasing/purchase-request?dept=ppic') }}" style="display: flex; justify-content: space-between; align-items: center; {{ request()->query('dept') == 'ppic' ? 'color: #3b82f6; font-weight: bold;' : '' }}">
                <span><i class="fas fa-angle-right"></i> PR dari PPIC</span>
                @if($pendingPpic > 0) <span style="background: #ef4444; color: white; padding: 1px 6px; border-radius: 10px; font-size: 10px; font-weight: bold;">{{ $pendingPpic }}</span> @endif
            </a>
        </li>
        <li>
            <a href="{{ url('/purchasing/purchase-request?dept=engineering') }}" style="display: flex; justify-content: space-between; align-items: center; {{ request()->query('dept') == 'engineering' ? 'color: #3b82f6; font-weight: bold;' : '' }}">
                <span><i class="fas fa-angle-right"></i> PR dari Engineering</span>
                @if($pendingEng > 0) <span style="background: #ef4444; color: white; padding: 1px 6px; border-radius: 10px; font-size: 10px; font-weight: bold;">{{ $pendingEng }}</span> @endif
            </a>
        </li>
        <li>
            <a href="{{ url('/purchasing/purchase-request?dept=quality') }}" style="display: flex; justify-content: space-between; align-items: center; {{ request()->query('dept') == 'quality' ? 'color: #3b82f6; font-weight: bold;' : '' }}">
                <span><i class="fas fa-angle-right"></i> PR dari Quality</span>
                @if($pendingQc > 0) <span style="background: #ef4444; color: white; padding: 1px 6px; border-radius: 10px; font-size: 10px; font-weight: bold;">{{ $pendingQc }}</span> @endif
            </a>
        </li>
        <li>
            <a href="{{ url('/purchasing/purchase-request?dept=hrd_ga') }}" style="display: flex; justify-content: space-between; align-items: center; {{ request()->query('dept') == 'hrd_ga' ? 'color: #3b82f6; font-weight: bold;' : '' }}">
                <span><i class="fas fa-angle-right"></i> PR dari HRD & GA</span>
                @if($pendingHrd > 0) <span style="background: #ef4444; color: white; padding: 1px 6px; border-radius: 10px; font-size: 10px; font-weight: bold;">{{ $pendingHrd }}</span> @endif
            </a>
        </li>
        <li>
            <a href="{{ url('/purchasing/purchase-request?dept=development') }}" style="display: flex; justify-content: space-between; align-items: center; {{ request()->query('dept') == 'development' ? 'color: #3b82f6; font-weight: bold;' : '' }}">
                <span><i class="fas fa-angle-right"></i> PR dari Development</span>
                @if($pendingDev > 0) <span style="background: #ef4444; color: white; padding: 1px 6px; border-radius: 10px; font-size: 10px; font-weight: bold;">{{ $pendingDev }}</span> @endif
            </a>
        </li>
    </ul>
</li>

<li>
    <a href="{{ route('purchasing.riwayat_po') }}" style="{{ request()->routeIs('purchasing.riwayat_po*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-history" style="width: 20px;"></i> Riwayat PO
    </a>
</li>

<!-- MENU OUTSTANDING PO DENGAN NOTIFIKASI ORANYE -->
<li>
    <a href="{{ route('purchasing.outstanding_po') }}" style="display: flex; align-items: center; {{ request()->routeIs('purchasing.outstanding_po*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-truck-loading" style="width: 20px; color: #f59e0b;"></i>
        <span>Outstanding PO</span>

        @if($outstandingPoCount > 0)
            <span style="background: #f59e0b; color: white; padding: 2px 7px; border-radius: 12px; font-size: 11px; font-weight: bold; margin-left: auto; box-shadow: 0 2px 4px rgba(245,158,11,0.3); animation: pulseNotif 2s infinite;">
                {{ $outstandingPoCount }}
            </span>
        @endif
    </a>
</li>

<!-- Efek Animasi Kedap-Kedip untuk Semua Badge Notifikasi -->
<style>
    @keyframes pulseNotif {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
</style>