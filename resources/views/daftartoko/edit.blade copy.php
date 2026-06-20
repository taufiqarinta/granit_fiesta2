<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Toko') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    
                    <form method="POST" action="{{ route('daftartoko.update', $daftartoko) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            
                            <!-- Kode Toko -->
                            <div>
                                <label for="kode_toko" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kode Toko <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="kode_toko" 
                                    id="kode_toko" 
                                    value="{{ old('kode_toko', $daftartoko->kode_toko) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('kode_toko') border-red-500 @enderror"
                                    required>
                                @error('kode_toko')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nama Toko -->
                            <div>
                                <label for="nama_toko" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Toko <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="nama_toko" 
                                    id="nama_toko" 
                                    value="{{ old('nama_toko', $daftartoko->nama_toko) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('nama_toko') border-red-500 @enderror"
                                    required>
                                @error('nama_toko')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Jenis Toko -->
                            <div>
                                <label for="jenis_toko" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis Toko <span class="text-red-500">*</span>
                                </label>
                                <select name="jenis_toko" 
                                    id="jenis_toko" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('jenis_toko') border-red-500 @enderror"
                                    required>
                                    <option value="">Pilih Jenis Toko</option>
                                    <option value="Stockist" {{ old('jenis_toko', $daftartoko->jenis_toko) == 'Stockist' ? 'selected' : '' }}>Stockist</option>
                                    <option value="Makelar" {{ old('jenis_toko', $daftartoko->jenis_toko) == 'Makelar' ? 'selected' : '' }}>Makelar</option>
                                </select>
                                @error('jenis_toko')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Alamat (Full Width) -->
                            <div class="md:col-span-2">
                                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                                    Alamat Lengkap <span class="text-red-500">*</span>
                                </label>
                                <textarea name="alamat" 
                                    id="alamat" 
                                    rows="3"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('alamat') border-red-500 @enderror"
                                    required>{{ old('alamat', $daftartoko->alamat) }}</textarea>
                                @error('alamat')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kelurahan -->
                            <div>
                                <label for="kelurahan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kelurahan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="kelurahan" 
                                    id="kelurahan" 
                                    value="{{ old('kelurahan', $daftartoko->kelurahan) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('kelurahan') border-red-500 @enderror"
                                    required>
                                @error('kelurahan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kecamatan -->
                            <div>
                                <label for="kecamatan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kecamatan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="kecamatan" 
                                    id="kecamatan" 
                                    value="{{ old('kecamatan', $daftartoko->kecamatan) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('kecamatan') border-red-500 @enderror"
                                    required>
                                @error('kecamatan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kota -->
                            <div>
                                <label for="kota" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kota <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="kota" 
                                    id="kota" 
                                    value="{{ old('kota', $daftartoko->kota) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('kota') border-red-500 @enderror"
                                    required>
                                @error('kota')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Provinsi -->
                            <div>
                                <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-2">
                                    Provinsi <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="provinsi" 
                                    id="provinsi" 
                                    value="{{ old('provinsi', $daftartoko->provinsi) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('provinsi') border-red-500 @enderror"
                                    required>
                                @error('provinsi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- PIC -->
                            <div>
                                <label for="pic" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama PIC <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="pic" 
                                    id="pic" 
                                    value="{{ old('pic', $daftartoko->pic) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('pic') border-red-500 @enderror"
                                    required>
                                @error('pic')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nomor PIC -->
                            <div>
                                <label for="nomor_pic" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor HP<span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    name="nomor_pic" 
                                    id="nomor_pic" 
                                    value="{{ old('nomor_pic', $daftartoko->nomor_pic) }}"
                                    placeholder="08xxxxxxxxxx"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('nomor_pic') border-red-500 @enderror"
                                    required>
                                @error('nomor_pic')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-6 border-t border-gray-200">
                            <button type="submit" 
                                class="flex-1 text-center px-3 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                                Update
                            </button>
                            <a href="{{ route('daftartoko.index') }}" 
                                class="w-full sm:w-auto px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-center">
                                Batal
                            </a>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>