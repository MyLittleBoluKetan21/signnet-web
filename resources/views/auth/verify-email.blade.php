<x-guest-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --blue: #3b82f6;
            --blue-light: #60a5fa;
            
            --dark-bg: linear-gradient(180deg, 
                #050914 0%, #0a122c 22%, #151233 48%, #0b1636 72%, #111c44 90%, #03050a 100%
            );
            
            --dark-card: linear-gradient(145deg, #0d1730 0%, #132247 100%);
            --dark-card2: linear-gradient(145deg, #111e3d 0%, #182c59 100%);
            --border: rgba(59, 130, 246, 0.25);
            --text: #ffffff;
            --text-muted: #94a3b8;
            --nav-bg: rgba(6, 11, 25, 0.75);

            --bg-card: var(--dark-card);
            --border-login: var(--border);
            --border-focus: var(--blue-light);
            --text-primary: var(--text);
            --text-label: var(--text);
            --bg-input: rgba(13, 23, 48, 0.5);
            --accent: var(--blue);
            --accent-hover: var(--blue-light);
        }

        [data-theme="light"] {
            --blue: #2563eb;
            --blue-light: #3b82f6;
            
            --dark-bg: linear-gradient(180deg, 
                #f0f7ff 0%, #e0e7ff 20%, #e0f2fe 45%, #f0fdf4 70%, #fff1f2 88%, #f8fafc 100%
            );
            
            --dark-card: linear-gradient(145deg, #ffffff 0%, #f0fdf4 100%); 
            --dark-card2: linear-gradient(145deg, #f8fafc 0%, #e0f2fe 100%); 
            --border: rgba(37, 99, 235, 0.2);
            --text: #0f172a;
            --text-muted: #475569;
            --nav-bg: rgba(239, 246, 255, 0.75);

            --bg-card: #ffffff;
            --border-login: var(--border);
            --border-focus: var(--blue);
            --text-primary: var(--text);
            --text-label: var(--text);
            --bg-input: #f8fafc;
            --accent: var(--blue);
            --accent-hover: var(--blue-light);
        }

        html, body { min-height: 100vh; height: 100%; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--dark-bg); background-attachment: fixed; background-size: cover; color: var(--text); }

        body::before { 
            content: ''; position: fixed; inset: 0;
            background: radial-gradient(ellipse 60% 50% at 50% 40%, rgba(37,99,235,.12) 0%, transparent 70%); 
            pointer-events: none; z-index: 0; animation: bgFloat 8s ease-in-out infinite alternate; 
        }

        @keyframes bgFloat { from { transform: translateY(0); } to { transform: translateY(-15px); } }

        /* --- NAVBAR --- */
        nav { 
            position: fixed; top: 0; left: 0; right: 0; z-index: 1030; 
            display: flex; align-items: center; justify-content: space-between; 
            padding: 18px 60px; background: transparent; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
        }
        nav.scrolled { 
            background: var(--nav-bg); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 4px 30px rgba(0,0,0,0.05); padding: 12px 60px;
        }
        .logo { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.5px; color: var(--text); text-decoration: none; z-index: 1050; }
        .logo span { color: var(--blue); }

        .nav-links { position: absolute; left: 50%; transform: translateX(-50%); display: flex; gap: 36px; list-style: none; }
        .nav-links li { display: flex; align-items: center; }
        .nav-links a { 
            display: inline-block;
            color: var(--text-muted); 
            text-decoration: none; 
            font-size: 0.9rem; 
            font-weight: 500; 
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); 
            position: relative;
            padding: 4px 0;
        }
        .nav-links a:hover { 
            color: var(--text); 
            transform: scale(1.1); 
        }
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--blue);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .nav-links a:hover::after { width: 100%; }

        .nav-right { width: 100px; display: flex; justify-content: flex-end; align-items: center; gap: 12px; z-index: 1050; }
        .theme-toggle-btn { background: var(--dark-card2); border: 1px solid var(--border); color: var(--text); width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; transition: all 0.4s ease; }
        .theme-toggle-btn:hover { border-color: var(--blue); color: var(--blue); transform: scale(1.1) rotate(15deg); }

        .menu-toggle-btn {
            display: none;
            background: var(--dark-card2);
            border: 1px solid var(--border);
            color: var(--text);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: all 0.4s ease;
        }
        .menu-toggle-btn:hover { border-color: var(--blue); color: var(--blue); }

        .nav-overlay {
            position: fixed; 
            inset: 0; 
            background: rgba(6, 11, 25, 0.5); 
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 1010; 
            display: none; 
            opacity: 0; 
            transition: opacity 0.3s ease;
        }
        .nav-overlay.active {
            display: block;
            opacity: 1;
        }

        /* --- WRAPPERS & CARD --- */
        .login-page-wrapper { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 100px 24px 40px; position: relative; z-index: 1; }
        .login-container { display: flex; flex-direction: column; align-items: center; width: 100%; }
        
        .card-animator { width: 100%; max-width: 440px; animation: cardEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1) both; will-change: transform, opacity; }
        @keyframes cardEntrance { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }

        .card { 
            background: var(--bg-card); border: 1px solid var(--border-login); border-radius: 18px; padding: 38px 32px 30px; width: 100%; position: relative;
            box-shadow: 0 0 0 1px rgba(255,255,255,.04) inset, 0 20px 50px rgba(0,0,0,.4); transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1), box-shadow 0.3s cubic-bezier(0.25, 1, 0.5, 1);
            transform-style: preserve-3d; backface-visibility: hidden; -webkit-font-smoothing: subpixel-antialiased;
        }
        .card:hover { transform: translateY(-5px); box-shadow: 0 0 0 1px rgba(255,255,255,.06) inset, 0 28px 60px rgba(0,0,0,.5); }

        .brand { text-align: center; margin-bottom: 22px; }
        .brand-logo { font-size: 26px; font-weight: 800; letter-spacing: -.5px; color: var(--text-primary); }
        .brand-logo span { color: var(--blue); }
        .brand-sub { margin-top: 6px; font-size: 12px; font-weight: 600; color: var(--text-muted); }
        
        .info-text { font-size: 13px; color: var(--text-muted); text-align: center; line-height: 1.6; margin-bottom: 24px; }
        
        /* --- ALERT STATUS --- */
        .status-alert { background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 24px; text-align: center; font-weight: 500; line-height: 1.5; }

        .actions-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-top: 12px; }

        .btn-submit { display: flex; align-items: center; justify-content: center; gap: 8px; background: var(--accent);
            color: #fff; font-family: inherit; font-size: 12.5px; font-weight: 700; border: none; border-radius: 10px; padding: 12px 20px;
            cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 15px rgba(37,99,235,.25); }
        .btn-submit:hover { background: var(--accent-hover); box-shadow: 0 6px 22px rgba(37,99,235,.38); transform: translateY(-1.5px); }

        .btn-logout { background: none; border: none; font-family: inherit; font-size: 12.5px; font-weight: 600; color: var(--text-muted); text-decoration: underline; cursor: pointer; transition: color .2s ease; }
        .btn-logout:hover { color: var(--text-primary); }
        
        .footer-bottom { margin-top: 16px; text-align: center; }
        .footer-bottom p { font-size: 11px; color: var(--text-muted); }
        
        @media (max-width: 900px) { nav { padding: 16px 24px; } nav.scrolled { padding: 12px 24px; } }
        @media (max-width: 768px) {
            nav { z-index: 1060 !important; position: fixed !important; transition: background 0.4s ease, border-color 0.4s ease; }
            .logo { transition: opacity 0.2s ease, visibility 0.2s ease, color 0.4s ease !important; }
            .menu-toggle-btn { display: flex !important; }
            .nav-right { width: auto !important; }
            
            .nav-links { 
                position: fixed !important; top: 0; right: -100%; width: 210px; max-width: 65vw; height: 100vh; background: var(--nav-bg) !important; backdrop-filter: blur(16px) !important; -webkit-backdrop-filter: blur(16px) !important;
                border-left: 1px solid var(--border); flex-direction: column !important; justify-content: flex-start !important; align-items: flex-start !important; padding: 100px 20px 40px !important; gap: 8px !important; box-shadow: -15px 0 40px rgba(0,0,0,0.15); z-index: 1040 !important; list-style: none !important; left: auto !important; transform: none !important; transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1), background 0.4s ease, border-color 0.4s ease !important; 
            }
            .nav-links.active { right: 0 !important; }
            .nav-links li { width: 100% !important; display: block !important; }
            .nav-links a { font-size: 0.95rem !important; font-weight: 600 !important; color: var(--text-muted); width: 100% !important; display: block !important; padding: 12px 16px !important; border-radius: 12px; transition: all 0.2s ease, color 0.4s ease !important; text-align: left !important; box-sizing: border-box !important; }
            .nav-links a:hover { color: var(--blue) !important; background: var(--dark-card2) !important; transform: translateX(6px) !important; }
            .nav-links a::after { display: none !important; }
        }
        @media (max-width: 480px) { .login-page-wrapper { padding: 90px 16px 30px; } .card { padding: 32px 20px 24px; } .actions-row { flex-direction: column; gap: 14px; align-items: center; } .btn-submit { width: 100%; } }
    </style>

    <div id="navOverlay" class="nav-overlay"></div>

    <nav id="navbar">
        <a href="{{ route('home') }}#beranda" class="logo">Sign<span>Net</span></a>
        <ul class="nav-links" id="navLinks">
            <li><a href="{{ route('home') }}#beranda" class="nav-item">Beranda</a></li>
            <li><a href="{{ route('home') }}#tentang" class="nav-item">Tentang</a></li>
            <li><a href="{{ route('home') }}#fitur" class="nav-item">Fitur</a></li>
            <li><a href="{{ route('home') }}#daftar-isyarat" class="nav-item">Daftar Isyarat</a></li>
            <li><a href="{{ route('recognition') }}" class="nav-item">Deteksi</a></li>
            <li><a href="{{ route('login') }}" class="nav-item">Login</a></li>
        </ul>
        <div class="nav-right">
            <button id="themeToggle" class="theme-toggle-btn" title="Ubah Tema"><i class="fa-solid fa-moon"></i></button>
            <button id="mobileNavToggle" class="menu-toggle-btn" aria-label="Toggle Navigation"><i class="fa-solid fa-bars"></i></button>
        </div> 
    </nav>

    <div class="login-page-wrapper">
        <div class="login-container">
            <div class="card-animator">
                <div class="card">
                    <div class="brand">
                        <div class="brand-logo">Sign<span>Net</span></div>
                        <div class="brand-sub">Verifikasi Alamat Email</div>
                    </div>

                    <div class="info-text">
                        Terima kasih telah mendaftar! Sebelum memulai, silakan konfirmasi email Anda melalui tautan yang baru saja kami kirimkan. Jika tidak menerima email tersebut, kami akan dengan senang hati mengirimkannya kembali.
                    </div>

                    @if (session('status') == 'verification-link-sent')
                        <div class="status-alert">
                            Tautan verifikasi baru telah berhasil dikirimkan ke alamat email yang Anda daftarkan.
                        </div>
                    @endif

                    <div class="actions-row">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn-submit">
                                Kirim Ulang Email Verifikasi
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn-logout">
                                Keluar Akun
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>© 2026 SignNet Project &bull; Bahasa Isyarat BISINDO.</p>
            </div>
        </div>
    </div>

    <script>
        const themeToggleBtn = document.getElementById('themeToggle');
        const themeIcon = themeToggleBtn.querySelector('i');
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
        updateToggleIcon(savedTheme);

        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateToggleIcon(newTheme);
        });

        function updateToggleIcon(theme) {
            if (theme === 'light') {
                themeIcon.className = 'fa-solid fa-sun';
                themeIcon.style.color = '#f59e0b';
            } else {
                themeIcon.className = 'fa-solid fa-moon';
                themeIcon.style.color = 'var(--text)';
            }
        }

        function initResponsiveNavbar() {
            const navToggle = document.getElementById('mobileNavToggle');
            const navLinks = document.getElementById('navLinks');
            const navOverlay = document.getElementById('navOverlay');
            const navItems = document.querySelectorAll('.nav-item');

            if (!navLinks || !navOverlay || !navToggle) return;

            function openMobileNav() { navLinks.classList.add('active'); navOverlay.style.display = 'block'; setTimeout(() => navOverlay.style.opacity = '1', 10); }
            function closeMobileNav() { navLinks.classList.remove('active'); navOverlay.style.opacity = '0'; setTimeout(() => navOverlay.style.display = 'none', 200); }

            navToggle.addEventListener('click', (e) => { e.stopPropagation(); if (navLinks.classList.contains('active')) closeMobileNav(); else openMobileNav(); });
            navOverlay.addEventListener('click', closeMobileNav);
            navItems.forEach(item => item.addEventListener('click', closeMobileNav));
            window.addEventListener('resize', () => { if (window.innerWidth >= 769) closeMobileNav(); });
        }

        function initNavbarScroll() {
            const navbar = document.getElementById('navbar');
            if (!navbar) return;
            window.addEventListener('scroll', () => { if (window.scrollY > 50) navbar.classList.add('scrolled'); else navbar.classList.remove('scrolled'); });
        }

        document.addEventListener('DOMContentLoaded', () => { initResponsiveNavbar(); initNavbarScroll(); });
    </script>
</x-guest-layout>