<!-- 2. Dashboard Utama PPIC -->
<li>
    <a href="{{ url('/ppic/dashboard') }}" style="{{ request()->is('ppic/dashboard') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-th-large" style="width: 20px;"></i> Dashboard Utama
    </a>
</li>
<!-- 4. Hasil MRP -->
<li>
     <a href="{{ route('ppic.mrp.index') }}" style="{{ request()->routeIs('ppic.mrp.*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-calculator" style="width: 20px;"></i> Hasil MRP
    </a>
</li>
<!-- 7. Parameter Batas Stok -->
<li>
    <a href="{{ route('ppic.mrp_parameter.index') }}" style="{{ request()->is('ppic/mrp-parameter*') || request()->routeIs('ppic.mrp_parameter.*') ? 'color: #ffffff; background-color: var(--sidebar-hover); font-weight: bold;' : '' }}">
        <i class="fas fa-sliders-h" style="width: 20px;"></i> Parameter Batas Stok
    </a>
</li>
<!-- 3. Menu Daftar PO Masuk (Pesanan Customer) -->
<li>
    <a href="{{ route('ppic.po.index') }}" style="{{ request()->is('ppic/dashboard-po*') || request()->routeIs('ppic.po.*') ? 'color: #ffffff; background-color: var(--sidebar-hover); font-weight: bold;' : '' }}">
        <i class="fas fa-file-alt" style="width: 20px;"></i> Daftar PO Masuk
    </a>
</li>
<!-- 5. Riwayat PR -->
<li>
    <a href="{{ url('/ppic/riwayat-pr') }}" style="{{ request()->is('ppic/riwayat-pr*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-history" style="width: 20px;"></i> Riwayat PR
    </a>
</li>
<!-- 6. Manajemen Work Order (SPK Produksi) -->
<li>
    <!-- Pastikan Anda membuat route untuk url ini di web.php -->
    <a href="{{ url('/ppic/work-order') }}" style="{{ request()->is('ppic/work-order*') ? 'color: #ffffff; background-color: var(--sidebar-hover); font-weight: bold;' : '' }}">
        <i class="fas fa-industry" style="width: 20px;"></i> Manajemen Work Order
    </a>
</li>