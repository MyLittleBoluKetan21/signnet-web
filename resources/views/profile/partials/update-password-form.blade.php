<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-slate-400">
            {{ __('Pastikan akun Anda menggunakan kata sandi acak yang panjang untuk menjaga keamanan.') }}
        </p>
    </header>

    <div class="mt-4 p-4 rounded-xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100/50 dark:border-indigo-900/30">
        <h4 class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 flex items-center gap-2 mb-2">
            <i class="fa-solid fa-shield-halved"></i> Tata Cara & Keamanan Kata Sandi
        </h4>
        <ul class="text-xs text-slate-500 dark:text-slate-400 space-y-1 list-disc pl-4 leading-relaxed">
            <li>Kata sandi baru minimal harus terdiri dari <strong>8 karakter</strong>.</li>
            <li>Disarankan mengombinasikan <strong>huruf besar, huruf kecil, angka,</strong> dan <strong>simbol</strong> (@, #, $, dll).</li>
            <li>Jangan gunakan informasi personal yang mudah ditebak seperti tanggal lahir atau nama Anda.</li>
            <li>Setelah berhasil disimpan, sesi login di perangkat lain mungkin akan diminta untuk melakukan autentikasi ulang demi keamanan.</li>
        </ul>
    </div>

    <form 
        x-data="{
            current_password: '',
            password: '',
            password_confirmation: '',
            
            // State untuk visibilitas password
            hideCurrent: true,
            hideNew: true,
            hideConfirm: true,

            errors: { current_password: '', password: '', password_confirmation: '' },

            // Fitur Indikator Kekuatan Password
            get passwordStrength() {
                if (!this.password) return { score: 0, label: '', color: 'bg-slate-200 dark:bg-slate-700', text: 'text-slate-400' };
                let points = 0;
                
                if (this.password.length >= 8) points++;
                if (/[A-Z]/.test(this.password) && /[a-z]/.test(this.password)) points++;
                if (/[0-9]/.test(this.password)) points++;
                if (/[^A-Za-z0-9]/.test(this.password)) points++;

                if (points <= 1) return { score: 25, label: 'Lemah', color: 'bg-rose-500', text: 'text-rose-500' };
                if (points === 2 || points === 3) return { score: 60, label: 'Sedang', color: 'bg-amber-500', text: 'text-amber-500' };
                return { score: 100, label: 'Sangat Kuat', color: 'bg-emerald-500', text: 'text-emerald-500' };
            },

            handleSubmit(event) {
                event.preventDefault();

                this.errors.current_password = '';
                this.errors.password = '';
                this.errors.password_confirmation = '';
                let hasError = false;

                // 1. Validasi Input Kosong
                if (!this.current_password) {
                    this.errors.current_password = 'Kolom password saat ini wajib diisi!';
                    hasError = true;
                }
                if (!this.password) {
                    this.errors.password = 'Kolom password baru wajib diisi!';
                    hasError = true;
                } else if (this.password.length < 8) {
                    this.errors.password = 'Password baru minimal harus 8 karakter!';
                    hasError = true;
                }
                if (!this.password_confirmation) {
                    this.errors.password_confirmation = 'Kolom konfirmasi password wajib diisi!';
                    hasError = true;
                }

                // 2. Validasi Kesesuaian Password Baru & Konfirmasi
                if (this.password && this.password_confirmation && this.password !== this.password_confirmation) {
                    this.errors.password_confirmation = 'Konfirmasi password baru tidak cocok!';
                    hasError = true;
                }

                if (hasError) {
                    if (typeof AppAlert !== 'undefined') {
                        AppAlert.fire('error', 'PROSES GAGAL', 'Harap periksa kembali kesesuaian data form input Anda.');
                    }
                    return;
                }

                // 3. Pengiriman Data Menggunakan Fetch API
                let formData = new FormData(this.$el);
                let csrfToken = document.querySelector('meta[name=\'csrf-token\']') 
                                ? document.querySelector('meta[name=\'csrf-token\']').getAttribute('content') 
                                : '{{ csrf_token() }}';

                fetch(this.$el.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        if (typeof AppAlert !== 'undefined') {
                            AppAlert.fire('success', 'BERHASIL', 'Kata sandi Anda berhasil diperbarui.');
                        }
                        this.current_password = '';
                        this.password = '';
                        this.password_confirmation = '';
                    } else {
                        return response.json().then(errData => {
                            if (errData.errors) {
                                if (errData.errors.current_password) {
                                    let msgCurrent = errData.errors.current_password[0];
                                    if (msgCurrent.toLowerCase().includes('incorrect')) {
                                        this.errors.current_password = 'Password saat ini salah!';
                                    } else {
                                        this.errors.current_password = msgCurrent + '!';
                                    }
                                }
                                if (errData.errors.password) {
                                    this.errors.password = errData.errors.password[0] + '!';
                                }
                                if (errData.errors.password_confirmation) {
                                    this.errors.password_confirmation = errData.errors.password_confirmation[0] + '!';
                                }
                            }
                            if (typeof AppAlert !== 'undefined') {
                                AppAlert.fire('error', 'PROSES GAGAL', 'Harap periksa kembali kesesuaian data form input Anda.');
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }"
        @submit="handleSubmit"
        method="post" 
        action="{{ route('password.update') }}" 
        class="mt-6 space-y-6"
        novalidate
    >
        @csrf
        @method('put')

        {{-- Input Password Saat Ini --}}
        <div>
            <x-input-label for="update_password_current_password" :value="__('Password Saat Ini')" />
            
            <div class="relative mt-1" :class="errors.current_password ? '[&_input]:!border-rose-500 [&_input]:!focus:border-rose-500 [&_input]:!focus:ring-rose-500/20' : ''">
                <x-text-input 
                    id="update_password_current_password" 
                    name="current_password" 
                    ::type="hideCurrent ? 'password' : 'text'" 
                    class="block w-full pr-10 transition-colors duration-200" 
                    x-model="current_password"
                    autocomplete="current-password" 
                />
                <button 
                    type="button" 
                    @click="hideCurrent = !hideCurrent" 
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none"
                >
                    <i class="fa-solid" :class="hideCurrent ? 'fa-eye' : 'fa-eye-slash'"></i>
                </button>
            </div>
            
            <div x-show="errors.current_password" style="color: #f43f5e !important;" class="mt-2 text-[11px] font-bold uppercase tracking-wider flex items-center gap-1.5 animate__animated animate__fadeInUp animate__faster">
                <i class="fa-solid fa-circle-exclamation text-xs"></i>
                <span x-text="errors.current_password"></span>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        {{-- Input Password Baru --}}
        <div>
            <x-input-label for="update_password_password" :value="__('Password Baru')" />
            
            <div class="relative mt-1" :class="errors.password ? '[&_input]:!border-rose-500 [&_input]:!focus:border-rose-500 [&_input]:!focus:ring-rose-500/20' : ''">
                <x-text-input 
                    id="update_password_password" 
                    name="password" 
                    ::type="hideNew ? 'password' : 'text'" 
                    class="block w-full pr-10 transition-colors duration-200" 
                    x-model="password"
                    autocomplete="new-password" 
                />
                <button 
                    type="button" 
                    @click="hideNew = !hideNew" 
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none"
                >
                    <i class="fa-solid" :class="hideNew ? 'fa-eye' : 'fa-eye-slash'"></i>
                </button>
            </div>

            {{-- INDIKATOR KEKUATAN PASSWORD BARU --}}
            <div class="mt-2.5" x-show="password">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Kekuatan Keamanan:</span>
                    <span class="text-[11px] font-extrabold uppercase tracking-wide transition-colors" :class="passwordStrength.text" x-text="passwordStrength.label"></span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                    <div class="h-full transition-all duration-500 rounded-full" :class="passwordStrength.color" :style="`width: ${passwordStrength.score}%`"></div>
                </div>
            </div>
            
            <div x-show="errors.password" style="color: #f43f5e !important;" class="mt-2 text-[11px] font-bold uppercase tracking-wider flex items-center gap-1.5 animate__animated animate__fadeInUp animate__faster">
                <i class="fa-solid fa-circle-exclamation text-xs"></i>
                <span x-text="errors.password"></span>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        {{-- Input Konfirmasi Password --}}
        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Password')" />
            
            <div class="relative mt-1" :class="errors.password_confirmation ? '[&_input]:!border-rose-500 [&_input]:!focus:border-rose-500 [&_input]:!focus:ring-rose-500/20' : ''">
                <x-text-input 
                    id="update_password_password_confirmation" 
                    name="password_confirmation" 
                    ::type="hideConfirm ? 'password' : 'text'" 
                    class="block w-full pr-10 transition-colors duration-200" 
                    x-model="password_confirmation"
                    autocomplete="new-password" 
                />
                <button 
                    type="button" 
                    @click="hideConfirm = !hideConfirm" 
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none"
                >
                    <i class="fa-solid" :class="hideConfirm ? 'fa-eye' : 'fa-eye-slash'"></i>
                </button>
            </div>

            {{-- INDIKATOR KESESUAIAN KONFIRMASI PASSWORD --}}
            <div class="mt-2 flex items-center gap-1.5" x-show="password && password_confirmation">
                <template x-if="password === password_confirmation">
                    <span class="text-[10px] text-emerald-500 font-bold uppercase tracking-wide flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i> Password Cocok
                    </span>
                </template>
                <template x-if="password !== password_confirmation">
                    <span class="text-[10px] text-rose-500 font-bold uppercase tracking-wide flex items-center gap-1">
                        <i class="fa-solid fa-circle-xmark"></i> Password Belum Sesuai
                    </span>
                </template>
            </div>
            
            <div x-show="errors.password_confirmation" style="color: #f43f5e !important;" class="mt-2 text-[11px] font-bold uppercase tracking-wider flex items-center gap-1.5 animate__animated animate__fadeInUp animate__faster">
                <i class="fa-solid fa-circle-exclamation text-xs"></i>
                <span x-text="errors.password_confirmation"></span>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan Kata Sandi') }}</x-primary-button>
        </div>
    </form>
</section>