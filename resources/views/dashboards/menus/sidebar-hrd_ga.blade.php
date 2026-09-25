<!-- 1. Dashboard Utama HRD & GA -->
<li>
    <a href="{{ url('/hrd-ga/dashboard') }}" style="{{ request()->is('hrd-ga/dashboard*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-th-large" style="width: 20px;"></i> Dashboard Utama
    </a>
</li>

<!-- 2. Data Karyawan -->
<li>
    <a href="{{ url('/hrd/karyawan') }}" style="{{ request()->is('hrd/karyawan*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-id-card" style="width: 20px;"></i> Data Karyawan
    </a>
</li>

<!-- Perbaikan tag <li> untuk Slip Gaji (Payroll) -->
<li>
    <a href="{{ route('hrd.payrolls.index') }}" style="display: flex; align-items: center; padding: 12px 16px; text-decoration: none; font-size: 14px; border-radius: 8px; transition: all 0.2s ease; {{ request()->routeIs('hrd.payrolls.*') ? 'color: #3b82f6; font-weight: bold; background-color: rgba(59, 130, 246, 0.1);' : 'color: var(--sidebar-text); font-weight: 500;' }}">
        <i class="fas fa-file-invoice-dollar" style="width: 20px; font-size: 16px; {{ request()->routeIs('hrd.payrolls.*') ? 'color: #3b82f6;' : 'color: #94a3b8;' }}"></i> Slip Gaji (Payroll)
    </a>
</li>
    
<!-- 3. Menu Approval Cuti -->
<li>
    <a href="{{ url('/cuti-approval') }}" style="{{ request()->is('cuti-approval*') ? 'color: #ffffff; background-color: var(--sidebar-hover); font-weight: bold;' : '' }}">
        <i class="fas fa-calendar-check" style="width: 20px;"></i> Approval Cuti
    </a>
</li>

<!-- 4. Menu Approval Lembur -->
<li>
    <a href="{{ route('lembur.hrd.approval') }}" style="{{ request()->is('hrd/lembur/approval*') ? 'color: #ffffff; background-color: var(--sidebar-hover); font-weight: bold;' : '' }}">
        <i class="fas fa-clipboard-check" style="width: 20px;"></i> Approval Lembur
    </a>
</li>

<!-- 5. Manajemen User -->
<li>
    <a href="{{ url('/hrd/user') }}" style="{{ request()->is('hrd/user*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-users-cog" style="width: 20px;"></i> Manajemen User
    </a>
</li>

<!-- 6. Pengadaan -->
<li style="padding: 15px 15px 5px 15px; font-size: 11px; font-weight: bold; color: #94a3b8; text-transform: uppercase; pointer-events: none;">
    Pengadaan
</li>
<li>
    <a href="{{ url('/departemen/purchase-request') }}" style="{{ request()->is('departemen/purchase-request*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-file-invoice" style="{{ request()->is('departemen/purchase-request*') ? 'color: #3b82f6;' : 'color: #3b82f6;' }}"></i> 
        <span>Purchase Request (PR)</span>
    </a>
</li>