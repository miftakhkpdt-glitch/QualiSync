<ul class="menu-list">
    <li>
        <a href="{{ url('/home') }}" class="{{ request()->is('home*') ? 'aktif' : '' }}" style="{{ request()->is('home*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
            <i class="fas fa-th-large"></i> Portal Menu
        </a>
    </li>
    <li>
        <a href="{{ url('/engineering/maintenance') }}" style="{{ request()->is('engineering/maintenance*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
            <i class="fas fa-tools"></i> Maintenance Mesin
        </a>
    </li>
    <li>
        <a href="{{ url('/engineering/troubleshooting') }}" style="{{ request()->is('engineering/troubleshooting*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
            <i class="fas fa-clipboard-list"></i> Catatan Perbaikan
        </a>
    </li>

    <!-- TAMBAHAN MENU PURCHASE REQUEST -->
    <li style="padding: 15px 15px 5px 15px; font-size: 11px; font-weight: bold; color: #94a3b8; text-transform: uppercase; pointer-events: none;">
        Pengadaan
    </li>
    <li>
        <a href="{{ url('/departemen/purchase-request') }}" style="{{ request()->is('departemen/purchase-request*') ? 'background-color: var(--sidebar-hover); color: #ffffff; font-weight: bold;' : '' }}">
            <i class="fas fa-file-invoice" style="{{ request()->is('departemen/purchase-request*') ? 'color: #ffffff;' : 'color: #3b82f6;' }}"></i> 
            <span>Purchase Request (PR)</span>
        </a>
    </li>
</ul>