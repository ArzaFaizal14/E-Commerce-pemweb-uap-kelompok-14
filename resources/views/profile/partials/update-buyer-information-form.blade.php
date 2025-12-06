<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informasi Pembeli') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Informasi tambahan untuk profil pembeli Anda.') }}
        </p>
    </header>

    @if($user->buyer)
        <form method="post" action="{{ route('buyer.profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
            @csrf
            @method('patch')

            <!-- Current Profile Picture -->
            <div>
                <x-input-label for="current_profile_picture" :value="__('Foto Profil Saat Ini')" />
                <div class="mt-2 flex items-center gap-4">
                    @if($user->buyer->profile_picture)
                        <img 
                            src="{{ asset('storage/' . $user->buyer->profile_picture) }}" 
                            alt="Profile Picture"
                            class="h-24 w-24 rounded-full object-cover border-2 border-gray-300"
                        >
                        <div>
                            <p class="text-sm text-gray-600 mb-2">{{ __('Hapus foto profil saat ini') }}</p>
                            <form method="POST" action="{{ route('buyer.profile.picture.delete') }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <x-danger-button type="submit" onclick="return confirm('Yakin ingin menghapus foto profil?')">
                                    {{ __('Hapus Foto') }}
                                </x-danger-button>
                            </form>
                        </div>
                    @else
                        <div class="h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-300">
                            <span class="text-xs text-gray-500 text-center px-2">Belum ada foto profil</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upload New Profile Picture -->
            <div>
                <x-input-label for="profile_picture" :value="__('Upload Foto Profil Baru (Opsional)')" />
                <input 
                    id="profile_picture" 
                    name="profile_picture" 
                    type="file" 
                    accept="image/*"
                    class="mt-1 block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-indigo-50 file:text-indigo-700
                        hover:file:bg-indigo-100"
                />
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Format: JPG, PNG. Maksimal 2MB. Kosongkan jika tidak ingin mengubah foto.') }}
                </p>
                <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
            </div>

            <!-- Phone Number -->
            <div>
                <x-input-label for="phone_number" :value="__('Nomor Telepon')" />
                <x-text-input 
                    id="phone_number" 
                    name="phone_number" 
                    type="text" 
                    class="mt-1 block w-full" 
                    :value="old('phone_number', $user->buyer->phone_number)" 
                    required 
                />
                <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Simpan') }}</x-primary-button>

                @if (session('status') === 'buyer-profile-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-gray-600"
                    >{{ __('Tersimpan.') }}</p>
                @endif

                @if (session('status') === 'profile-picture-deleted')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-gray-600"
                    >{{ __('Foto profil dihapus.') }}</p>
                @endif
            </div>
        </form>
    @else
        <div class="mt-6">
            <div class="rounded-md bg-yellow-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            {{ __('Profil Pembeli Belum Lengkap') }}
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>{{ __('Anda belum melengkapi profil pembeli. Silakan lengkapi profil untuk dapat berbelanja.') }}</p>
                        </div>
                        <div class="mt-4">
                            <a 
                                href="{{ route('buyer.profile.create') }}" 
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                {{ __('Lengkapi Profil Pembeli') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>