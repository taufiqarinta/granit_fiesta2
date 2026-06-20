<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Tambah Master Paket') }}
        </h2>
    </x-slot>

    <style>
        body::after {
            content: "";
            position: fixed;
            right: 20px;
            bottom: 20px;
            width: 240px;
            height: 240px;
            background-image: url('{{ asset('corner.png') }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.1;
            pointer-events: none;
            z-index: 5;
        }
        
        .max-w-7xl {
            position: relative;
            z-index: 10;
        }
        
        table {
            background: white;
            position: relative;
            z-index: 15;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('mastertarget.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="target" class="block text-gray-700 text-sm font-bold mb-2">Paket:</label>
                            <input type="text" name="target" id="target" value="{{ old('target') }}" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('target') border-red-500 @enderror"
                                   placeholder="Masukkan nama paket">
                            @error('target')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="point" class="block text-gray-700 text-sm font-bold mb-2">Point:</label>
                            <input type="number" name="point" id="point" value="{{ old('point') }}" min="0"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('point') border-red-500 @enderror"
                                   placeholder="Masukkan point">
                            @error('point')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="kupon" class="block text-gray-700 text-sm font-bold mb-2">Kupon:</label>
                            <input type="number" name="kupon" id="kupon" value="{{ old('kupon') }}" min="0"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('kupon') border-red-500 @enderror"
                                   placeholder="Masukkan kupon">
                            @error('kupon')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="periode_awal" class="block text-gray-700 text-sm font-bold mb-2">Periode Awal:</label>
                                <input type="date" name="periode_awal" id="periode_awal" value="{{ old('periode_awal') }}"
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('periode_awal') border-red-500 @enderror">
                                @error('periode_awal')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="periode_akhir" class="block text-gray-700 text-sm font-bold mb-2">Periode Akhir:</label>
                                <input type="date" name="periode_akhir" id="periode_akhir" value="{{ old('periode_akhir') }}"
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('periode_akhir') border-red-500 @enderror">
                                @error('periode_akhir')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('mastertarget.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Kembali
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>