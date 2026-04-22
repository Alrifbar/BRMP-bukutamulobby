<aside class="sidebar">
    <div>
        <div class="sidebar-controls">
            <button class="sidebar-btn" id="btnSidebarToggle" title="Buka/Tutup">
                <i class="bi bi-list"></i>
            </button>
            <button class="sidebar-btn" id="btnThemeToggle" title="Ubah Tema">
                <i id="themeIcon" class="bi"></i>
            </button>
        </div>
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">
                <img src="{{ \App\Models\Setting::getLogoUrl() }}" alt="Logo" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div class="sidebar-logo-text">
                <span>Buku Tamu</span>
                <span>Admin Panel</span>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li class="sidebar-menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" onclick="window.location.href='{{ route('admin.dashboard') }}'">
                <div class="sidebar-menu-icon"><i class="bi bi-house-door-fill"></i></div>
                <span>Dashboard</span>
            </li>
            <li class="sidebar-menu-item" onclick="window.location.href='{{ route('buku-tamu.create') }}'">
                <div class="sidebar-menu-icon"><i class="bi bi-pencil-square"></i></div>
                <span>Isi Buku Tamu</span>
            </li>
            <li class="sidebar-menu-item {{ request()->routeIs('admin.grafik') ? 'active' : '' }}" onclick="window.location.href='{{ route('admin.grafik') }}'">
                <div class="sidebar-menu-icon"><i class="bi bi-bar-chart-fill"></i></div>
                <span>Grafik Lengkap</span>
            </li>
            <li class="sidebar-menu-item {{ request()->routeIs('admin.rekap-pengunjung') ? 'active' : '' }}" onclick="window.location.href='{{ route('admin.rekap-pengunjung') }}'">
                <div class="sidebar-menu-icon"><i class="bi bi-clipboard-data-fill"></i></div>
                <span>Rekapan Pengunjung</span>
            </li>
            <li class="sidebar-menu-item {{ request()->routeIs('admin.form-builder.index') ? 'active' : '' }}" onclick="window.location.href='{{ route('admin.form-builder.index') }}'">
                <div class="sidebar-menu-icon"><i class="bi bi-ui-checks"></i></div>
                <span>Form Tamu</span>
            </li>

            <li class="sidebar-menu-item {{ request()->routeIs('admin.pengaturan') ? 'active' : '' }}" onclick="window.location.href='{{ route('admin.pengaturan') }}'">
                <div class="sidebar-menu-icon"><i class="bi bi-gear-fill"></i></div>
                <span>Pengaturan</span>
            </li>
            @unless(request()->routeIs('buku-tamu.*'))
            <li class="sidebar-menu-item" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                <div class="sidebar-menu-icon"><i class="bi bi-box-arrow-right"></i></div>
                <span>Logout</span>
            </li>
            @endunless
        </ul>
    </div>

    <div class="sidebar-footer">
        &copy; {{ date('Y') }} Buku Tamu Admin
    </div>
</aside>

<form id="logout-form-sidebar" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
    @csrf
</form>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var root = document.documentElement;
    var body = document.body;
    var sidebar = document.querySelector('.sidebar');
    var btnSidebarToggle = document.getElementById('btnSidebarToggle');
    var btnThemeToggle = document.getElementById('btnThemeToggle');
    var themeIcon = document.getElementById('themeIcon');

    function applyTheme(t) {
        root.classList.remove('theme-light', 'theme-dark');
        root.classList.add(t === 'dark' ? 'theme-dark' : 'theme-light');
        localStorage.setItem('theme', t);
        themeIcon.className = 'bi ' + (t === 'dark' ? 'bi-moon-fill' : 'bi-sun-fill');
        btnThemeToggle.title = t === 'dark' ? 'Mode Gelap' : 'Mode Terang';
    }

    function setCollapsed(c) {
        sidebar.classList.toggle('collapsed', c);
        body.classList.toggle('sidebar-collapsed', c);
        localStorage.setItem('sidebarCollapsed', c ? 'true' : 'false');
    }

    var savedTheme = localStorage.getItem('theme') || 'light';
    var savedCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    applyTheme(savedTheme);
    setCollapsed(savedCollapsed);

    btnSidebarToggle.addEventListener('click', function () {
        setCollapsed(!sidebar.classList.contains('collapsed'));
    });

    btnThemeToggle.addEventListener('click', function () {
        var current = localStorage.getItem('theme') || 'light';
        applyTheme(current === 'dark' ? 'light' : 'dark');
    });
});
</script>
