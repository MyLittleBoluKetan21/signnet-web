<div id="sidebarOverlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[998] hidden opacity-0 transition-opacity duration-200 md:hidden"></div>

<aside id="sidebar" class="fixed top-0 bottom-0 left-0 md:sticky h-screen bg-[#020617] dark:bg-[#020617] flex flex-col items-start justify-between py-6 border-r border-white/10 shadow-xl w-64 md:w-24 md:hover:w-64 transition-all duration-200 ease-in-out group/sidebar overflow-hidden z-[999] -translate-x-full md:translate-x-0">
    
    <div class="flex items-center justify-start w-full px-6 font-black text-2xl tracking-tighter gap-4 mb-6 shrink-0">
        <div class="bg-indigo-600 rounded-lg min-w-[44px] h-11 flex items-center justify-center text-white text-base shadow-md shadow-indigo-600/20">
            SN
        </div>
        <span class="logo-gradient opacity-100 md:opacity-0 md:group-hover/sidebar:opacity-100 transition-all duration-200 font-extrabold whitespace-nowrap text-lg">
            SignNet
        </span>
    </div>

    <nav class="sidebar-nav flex flex-col gap-1 w-full px-3 flex-1 overflow-y-auto overflow-x-hidden custom-scroll">
        
        <div class="flex md:hidden md:group-hover/sidebar:flex text-[10px] font-bold text-slate-500 tracking-wider uppercase px-3 pt-3 pb-1 transition-all duration-200 whitespace-nowrap">
            Ringkasan Performa
        </div>
        
        <a href="{{ url('/admin/dashboard') }}" class="sidebar-link flex items-center gap-4 w-full h-12 px-3 rounded-xl transition-all duration-150 {{ Request::is('admin/dashboard') ? 'text-indigo-400 bg-indigo-500/10 shadow-sm ring-1 ring-indigo-500/20' : 'text-slate-400 hover:text-indigo-400 hover:bg-white/5' }}">
            <div class="w-11 h-11 flex items-center justify-center shrink-0"><i class="fa-solid fa-chart-pie text-xl"></i></div>
            <span class="opacity-100 md:opacity-0 md:group-hover/sidebar:opacity-100 transition-all duration-200 font-semibold text-sm whitespace-nowrap">Analisis Model</span>
        </a>

        <div class="flex md:hidden md:group-hover/sidebar:flex text-[10px] font-bold text-slate-500 tracking-wider uppercase px-3 pt-4 pb-1 transition-all duration-200 whitespace-nowrap">
            Manajemen Data & Model
        </div>

        <a href="{{ route('admin.trainmodel') }}" class="sidebar-link flex items-center gap-4 w-full h-12 px-3 rounded-xl transition-all duration-150 {{ Request::routeIs('admin.trainmodel') ? 'text-indigo-400 bg-indigo-500/10 shadow-sm ring-1 ring-indigo-500/20' : 'text-slate-400 hover:text-indigo-400 hover:bg-white/5' }}">
            <div class="w-11 h-11 flex items-center justify-center shrink-0"><i class="fa-solid fa-video text-xl"></i></div>          
            <span class="opacity-100 md:opacity-0 md:group-hover/sidebar:opacity-100 transition-all duration-200 font-semibold text-sm whitespace-nowrap">Koleksi Data & Retrain</span>
        </a>

        <a href="{{ route('admin.dataset.index') }}" class="sidebar-link flex items-center gap-4 w-full h-12 px-3 rounded-xl transition-all duration-150 {{ Request::is('admin/dataset*') ? 'text-indigo-400 bg-indigo-500/10 shadow-sm ring-1 ring-indigo-500/20' : 'text-slate-400 hover:text-indigo-400 hover:bg-white/5' }}">
            <div class="w-11 h-11 flex items-center justify-center shrink-0"><i class="fa-solid fa-database text-xl"></i></div>
            <span class="opacity-100 md:opacity-0 md:group-hover/sidebar:opacity-100 transition-all duration-200 font-semibold text-sm whitespace-nowrap">Dataset Isyarat</span>
        </a>

        <a href="{{ route('profile.edit') }}" class="sidebar-link flex items-center gap-4 w-full h-12 px-3 rounded-xl transition-all duration-150 {{ Request::routeIs('profile.edit') ? 'text-indigo-400 bg-indigo-500/10 shadow-sm ring-1 ring-indigo-500/20' : 'text-slate-400 hover:text-indigo-400 hover:bg-white/5' }}">
            <div class="w-11 h-11 flex items-center justify-center shrink-0"><i class="fa-solid fa-user-gear text-xl"></i></div>
            <span class="opacity-100 md:opacity-0 md:group-hover/sidebar:opacity-100 transition-all duration-200 font-semibold text-sm whitespace-nowrap">Pengaturan Profil</span>
        </a>

        <div class="flex md:hidden md:group-hover/sidebar:flex text-[10px] font-bold text-slate-500 tracking-wider uppercase px-3 pt-4 pb-1 transition-all duration-200 whitespace-nowrap">
            Hak Akses
        </div>

        <a href="{{ route('admin.manage.index') }}" class="sidebar-link flex items-center gap-4 w-full h-12 px-3 rounded-xl transition-all duration-150 {{ Request::is('admin/kelola-admin*') ? 'text-indigo-400 bg-indigo-500/10 shadow-sm ring-1 ring-indigo-500/20' : 'text-slate-400 hover:text-indigo-400 hover:bg-white/5' }}">
            <div class="w-11 h-11 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-user-shield text-xl"></i>
            </div>
            <span class="opacity-100 md:opacity-0 md:group-hover/sidebar:opacity-100 transition-all duration-200 font-semibold text-sm whitespace-nowrap">
                Kelola Akun Admin
            </span>
        </a>
    </nav>

    <div class="flex flex-col gap-2 w-full px-3 shrink-0">
        <button id="themeToggleSidebar" type="button" class="flex items-center gap-4 w-full h-12 px-3 rounded-xl text-slate-400 hover:bg-white/5 transition-all duration-150 text-left cursor-pointer select-none">
            <div class="w-11 h-11 flex items-center justify-center shrink-0">
                <i id="sidebar-theme-icon" class="fa-solid fa-moon text-xl"></i>
            </div>
            <span id="sidebar-theme-text" class="opacity-100 md:opacity-0 md:group-hover/sidebar:opacity-100 transition-all duration-200 font-semibold text-sm whitespace-nowrap">Mode Gelap</span>
        </button>

        <form method="POST" action="{{ route('logout') }}" id="logout-form" class="w-full m-0 pb-4">
            @csrf
            <button type="submit" class="flex items-center gap-4 w-full h-12 px-3 rounded-xl text-rose-500 hover:bg-rose-500/10 transition-all duration-150 text-left cursor-pointer">
                <div class="w-11 h-11 flex items-center justify-center shrink-0"><i class="fa-solid fa-power-off text-xl"></i></div>
                <span class="opacity-100 md:opacity-0 md:group-hover/sidebar:opacity-100 transition-all duration-200 font-semibold text-sm whitespace-nowrap">Keluar Sesi</span>
            </button>
        </form>
    </div>
</aside>

<script>
    function initResponsiveSidebar() {
        const mobileBtn = document.getElementById('mobileSidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const links = document.querySelectorAll('.sidebar-link');

        if (!sidebar || !overlay) return;

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.add('opacity-100'), 10);
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100');
            setTimeout(() => overlay.classList.add('hidden'), 200);
        }

        document.body.addEventListener('click', (e) => {
            if (e.target.closest('#mobileSidebarToggle')) {
                e.stopPropagation();
                const isClosed = sidebar.classList.contains('-translate-x-full');
                if (isClosed) openSidebar(); else closeSidebar();
            }
        });

        overlay.addEventListener('click', closeSidebar);
        links.forEach(link => link.addEventListener('click', closeSidebar));

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
                overlay.classList.remove('opacity-100');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });
    }

    function initSidebarTheme() {
        const btn = document.getElementById('themeToggleSidebar');
        const icon = document.getElementById('sidebar-theme-icon');
        const text = document.getElementById('sidebar-theme-text');
        
        function updateUI(theme) {
            if (!icon) return;
            if (theme === 'light') {
                icon.className = 'fa-solid fa-sun text-xl';
                icon.style.color = '#f59e0b';
                if (text) text.innerText = 'Mode Terang';
            } else {
                icon.className = 'fa-solid fa-moon text-xl';
                icon.style.color = '';
                if (text) text.innerText = 'Mode Gelap';
            }
        }

        const currentTheme = localStorage.getItem('theme') || 'dark';
        updateUI(currentTheme);

        if (btn) {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const oldTheme = localStorage.getItem('theme') || 'dark';
                const newTheme = oldTheme === 'dark' ? 'light' : 'dark';
                localStorage.setItem('theme', newTheme);
                document.documentElement.classList.toggle('dark', newTheme === 'dark');
                window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: newTheme } }));
            });
        }
        window.addEventListener('themeChanged', (e) => updateUI(e.detail.theme));
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initResponsiveSidebar();
            initSidebarTheme();
        });
    } else {
        initResponsiveSidebar();
        initSidebarTheme();
    }
</script>