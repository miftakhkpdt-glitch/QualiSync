<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Staff - PT KIMPAI DYNA TUBE</title>
    
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- ============================================== -->
    <!-- CSS DATATABLES                                 -->
    <!-- ============================================== -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <style>
        :root {
            --sidebar-bg: #1e293b;
            --sidebar-text: #cbd5e1;
            --sidebar-hover: #334155;
            --primary-color: #198754;
            --bg-body: #f1f5f9;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        body { 
            margin: 0; padding: 0; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: var(--bg-body); 
            display: flex; height: 100vh; overflow: hidden; color: var(--text-main);
        }

        .sidebar { 
            width: 260px; background-color: var(--sidebar-bg); color: var(--sidebar-text);
            display: flex; flex-direction: column; box-shadow: 4px 0 10px rgba(0,0,0,0.05); z-index: 20;
        }
        .sidebar-judul { padding: 25px 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-judul h2 { color: #ffffff; font-family: "Times New Roman", Times, serif; margin: 0; font-size: 20px; font-weight: bold; }
        
        /* CSS Sidebar Menu */
        .menu-list { list-style: none; padding: 15px 10px; margin: 0; flex: 1; overflow-y: auto; }
        .menu-list li { margin-bottom: 4px; }
        .menu-list a { 
            display: flex; align-items: center; padding: 12px 16px; color: var(--sidebar-text); 
            text-decoration: none; font-size: 14px; font-weight: 500; border-radius: 8px; transition: all 0.2s ease;
        }
        .menu-list a i { width: 28px; font-size: 16px; color: #94a3b8; }
        .menu-list a i.fa-angle-left, .menu-list a i.fa-angle-down, .menu-list a i.fa-chevron-down {
            transition: transform 0.3s ease;
            margin-left: auto; /* Memastikan panah selalu berada di paling kanan */
        }
        .menu-list a:hover { background-color: var(--sidebar-hover); color: #ffffff; }
        .menu-list a:hover i { color: #ffffff; }
        
        .sub-menu { list-style: none; padding-left: 36px; margin: 4px 0 8px 0; }
        .sub-menu li a { padding: 8px 12px; font-size: 13px; color: #94a3b8; }
        .sub-menu li a:hover { color: #ffffff; background-color: var(--sidebar-hover); }

        .area-kanan { flex: 1; display: flex; flex-direction: column; overflow-y: auto; }
        .konten { padding: 35px 40px; }

        /* --- CSS Topbar & Profil --- */
        .topbar { 
            background-color: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px);
            height: 70px; padding: 0 40px; 
            display: flex; align-items: center; justify-content: flex-end; 
            border-bottom: 1px solid var(--border-color); 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            z-index: 10;
            position: sticky; top: 0;
        }
        
        .profil { 
            display: flex; align-items: center; gap: 15px; 
            padding: 6px 8px 6px 18px; 
            border-radius: 40px; 
            transition: all 0.3s ease; cursor: pointer; 
            background: #f8fafc; border: 1px solid transparent;
        }
        .profil:hover { 
            background: #ffffff; border-color: #e2e8f0; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.06); transform: translateY(-1px);
        }
        
        .profil-info { display: flex; flex-direction: column; align-items: flex-end; line-height: 1.2; }
        .profil-name { font-size: 14px; font-weight: 700; color: #1e293b; }
        .profil-role { font-size: 11px; font-weight: 700; color: #10b981; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .profil-avatar { 
            width: 42px; height: 42px; 
            background: linear-gradient(135deg, #0ea5e9, #2563eb); 
            border-radius: 50%; display: flex; align-items: center; justify-content: center; 
            font-weight: bold; font-size: 18px; color: #ffffff; 
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25); border: 2px solid #ffffff;
        }
        
        /* --- CSS Tambahan untuk Dropdown Profil --- */
        .profil-container { position: relative; }
        .profil i { transition: transform 0.3s ease; }
        .profil-dropdown {
            position: absolute; top: 120%; right: 0;
            width: 240px; background: #ffffff;
            border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0; display: none;
            flex-direction: column; z-index: 100; overflow: hidden;
            animation: fadeInDown 0.2s ease-out;
        }
        .profil-dropdown.show { display: flex; }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .dropdown-header { padding: 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        .dropdown-header strong { display: block; color: #1e293b; font-size: 15px; }
        .dropdown-header p { margin: 5px 0 0 0; color: #64748b; font-size: 13px; }
        .profil-dropdown a {
            padding: 12px 20px; color: #475569; text-decoration: none; font-size: 14px;
            display: flex; align-items: center; gap: 12px; transition: background 0.2s;
        }
        .profil-dropdown a:hover { background: #f1f5f9; color: #1e293b; }

        /* --- CSS Kustom DataTables --- */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 4px 10px; margin-left: 4px; border-radius: 6px; border: 1px solid #e2e8f0;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #3b82f6 !important; color: white !important; border: 1px solid #3b82f6 !important;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 8px; outline: none; margin-bottom: 10px;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #3b82f6;
        }
        table.dataTable.no-footer {
            border-bottom: 1px solid #e2e8f0;
        }
        table.dataTable thead th, table.dataTable thead td {
            border-bottom: 2px solid #e2e8f0; background-color: #f8fafc;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-judul">
            <a href="{{ url('/home') }}" style="text-decoration: none; color: inherit;">
                <h2>General Dashboard</h2>
            </a>
        </div>
        
        <div style="flex: 1; overflow-y: auto;">
            {{-- 1. PANGGIL SIDEBAR DEPARTEMEN DINAMIS --}}
            @if(Auth::check())
                @includeIf('dashboards.menus.sidebar-' . Auth::user()->role)
            @endif

            {{-- 2. PANGGIL MENU KHUSUS KARYAWAN (CUTI & LEMBUR) --}}
            @includeIf('dashboards.menus.sidebar-menu-karyawan')

            {{-- 3. TAMBAHAN MENU MANUAL UNTUK MANAJEMEN USER --}}
            <div style="padding: 10px 15px;">
                @if(Auth::check() && in_array(Auth::user()->role, ['hrd_ga', 'admin']))
                    <a href="{{ url('/hrd/user') }}" style="color: #cbd5e1; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 12px; text-decoration: none; padding: 10px 15px; border-radius: 8px; background: rgba(255,255,255,0.05);">
                        <i class="fas fa-users-cog" style="color: #34d399; width: 20px;"></i> Manajemen User
                    </a>
                @endif
            </div>
        </div>
        
    </div>

    <div class="area-kanan">
        
        <!-- TOPBAR PROFIL -->
        <div class="topbar">
            <div class="profil-container">
                <div class="profil" onclick="toggleProfileDropdown()">
                    <div class="profil-info">
                        <span class="profil-name">{{ Auth::user()->name ?? 'Nama Karyawan' }}</span>
                        <span class="profil-role">{{ Auth::user()->role ?? 'Staff' }}</span>
                    </div>
                    <div class="profil-avatar">
                        {{ strtoupper(substr(Auth::user()->name ?? 'S', 0, 1)) }}
                    </div>
                    <i class="fas fa-chevron-down" style="font-size: 12px; color: #94a3b8; margin-left: 5px;" id="profilArrow"></i>
                </div>

                <!-- Dropdown Profil -->
                <div class="profil-dropdown" id="profilMenu">
                    <div class="dropdown-header">
                        <strong>{{ Auth::user()->name ?? 'Nama Karyawan' }}</strong>
                        <p>{{ Auth::user()->email ?? 'user@kimpaidynatube.com' }}</p>
                    </div>
                    <a href="#"><i class="fas fa-user-circle" style="color: #0ea5e9;"></i> Profil Saya</a>
                    <a href="#"><i class="fas fa-cog" style="color: #64748b;"></i> Pengaturan Akun</a>
                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 0;">
                    
                    <a href="{{ url('/logout') }}" style="color: #ef4444;">
                        <i class="fas fa-sign-out-alt"></i> Keluar Sistem
                    </a>
                </div>
            </div>
        </div>
        
        <div class="konten">
            @yield('konten')
        </div>
    </div>

    <!-- ================= KUMPULAN JAVASCRIPT UI CLEAN ================= -->
    <script>
        // FUNGSI TOGGLE UNIVERSAL DROPDOWN SIDEBAR
        function toggleSubmenu(element) {
            var parentLi = element.closest('li');
            var submenu = parentLi ? parentLi.querySelector('.sub-menu, ul') : element.nextElementSibling;
            var arrow = element.querySelector('.fa-chevron-down, .fa-angle-down, .fa-angle-left');
            
            if (submenu) {
                var isHidden = window.getComputedStyle(submenu).display === 'none';
                var id = submenu.id;
                var openMenus = JSON.parse(localStorage.getItem('kimpa-open-menus')) || [];

                if (isHidden) {
                    submenu.style.display = 'block';
                    if (arrow) arrow.style.transform = 'rotate(180deg)';
                    
                    if (id && !openMenus.includes(id)) {
                        openMenus.push(id);
                        localStorage.setItem('kimpa-open-menus', JSON.stringify(openMenus));
                    }
                } else {
                    submenu.style.display = 'none';
                    if (arrow) arrow.style.transform = 'rotate(0deg)';
                    
                    if (id) {
                        openMenus = openMenus.filter(mId => mId !== id);
                        localStorage.setItem('kimpa-open-menus', JSON.stringify(openMenus));
                    }
                }
            }
        }

        // FUNGSI ALIAS (Agar kompatibel jika Anda panggil toggleDeptDropdown)
        function toggleDeptDropdown(id) {
            var submenu = document.getElementById(id);
            if (submenu) {
                var parentLink = submenu.previousElementSibling;
                if (parentLink) toggleSubmenu(parentLink);
            }
        }

        // TOGGLE DROPDOWN PROFIL TOPBAR
        function toggleProfileDropdown() {
            var menu = document.getElementById('profilMenu');
            var arrow = document.getElementById('profilArrow');
            if (menu) {
                menu.classList.toggle('show');
                if (arrow) {
                    arrow.style.transform = menu.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
                }
            }
        }

        // TUTUP DROPDOWN PROFIL JIKA KLIK DI LUAR
        window.addEventListener('click', function(event) {
            if (!event.target.closest('.profil-container')) {
                var menu = document.getElementById('profilMenu');
                var arrow = document.getElementById('profilArrow');
                if (menu && menu.classList.contains('show')) {
                    menu.classList.remove('show');
                    if (arrow) arrow.style.transform = 'rotate(0deg)';
                }
            }
        });
    </script>

    <!-- SCRIPT INITIALIZATION (MEMUAT STATE TERBACA DARI LOCALSTORAGE & HIGHLIGHT) -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Buka kembali dropdown yang disimpan di localStorage
            let openMenus = JSON.parse(localStorage.getItem('kimpa-open-menus')) || [];
            openMenus.forEach(function(id) {
                let submenu = document.getElementById(id);
                if (submenu) {
                    submenu.style.display = 'block';
                    let parentLi = submenu.closest('li');
                    let parentLink = parentLi ? parentLi.querySelector('a') : submenu.previousElementSibling;
                    if (parentLink) {
                        let arrow = parentLink.querySelector('.fa-chevron-down, .fa-angle-down, .fa-angle-left');
                        if (arrow) arrow.style.transform = 'rotate(180deg)';
                    }
                }
            });

            // 2. Logika Highlight Active Link
            let currentUrl = window.location.href.split('?')[0]; 
            let sidebarLinks = document.querySelectorAll('.sidebar a');
            let exactMatchFound = false;

            sidebarLinks.forEach(function(link) {
                if (link.getAttribute('href') === '#' || link.getAttribute('href') === 'javascript:void(0);') return;

                if (link.href === currentUrl) {
                    exactMatchFound = true;
                    highlightMenu(link);
                }
            });

            if (!exactMatchFound) {
                let bestMatch = null;
                sidebarLinks.forEach(function(link) {
                    if (link.getAttribute('href') === '#' || link.getAttribute('href') === 'javascript:void(0);') return;
                    
                    if (currentUrl.startsWith(link.href + '/')) {
                        if (!bestMatch || link.href.length > bestMatch.href.length) {
                            bestMatch = link;
                        }
                    }
                });

                if (bestMatch) {
                    highlightMenu(bestMatch);
                }
            }

            function highlightMenu(link) {
                link.style.color = '#ffffff';
                link.style.backgroundColor = 'var(--sidebar-hover)';
                
                let parentSubmenu = link.closest('.sub-menu, ul');
                if (parentSubmenu && !parentSubmenu.classList.contains('menu-list')) {
                    parentSubmenu.style.display = 'block';
                    
                    if (parentSubmenu.id && !openMenus.includes(parentSubmenu.id)) {
                        openMenus.push(parentSubmenu.id);
                        localStorage.setItem('kimpa-open-menus', JSON.stringify(openMenus));
                    }
                    
                    let parentLi = parentSubmenu.closest('li');
                    let parentMenu = parentLi ? parentLi.querySelector('a') : parentSubmenu.previousElementSibling; 
                    if (parentMenu) {
                        let arrow = parentMenu.querySelector('.fa-chevron-down, .fa-angle-down, .fa-angle-left');
                        if (arrow) arrow.style.transform = 'rotate(180deg)';
                    }
                }
            }

            // 3. Simpan Scroll Position Sidebar
            let sidebarScrollArea = document.querySelector('.sidebar > div:nth-child(2)'); 
            if (sidebarScrollArea) {
                let scrollPos = localStorage.getItem('kimpa-sidebar-scroll');
                if (scrollPos) {
                    sidebarScrollArea.scrollTop = scrollPos;
                }
                sidebarScrollArea.addEventListener('scroll', function() {
                    localStorage.setItem('kimpa-sidebar-scroll', sidebarScrollArea.scrollTop);
                });
            }
        });
    </script>

    <!-- SCRIPT GLOBAL: PENCEGAH DOUBLE SUBMIT FORM -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const allForms = document.querySelectorAll('form');
            
            allForms.forEach(function(form) {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    
                    if (submitBtn) {
                        if (!submitBtn.classList.contains('no-disable')) {
                            submitBtn.disabled = true;
                            submitBtn.style.width = submitBtn.offsetWidth + 'px'; 
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                            submitBtn.style.opacity = '0.7';
                            submitBtn.style.cursor = 'not-allowed';
                        }
                    }
                });
            });
        });
    </script>

    <!-- ============================================== -->
    <!-- JQUERY & DATATABLES SCRIPT                     -->
    <!-- ============================================== -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            if ($('.datatable').length > 0) {
                $('.datatable').DataTable({
                    "language": {
                        "search": "Cari Data:",
                        "lengthMenu": "Tampilkan _MENU_ baris",
                        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        "infoEmpty": "Tidak ada data",
                        "infoFiltered": "(difilter dari _MAX_ data)",
                        "zeroRecords": "Data tidak ditemukan",
                        "paginate": {
                            "first": "Pertama",
                            "last": "Terakhir",
                            "next": "Selanjutnya",
                            "previous": "Sebelumnya"
                        }
                    },
                    "pageLength": 10,
                    "ordering": true
                });
            }
        });
    </script>

    <!-- ============================================== -->
    <!-- SWEETALERT GLOBAL NOTIFICATION                 -->
    <!-- ============================================== -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success', 
                    title: 'Berhasil!', 
                    text: '{{ session('success') }}',
                    showConfirmButton: false, 
                    timer: 2500, 
                    background: '#ffffff', 
                    color: '#1e293b',
                    toast: true,
                    position: 'top-end'
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error', 
                    title: 'Gagal!', 
                    text: '{{ session('error') }}', 
                    confirmButtonColor: '#ef4444'
                });
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>