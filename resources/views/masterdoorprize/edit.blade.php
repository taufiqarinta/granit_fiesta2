<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Edit Master Doorprize') }}
        </h2>
    </x-slot>

    <script src="https://cdn.tailwindcss.com"></script>


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
                    <form action="{{ route('masterdoorprize.update', $masterDoorprize) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="nama_doorprize" class="block text-gray-700 text-sm font-bold mb-2">Nama Doorprize:</label>
                            <input type="text" name="nama_doorprize" id="nama_doorprize" value="{{ old('nama_doorprize', $masterDoorprize->nama_doorprize) }}" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nama_doorprize') border-red-500 @enderror">
                            @error('nama_doorprize')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="jumlah_doorprize" class="block text-gray-700 text-sm font-bold mb-2">Jumlah Doorprize:</label>
                            <input type="number" name="jumlah_doorprize" id="jumlah_doorprize" value="{{ old('jumlah_doorprize', $masterDoorprize->jumlah_doorprize) }}" min="0"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('jumlah_doorprize') border-red-500 @enderror">
                            @error('jumlah_doorprize')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="nama_file" class="block text-gray-700 text-sm font-bold mb-2">Gambar Doorprize:</label>
                            
                            @if($masterDoorprize->nama_file)
                                <div class="mb-2">
                                    <img src="{{ asset('images/doorprizes/' . $masterDoorprize->nama_file) }}" 
                                         alt="{{ $masterDoorprize->nama_doorprize }}" 
                                         class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                                    <p class="text-gray-500 text-xs mt-1">Gambar saat ini: {{ $masterDoorprize->nama_file }}</p>
                                </div>
                            @endif
                            
                            <input type="file" name="nama_file" id="nama_file" accept="image/*"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nama_file') border-red-500 @enderror">
                            <p class="text-gray-500 text-xs mt-1">Format: jpeg, png, jpg, gif, svg | Maks: 2MB</p>
                            <p class="text-gray-500 text-xs">Kosongkan jika tidak ingin mengganti gambar</p>
                            @error('nama_file')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('masterdoorprize.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Kembali
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>