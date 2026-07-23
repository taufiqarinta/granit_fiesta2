<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-black">
            {{ __('Link Konfirmasi Kehadiran') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($links->isEmpty())
                        <p class="text-gray-500">Tidak ada lokasi event aktif.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">#</th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Lokasi Event</th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Link</th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($links as $i => $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $item['lokasi']->nama_lokasi }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ $item['lokasi']->tanggal ? \Carbon\Carbon::parse($item['lokasi']->tanggal)->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" class="w-full px-2 py-1 text-sm border border-gray-300 rounded read-only:bg-gray-100"
                                                value="{{ $item['url'] }}" readonly
                                                id="link-{{ $i }}">
                                        </td>
                                        <td class="px-4 py-3">
                                            <button onclick="copyLink({{ $i }})"
                                                class="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                Copy
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyLink(index) {
            const input = document.getElementById('link-' + index);
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value)
                .then(() => {
                    const btn = input.nextElementSibling;
                    const origText = btn.textContent;
                    btn.textContent = 'Copied!';
                    btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    btn.classList.add('bg-green-600', 'hover:bg-green-700');
                    setTimeout(() => {
                        btn.textContent = origText;
                        btn.classList.remove('bg-green-600', 'hover:bg-green-700');
                        btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    }, 2000);
                });
        }
    </script>
</x-app-layout>
