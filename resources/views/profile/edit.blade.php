<x-app-layout>
    <x-editprofile.styles />

    <div class="dashboard-wrapper antialiased custom-scroll relative min-h-screen bg-white dark:bg-slate-950 transition-colors duration-200">
        <div class="flex flex-col md:flex-row min-h-screen w-full">            
            
            {{-- Navigasi Sidebar Tetap Dipertahankan --}}
            <div id="sidebar-container" class="z-[999] shrink-0">
                @include('layouts.sidebar')
            </div>

            <main class="main-content flex-grow p-4 sm:p-6 md:p-8 lg:p-10 w-full overflow-y-auto h-screen custom-scroll">
                <div class="max-w-7xl mx-auto space-y-6 flex flex-col min-h-full justify-between">
                    
                    {{-- Mengembalikan glass-form-container agar inputan kembali presisi --}}
                    <div class="space-y-6 flex-grow glass-form-container">
                        
                        {{-- Mengembalikan Header Awal: Pengaturan Pengguna & Profil Akun --}}
                        <header class="flex items-center justify-between gap-4 pt-2 animate-card delay-1 w-full">
                            <div>
                                <span class="text-indigo-500 dark:text-indigo-400 text-[10px] font-bold uppercase tracking-[0.3em] block">Pengaturan Pengguna</span>
                                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white mt-0.5">
                                    <span class="logo-gradient">Profil Akun</span>
                                </h1>
                            </div>
                            
                            {{-- Tombol Toggle Sidebar Mobile - Sekarang sejajar di sebelah kanan --}}
                            <div class="flex items-center md:hidden shrink-0">
                                <button id="mobileSidebarToggle" type="button" class="flex items-center justify-center w-11 h-11 bg-slate-100 dark:bg-slate-900/80 text-slate-800 dark:text-white rounded-2xl border border-slate-200 dark:border-white/10 shadow-lg shadow-indigo-500/5 active:scale-95 transition-all duration-200 cursor-pointer">
                                    <i class="fa-solid fa-bars text-xl"></i>
                                </button>
                            </div>
                        </header>

                        <div class="p-4 rounded-2xl glass border-l-4 border-l-indigo-500 animate-card delay-1 flex items-start gap-3">
                            <i class="fa-solid fa-circle-info text-indigo-500 mt-0.5 text-lg"></i>
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Pemberitahuan Sistem</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pastikan input data profil atau kata sandi sudah sesuai. Untuk menjaga autentikasi akun, sistem mencocokkan riwayat masuk berdasarkan jejak digital perangkat Anda.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                            
                            {{-- KOLOM KIRI: UPDATE PROFILE & PASSWORD --}}
                            <div class="space-y-6 w-full">
                                
                                <div class="glass p-6 md:p-8 rounded-[2rem] animate-card delay-2 relative overflow-hidden flex flex-col">
                                    <div class="w-full">
                                        @include('profile.partials.update-profile-information-form')
                                    </div>
                                </div>

                                <div class="glass p-6 md:p-8 rounded-[2rem] animate-card delay-3 relative overflow-hidden flex flex-col">
                                    <div class="w-full">
                                        @include('profile.partials.update-password-form')
                                    </div>
                                </div>

                            </div>

                            {{-- KOLOM KANAN: SESI LOGIN AKTIF & ZONA BAHAYA --}}
                            <div class="w-full space-y-6 animate-card delay-4">
                                
                                {{-- PANEL: INFORMASI SESI LOGIN AKTIF REAL-TIME --}}
                                <div class="glass p-6 md:p-8 rounded-[2rem] relative overflow-hidden flex flex-col" x-data="{
                                    deviceOS: 'Mendeteksi...',
                                    browserName: 'Memuat...',
                                    ipAddress: '{{ request()->ip() }}',
                                    init() {
                                        const ua = navigator.userAgent;
                                        if (ua.indexOf('Win') != -1) this.deviceOS = 'Windows PC';
                                        else if (ua.indexOf('Mac') != -1) this.deviceOS = 'macOS Device';
                                        else if (ua.indexOf('Linux') != -1) this.deviceOS = 'Linux OS';
                                        else if (ua.indexOf('Android') != -1) this.deviceOS = 'Android Smartphone';
                                        else if (ua.indexOf('iPhone') != -1) this.deviceOS = 'iOS Device (iPhone)';
                                        else this.deviceOS = 'Perangkat Mobile';

                                        if (ua.indexOf('Chrome') != -1 && ua.indexOf('Edge') == -1) this.browserName = 'Google Chrome';
                                        else if (ua.indexOf('Safari') != -1 && ua.indexOf('Chrome') == -1) this.browserName = 'Apple Safari';
                                        else if (ua.indexOf('Firefox') != -1) this.browserName = 'Mozilla Firefox';
                                        else if (ua.indexOf('Edge') != -1) this.browserName = 'Microsoft Edge';
                                        else this.browserName = 'Browser Umum';
                                    }
                                }">
                                    <div class="w-full">
                                        <header class="mb-4">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <h2 class="text-base font-bold text-slate-900 dark:text-white m-0">Sesi Login Aktif</h2>
                                            </div>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                                Daftar perangkat yang saat ini memiliki akses resmi ke akun Anda.
                                            </p>
                                        </header>

                                        <div class="space-y-3.5 mt-4">
                                            <div class="p-4 rounded-2xl bg-slate-100/70 dark:bg-slate-800/40 border border-slate-200/50 dark:border-slate-700/30 flex items-center justify-between">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center text-lg">
                                                        <template x-if="deviceOS.includes('Windows') || deviceOS.includes('macOS') || deviceOS.includes('Linux')">
                                                            <i class="fa-solid fa-desktop"></i>
                                                        </template>
                                                        <template x-if="!deviceOS.includes('Windows') && !deviceOS.includes('macOS') && !deviceOS.includes('Linux')">
                                                            <i class="fa-solid fa-mobile-screen-button"></i>
                                                        </template>
                                                    </div>
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200" x-text="deviceOS"></span>
                                                            <span class="text-[9px] font-extrabold bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 px-2 py-0.5 rounded-full uppercase tracking-wider">Sesi Ini</span>
                                                        </div>
                                                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                                            <span x-text="browserName"></span> &bull; IP: <span class="font-mono text-[11px]" x-text="ipAddress"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wide">Aktif</span>
                                            </div>

                                            <div class="p-3.5 rounded-xl bg-amber-500/5 border border-amber-500/15 flex gap-2.5 items-start">
                                                <i class="fa-solid fa-triangle-exclamation text-amber-500 text-xs mt-0.5"></i>
                                                <p class="text-[11px] text-amber-600 dark:text-amber-400/90 leading-normal font-medium">
                                                    Jika Anda melihat kejanggalan aktivitas atau detail perangkat ilegal yang tidak Anda kenali, segera lakukan pembaruan kata sandi di panel sebelah kiri untuk memutus seluruh tautan log luar secara otomatis.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- PANEL HAPUS AKUN (ZONA BAHAYA) --}}
                                <div class="glass p-6 md:p-8 rounded-[2rem] relative overflow-hidden" id="custom-danger-zone">
                                    <div class="w-full">
                                        @include('profile.partials.delete-user-form')
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <footer class="footer-bottom pt-8 pb-2 text-center animate-card delay-4" style="animation-delay: 0.4s;">
                        <p class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 tracking-wider">
                            © 2026 SignNet Project &bull; Bahasa Isyarat BISINDO.
                        </p>
                    </footer>

                </div>
            </main>
        </div>
    </div>

    <script>
        // Sinkronisasi Tema Tampilan (Gelap / Terang)
        const htmlEl = document.documentElement;

        function handleThemeSync(theme) {
            htmlEl.setAttribute('data-theme', theme);
            if (theme === 'dark') {
                htmlEl.classList.add('dark');
            } else {
                htmlEl.classList.remove('dark');
            }
        }

        window.addEventListener('themeChanged', (e) => {
            handleThemeSync(e.detail.theme);
        });

        handleThemeSync(localStorage.getItem('theme') || 'dark');

        document.addEventListener('DOMContentLoaded', () => {
            
            // Integrasi Notifikasi Berhasil Simpan Data Menggunakan AppAlert
            @if(session('status') === 'profile-updated' || session('status') === 'password-updated' || session('success'))
                if (typeof AppAlert !== 'undefined') {
                    AppAlert.fire('success', 'BERHASIL', 'Perubahan konfigurasi akun berhasil disimpan.');
                }
            @endif

            // Integrasi Notifikasi Error / Salah Validasi Kolom Menggunakan AppAlert
            @if($errors->any())
                if (typeof AppAlert !== 'undefined') {
                    AppAlert.fire('error', 'PROSES GAGAL', 'Harap periksa kembali kesesuaian data form input Anda.');
                }
            @endif

            /* INTEGRASI KUSTOM TOMBOL HAPUS AKUN */
            const rawDeleteButton = document.querySelector('#custom-danger-zone button[type="submit"], #custom-danger-zone button, #btn-trigger-delete');
            const rawDeleteForm = document.querySelector('#custom-danger-zone form');

            if (rawDeleteButton && rawDeleteForm) {
                rawDeleteButton.removeAttribute('x-on:click');
                rawDeleteButton.removeAttribute('@click');
                
                rawDeleteButton.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    if (typeof AppAlert !== 'undefined' && typeof AppAlert.confirm === 'function') {
                        AppAlert.confirm('HAPUS AKUN PERMANEN', 'Apakah Anda yakin ingin menghapus akun ini secara permanen? Seluruh riwayat data pengerjaan Anda akan dihapus selamanya.', () => {
                            rawDeleteForm.submit();
                        });
                    }
                });
            }
        });
    </script>
</x-app-layout>