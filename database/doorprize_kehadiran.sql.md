# SQL Doorprize Kehadiran

Jalankan query di bawah ini di **phpMyAdmin** (tab SQL) secara berurutan. Pastikan database sudah dipilih terlebih dahulu.

> Catatan: Tidak perlu `php artisan migrate`, cukup jalankan query ini sekali.

---

## 1. Tabel `doorprize_kehadiran` (master hadiah)

Sama persis struktur tabel `doorprizes`.

```sql
CREATE TABLE IF NOT EXISTS `doorprize_kehadiran` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_doorprize` varchar(255) NOT NULL,
  `nama_file` varchar(255) DEFAULT NULL,
  `batas_jam_kehadiran` time NOT NULL DEFAULT '18:00:00',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

---

## 1b. Migrasi tabel lama (sudah terlanjur dibuat tanpa kolom `batas_jam_kehadiran`)

Jalankan sekali saja jika tabel `doorprize_kehadiran` sudah ada:

```sql
ALTER TABLE `doorprize_kehadiran`
  ADD COLUMN `batas_jam_kehadiran` time NOT NULL DEFAULT '18:00:00';
```

---

## 2. Tabel `doorprize_kehadiran_lokasi` (jumlah hadiah per lokasi)

Sama persis struktur tabel `doorprize_lokasi`.

```sql
CREATE TABLE IF NOT EXISTS `doorprize_kehadiran_lokasi` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `doorprize_kehadiran_id` bigint(20) UNSIGNED NOT NULL,
  `lokasi_event` varchar(255) NOT NULL,
  `jumlah_doorprize` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `doorprize_kehadiran_lokasi_doorprize_kehadiran_id_index` (`doorprize_kehadiran_id`),
  CONSTRAINT `doorprize_kehadiran_lokasi_doorprize_kehadiran_id_foreign`
    FOREIGN KEY (`doorprize_kehadiran_id`) REFERENCES `doorprize_kehadiran` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

---

## 3. Tabel `doorprize_kehadiran_pemenang` (pemenang & penukaran)

Satu `kode_toko` hanya boleh menang satu kali per `lokasi_event` (dijaga lewat query undian, bukan constraint).

```sql
CREATE TABLE IF NOT EXISTS `doorprize_kehadiran_pemenang` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `doorprize_kehadiran_id` bigint(20) UNSIGNED NOT NULL,
  `kode_toko` varchar(255) NOT NULL,
  `nama_toko` varchar(255) NOT NULL,
  `nama_pic` varchar(255) DEFAULT NULL,
  `kota` varchar(255) DEFAULT NULL,
  `lokasi_event` varchar(255) NOT NULL,
  `hadiah` varchar(255) DEFAULT NULL,
  `sudah_ditukarkan` tinyint(1) NOT NULL DEFAULT 0,
  `ditukarkan_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `doorprize_kehadiran_pemenang_doorprize_kehadiran_id_index` (`doorprize_kehadiran_id`),
  KEY `doorprize_kehadiran_pemenang_lokasi_event_index` (`lokasi_event`),
  KEY `doorprize_kehadiran_pemenang_kode_toko_index` (`kode_toko`),
  CONSTRAINT `doorprize_kehadiran_pemenang_doorprize_kehadiran_id_foreign`
    FOREIGN KEY (`doorprize_kehadiran_id`) REFERENCES `doorprize_kehadiran` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

---

## Contoh data (opsional)

Untuk menguji halaman, bisa isi 1 hadiah contoh:

```sql
INSERT INTO `doorprize_kehadiran` (`nama_doorprize`, `nama_file`, `batas_jam_kehadiran`, `status`, `created_at`, `updated_at`)
VALUES ('Sepeda Motor Listrik', 'sepedamotorlistrik.jpeg', '18:00:00', 1, NOW(), NOW());

INSERT INTO `doorprize_kehadiran_lokasi` (`doorprize_kehadiran_id`, `lokasi_event`, `jumlah_doorprize`, `status`, `created_at`, `updated_at`)
VALUES (LAST_INSERT_ID(), 'SEMARANG', 3, 1, NOW(), NOW());
```

Ganti `SEMARANG` dengan nama lokasi event sesuai `master_lokasi_event`, dan sesuaikan nama file gambar dengan yang ada di folder `public/images/doorprizes/`.
