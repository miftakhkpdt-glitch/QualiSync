@php
    // Logika pintar untuk dropdown Stok & Mutasi
    $isStokWarehouseActive = request()->routeIs('warehouse.stok.rekap*') || request()->routeIs('warehouse.stok.raw_material*') || request()->routeIs('warehouse.mutasi.approval.index*');
    
    // Logika pintar untuk dropdown Outgoing
    $isOutgoingActive = request()->is('warehouse/outgoing*') || request()->is('warehouse/approval-sj*');
@endphp

<!-- Submenu 0: Dashboard Utama -->
<li>
    <a href="{{ route('warehouse.dashboard') }}" style="{{ request()->routeIs('warehouse.dashboard*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <span><i class="fas fa-desktop" style="width: 20px;"></i> Dashboard Utama</span>
    </a>
</li>

<!-- Submenu 1: Work Order -->
<li class="nav-item">
    <a href="{{ route('warehouse.work_order.index') }}" class="nav-link" style="text-decoration: none; display: block; padding: 10px 15px; {{ request()->routeIs('warehouse.work_order.index*') ? 'color: #3b82f6; font-weight: bold;' : 'color: #cbd5e1;' }}">
        <i class="fas fa-clipboard-list" style="width: 20px;"></i> Work Order
    </a>
</li>

<!-- Submenu 2: Stok & Mutasi -->
<li>
    <a href="#" onclick="event.preventDefault(); toggleDeptDropdown('stokWarehouseSubmenu')" style="display: flex; align-items: center; justify-content: space-between; {{ $isStokWarehouseActive ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <span><i class="fas fa-boxes" style="width: 20px;"></i> Stok & Mutasi</span>
        <i class="fas fa-chevron-down" style="font-size: 13px; transition: transform 0.3s ease; {{ $isStokWarehouseActive ? 'transform: rotate(180deg);' : '' }}"></i>
    </a>
    
    <ul class="sub-menu" id="stokWarehouseSubmenu" style="display: {{ $isStokWarehouseActive ? 'block' : 'none' }}; padding-left: 15px;">
        <li style="padding: 8px 10px 4px; font-size: 10px; color: #94a3b8; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">Menu Stok</li>
        <li>
            <a href="{{ route('warehouse.stok.rekap') }}" style="{{ request()->routeIs('warehouse.stok.rekap*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
                <i class="fas fa-clipboard-list" style="font-size: 12px; width: 20px;"></i> Rekap All Material
            </a>
        </li>

        <li style="padding: 12px 10px 4px; font-size: 10px; color: #94a3b8; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">Menu Mutasi</li>
        <li>
            <a href="{{ route('warehouse.stok.raw_material') }}" style="{{ request()->routeIs('warehouse.stok.raw_material*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
                <i class="fas fa-circle" style="font-size: 6px; width: 20px;"></i> Mutasi Material
            </a>
        </li>
        <li>
            @php
                // PENGAMAN REVISI: Tambahkan Supplier dan WH agar sinkron dengan Controller 
                $pendingCount = \App\Models\MutasiMaterial::whereIn('ke_dept', ['Warehouse', 'WH', 'Supplier'])
                                                          ->where('status_approval', 'Pending')
                                                          ->count();
            @endphp
            <a href="{{ route('warehouse.mutasi.approval.index') }}" style="display: flex; align-items: center; {{ request()->routeIs('warehouse.mutasi.approval.index*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
                <i class="fas fa-check-circle" style="font-size: 12px; width: 20px; color: #10b981;"></i> 
                <span>Approval Mutasi</span> 
                @if($pendingCount > 0)
                    <span style="background: #ef4444; color: white; font-size: 10px; padding: 2px 6px; border-radius: 10px; margin-left: 5px; font-weight: bold;">{{ $pendingCount }}</span>
                @endif
            </a>
        </li>
    </ul>
</li>

<!-- Submenu 3: Incoming -->
<li>
    <a href="{{ route('warehouse.incoming.index') }}" style="{{ request()->routeIs('warehouse.incoming.index*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <span><i class="fas fa-truck-loading" style="width: 20px;"></i> Incoming</span>
    </a>
</li>

<!-- OUTGOING (Dropdown) -->
<li class="{{ $isOutgoingActive ? 'active' : '' }}">
    <a href="#" onclick="event.preventDefault(); toggleDeptDropdown('outgoingSubmenu')" style="{{ $isOutgoingActive ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-truck" style="width: 20px;"></i> 
        <span>Outgoing / SJ</span>
        <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 13px; {{ $isOutgoingActive ? 'transform: rotate(180deg);' : '' }}"></i>
    </a>
    
    <ul class="sub-menu" id="outgoingSubmenu" style="display: {{ $isOutgoingActive ? 'block' : 'none' }}; padding-left: 15px;">
        
        <!-- Menu Input SJ -->
        <li>
            <a href="{{ url('/warehouse/outgoing') }}" 
               class="{{ request()->is('warehouse/outgoing*') && !request()->is('warehouse/approval-sj*') ? 'active' : '' }}" 
               style="{{ request()->is('warehouse/outgoing*') && !request()->is('warehouse/approval-sj*') ? 'background-color: rgba(255,255,255,0.1); color: white; font-weight: bold;' : '' }}"> 
                <i class="far fa-circle" style="width: 14px; font-size: 10px;"></i> 
                <span>Input Surat Jalan</span>
            </a>
        </li>
        
        <!-- Menu Approval SJ -->
        <li class="{{ request()->is('warehouse/approval-sj*') ? 'active' : '' }}">
            <!-- PASTIKAN href-NYA MENGARAH KE /warehouse/approval-sj -->
            <a href="{{ url('/warehouse/approval-sj') }}" 
               style="{{ request()->is('warehouse/approval-sj*') ? 'background-color: rgba(255,255,255,0.1); color: white; font-weight: bold;' : '' }}"> 
                <i class="far fa-circle" style="width: 14px; font-size: 10px;"></i> 
                <span>Approval SJ</span>
            </a>
        </li>
        
    </ul>
</li>