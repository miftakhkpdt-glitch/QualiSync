@php
    // Logika Pintar untuk mendeteksi Dropdown/Menu mana yang sedang aktif
    $isInProsesActive = request()->is('in-proses/fg/menu*') || request()->is('qir*') || request()->is('quality/approval-traceability*');
    $isCapaActive = request()->is('capa-8d*');
    $isStokQcActive = request()->routeIs('quality.stok.*') || request()->is('quality/mutasi-stok*') || request()->is('quality/approval-mutasi*');
    $isSupplierActive = request()->is('quality/supplier-performance*'); 
@endphp

<!-- 1. Dashboard Utama -->
<li>
    <a href="{{ url('dashboard-qa') }}" style="{{ request()->is('quality/dashboard*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-border-all" style="width: 20px;"></i> 
        <span>Dashboard Utama</span>
    </a>
</li>

<!-- 2. Incoming Material -->
@php
    // Menghitung jumlah material masuk yang butuh verifikasi QC
    $pendingIncoming = \Illuminate\Support\Facades\DB::table('incoming_materials')
                        ->where('status_qc', 'Pending')
                        ->count();
@endphp
<li>
    <a href="{{ url('/quality/incoming') }}" style="display: flex; align-items: center; {{ request()->is('quality/incoming*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-boxes" style="color: #2116ef; width: 20px;"></i> 
        <span>Incoming Material</span>

        <!-- Badge Notifikasi Merah -->
        @if($pendingIncoming > 0)
            <span style="background-color: #ef4444; color: white; font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 50px; margin-left: auto;">
                {{ $pendingIncoming }}
            </span>
        @endif
    </a>
</li>

<!-- 3. IN PROSES (Dropdown) -->
<li>
    <a href="#" onclick="event.preventDefault(); toggleDeptDropdown('inProsesQualitySubmenu')" style="{{ $isInProsesActive ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-cogs" style="color: #ef169c; width: 20px;"></i> 
        <span>In Proses</span>
        <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 13px; {{ $isInProsesActive ? 'transform: rotate(180deg);' : '' }}"></i>
    </a>
    <ul class="sub-menu" id="inProsesQualitySubmenu" style="display: {{ $isInProsesActive ? 'block' : 'none' }}; padding-left: 15px;">
        <li>
            <a href="{{ url('/in-proses/fg/menu') }}" style="{{ request()->is('in-proses/fg/menu*') ? 'color: #3b82f6; font-weight: bold;' : '' }}"> 
                <i class="far fa-circle" style="color: #aaef16; width: 14px; font-size: 10px;"></i> 
                <span>Finish Good (FG)</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/qir') }}" style="{{ request()->is('qir*') ? 'color: #3b82f6; font-weight: bold;' : '' }}"> 
                <i class="far fa-circle" style="color: #16efef; width: 14px; font-size: 10px;"></i> 
                <span>Quality IR (QIR)</span>
            </a>
        </li>
        
        @php
            $pendingTraceability = \App\Models\TraceabilityLog::whereNull('qa_status')
                                        ->orWhere('qa_status', 'Pending')
                                        ->count();
        @endphp
        <li>
            <a href="{{ url('/quality/approval-traceability') }}" style="display: flex; align-items: center; {{ request()->is('quality/approval-traceability*') ? 'color: #3b82f6; font-weight: bold;' : '' }}"> 
                <i class="far fa-circle" style="color: #f50d0d; width: 14px; font-size: 10px; margin-right: 5px;"></i> 
                <span>Approval Traceability</span>
                
                @if($pendingTraceability > 0)
                    <span style="background-color: #ef4444; color: white; font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 50px; margin-left: auto;">
                        {{ $pendingTraceability }}
                    </span>
                @endif
            </a>
        </li>
    </ul>
</li>

<!-- 4. CAPA 8D (Dropdown) -->
<li>
    <a href="#" onclick="event.preventDefault(); toggleDeptDropdown('capaQualitySubmenu')" style="{{ $isCapaActive ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-file-alt" style="color: #ebef16;width: 20px;"></i> 
        <span>CAPA 8D</span>
        <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 13px; {{ $isCapaActive ? 'transform: rotate(180deg);' : '' }}"></i>
    </a>
    <ul class="sub-menu" id="capaQualitySubmenu" style="display: {{ $isCapaActive ? 'block' : 'none' }}; padding-left: 15px;">
        <li>
            <a href="{{ url('/capa-8d/customer') }}" style="{{ request()->is('capa-8d/customer*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
                <i class="far fa-circle" style="color: #1682ef;width: 14px; font-size: 10px;"></i> 
                <span>CAPA Customer</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/capa-8d/supplier') }}" style="{{ request()->is('capa-8d/supplier*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
                <i class="far fa-circle" style="color: #ae16ef;width: 14px; font-size: 10px;"></i> 
                <span>CAPA Supplier</span>
            </a>
        </li>
    </ul>
</li>

<!-- 5. COA Manager -->
<li>
    <a href="{{ url('/coa') }}" style="{{ request()->is('coa*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-certificate" style="color: #16ef45; width: 20px;"></i> 
        <span>COA Manager</span>
    </a>
</li>

<!-- 5. Performa Supplier -->
<li>
    <a href="{{ route('quality.supplier_performance.index') }}" style="{{ $isSupplierActive ? 'background-color: var(--sidebar-hover); color: #ffffff; font-weight: bold;' : '' }}">
        <i class="fas fa-star-half-alt" style="{{ $isSupplierActive ? 'color: #ffffff;' : 'color: #f59e0b;' }} width: 20px;"></i> 
        <span>Performa Supplier</span>
    </a>
</li>

<!-- 6. STOK & MUTASI (Dropdown Lengkap) -->
<li>
    <a href="#" onclick="event.preventDefault(); toggleDeptDropdown('stokQualitySubmenu')" style="{{ $isStokQcActive ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-layer-group" style="color: #16efef;width: 20px;"></i> 
        <span>Stok & Mutasi</span>
        <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 13px; {{ $isStokQcActive ? 'transform: rotate(180deg);' : '' }}"></i>
    </a>
    <ul class="sub-menu" id="stokQualitySubmenu" style="display: {{ $isStokQcActive ? 'block' : 'none' }}; padding-left: 15px;">
        <li>
            <a href="{{ route('quality.stok.index') }}" style="{{ request()->routeIs('quality.stok.*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
                <i class="fas fa-box" style="color: #1def16; width: 20px;"></i> 
                <span>Daftar Stok</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/quality/mutasi-stok') }}" style="{{ request()->is('quality/mutasi-stok*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
                <i class="fas fa-exchange-alt" style="color: #ef9c16; width: 20px;"></i> 
                <span>Mutasi Stok</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/quality/approval-mutasi') }}" style="{{ request()->is('quality/approval-mutasi*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
                <i class="fas fa-check-circle" style="color: #c016ef; width: 20px;"></i> 
                <span>Approval Mutasi</span>
            </a>
        </li>
    </ul>
</li>


<!-- [BARU] Master Parameter QIR -->
<li>
    <a href="{{ url('/master-standar') }}" style="{{ request()->is('master-standar*') ? 'font-weight: bold; color: #3b82f6;' : '' }}">
        <i class="fas fa-sliders-h" style="color: #f21111; width: 20px;"></i> 
        <span>Master Standar QIR</span>
    </a>
</li>

<!-- [BARU] Master Parameter QIR (Pusat Pengaturan Jenis Parameter) -->
<li>
    <a href="{{ url('/master-parameters') }}" style="{{ request()->is('master-parameters*') ? 'font-weight: bold; color: #3b82f6;' : '' }}">
        <i class="fas fa-sliders-h" style="color: #f21111; width: 20px;"></i> 
        <span>Master Parameter QIR</span>
    </a>
</li>
<li>
    <a href="{{ url('/master-aql') }}" style="{{ request()->is('master-aql*') ? 'font-weight: bold; color: #3b82f6;' : '' }}">
        <i class="nav-icon fas fa-clipboard-check" style="color: #f21111; width: 20px;"></i>
        <span>Master AQL</span>
    </a>
</li>
<li>
    <a href="{{ url('/master-defect') }}" class="nav-link {{ Request::is('master-defect*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-exclamation-triangle" style="color: #f21111; width: 20px;"></i>
        <span>Master Defect</span>
    </a>
</li>
<!-- 7. Label Pengadaan -->
<li style="padding: 15px 15px 5px 15px; font-size: 11px; font-weight: bold; color: #94a3b8; text-transform: uppercase; pointer-events: none;">
    Pengadaan
</li>
<li>
    <a href="{{ url('/departemen/purchase-request') }}" style="{{ request()->is('departemen/purchase-request*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-file-invoice" style="color: #3b82f6; width: 20px;"></i> 
        <span>Purchase Request (PR)</span>
    </a>
</li>