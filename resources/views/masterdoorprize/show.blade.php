<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Detail Master Doorprize') }}
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Doorprize:</label>
                            <p class="text-gray-900">{{ $masterDoorprize->nama_doorprize }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Jumlah Doorprize:</label>
                            <p class="text-gray-900">{{ $masterDoorprize->jumlah_doorprize }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Gambar Doorprize:</label>
                            @if($masterDoorprize->nama_file)
                                <div class="mt-2">
                                    <img src="{{ asset('images/doorprizes/' . $masterDoorprize->nama_file) }}" 
                                         alt="{{ $masterDoorprize->nama_doorprize }}" 
                                         class="w-48 h-48 object-cover rounded-lg border border-gray-200">
                                </div>
                            @else
                                <p class="text-gray-500 italic">Tidak ada gambar</p>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Dibuat:</label>
                            <p class="text-gray-900">{{ $masterDoorprize->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Diupdate:</label>
                            <p class="text-gray-900">{{ $masterDoorprize->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        <a href="{{ route('masterdoorprize.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Kembali
                        </a>
                        <div class="space-x-2">
                            <a href="{{ route('masterdoorprize.edit', $masterDoorprize) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>