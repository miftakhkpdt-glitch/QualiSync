<!-- ==================== MENU KARYAWAN (CUTI & LEMBUR PRIBADI) ==================== -->
<div style="border-top: 1px solid rgba(255,255,255,0.1); margin: 15px 10px; padding-top: 15px;">
    <ul class="menu-list" style="padding: 0; margin: 0;">
        <!-- Menu Cuti -->
        <li>
            <a href="{{ route('pengajuan.cuti') }}">
                <i class="fas fa-calendar-alt" style="color: #38bdf8;"></i> Pengajuan Cuti
            </a>
        </li>

        <!-- Menu Lembur -->
        <li>
            <a href="{{ route('lembur.pribadi') }}">
                <i class="fas fa-clock" style="color: #34d399;"></i> Pengajuan Lembur
            </a>
        </li>
    </ul>
</div>