<div id="navOverlay" class="nav-overlay"></div>

<nav id="navbar">
    <a href="{{ route('home') }}#beranda" class="logo">Sign<span>Net</span></a>
    
    <ul class="nav-links" id="navLinks">
        <li><a href="{{ route('home') }}#beranda" class="nav-item">Beranda</a></li>
        <li><a href="{{ route('home') }}#tentang" class="nav-item">Tentang</a></li>
        <li><a href="{{ route('home') }}#fitur" class="nav-item">Fitur</a></li>
        <li><a href="{{ route('home') }}#daftar-isyarat" class="nav-item">Daftar Isyarat</a></li>
        <li><a href="{{ route('recognition') }}" class="nav-item">Deteksi</a></li>

        @guest
            @if (!request()->routeIs('recognition'))
                <li><a href="{{ route('login') }}" class="nav-item">Login</a></li>
            @endif
        @endguest

        @auth
            <li><a href="{{ route('dashboard') }}" class="nav-item">Dashboard</a></li>
        @endauth
    </ul>

    <div class="nav-right">
        <button id="themeToggle" class="theme-toggle-btn" title="Ubah Tema">
            <i class="fa-solid fa-moon"></i>
        </button>
        
        <button id="mobileNavToggle" class="menu-toggle-btn" aria-label="Toggle Navigation">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div> 
    
</nav>

<script>
    // --- RESPONSIVE SIDEBAR & OVERLAY ENGINE ---
    function initResponsiveNavbar() {
        const navToggle = document.getElementById('mobileNavToggle');
        const navLinks = document.getElementById('navLinks');
        const navOverlay = document.getElementById('navOverlay');
        const navItems = document.querySelectorAll('.nav-item');

        if (!navLinks || !navOverlay || !navToggle) return;

        function openMobileNav() {
            navLinks.classList.add('active');
            navOverlay.classList.add('active');
            
            const icon = navToggle.querySelector('i');
            if(icon) icon.className = 'fa-solid fa-xmark';
        }

        function closeMobileNav() {
            navLinks.classList.remove('active');
            navOverlay.classList.remove('active');
            
            const icon = navToggle.querySelector('i');
            if(icon) icon.className = 'fa-solid fa-bars';
        }

        navToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = navLinks.classList.contains('active');
            if (isOpen) closeMobileNav(); else openMobileNav();
        });

        navOverlay.addEventListener('click', closeMobileNav);
        navItems.forEach(item => item.addEventListener('click', closeMobileNav));

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 769) {
                closeMobileNav();
            }
        });
    }

    // --- THEME MANAGEMENT ---
    function initNavbarTheme() {
        const themeToggleBtn = document.getElementById('themeToggle');
        if (!themeToggleBtn) return;
        const themeIcon = themeToggleBtn.querySelector('i');
        
        function updateToggleIcon(theme) {
            if (!themeIcon) return;
            if (theme === 'light') {
                themeIcon.className = 'fa-solid fa-sun';
                themeIcon.style.color = '#f59e0b';
            } else {
                themeIcon.className = 'fa-solid fa-moon';
                themeIcon.style.color = 'var(--text)';
            }
        }

        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
        updateToggleIcon(savedTheme);

        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateToggleIcon(newTheme);
            
            window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: newTheme } }));
        });

        window.addEventListener('themeChanged', (e) => {
            document.documentElement.setAttribute('data-theme', e.detail.theme);
            updateToggleIcon(e.detail.theme);
        });
    }

    // --- SCROLL EFFECT NAVBAR ---
    function initNavbarScroll() {
        const navbar = document.getElementById('navbar');
        if (!navbar) return;
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // --- DOM INITIALIZER ---
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initResponsiveNavbar();
            initNavbarTheme();
            initNavbarScroll();
        });
    } else {
        initResponsiveNavbar();
        initNavbarTheme();
        initNavbarScroll();
    }
</script>