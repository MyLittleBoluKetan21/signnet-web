<section class="w-full space-y-6">
    <header>
        <h2 class="text-lg font-medium text-slate-900 dark:text-white">
            Hapus Akun Permanen
        </h2>

        <p class="mt-1 text-sm text-slate-400 dark:text-slate-400">
            Setelah akun Anda dihapus, seluruh data, riwayat pengerjaan, dan aset di dalamnya akan dihapus secara permanen. Sebelum melanjutkan, harap unduh atau amankan data penting yang masih Anda butuhkan.
        </p>
    </header>

    <div class="pt-2">
        <button
            type="button"
            id="btn-trigger-delete-custom"
            class="inline-flex items-center justify-center bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold uppercase tracking-wider px-6 py-3.5 rounded-xl transition duration-200 shadow-lg shadow-rose-600/20"
        >
            <i class="fa-solid fa-trash-can mr-2"></i> Hapus Akun Saya
        </button>
    </div>
</section>

<div 
    id="custom-delete-modal" 
    class="fixed inset-0 z-[99999] hidden items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4"
    style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh;"
>
    <div 
        id="custom-delete-card"
        class="glass max-w-lg w-full rounded-[2rem] p-6 md:p-8 border border-rose-500/30 bg-[#0c111d] shadow-2xl relative"
    >
        <header class="mb-5">
            <div class="w-12 h-12 bg-rose-500/10 border border-rose-500/20 rounded-full flex items-center justify-center text-rose-500 text-xl mb-4">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3 class="text-lg font-extrabold text-white tracking-tight">
                Apakah Anda yakin ingin menghapus akun?
            </h3>
            <p class="mt-2 text-sm text-slate-400 leading-relaxed">
                Tindakan ini bersifat permanen. Silakan masukkan kata sandi akun Anda untuk memverifikasi bahwa Anda benar-benar pemilik sah akun ini.
            </p>
        </header>

        <form method="post" action="{{ route('profile.destroy') }}" id="real-delete-user-form" class="space-y-4 m-0 p-0">
            @csrf
            @method('delete')

            <div>
                <label for="delete_password" class="block text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400 mb-2">
                    Kata Sandi Konfirmasi
                </label>
                <input 
                    id="delete_password"
                    name="password"
                    type="password"
                    class="w-full bg-slate-950/50 border border-white/10 rounded-xl px-4 py-3.5 text-sm text-white focus:outline-none focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10"
                    placeholder="Masukkan kata sandi Anda"
                />
                
                <div id="ajax-error-msg" class="text-xs text-rose-500 font-semibold mt-2 hidden items-center gap-1 animate__animated animate__headShake">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span id="error-text">Kata sandi yang Anda masukkan salah.</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-white/5 mt-6">
                <button
                    type="button"
                    id="btn-cancel-delete"
                    class="px-5 py-3 rounded-xl text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-white bg-slate-800/40 hover:bg-slate-800 border border-white/5 transition duration-150"
                >
                    Batal
                </button>
                
                <button
                    type="submit"
                    id="btn-confirm-submit"
                    class="px-5 py-3 rounded-xl text-xs font-bold uppercase tracking-wider bg-rose-600 hover:bg-rose-700 text-white shadow-lg shadow-rose-600/20 transition duration-150"
                >
                    Hapus Akun Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const triggerBtn = document.getElementById('btn-trigger-delete-custom');
        const modalOverlay = document.getElementById('custom-delete-modal');
        const modalCard = document.getElementById('custom-delete-card');
        const cancelBtn = document.getElementById('btn-cancel-delete');
        const passwordInput = document.getElementById('delete_password');
        const realForm = document.getElementById('real-delete-user-form');
        const errorContainer = document.getElementById('ajax-error-msg');
        const errorText = document.getElementById('error-text');

        // Pindahkan modal langsung ke <body> terluar agar posisi layouting fix di tengah screen
        if (modalOverlay && modalOverlay.parentNode !== document.body) {
            document.body.appendChild(modalOverlay);
        }

        // Fungsi Membuka Modal + Animasi Masuk
        function openDeleteModal() {
            if (modalOverlay && modalCard) {
                if (errorContainer) errorContainer.classList.add('hidden');
                if (passwordInput) {
                    passwordInput.value = '';
                    passwordInput.classList.remove('border-rose-500/50', 'ring-4', 'ring-rose-500/10');
                }
                
                modalOverlay.classList.remove('hidden');
                modalOverlay.classList.add('flex');
                
                modalOverlay.className = "fixed inset-0 z-[99999] flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4 animate__animated animate__fadeIn animate__faster";
                modalCard.className = "glass max-w-lg w-full rounded-[2rem] p-6 md:p-8 border border-rose-500/30 bg-[#0c111d] shadow-2xl relative animate__animated animate__zoomIn animate__faster";
                
                setTimeout(() => {
                    if (passwordInput) passwordInput.focus();
                }, 200);
            }
        }

        // Fungsi Menutup Modal + Animasi Keluar Tanpa Page Refresh
        function closeDeleteModal() {
            if (modalOverlay && modalCard) {
                modalOverlay.classList.remove('animate__fadeIn');
                modalOverlay.classList.add('animate__fadeOut');
                
                cardClassName = "glass max-w-lg w-full rounded-[2rem] p-6 md:p-8 border border-rose-500/30 bg-[#0c111d] shadow-2xl relative animate__animated animate__zoomOut animate__faster";
                modalCard.className = cardClassName;

                modalOverlay.addEventListener('animationend', function handler() {
                    modalOverlay.classList.remove('flex', 'animate__fadeOut');
                    modalOverlay.classList.add('hidden');
                    modalOverlay.removeEventListener('animationend', handler);
                });
            }
        }

        // Klik tombol utama "Hapus Akun Saya"
        if (triggerBtn) {
            triggerBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (typeof AppAlert !== 'undefined' && typeof AppAlert.confirm === 'function') {
                    AppAlert.confirm(
                        'KONFIRMASI TINDAKAN', 
                        'Apakah Anda yakin ingin membuka panel verifikasi penghapusan akun?', 
                        () => {
                            openDeleteModal();
                        }
                    );
                } else {
                    openDeleteModal();
                }
            });
        }

        // Klik tombol Batal di dalam modal (Tutup modal secara aman tanpa refresh)
        if (cancelBtn) {
            cancelBtn.addEventListener('click', (e) => {
                e.preventDefault();
                closeDeleteModal();
            });
        }

        // Menutup modal jika area luar overlay diklik
        if (modalOverlay) {
            modalOverlay.addEventListener('click', (e) => {
                if (e.target === modalOverlay) {
                    closeDeleteModal();
                }
            });
        }

        // Intersepsi Form Menggunakan AJAX Fetch API
        if (realForm) {
            realForm.addEventListener('submit', (e) => {
                e.preventDefault(); // Menghentikan refresh bawaan HTML Form

                // Validasi Client Side: Field Password Kosong
                if (passwordInput && passwordInput.value.trim() === '') {
                    if (typeof AppAlert !== 'undefined' && typeof AppAlert.fire === 'function') {
                        AppAlert.fire(
                            'error', 
                            'KATA SANDI WAJIB DIISI', 
                            'Harap isi kolom kata sandi konfirmasi Anda terlebih dahulu.'
                        );
                    }
                    passwordInput.focus();
                    return;
                }

                const formData = new FormData(realForm);

                // Kirim data via background thread
                fetch(realForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    // Jika sukses validasi & diarahkan otomatis oleh Laravel Session, ikuti redirect ke Welcome Page (/)
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    return response.json().then(data => {
                        if (!response.ok) {
                            throw data; // Lempar ke catch block jika ada status error (422 / 401)
                        }
                        window.location.href = '/'; // Fallback redirect jika status ok tanpa explicit redirect URL
                    });
                })
                .catch(error => {
                    // Penanganan & Terjemahan Pesan Error ke Bahasa Indonesia
                    let msg = 'KATA SANDI YANG ANDA MASUKKAN SALAH.';
                    
                    if (error && error.errors && error.errors.password) {
                        let backendMsg = error.errors.password[0].toLowerCase();
                        // Cek jika ada keyword 'incorrect' atau 'wrong', paksa ubah ke Bahasa Indonesia
                        if (backendMsg.includes('incorrect') || backendMsg.includes('wrong') || backendMsg.includes('salah')) {
                            msg = 'KATA SANDI YANG ANDA MASUKKAN SALAH.';
                        } else {
                            msg = error.errors.password[0].toUpperCase();
                        }
                    }

                    // Suntik teks error baru ke elemen teks HTML di bawah input
                    if (errorContainer && errorText) {
                        errorText.innerText = msg;
                        errorContainer.classList.remove('hidden');
                        errorContainer.classList.add('flex');
                    }

                    // Beri highlight border merah pada input password
                    if (passwordInput) {
                        passwordInput.classList.add('border-rose-500/50', 'ring-4', 'ring-rose-500/10');
                        passwordInput.focus();
                    }

                    // Tampilkan SweetAlert / AppAlert tanpa merusak atau menutup modal kustom
                    if (typeof AppAlert !== 'undefined' && typeof AppAlert.fire === 'function') {
                        AppAlert.fire(
                            'error', 
                            'KATA SANDI SALAH', 
                            'Verifikasi gagal. Kata sandi salah dan proses hapus akun ditangguhkan.'
                        );
                    }
                });
            });
        }
    });
</script>