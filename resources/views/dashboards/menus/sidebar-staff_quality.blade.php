@php
    // Logika Pintar untuk mendeteksi Dropdown/Menu mana yang sedang aktif
    $isInProsesActive = request()->is('in-proses/fg/menu*') || request()->is('qir*') || request()->is('quality/approval-traceability*');
    $isCapaActive = request()->is('capa-8d*');
    $isStokQcActive = request()->routeIs('quality.stok.*') || request()->is('quality/mutasi-stok*') || request()->is('quality/approval-mutasi*');
    $isSupplierActive = request()->is('quality/supplier-performance*'); 
@endphp

<!-- 1. Dashboard Utama -->
<li>
    <a href="{{ url('dashboard-qa') }}" style="color: {{ request()->is('quality/dashboard*') ? '#3b82f6' : '#cbd5e1' }}; font-weight: {{ request()->is('quality/dashboard*') ? 'bold' : 'normal' }}; display: flex; align-items: center; padding: 8px 10px; text-decoration: none;">
        <i class="fas fa-border-all" style="width: 20px; color: #cbd5e1;"></i> 
        <span>Dashboard Utama</span>
    </a>
</li>

<!-- 2. Incoming Material -->
@php
    $pendingIncoming = \Illuminate\Support\Facades\DB::table('incoming_materials')
                        ->where('status_qc', 'Pending')
                        ->count();
@endphp
<li>
    <a href="{{ url('/quality/incoming') }}" style="display: flex; align-items: center; color: {{ request()->is('quality/incoming*') ? '#3b82f6' : '#cbd5e1' }}; font-weight: {{ request()->is('quality/incoming*') ? 'bold' : 'normal' }}; padding: 8px 10px; text-decoration: none;">
        <i class="fas fa-boxes" style="width: 20px; color: #38bdf8;"></i> 
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
    <a href="#" onclick="event.preventDefault(); toggleDeptDropdown('inProsesQualitySubmenu')" style="display: flex; align-items: center; color: {{ $isInProsesActive ? '#3b82f6' : '#cbd5e1' }}; font-weight: {{ $isInProsesActive ? 'bold' : 'normal' }}; padding: 8px 10px; text-decoration: none;">
        <i class="fas fa-cogs" style="width: 20px; color: #ec4899;"></i> 
        <span>In Proses</span>
        <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 13px; transform: {{ $isInProsesActive ? 'rotate(180deg)' : 'rotate(0deg)' }};"></i>
    </a>
    <ul class="sub-menu" id="inProsesQualitySubmenu" style="display: {{ $isInProsesActive ? 'block' : 'none' }}; padding-left: 15px; list-style: none;">
        <li>
            <a href="{{ url('/in-proses/fg/menu') }}" style="display: flex; align-items: center; color: {{ request()->is('in-proses/fg/menu*') ? '#3b82f6' : '#cbd5e1' }}; padding: 6px 10px; text-decoration: none;"> 
                <i class="far fa-circle" style="width: 14px; font-size: 8px; color: #a3e635; margin-right: 8px;"></i> 
                <span>Finish Good (FG)</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/qir') }}" style="display: flex; align-items: center; color: {{ request()->is('qir*') ? '#3b82f6' : '#cbd5e1' }}; padding: 6px 10px; text-decoration: none;"> 
                <i class="far fa-circle" style="width: 14px; font-size: 8px; color: #22d3ee; margin-right: 8px;"></i> 
                <span>Quality IR (QIR)</span>
            </a>
        </li>
        
        @php
            $pendingTraceability = \App\Models\TraceabilityLog::whereNull('qa_status')
                                    ->orWhere('qa_status', 'Pending')
                                    ->count();
        @endphp
        <li>
            <a href="{{ url('/quality/approval-traceability') }}" style="display: flex; align-items: center; color: {{ request()->is('quality/approval-traceability*') ? '#3b82f6' : '#cbd5e1' }}; padding: 6px 10px; text-decoration: none;"> 
                <i class="far fa-circle" style="width: 14px; font-size: 8px; color: #f87171; margin-right: 8px;"></i> 
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
    <a href="#" onclick="event.preventDefault(); toggleDeptDropdown('capaQualitySubmenu')" style="display: flex; align-items: center; color: {{ $isCapaActive ? '#3b82f6' : '#cbd5e1' }}; font-weight: {{ $isCapaActive ? 'bold' : 'normal' }}; padding: 8px 10px; text-decoration: none;">
        <i class="fas fa-file-alt" style="width: 20px; color: #facc15;"></i> 
        <span>CAPA 8D</span>
        <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 13px; transform: {{ $isCapaActive ? 'rotate(180deg)' : 'rotate(0deg)' }};"></i>
    </a>
    <ul class="sub-menu" id="capaQualitySubmenu" style="display: {{ $isCapaActive ? 'block' : 'none' }}; padding-left: 15px; list-style: none;">
        <li>
            <a href="{{ url('/capa-8d/customer') }}" style="display: flex; align-items: center; color: {{ request()->is('capa-8d/customer*') ? '#3b82f6' : '#cbd5e1' }}; padding: 6px 10px; text-decoration: none;">
                <i class="far fa-circle" style="width: 14px; font-size: 8px; color: #60a5fa; margin-right: 8px;"></i> 
                <span>CAPA Customer</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/capa-8d/supplier') }}" style="display: flex; align-items: center; color: {{ request()->is('capa-8d/supplier*') ? '#3b82f6' : '#cbd5e1' }}; padding: 6px 10px; text-decoration: none;">
                <i class="far fa-circle" style="width: 14px; font-size: 8px; color: #c084fc; margin-right: 8px;"></i> 
                <span>CAPA Supplier</span>
            </a>
        </li>
    </ul>
</li>

<!-- 5. COA Manager -->
<li>
    <a href="{{ url('/coa') }}" style="display: flex; align-items: center; color: {{ request()->is('coa*') ? '#3b82f6' : '#cbd5e1' }}; font-weight: {{ request()->is('coa*') ? 'bold' : 'normal' }}; padding: 8px 10px; text-decoration: none;">
        <i class="fas fa-certificate" style="width: 20px; color: #4ade80;"></i> 
        <span>COA Manager</span>
    </a>
</li>

<!-- 6. Performa Supplier -->
<li>
    <a href="{{ route('quality.supplier_performance.index') }}" style="display: flex; align-items: center; color: {{ $isSupplierActive ? '#ffffff' : '#cbd5e1' }}; background-color: {{ $isSupplierActive ? 'rgba(59, 130, 246, 0.2)' : 'transparent' }}; font-weight: bold; padding: 8px 10px; text-decoration: none; border-radius: 4px;">
        <i class="fas fa-star-half-alt" style="width: 20px; color: #fbbf24;"></i> 
        <span>Performa Supplier</span>
    </a>
</li>

<!-- 7. STOK & MUTASI (Dropdown Lengkap) -->
<li>
    <a href="#" onclick="event.preventDefault(); toggleDeptDropdown('stokQualitySubmenu')" style="display: flex; align-items: center; color: {{ $isStokQcActive ? '#3b82f6' : '#cbd5e1' }}; font-weight: {{ $isStokQcActive ? 'bold' : 'normal' }}; padding: 8px 10px; text-decoration: none;">
        <i class="fas fa-layer-group" style="width: 20px; color: #22d3ee;"></i> 
        <span>Stok & Mutasi</span>
        <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 13px; transform: {{ $isStokQcActive ? 'rotate(180deg)' : 'rotate(0deg)' }};"></i>
    </a>
    <ul class="sub-menu" id="stokQualitySubmenu" style="display: {{ $isStokQcActive ? 'block' : 'none' }}; padding-left: 15px; list-style: none;">
        <li>
            <a href="{{ route('quality.stok.index') }}" style="display: flex; align-items: center; color: {{ request()->routeIs('quality.stok.*') ? '#3b82f6' : '#cbd5e1' }}; padding: 6px 10px; text-decoration: none;">
                <i class="fas fa-box" style="width: 20px; color: #4ade80;"></i> 
                <span>Daftar Stok</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/quality/mutasi-stok') }}" style="display: flex; align-items: center; color: {{ request()->is('quality/mutasi-stok*') ? '#3b82f6' : '#cbd5e1' }}; padding: 6px 10px; text-decoration: none;">
                <i class="fas fa-exchange-alt" style="width: 20px; color: #fbbf24;"></i> 
                <span>Mutasi Stok</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/quality/approval-mutasi') }}" style="display: flex; align-items: center; color: {{ request()->is('quality/approval-mutasi*') ? '#3b82f6' : '#cbd5e1' }}; padding: 6px 10px; text-decoration: none;">
                <i class="fas fa-check-circle" style="width: 20px; color: #e879f9;"></i> 
                <span>Approval Mutasi</span>
            </a>
        </li>
    </ul>
</li>

<!-- Master Parameter QIR -->
<li>
    <a href="{{ url('/master-standar') }}" style="display: flex; align-items: center; color: {{ request()->is('master-standar*') ? '#3b82f6' : '#cbd5e1' }}; font-weight: {{ request()->is('master-standar*') ? 'bold' : 'normal' }}; padding: 8px 10px; text-decoration: none;">
        <i class="fas fa-sliders-h" style="width: 20px; color: #f87171;"></i> 
        <span>Master Standar QIR</span>
    </a>
</li>

<li>
    <a href="{{ url('/master-parameters') }}" style="display: flex; align-items: center; color: {{ request()->is('master-parameters*') ? '#3b82f6' : '#cbd5e1' }}; font-weight: {{ request()->is('master-parameters*') ? 'bold' : 'normal' }}; padding: 8px 10px; text-decoration: none;">
        <i class="fas fa-sliders-h" style="width: 20px; color: #f87171;"></i> 
        <span>Master Parameter QIR</span>
    </a>
</li>

<li>
    <a href="{{ url('/master-aql') }}" style="display: flex; align-items: center; color: {{ request()->is('master-aql*') ? '#3b82f6' : '#cbd5e1' }}; font-weight: {{ request()->is('master-aql*') ? 'bold' : 'normal' }}; padding: 8px 10px; text-decoration: none;">
        <i class="fas fa-clipboard-check" style="width: 20px; color: #f87171;"></i>
        <span>Master AQL</span>
    </a>
</li>

<li>
    <a href="{{ url('/master-defect') }}" style="display: flex; align-items: center; color: {{ request()->is('master-defect*') ? '#3b82f6' : '#cbd5e1' }}; font-weight: {{ request()->is('master-defect*') ? 'bold' : 'normal' }}; padding: 8px 10px; text-decoration: none;">
        <i class="fas fa-exclamation-triangle" style="width: 20px; color: #f87171;"></i>
        <span>Master Defect</span>
    </a>
</li>

<!-- Label Pengadaan -->
<li style="padding: 15px 10px 5px 10px; font-size: 11px; font-weight: bold; color: #64748b; text-transform: uppercase; pointer-events: none;">
    Pengadaan
</li>
<li>
    <a href="{{ url('/departemen/purchase-request') }}" style="display: flex; align-items: center; color: {{ request()->is('departemen/purchase-request*') ? '#3b82f6' : '#cbd5e1' }}; font-weight: {{ request()->is('departemen/purchase-request*') ? 'bold' : 'normal' }}; padding: 8px 10px; text-decoration: none;">
        <i class="fas fa-file-invoice" style="width: 20px; color: #60a5fa;"></i> 
        <span>Purchase Request (PR)</span>
    </a>
</li>