<x-app-layout>
    <x-slot name="header">
        <h2>Permintaan Terkirim</h2>
    </x-slot>

    <div class="p-6">
        <p>{{ $success }}</p>
        <p>Membuka WhatsApp...</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @foreach($waLinks as $link)
                window.open('{{ $link }}', '_blank');
            @endforeach

            // Redirect ke halaman list permintaan kalau mau
            setTimeout(() => {
                window.location.href = "{{ route('permintaan') }}";
            }, 2000);
        });
    </script>
</x-app-layout>
