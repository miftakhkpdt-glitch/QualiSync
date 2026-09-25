<!-- 1. Dashboard Utama -->
<li>
    <a href="{{ url('/sales-marketing/dashboard') }}" style="{{ request()->is('sales-marketing/dashboard*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-th-large" style="width: 20px;"></i> Dashboard Utama
    </a>
</li>

<!-- Menu Utama Sales & Marketing -->

<!-- 2. Data Forecast -->
<li>
    <a href="{{ route('sales.forecast.index') }}" style="{{ request()->is('sales/forecast*') || request()->routeIs('sales.forecast.*') ? 'color: #ffffff; background-color: var(--sidebar-hover); font-weight: bold;' : '' }}">
        <i class="fas fa-chart-line" style="width: 20px;"></i> Data Forecast
    </a>
</li>

<!-- 3. Input PO Customer -->
<li>
    <a href="{{ route('sales.po.input') }}" style="{{ request()->is('sales/po/input*') || request()->routeIs('sales.po.*') ? 'color: #ffffff; background-color: var(--sidebar-hover); font-weight: bold;' : '' }}">
        <i class="fas fa-file-invoice" style="width: 20px;"></i> Input PO Customer
    </a>
</li>

<!-- 4. OSPO (Outstanding PO) -->
<li>
    <a href="{{ url('/sales/ospo') }}" style="{{ request()->is('sales/ospo*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-clipboard-list" style="width: 20px;"></i> 
        <span>OSPO (Outstanding PO)</span>
    </a>
</li>

<!-- 5. Kelola Harga PO -->
<li>
    <a href="{{ route('sales.prices.index') }}" style="{{ request()->is('sales/prices*') || request()->routeIs('sales.prices.*') ? 'color: #ffffff; background-color: var(--sidebar-hover); font-weight: bold;' : '' }}">
        <i class="fas fa-tags" style="width: 20px;"></i> Kelola Harga PO
    </a>
</li>
<!-- Tambahkan menu Master Customer ini -->
</li>
    <a href="{{ url('/sales/master-customer') }}" class="sidebar-item {{ request()->is('sales/master-customer*') ? 'active' : '' }}">
        <i class="fas fa-users"></i> Master Customer
    </a>
</li>