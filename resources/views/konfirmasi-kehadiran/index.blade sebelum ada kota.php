<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Konfirmasi Kehadiran - The Next Dimension of Granite 2026</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --brand-start: #950000;
            --brand-end:   #fa0000;
            --bg:      #fff5f5;
            --surface: #ffffff;
            --panel:   #fff7f7;
            --border:  #fde2e2;
            --text:    #1e293b;
            --muted:   #6b7280;
            --success: #16a34a;
            --success-lt: #f0fdf4;
            --danger:  #ef4444;
            --danger-lt: #fef2f2;
            --radius: 14px;
            --shadow: 0 8px 32px rgba(149,0,0,.10);
        }

        body {
            background: var(--bg);
            font-family: 'Sora', sans-serif;
            color: var(--text);
            min-height: 100vh;
        }

        .page { padding: 1.5rem 1rem 3rem; }

        .card {
            background: var(--surface);
            border-radius: 20px;
            box-shadow: var(--shadow);
            max-width: 620px;
            margin: 0 auto;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, var(--brand-start), var(--brand-end));
            color: #fff;
            padding: 1.75rem 1.5rem;
            text-align: center;
        }
        .card-header h1 {
            font-size: 1.15rem;
            font-weight: 800;
            line-height: 1.4;
        }
        .card-header p {
            font-size: .8rem;
            opacity: .9;
            margin-top: .35rem;
            font-weight: 500;
        }

        .card-body { padding: 1.75rem 1.5rem; }

        @media (max-width: 600px) {
            .page { padding: .75rem .5rem 2rem; }
            .card { border-radius: 14px; }
            .card-body { padding: 1.25rem 1rem; }
            .card-header { padding: 1.25rem 1rem; }
        }

        /* Alert */
        .alert {
            display: none;
            align-items: center;
            gap: .6rem;
            border-radius: 10px;
            padding: .85rem 1rem;
            margin-bottom: 1.25rem;
            font-size: .875rem;
            font-weight: 500;
        }
        .alert.show { display: flex; }
        .alert svg { flex-shrink: 0; width: 18px; height: 18px; }
        .alert-error { background: var(--danger-lt); border: 1.5px solid #fca5a5; color: #991b1b; }

        /* Fields */
        .field { margin-bottom: 1.1rem; position: relative; }
        .label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: var(--muted);
            margin-bottom: .4rem;
            letter-spacing: .03em;
        }
        .label .req { color: var(--danger); margin-left: 2px; }

        .input, select.input {
            width: 100%;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: .6rem .85rem;
            font-size: 16px;
            font-family: 'Sora', sans-serif;
            color: var(--text);
            background: #fff;
            outline: none;
            transition: border-color .18s, box-shadow .18s;
        }
        .input:focus { border-color: var(--brand-end); box-shadow: 0 0 0 3px rgba(250,0,0,.10); }
        .input:disabled { background: var(--panel); color: var(--muted); cursor: not-allowed; }

        textarea.input { resize: vertical; min-height: 70px; }

        /* Autocomplete (ketik langsung di input) */
        .autocomplete-wrap { position: relative; }
        .autocomplete-input { padding-right: 2.2rem; }
        .autocomplete-spinner {
            display: none;
            position: absolute;
            right: .75rem;
            top: 38px;
            width: 16px; height: 16px;
            border: 2px solid var(--border);
            border-top-color: var(--brand-end);
            border-radius: 50%;
            animation: ac-spin .6s linear infinite;
        }
        .autocomplete-spinner.show { display: block; }
        @keyframes ac-spin { to { transform: rotate(360deg); } }

        .autocomplete-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0; right: 0;
            margin-top: 4px;
            background: #fff;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            max-height: 230px;
            overflow-y: auto;
            box-shadow: 0 10px 28px rgba(149,0,0,.14);
            z-index: 50;
        }
        .autocomplete-dropdown.show { display: block; }
        .autocomplete-item {
            padding: .65rem .85rem;
            font-size: .88rem;
            cursor: pointer;
        }
        .autocomplete-item:hover,
        .autocomplete-item.active { background: var(--panel); }
        .autocomplete-item + .autocomplete-item { border-top: 1px solid var(--border); }
        .autocomplete-empty {
            padding: .7rem .85rem;
            font-size: .85rem;
            color: var(--muted);
        }

        /* Checkbox konfirmasi */
        .confirm-box {
            background: var(--panel);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1.5rem;
        }
        .confirm-check {
            display: flex;
            align-items: flex-start;
            gap: .65rem;
            cursor: pointer;
        }
        .confirm-check input[type="checkbox"] {
            width: 22px; height: 22px;
            accent-color: var(--brand-end);
            flex-shrink: 0;
            margin-top: 1px;
            cursor: pointer;
        }
        .confirm-check span {
            font-size: .9rem;
            font-weight: 600;
        }

        .helper-text {
            font-size: .72rem;
            color: var(--muted);
            margin-top: .35rem;
        }

        .footer-note {
            text-align: center;
            font-size: .72rem;
            color: var(--muted);
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>

<div class="page">
    <div class="card">
        <div class="card-header">
            <h1>Konfirmasi Kehadiran</h1>
            <p>The Next Dimension of Granite 2026</p>
        </div>

        <div class="card-body">

            <div id="alertError" class="alert alert-error">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span id="alertErrorText">Terjadi kesalahan</span>
            </div>

            <form id="konfirmasiForm">
                @csrf

                <div class="field">
                    <label class="label" for="lokasiEvent">Lokasi Event <span class="req">*</span></label>
                    <select id="lokasiEvent" name="lokasi_event" class="input" required>
                        <option value="">-- Pilih Lokasi Event --</option>
                        @foreach($lokasiEvents as $lokasi)
                            <option value="{{ $lokasi->nama_lokasi }}">{{ $lokasi->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field autocomplete-wrap">
                    <label class="label" for="namaTokoInput">Nama Toko <span class="req">*</span></label>
                    <input type="text" id="namaTokoInput" class="input autocomplete-input" placeholder="Pilih lokasi event dahulu..." autocomplete="off" disabled required>
                    <div id="tokoSpinner" class="autocomplete-spinner"></div>
                    <input type="hidden" id="namaToko" name="kode_toko">
                    <div id="tokoDropdown" class="autocomplete-dropdown"></div>
                    <p class="helper-text">Pilih lokasi event terlebih dahulu, lalu ketik nama toko Anda</p>
                </div>

                <div class="field">
                    <label class="label" for="alamat">Alamat</label>
                    <textarea id="alamat" class="input" rows="3" readonly placeholder="Alamat akan terisi otomatis setelah memilih toko"></textarea>
                </div>

                <div class="field">
                    <label class="label" for="pic">PIC <span class="req">*</span></label>
                    <input type="text" id="pic" name="pic" class="input" placeholder="Nama PIC yang hadir" autocomplete="off" required>
                </div>

                <div class="confirm-box">
                    <label class="confirm-check">
                        <input type="checkbox" id="konfirmasiCheckbox">
                        <span>Konfirmasi Kehadiran</span>
                    </label>
                </div>

            </form>

            <div id="successNote" style="display:none; margin-top:1rem; padding:.85rem 1rem; background:var(--success-lt); border:1.5px solid #86efac; border-radius:10px; font-size:.85rem; color:#166534;">
                ✓ Kehadiran Anda sudah tercatat. Terima kasih!
            </div>

            <button type="button" id="btnKonfirmasiLagi" class="input" style="display:none; margin-top:1rem; background:#fff; border:1.5px solid var(--brand-end); color:var(--brand-end); font-weight:700; cursor:pointer;">
                + Konfirmasi Toko Lain
            </button>

            <p class="footer-note">Centang kotak di atas untuk mengonfirmasi kehadiran Anda pada event ini</p>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let submitting = false;

    /* ══════════════════════════════════════
       AUTOCOMPLETE TOKO (ketik langsung di input)
    ══════════════════════════════════════ */
    const $tokoInput    = $('#namaTokoInput');
    const $tokoHidden    = $('#namaToko');
    const $tokoDropdown = $('#tokoDropdown');
    const $tokoSpinner  = $('#tokoSpinner');

    let tokoDebounce = null;
    let tokoRequestSeq = 0;
    let activeIndex = -1;

    function debounce(fn, delay) {
        clearTimeout(tokoDebounce);
        tokoDebounce = setTimeout(fn, delay);
    }

    function fetchToko(term) {
        const lokasi = $('#lokasiEvent').val();
        if (!lokasi) return;

        const seq = ++tokoRequestSeq;
        $tokoSpinner.addClass('show');

        fetch('{{ url("/api/konfirmasi-kehadiran/toko") }}?q=' + encodeURIComponent(term || '') + '&lokasi_event=' + encodeURIComponent(lokasi))
            .then(r => r.json())
            .then(data => {
                if (seq !== tokoRequestSeq) return; // hasil basi, abaikan
                $tokoSpinner.removeClass('show');
                renderTokoDropdown(data.results || []);
            })
            .catch(() => {
                if (seq !== tokoRequestSeq) return;
                $tokoSpinner.removeClass('show');
                renderTokoDropdown([]);
            });
    }

    function renderTokoDropdown(results) {
        activeIndex = -1;
        $tokoDropdown.empty();

        if (!results.length) {
            $tokoDropdown.append('<div class="autocomplete-empty">Toko tidak ditemukan</div>');
        } else {
            results.forEach(function (item) {
                const $item = $('<div class="autocomplete-item"></div>')
                    .text(item.text)
                    .attr('data-id', item.id)
                    .attr('data-text', item.text);
                $tokoDropdown.append($item);
            });
        }

        $tokoDropdown.addClass('show');
    }

    function closeTokoDropdown() {
        $tokoDropdown.removeClass('show').empty();
        activeIndex = -1;
    }

    function selectToko(id, text) {
        $tokoHidden.val(id);
        $tokoInput.val(text).data('selected-text', text);
        closeTokoDropdown();
        loadAlamat(id);
    }

    // fokus pada input -> munculkan pilihan (minimum ketik 0, sama seperti sebelumnya)
    $tokoInput.on('focus', function () {
        if ($tokoInput.prop('disabled')) return;
        fetchToko($tokoInput.val().trim() === ($tokoInput.data('selected-text') || '') ? '' : $tokoInput.val().trim());
    });

    // ketik langsung di input -> cari & lepaskan toko yang sudah dipilih
    $tokoInput.on('input', function () {
        $tokoHidden.val('');
        $('#alamat').val('');
        resetCheckbox();

        const term = $tokoInput.val().trim();
        debounce(function () { fetchToko(term); }, 300);
    });

    // navigasi keyboard
    $tokoInput.on('keydown', function (e) {
        const $items = $tokoDropdown.find('.autocomplete-item');
        if (!$items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = Math.min(activeIndex + 1, $items.length - 1);
            $items.removeClass('active').eq(activeIndex).addClass('active');
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = Math.max(activeIndex - 1, 0);
            $items.removeClass('active').eq(activeIndex).addClass('active');
        } else if (e.key === 'Enter') {
            e.preventDefault();
            const $target = activeIndex >= 0 ? $items.eq(activeIndex) : $items.first();
            if ($target.length && $target.attr('data-id')) {
                selectToko($target.attr('data-id'), $target.attr('data-text'));
            }
        } else if (e.key === 'Escape') {
            closeTokoDropdown();
        }
    });

    // klik salah satu hasil
    $tokoDropdown.on('click', '.autocomplete-item', function () {
        const id = $(this).attr('data-id');
        if (!id) return;
        selectToko(id, $(this).attr('data-text'));
    });

    // klik di luar -> tutup dropdown
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.autocomplete-wrap').length) {
            closeTokoDropdown();
        }
    });

    function loadAlamat(kodeToko) {
        const lokasi = $('#lokasiEvent').val();
        $('#alamat').val('Memuat alamat...');

        fetch('{{ url("/api/konfirmasi-kehadiran/toko") }}/' + encodeURIComponent(kodeToko) + '?lokasi_event=' + encodeURIComponent(lokasi))
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    $('#alamat').val(data.data.alamat);
                } else {
                    $('#alamat').val('');
                    showError(data.message || 'Gagal memuat alamat toko');
                }
            })
            .catch(() => {
                $('#alamat').val('');
                showError('Terjadi kesalahan saat memuat alamat');
            });
    }

    /* ══════════════════════════════════════
       LOKASI EVENT CHANGE -> reset toko
    ══════════════════════════════════════ */
    $('#lokasiEvent').on('change', function () {
        const lokasi = $(this).val();

        $tokoHidden.val('');
        $tokoInput.val('').data('selected-text', '');
        closeTokoDropdown();
        $('#alamat').val('');
        $('#pic').val('');
        resetCheckbox();

        if (lokasi) {
            $tokoInput.prop('disabled', false).attr('placeholder', 'Ketik nama toko...');
        } else {
            $tokoInput.prop('disabled', true).attr('placeholder', 'Pilih lokasi event dahulu...');
        }
    });

    /* ══════════════════════════════════════
       AUTO UPPERCASE PIC
    ══════════════════════════════════════ */
    $('#pic').on('input', function () {
        this.value = this.value.toUpperCase();
    });

    /* ══════════════════════════════════════
       CHECKBOX KONFIRMASI
    ══════════════════════════════════════ */
    function resetCheckbox() {
        $('#konfirmasiCheckbox').prop('checked', false);
    }

    $('#konfirmasiCheckbox').on('change', function () {
        const checkbox = this;

        if (!checkbox.checked) return; // uncheck manual, biarkan saja

        const lokasiEvent = $('#lokasiEvent').val();
        const kodeToko = $tokoHidden.val();
        const pic = $('#pic').val().trim();

        if (!lokasiEvent || !kodeToko || !pic) {
            checkbox.checked = false;
            showError('Mohon lengkapi Lokasi Event, Nama Toko, dan PIC terlebih dahulu.');
            return;
        }

        if (submitting) {
            checkbox.checked = false;
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Kehadiran',
            text: 'Apakah anda yakin untuk hadir pada event The Next Dimension of Granite 2026 pada lokasi ' + lokasiEvent + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak',
            confirmButtonColor: '#950000',
            cancelButtonColor: '#9ca3af',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                submitKonfirmasi(lokasiEvent, kodeToko, pic);
            } else {
                checkbox.checked = false;
            }
        });
    });

    /* ══════════════════════════════════════
       SUBMIT
    ══════════════════════════════════════ */
    function submitKonfirmasi(lokasiEvent, kodeToko, pic) {
        submitting = true;

        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        fetch('{{ route("konfirmasi-kehadiran.submit") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                lokasi_event: lokasiEvent,
                kode_toko: kodeToko,
                pic: pic
            })
        })
        .then(r => r.json())
        .then(data => {
            submitting = false;

            if (data.success) {
                Swal.fire({
                    title: 'Terima Kasih!',
                    text: 'Terima kasih telah mengkonfirmasi kehadiran Anda, sampai berjumpa di event.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#950000',
                    allowOutsideClick: false
                }).then(() => {
                    lockFormReadonly();
                });
            } else {
                $('#konfirmasiCheckbox').prop('checked', false);
                showError(data.message || 'Terjadi kesalahan saat menyimpan data');
            }
        })
        .catch(() => {
            submitting = false;
            $('#konfirmasiCheckbox').prop('checked', false);
            showError('Terjadi kesalahan saat mengirim data');
        });
    }

    /* ══════════════════════════════════════
       KUNCI FORM SETELAH SUKSES (readonly, data tetap tampil)
    ══════════════════════════════════════ */
    function lockFormReadonly() {
        $('#lokasiEvent').prop('disabled', true);
        $tokoInput.prop('disabled', true);
        closeTokoDropdown();
        $('#alamat').prop('readonly', true);
        $('#pic').prop('readonly', true);
        $('#konfirmasiCheckbox').prop('disabled', true);

        $('#successNote').show();
        // $('#btnKonfirmasiLagi').show();

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /* ══════════════════════════════════════
       RESET FORM (manual, untuk konfirmasi toko lain)
    ══════════════════════════════════════ */
    function resetForm() {
        $('#lokasiEvent').prop('disabled', false).val('').trigger('change');
        $tokoHidden.val('');
        $tokoInput.prop('disabled', true).val('').attr('placeholder', 'Pilih lokasi event dahulu...');
        closeTokoDropdown();
        $('#alamat').prop('readonly', true).val('');
        $('#pic').prop('readonly', false).val('');
        $('#konfirmasiCheckbox').prop('disabled', false);
        resetCheckbox();

        $('#successNote').hide();
        $('#btnKonfirmasiLagi').hide();

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    $('#btnKonfirmasiLagi').on('click', function () {
        resetForm();
    });

    /* ══════════════════════════════════════
       HELPERS
    ══════════════════════════════════════ */
    function showError(msg) {
        $('#alertErrorText').text(msg);
        $('#alertError').addClass('show');
        setTimeout(() => $('#alertError').removeClass('show'), 5000);
    }

});
</script>
</body>
</html>