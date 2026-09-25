<!-- ============================================== -->
<!-- MENU PRODUKSI -->
<!-- ============================================== -->

<!-- 1. Dashboard Utama Produksi -->
<li>
    <a href="{{ url('/produksi/dashboard') }}" style="{{ request()->is('produksi/dashboard*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-industry" style="width: 20px;"></i> Dashboard Utama
    </a>
</li>

<!-- Pastikan class aktifnya menggunakan request()->routeIs -->
<li class="{{ request()->routeIs('produksi.wo.list*') ? 'active' : '' }}">
    <a href="{{ route('produksi.wo.list') }}" style="{{ request()->routeIs('produksi.wo.list*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-clipboard-check"></i> Tugas Produksi (WO)
    </a>
</li>

<!-- PARENT MENU: PROSES PRODUKSI -->
<div style="padding-left: 15px; margin-top: 15px; margin-bottom: 5px; font-size: 11px; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">
    EKSEKUSI PROSES PRODUKSI
</div>

<!-- 1. WO Berjalan (Link yang sudah ada sebelumnya) -->
<a href="{{ url('/produksi/proses') }}" style="display: flex; align-items: center; padding: 10px 16px 10px 25px; text-decoration: none; font-size: 14px; transition: 0.2s; {{ request()->is('produksi/proses*') ? 'color: #ffffff; font-weight: bold;' : 'color: var(--sidebar-text, #cbd5e1);' }}" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='{{ request()->is('produksi/proses*') ? '#ffffff' : 'var(--sidebar-text, #cbd5e1)' }}'">
    <i class="fas fa-play-circle" style="width: 25px; font-size: 14px; color: #10b981;"></i> WO Berjalan
</a>

<!-- 2. Input Traceability -->
<a href="{{ url('/produksi/traceability') }}" style="display: flex; align-items: center; padding: 10px 16px 10px 25px; text-decoration: none; font-size: 14px; transition: 0.2s; {{ request()->is('produksi/traceability*') ? 'color: #ffffff; font-weight: bold;' : 'color: var(--sidebar-text, #cbd5e1);' }}" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='{{ request()->is('produksi/traceability*') ? '#ffffff' : 'var(--sidebar-text, #cbd5e1)' }}'">
    <i class="fas fa-qrcode" style="width: 25px; font-size: 14px; color: #3b82f6;"></i> Input Traceability
</a>

<!-- 3. Input Daily Report (Check Sheet) -->
<a href="{{ url('/produksi/daily-report') }}" style="display: flex; align-items: center; padding: 10px 16px 10px 25px; text-decoration: none; font-size: 14px; transition: 0.2s; {{ request()->is('produksi/daily-report*') ? 'color: #ffffff; font-weight: bold;' : 'color: var(--sidebar-text, #cbd5e1);' }}" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='{{ request()->is('produksi/daily-report*') ? '#ffffff' : 'var(--sidebar-text, #cbd5e1)' }}'">
    <i class="fas fa-clipboard-check" style="width: 25px; font-size: 14px; color: #f59e0b;"></i> Input Daily Report
</a>

<div style="border-bottom: 1px solid rgba(255,255,255,0.05); margin: 10px 15px;"></div>
</li>

<li class="{{ request()->routeIs('produksi.riwayat.index*') ? 'active' : '' }}">
    <a href="{{ route('produksi.riwayat.index') }}" style="{{ request()->routeIs('produksi.riwayat.*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-history"></i> Riwayat Produksi
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('produksi.oee.dashboard') }}" class="nav-link" style="{{ request()->routeIs('produksi.oee.*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
        <i class="fas fa-chart-pie" style="color: #0ea5e9;"></i> 
        <span>Dashboard OEE</span>
    </a>
</li>

<!-- 4. STOK PRODUKSI & MUTASI (Tetap pakai toggle) -->
<li>
    <!-- Hitung Notifikasi Approval Produksi -->
    @php
        $pendingProduksi = \App\Models\MutasiMaterial::where('ke_dept', 'Produksi')
                            ->where('status_approval', 'Pending')
                            ->count();
                            
        // Logika pintar agar dropdown terbuka jika salah satu menu di dalamnya sedang diakses
        $isStokActive = request()->is('produksi/stok*') || request()->is('produksi/mutasi-stok*') || request()->is('produksi/approval-mutasi*');
    @endphp

    <!-- Menu Induk (Dengan Notifikasi juga agar terlihat meski dropdown tertutup) -->
    <a href="#" onclick="event.preventDefault(); toggleDeptDropdown('stokProduksiSubmenu')" style="display: flex; justify-content: space-between; align-items: center; padding-right: 15px; {{ $isStokActive ? 'color: #ffffff; font-weight: bold;' : '' }}">
        <div>
            <i class="fas fa-layer-group" style="width: 20px;"></i> Stok & Mutasi
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            @if($pendingProduksi > 0)
                <!-- Titik merah kecil di menu induk sebagai penanda -->
                <span style="background: #ef4444; width: 8px; height: 8px; border-radius: 50%; display: inline-block;"></span>
            @endif
            <i class="fas fa-chevron-down" style="font-size: 13px;"></i>
        </div>
    </a>

    <!-- Submenu -->
    <ul class="sub-menu" id="stokProduksiSubmenu" style="display: {{ $isStokActive ? 'block' : 'none' }}; padding-left: 15px;">
        <li>
            <a href="{{ url('/produksi/stok') }}" style="{{ request()->is('produksi/stok*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
                <i class="fas fa-box" style="width: 20px;"></i> Daftar Stok
            </a>
        </li>
        <li>
            <a href="{{ url('/produksi/mutasi-stok') }}" style="{{ request()->is('produksi/mutasi-stok*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
                <i class="fas fa-exchange-alt" style="width: 20px;"></i> Mutasi Stok
            </a>
        </li>
        
        <!-- Menu Approval Mutasi dengan Angka Merah -->
        <li>
            <a href="{{ url('/produksi/approval-mutasi') }}" style="display: flex; justify-content: space-between; align-items: center; padding-right: 20px; {{ request()->is('produksi/approval-mutasi*') ? 'color: #3b82f6; font-weight: bold;' : '' }}">
                <div>
                    <i class="fas fa-check-circle" style="width: 20px;"></i> Approval Mutasi
                </div>
                
                @if($pendingProduksi > 0)
                    <!-- Badge Angka Merah -->
                    <span style="background-color: #ef4444; color: white; font-size: 11px; font-weight: bold; padding: 2px 6px; border-radius: 50%; min-width: 18px; text-align: center; box-shadow: 0 0 5px rgba(239, 68, 68, 0.4);">
                        {{ $pendingProduksi }}
                    </span>
                @endif
            </a>
        </li>
    </ul>
</li>