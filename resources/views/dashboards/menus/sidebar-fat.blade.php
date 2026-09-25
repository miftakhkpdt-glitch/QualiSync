<!-- ========================================== -->
<!-- SIDEBAR KHUSUS DEPARTEMEN FAT              -->
<!-- ========================================== -->
<ul class="sidebar-menu" style="list-style: none; padding: 0; margin: 0;">

    <!-- DASHBOARD -->
    <li class="nav-item">
        <a href="{{ route('fat.dashboard') }}" class="nav-link" style="display: flex; align-items: center; gap: 10px; padding: 10px 20px; {{ request()->routeIs('fat.dashboard*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
            <i class="fas fa-tachometer-alt" style="width: 20px;"></i> Dashboard FAT
        </a>
    </li>

    <!-- KELOMPOK 1: ACCOUNT RECEIVABLE (PIUTANG) -->
    <li style="padding: 15px 20px 5px 20px;">
        <span style="color: #94a3b8; font-size: 11px; font-weight: bold; text-transform: uppercase;">AR / Piutang Customer</span>
    </li>
    <li class="nav-item">
        <!-- INI TARGET UTAMA KITA SEKARANG -->
        <a href="{{ route('fat.invoices.index') }}" class="nav-link" style="color: #4ade80; display: flex; align-items: center; gap: 10px; padding: 10px 20px; {{ request()->routeIs('fat.invoices.*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
            <i class="fas fa-file-invoice-dollar" style="width: 20px;"></i> Faktur Penjualan
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('fat.invoices.history') }}" class="nav-link" style="display: flex; align-items: center; gap: 10px; padding: 10px 20px; {{ request()->routeIs('fat.invoices.history*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
            <i class="fas fa-hand-holding-usd" style="width: 20px;"></i> Penerimaan Pembayaran
        </a>
    </li>

    <!-- KELOMPOK 2: ACCOUNT PAYABLE (HUTANG) -->
    <li style="padding: 15px 20px 5px 20px;">
        <span style="color: #94a3b8; font-size: 11px; font-weight: bold; text-transform: uppercase;">Hutang Supplier</span>
    </li>
    <li class="nav-item">
        <a href="{{ route('fat.purchase_invoices.index') }}" class="nav-link" style="{{ request()->routeIs('fat.purchase_invoices.*') && !request()->routeIs('fat.purchase_invoices.history*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
            <i class="fas fa-file-invoice-dollar"></i> Faktur Pembelian
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('fat.purchase_invoices.history') }}" class="nav-link" style="{{ request()->routeIs('fat.purchase_invoices.history*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
            <i class="fas fa-money-check-alt"></i> Pembayaran Pembelian
        </a>
    </li>

    <!-- KELOMPOK 3: BUKU BESAR & KAS -->
    <li style="padding: 15px 20px 5px 20px;">
        <span style="color: #94a3b8; font-size: 11px; font-weight: bold; text-transform: uppercase;">Akuntansi & Kas</span>
    </li>
    <li class="nav-item">
        <a href="{{ route('fat.cash_banks.index') }}" class="nav-link" style="display: flex; align-items: center; gap: 10px; padding: 10px 20px; {{ request()->routeIs('fat.cash_banks.*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
            <i class="fas fa-wallet" style="width: 20px;"></i> Kas & Bank
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('fat.coas.index') }}" class="nav-link" style="{{ request()->routeIs('fat.coas.*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
            <i class="fas fa-list-ol"></i> Master COA
        </a>
    </li> <!-- Ditambahkan penutup </li> -->
    
    <li class="nav-item">
        <a href="{{ route('fat.journals.index') }}" class="nav-link" style="display: flex; align-items: center; gap: 10px; padding: 10px 20px; {{ request()->routeIs('fat.journals.*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
            <i class="fas fa-book" style="width: 20px;"></i> Jurnal Umum
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('fat.reports.general_ledger') }}" class="nav-link" style="{{ request()->routeIs('fat.reports.general_ledger*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
            <i class="fas fa-book-open"></i> Buku Besar
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('fat.reports.balance_sheet') }}" class="nav-link" style="{{ request()->routeIs('fat.reports.balance_sheet*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
            <i class="fas fa-balance-scale"></i> Neraca
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('fat.operational_expenses.index') }}" style="display: flex; align-items: center; color: #cbd5e1; text-decoration: none; padding: 10px 15px; border-radius: 6px; transition: 0.2s; {{ request()->routeIs('fat.operational_expenses.*') ? 'color: #ffffff; background-color: #3b82f6; font-weight: bold;' : '' }}" onmouseover="this.style.color='white'" onmouseout="this.style.color='{{ request()->routeIs('fat.operational_expenses.*') ? '#ffffff' : '#cbd5e1' }}'">
            <i class="fas fa-wallet" style="width: 25px;"></i>Biaya Operasional
        </a>
    </li> <!-- Ditambahkan penutup </a> dan </li> -->

    <!-- KATEGORI BARU: LAPORAN -->
    <div style="font-size: 11px; font-weight: bold; color: #94a3b8; text-transform: uppercase; margin-top: 25px; margin-bottom: 10px; padding-left: 15px;">
        LAPORAN KEUANGAN
    </div>

    <!-- TOMBOL MENU LABA RUGI -->
    <a href="{{ route('fat.reports.profit_loss') }}" style="display: flex; align-items: center; color: #cbd5e1; text-decoration: none; padding: 10px 15px; border-radius: 6px; transition: 0.2s; {{ request()->routeIs('fat.reports.profit_loss*') ? 'color: #ffffff; background-color: #3b82f6; font-weight: bold;' : '' }}" onmouseover="this.style.color='white'" onmouseout="this.style.color='{{ request()->routeIs('fat.reports.profit_loss*') ? '#ffffff' : '#cbd5e1' }}'">
        <i class="fas fa-chart-line" style="width: 20px;"></i>
        Laporan Laba Rugi
    </a>

</ul>