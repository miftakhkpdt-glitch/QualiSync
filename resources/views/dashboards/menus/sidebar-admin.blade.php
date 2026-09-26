<ul class="menu-list">
    <!-- Dashboard Utama -->
    <li><a href="{{ url('/home') }}"><i class="fas fa-th-large"></i> Dashboard Utama</a></li>

    <!-- ==================== QUALITY DEPARTMENT ==================== -->
    <li class="menu-item-has-children">
        <a href="#" onclick="toggleDeptDropdown('deptQuality'); event.preventDefault();">
            <i class="fas fa-award" style="width: 28px;"></i> Quality Dept 
            <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 11px;"></i>
        </a>
        <ul class="sub-menu" id="deptQuality" style="display: none; padding-left: 15px;">
            
            {{-- AMAN DARI HURUF BESAR/KECIL DENGAN strtoupper() --}}
            @if(in_array(strtoupper(auth()->user()->role ?? ''), ['ADMIN', 'STAFF_QUALITY']))
                @include('dashboards.menus.sidebar-main_quality')
            @endif

            @if(in_array(strtoupper(auth()->user()->role ?? ''), ['OPERATOR']))
                @include('dashboards.menus.sidebar-operator_quality')
            @endif

        </ul>
    </li>

    <!-- ==================== 2. PRODUKSI ==================== -->
    <li class="menu-item-has-children">
        <a href="#" onclick="toggleDeptDropdown('deptProduksi'); event.preventDefault();">
            <i class="fas fa-industry" style="width: 28px;"></i> Produksi 
            <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 11px;"></i>
        </a>
        <ul class="sub-menu" id="deptProduksi" style="display: none; padding-left: 15px;">
            @include('dashboards.menus.sidebar-produksi')
            @include('dashboards.menus.sidebar-operator_produksi')
        </ul>
    </li>

    <!-- ==================== 3. ENGINEERING ==================== -->
    <li class="menu-item-has-children">
        <a href="#" onclick="toggleDeptDropdown('deptEngineering'); event.preventDefault();">
            <i class="fas fa-tools" style="width: 28px;"></i> Engineering 
            <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 11px;"></i>
        </a>
        <ul class="sub-menu" id="deptEngineering" style="display: none; padding-left: 15px;">
            @include('dashboards.menus.sidebar-engineering')
        </ul>
    </li>

    <!-- ==================== 4. DEVELOPMENT ==================== -->
    <li class="menu-item-has-children">
        <a href="#" onclick="toggleDeptDropdown('deptDevelopment'); event.preventDefault();">
            <i class="fas fa-flask" style="width: 28px;"></i> Development 
            <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 11px;"></i>
        </a>
        <ul id="deptDevelopment" class="sub-menu" style="display: none; padding-left: 15px;">
            @include('dashboards.menus.sidebar-development')
        </ul>
    </li>

    <!-- ==================== 5. PPIC ==================== -->
    <li class="menu-item-has-children">
        <a href="#" onclick="toggleDeptDropdown('ppicSubmenu'); event.preventDefault();">
            <i class="fas fa-tasks" style="width: 28px;"></i> PPIC 
            <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 11px;"></i>
        </a>
        <ul id="ppicSubmenu" class="sub-menu" style="display: none; padding-left: 15px;">
            @include('dashboards.menus.sidebar-ppic')
        </ul>
    </li>

    <!-- ==================== 6. FAT ==================== -->
    <li class="menu-item-has-children">
        <a href="#" onclick="toggleDeptDropdown('deptFat'); event.preventDefault();">
            <i class="fas fa-file-invoice-dollar" style="width: 28px;"></i> FAT 
            <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 11px;"></i>
        </a>
        <ul class="sub-menu" id="deptFat" style="display: none; padding-left: 15px;">
            @include('dashboards.menus.sidebar-fat')
        </ul>
    </li>

    <!-- ==================== 7. PURCHASING ==================== -->
    <li class="menu-item-has-children">
        <a href="#" onclick="toggleDeptDropdown('deptPurchasing'); event.preventDefault();">
            <i class="fas fa-shopping-cart" style="width: 28px;"></i> Purchasing 
            <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 11px;"></i>
        </a>
        <ul class="sub-menu" id="deptPurchasing" style="display: none; padding-left: 15px;">
            @include('dashboards.menus.sidebar-purchasing')
        </ul>
    </li>

    <!-- ==================== 8. SALES & MARKETING ==================== -->
    <li class="menu-item-has-children">
        <a href="#" onclick="toggleDeptDropdown('deptSales'); event.preventDefault();">
            <i class="fas fa-chart-line" style="width: 28px;"></i> Sales & Marketing 
            <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 11px;"></i>
        </a>
        <ul class="sub-menu" id="deptSales" style="display: none; padding-left: 15px;">
            @include('dashboards.menus.sidebar-sales_marketing')
        </ul>
    </li>

    <!-- ==================== 9. HRD & GA ==================== -->
    <li class="menu-item-has-children">
        <a href="#" onclick="toggleDeptDropdown('deptHrd'); event.preventDefault();">
            <i class="fas fa-users-cog" style="width: 28px;"></i> HRD & GA 
            <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 11px;"></i>
        </a>
        <ul class="sub-menu" id="deptHrd" style="display: none; padding-left: 15px;">
            @include('dashboards.menus.sidebar-hrd_ga')
        </ul>
    </li>

    <!-- ==================== 10. MENU WAREHOUSE ==================== -->
    <li class="nav-item">
        <a href="#" onclick="toggleDeptDropdown('warehouseSubmenu'); event.preventDefault();" class="nav-link">
            <i class="fas fa-warehouse" style="width: 28px; text-align: center;"></i> 
            <span>Warehouse</span> 
            <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 11px;"></i>
        </a>
        <ul class="sub-menu" id="warehouseSubmenu" style="display: none; padding-left: 15px;"> 
            @include('dashboards.menus.sidebar-warehouse')
        </ul>
    </li>

    <!-- ==================== Z. AREA BERBAHAYA (DEV ONLY) ==================== -->
    <li style="margin-top: 30px; padding: 0 15px; margin-bottom: 20px;">
        <form action="{{ route('developer.reset_transaksi') }}" method="POST" 
              onsubmit="return confirm('PERINGATAN KERAS! ⚠️\n\nApakah Anda YAKIN ingin MENGHAPUS SEMUA DATA TRANSAKSI dari semua departemen?\n\n* Data Master (User, Barang, Supplier) akan AMAN.\n* Semua stok dan transaksi akan kembali ke NOL.');">
            @csrf
            
            <button type="submit" class="no-disable" style="
    width: 100%; 
    background-color: #ef4444; 
    color: white; 
    padding: 12px 10px; 
    border-radius: 8px; 
    border: 2px solid #b91c1c; 
    cursor: pointer; 
    font-weight: bold; 
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
" onmouseover="this.style.backgroundColor='#dc2626'" onmouseout="this.style.backgroundColor='#ef4444'">
    <i class="fas fa-skull-crossbones" style="font-size: 16px;"></i> 
    <span>RESET DATA</span>
</button>
        </form>
    </li>
</ul>

<!-- SCRIPT PENGATUR ROTASI PANAH MENU STOK -->
<script>
    function toggleMenuStokMutasi(event, element) {
        event.preventDefault();
        const submenu = document.getElementById('stokSubmenu');
        // Mencari ikon panah secara langsung di dalam elemen yang diklik
        const arrow = element.querySelector('.mutasi-arrow'); 
        
        if (submenu.style.display === 'none' || submenu.style.display === '') {
            submenu.style.display = 'block';
            if (arrow) arrow.style.transform = 'rotate(-90deg)'; // Putar ke KANAN saat dibuka
        } else {
            submenu.style.display = 'none';
            if (arrow) arrow.style.transform = 'rotate(0deg)'; // Kembali ke BAWAH saat ditutup
        }
    }
</script>