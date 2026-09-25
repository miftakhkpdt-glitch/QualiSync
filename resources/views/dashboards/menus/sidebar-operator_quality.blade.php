@php
    // Logika Pintar untuk mendeteksi Dropdown/Menu mana yang sedang aktif
    $isInProsesActive = request()->is('in-proses/fg/menu*') || request()->is('qir*') || request()->is('quality/approval-traceability*');
    $isCapaActive = request()->is('capa-8d*');
    $isStokQcActive = request()->routeIs('quality.stok.*') || request()->is('quality/mutasi-stok*') || request()->is('quality/approval-mutasi*');
    $isSupplierActive = request()->is('quality/supplier-performance*'); 

    // Hitung badge notifikasi secara efisien
    $pendingIncoming = \Illuminate\Support\Facades\DB::table('incoming_materials')->where('status_qc', 'Pending')->count();
    $pendingTraceability = \App\Models\TraceabilityLog::whereNull('qa_status')->orWhere('qa_status', 'Pending')->count();
@endphp

<style>
    /* Styling dasar sidebar agar konsisten dan rapi */
    .sidebar-menu li a {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        color: #cbd5e1;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .sidebar-menu li a:hover {
        color: #ffffff;
        background-color: rgba(255, 255, 255, 0.05);
    }
    .sidebar-menu li a.active {
        color: #3b82f6;
        font-weight: bold;
    }
    .sidebar-menu .sub-menu {
        list-style: none;
        padding-left: 20px;
        margin: 0;
    }
    .sidebar-icon {
        width: 24px;
        text-align: center;
        margin-right: 10px;
        font-size: 14px;
    }
    .badge-notif {
        background-color: #ef4444; 
        color: white; 
        font-size: 10px; 
        font-weight: bold; 
        padding: 2px 6px; 
        border-radius: 50px; 
        margin-left: auto;
    }
</style>

<ul class="sidebar-menu" style="list-style: none; padding: 0; margin: 0;">

    <!-- 2. Incoming Material -->
    <li>
        <a href="{{ url('/quality/incoming') }}" class="{{ request()->is('quality/incoming*') ? 'active' : '' }}">
            <i class="fas fa-boxes sidebar-icon" style="color: #60a5fa;"></i> 
            <span>Incoming Material</span>
            @if($pendingIncoming > 0)
                <span class="badge-notif">{{ $pendingIncoming }}</span>
            @endif
        </a>
    </li>

    <!-- 3. IN PROSES (Dropdown) -->
    <li>
        <a href="#" onclick="event.preventDefault(); toggleDeptDropdown('inProsesQualitySubmenu')" class="{{ $isInProsesActive ? 'active' : '' }}">
            <i class="fas fa-cogs sidebar-icon" style="color: #f472b6;"></i> 
            <span>In Proses</span>
            <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 12px; transition: transform 0.2s; {{ $isInProsesActive ? 'transform: rotate(180deg);' : '' }}"></i>
        </a>
        <ul class="sub-menu" id="inProsesQualitySubmenu" style="display: {{ $isInProsesActive ? 'block' : 'none' }};">
            <li>
                <a href="{{ url('/in-proses/fg/menu') }}" class="{{ request()->is('in-proses/fg/menu*') ? 'active' : '' }}"> 
                    <i class="far fa-circle sidebar-icon" style="font-size: 8px;"></i> 
                    <span>Finish Good (FG)</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/qir') }}" class="{{ request()->is('qir*') ? 'active' : '' }}"> 
                    <i class="far fa-circle sidebar-icon" style="font-size: 8px;"></i> 
                    <span>Quality IR (QIR)</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/quality/approval-traceability') }}" class="{{ request()->is('quality/approval-traceability*') ? 'active' : '' }}"> 
                    <i class="far fa-circle sidebar-icon" style="font-size: 8px;"></i> 
                    <span>Approval Traceability</span>
                    @if($pendingTraceability > 0)
                        <span class="badge-notif">{{ $pendingTraceability }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </li>

    <!-- 5. COA Manager -->
    <li>
        <a href="{{ url('/coa') }}" class="{{ request()->is('coa*') ? 'active' : '' }}">
            <i class="fas fa-certificate sidebar-icon" style="color: #4ade80;"></i> 
            <span>COA Manager</span>
        </a>
    </li>

    <!-- 6. STOK & MUTASI (Dropdown Lengkap) -->
    <li>
        <a href="#" onclick="event.preventDefault(); toggleDeptDropdown('stokQualitySubmenu')" class="{{ $isStokQcActive ? 'active' : '' }}">
            <i class="fas fa-layer-group sidebar-icon" style="color: #38bdf8;"></i> 
            <span>Stok & Mutasi</span>
            <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 12px; transition: transform 0.2s; {{ $isStokQcActive ? 'transform: rotate(180deg);' : '' }}"></i>
        </a>
        <ul class="sub-menu" id="stokQualitySubmenu" style="display: {{ $isStokQcActive ? 'block' : 'none' }};">
            <li>
                <a href="{{ route('quality.stok.index') }}" class="{{ request()->routeIs('quality.stok.*') ? 'active' : '' }}">
                    <i class="fas fa-box sidebar-icon" style="color: #4ade80;"></i> 
                    <span>Daftar Stok</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/quality/mutasi-stok') }}" class="{{ request()->is('quality/mutasi-stok*') ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt sidebar-icon" style="color: #fbbf24;"></i> 
                    <span>Mutasi Stok</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/quality/approval-mutasi') }}" class="{{ request()->is('quality/approval-mutasi*') ? 'active' : '' }}">
                    <i class="fas fa-check-circle sidebar-icon" style="color: #c084fc;"></i> 
                    <span>Approval Mutasi</span>
                </a>
            </li>
        </ul>
    </li>

</ul>