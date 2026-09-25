<!-- 1. Dashboard Utama Development -->
<li>
    <a href="{{ url('/development/dashboard') }}" style="{{ request()->is('development/dashboard*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-desktop" style="width: 20px;"></i> Dashboard Utama
    </a>
</li>

<!-- 2. Master Material (MM) -->
<li>
    <a href="{{ route('development.master-material.index') }}" style="{{ request()->routeIs('development.master-material.*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-cube" style="width: 20px;"></i> Master Material (MM)
    </a>
</li>

<!-- 4. Bill of Materials -->
<li>
    <a href="{{ route('development.bom.index') }}" style="{{ request()->routeIs('development.bom.*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-clipboard-list" style="width: 20px;"></i> Bill of Materials
    </a>
</li>

<!-- 5. Master Reject (BARU) -->
<li>
    <a href="{{ route('produksi.master.reject') }}" style="{{ request()->routeIs('produksi.master.reject*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-database" style="width: 20px; color: #ef4444;"></i> Master Reject
    </a>
</li>

<!-- 6. Master Downtime (BARU) -->
<li>
    <a href="{{ route('produksi.master.downtime') }}" style="{{ request()->routeIs('produksi.master.downtime*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-power-off" style="width: 20px; color: #f59e0b;"></i> Master Downtime
    </a>
</li>

<!-- ============================================== -->

<li style="padding: 15px 15px 5px 15px; font-size: 11px; font-weight: bold; color: #94a3b8; text-transform: uppercase; pointer-events: none;">
    Pengadaan
</li>
<li>
    <a href="{{ url('/departemen/purchase-request') }}" style="{{ request()->is('departemen/purchase-request*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-file-invoice" style="color: #3b82f6; width: 20px;"></i> 
        <span>Purchase Request (PR)</span>
    </a>
</li>