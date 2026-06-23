<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-slate-400">
            {{ __('Perbarui informasi profil akun dan alamat email Anda.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form 
    x-data="{
        username: '{{ old('username', Auth::user()->username ?? '') }}',
        email: '{{ old('email', Auth::user()->email ?? '') }}',
        initialUsername: '{{ Auth::user()->username ?? '' }}',
        initialEmail: '{{ Auth::user()->email ?? '' }}',
        errors: { username: '', email: '' },
        
        handleSubmit(event) {
            event.preventDefault();
            
            this.errors.username = '';
            this.errors.email = '';
            let hasError = false;

            // Validasi Client-Side
            if (!this.username.trim()) {
                this.errors.username = 'Kolom username wajib diisi!';
                hasError = true;
            }
            if (!this.email.trim()) {
                this.errors.email = 'Kolom email wajib diisi!';
                hasError = true;
            }

            if (hasError) {
                if (typeof AppAlert !== 'undefined') {
                    AppAlert.fire('error', 'PROSES GAGAL', 'Harap periksa kembali kesesuaian data form input Anda.');
                }
                return;
            }

            if (this.username === this.initialUsername && this.email === this.initialEmail) {
                if (typeof AppAlert !== 'undefined') {
                    AppAlert.fire('error', 'TIDAK ADA PERUBAHAN', 'Anda belum melakukan perubahan apapun pada data profil.');
                }
                return;
            }

            let formData = new FormData(this.$el);
            let csrfToken = document.querySelector('meta[name=\x22csrf-token\x22]') 
                            ? document.querySelector('meta[name=\x22csrf-token\x22]').getAttribute('content') 
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
                // JIKA VALIDASI GAGAL (422)
                if (response.status === 422) {
                    return response.json().then(errData => {
                        if (errData.errors) {
                            if (errData.errors.username) this.errors.username = errData.errors.username[0] + '!';
                            if (errData.errors.email) this.errors.email = errData.errors.email[0] + '!';
                        }
                        // Tampilkan alert bahwa ada input yang salah tanpa refresh halaman
                        if (typeof AppAlert !== 'undefined') {
                            AppAlert.fire('error', 'VALIDASI GAGAL', 'Beberapa kolom data tidak valid atau sudah digunakan.');
                        }
                    });
                }
                
                // JIKA BERHASIL (200)
                if (response.ok) {
                    if (typeof AppAlert !== 'undefined') {
                        AppAlert.fire('success', 'BERHASIL', 'Perubahan konfigurasi akun berhasil disimpan.');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        window.location.reload();
                    }
                    return;
                }

                // JIKA ERROR SISTEM LAIN (500, dsb)
                if (typeof AppAlert !== 'undefined') {
                    AppAlert.fire('error', 'SYSTEM ERROR', 'Terjadi kesalahan internal pada sistem.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof AppAlert !== 'undefined') {
                    AppAlert.fire('error', 'PROSES GAGAL', 'Koneksi ke server terputus.');
                }
            });
        }
    }"
    @submit="handleSubmit"
    method="post" 
    action="{{ route('profile.update') }}" 
    class="mt-6 space-y-6" 
    novalidate
>
    @csrf
    @method('patch')

    {{-- Input Username --}}
    <div>
        <x-input-label for="username" :value="__('Username')" />
        
        <div :class="errors.username ? '[&_input]:!border-rose-500 [&_input]:!focus:border-rose-500 [&_input]:!focus:ring-rose-500/20' : ''">
            <x-text-input 
                id="username" 
                name="username" 
                type="text" 
                class="mt-1 block w-full transition-colors duration-200" 
                x-model="username"
                autofocus 
                autocomplete="username" 
            />
        </div>
        
        <div x-show="errors.username" style="color: #f43f5e !important;" class="mt-2 text-[11px] font-bold uppercase tracking-wider flex items-center gap-1.5 animate__animated animate__fadeInUp animate__faster">
            <i class="fa-solid fa-circle-exclamation text-xs"></i>
            <span x-text="errors.username"></span>
        </div>
        <x-input-error class="mt-2" :messages="$errors->get('username')" />
    </div>

    {{-- Input Alamat Email --}}
    <div>
        <x-input-label for="email" :value="__('Alamat Email')" />
        
        <div :class="errors.email ? '[&_input]:!border-rose-500 [&_input]:!focus:border-rose-500 [&_input]:!focus:ring-rose-500/20' : ''">
            <x-text-input 
                id="email" 
                name="email" 
                type="email" 
                class="mt-1 block w-full transition-colors duration-200" 
                x-model="email"
                autocomplete="username" 
            />
        </div>
        
        <div x-show="errors.email" style="color: #f43f5e !important;" class="mt-2 text-[11px] font-bold uppercase tracking-wider flex items-center gap-1.5 animate__animated animate__fadeInUp animate__faster">
            <i class="fa-solid fa-circle-exclamation text-xs"></i>
            <span x-text="errors.email"></span>
        </div>
        <x-input-error class="mt-2" :messages="$errors->get('email')" />

        @if (Auth::user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! Auth::user()->hasVerifiedEmail())
            <div>
                <p class="text-sm mt-2 text-gray-800 dark:text-slate-300">
                    {{ __('Alamat email Anda belum terverifikasi.') }}

                    <button form="send-verification" class="underline text-sm text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                        {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                    </p>
                @endif
            </div>
        @endif
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
    </div>
</form>
</section>