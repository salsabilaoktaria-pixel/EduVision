<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\Spp;
use App\Models\Prestasi;
use App\Models\Aspirasi;
use App\Models\BankSoal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seeder data dummy LENGKAP: 9 kelas (X, XI, XII masing-masing 3 jurusan),
     * 9 guru (tiap guru sekaligus wali kelas SATU kelas & pengampu SATU mapel,
     * merata 1:1 dengan jumlah kelas & mapel yang ada), 9 mata pelajaran, dan
     * 8 siswa di SETIAP kelas (72 siswa total, merata). Semua akun guru &
     * siswa login memakai email berbasis NAMA MASING-MASING
     * (nama.belakang@eduvision.test) — hanya admin yang tetap pakai
     * admin@eduvision.test. Password semua akun: "password".
     */

    /** @var array<string,bool> Dipakai untuk mencegah email bentrok kalau ada nama yang mirip/sama. */
    private array $emailTerpakai = [];

    public function run(): void
    {
        $this->emailTerpakai = [];

        // ===== Akun Admin (tetap, tidak berbasis nama) =====
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@eduvision.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // ===== 9 mapel + guru pengampunya — 1 guru = 1 mapel = sekaligus 1 wali kelas =====
        $dataMapelGuru = [
            ['kode' => 'MTK-01', 'mapel' => 'Matematika',       'guru' => 'Budi Santoso',     'nip' => '198501012010011001', 'jk' => 'Laki-laki'],
            ['kode' => 'BIN-01', 'mapel' => 'Bahasa Indonesia',  'guru' => 'Siti Nurhaliza',   'nip' => '198602022010012002', 'jk' => 'Perempuan'],
            ['kode' => 'BIG-01', 'mapel' => 'Bahasa Inggris',    'guru' => 'Agus Wijaya',      'nip' => '198703032010011003', 'jk' => 'Laki-laki'],
            ['kode' => 'IPA-01', 'mapel' => 'IPA',               'guru' => 'Dewi Lestari',     'nip' => '198804042010012004', 'jk' => 'Perempuan'],
            ['kode' => 'IPS-01', 'mapel' => 'IPS',               'guru' => 'Rudi Hartono',     'nip' => '198905052010011005', 'jk' => 'Laki-laki'],
            ['kode' => 'PKN-01', 'mapel' => 'PPKn',              'guru' => 'Fitriani Rahmawati', 'nip' => '199006062010012006', 'jk' => 'Perempuan'],
            ['kode' => 'PJK-01', 'mapel' => 'Penjaskes',         'guru' => 'Hendra Gunawan',   'nip' => '199107072010011007', 'jk' => 'Laki-laki'],
            ['kode' => 'SBD-01', 'mapel' => 'Seni Budaya',       'guru' => 'Maya Sari',        'nip' => '199208082010012008', 'jk' => 'Perempuan'],
            ['kode' => 'PKY-01', 'mapel' => 'Prakarya',          'guru' => 'Joko Susilo',      'nip' => '199309092010011009', 'jk' => 'Laki-laki'],
        ];

        // ===== 9 kelas: X/XI/XII, masing-masing jurusan IPA 1, IPA 2, IPS 1 =====
        $namaKelasList = [
            ['nama' => 'X IPA 1', 'tingkat' => 'X'], ['nama' => 'X IPA 2', 'tingkat' => 'X'], ['nama' => 'X IPS 1', 'tingkat' => 'X'],
            ['nama' => 'XI IPA 1', 'tingkat' => 'XI'], ['nama' => 'XI IPA 2', 'tingkat' => 'XI'], ['nama' => 'XI IPS 1', 'tingkat' => 'XI'],
            ['nama' => 'XII IPA 1', 'tingkat' => 'XII'], ['nama' => 'XII IPA 2', 'tingkat' => 'XII'], ['nama' => 'XII IPS 1', 'tingkat' => 'XII'],
        ];

        // ===== Buat 9 guru (1 guru : 1 mapel, merata 1:1 dengan 9 mapel) =====
        $guruList = [];
        $mapelList = [];
        foreach ($dataMapelGuru as $d) {
            $email = $this->emailDariNama($d['guru']);
            $userGuru = User::create([
                'name' => $d['guru'],
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]);
            $guru = Guru::create([
                'user_id' => $userGuru->id,
                'nip' => $d['nip'],
                'nama' => $d['guru'],
                'jenis_kelamin' => $d['jk'],
                'no_hp' => '0812' . str_pad((string) (3000000 + count($guruList)), 8, '0', STR_PAD_LEFT),
                'alamat' => 'Jl. Pendidikan No. ' . (count($guruList) + 1) . ', Jakarta',
            ]);
            $mapel = MataPelajaran::create([
                'kode_mapel' => $d['kode'],
                'nama_mapel' => $d['mapel'],
                'guru_id' => $guru->id,
                'kkm' => 75,
            ]);
            $guruList[] = $guru;
            $mapelList[] = $mapel;
        }

        // ===== Buat 9 kelas — wali kelas merata 1:1 dari 9 guru di atas =====
        $kelasList = [];
        foreach ($namaKelasList as $i => $k) {
            $kelas = Kelas::create([
                'nama_kelas' => $k['nama'],
                'tingkat' => $k['tingkat'],
                'wali_kelas_id' => $guruList[$i]->id,
            ]);
            $kelasList[] = $kelas;

            // Setiap kelas mempelajari SEMUA 9 mapel di atas (pivot kelas_mata_pelajaran).
            $kelas->mataPelajaran()->attach(collect($mapelList)->pluck('id'));
        }

        // ===== Siswa: TEPAT 8 siswa per kelas, merata di SEMUA 9 kelas (72 siswa) =====
        $namaDepanLaki = [
            'Ahmad', 'Muhammad', 'Rizky', 'Fajar', 'Arif', 'Dimas', 'Eko', 'Fahri',
            'Gilang', 'Hadi', 'Irfan', 'Johan', 'Krisna', 'Lukman', 'Made', 'Nanda',
        ];
        $namaDepanPerempuan = [
            'Siti', 'Putri', 'Ayu', 'Dewi', 'Fitria', 'Indah', 'Lestari', 'Mega',
            'Nabila', 'Ratna', 'Sari', 'Wulan', 'Zahra', 'Yuni', 'Rini', 'Citra',
        ];
        $namaBelakang = [
            'Pratama', 'Saputra', 'Wijaya', 'Kusuma', 'Setiawan', 'Firmansyah',
            'Ramadhan', 'Hidayat', 'Nugraha', 'Permadi', 'Santoso', 'Wulandari',
            'Rahayu', 'Maharani', 'Utami', 'Puspita', 'Gunawan', 'Siregar',
        ];

        $urutGlobal = 0;
        $nisnAwal = 51234567;
        $siswaList = [];

        foreach ($kelasList as $kelas) {
            for ($j = 0; $j < 8; $j++) {
                $lakiLaki = $j % 2 === 0;
                $namaDepan = $lakiLaki
                    ? $namaDepanLaki[$urutGlobal % count($namaDepanLaki)]
                    : $namaDepanPerempuan[$urutGlobal % count($namaDepanPerempuan)];
                // Offset berbeda untuk nama belakang supaya kombinasi depan+belakang tidak berulang.
                $belakang = $namaBelakang[intdiv($urutGlobal, 2) % count($namaBelakang)];
                $namaLengkap = $namaDepan . ' ' . $belakang;
                $urutGlobal++;

                $nisn = '00' . ($nisnAwal + $urutGlobal);
                $email = $this->emailDariNama($namaLengkap);

                $userSiswa = User::create([
                    'name' => $namaLengkap,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'siswa',
                ]);
                $siswa = Siswa::create([
                    'user_id' => $userSiswa->id,
                    'nisn' => $nisn,
                    'nama' => $namaLengkap,
                    'kelas_id' => $kelas->id,
                    'jenis_kelamin' => $lakiLaki ? 'Laki-laki' : 'Perempuan',
                    'nama_wali' => 'Orang Tua ' . $namaDepan,
                    'no_hp_wali' => '0813' . str_pad((string) (1000000 + $urutGlobal), 8, '0', STR_PAD_LEFT),
                ]);
                $siswaList[] = $siswa;
            }
        }

        // ===== Jadwal pelajaran: Senin-Jumat, 4 slot jam per hari, untuk SETIAP kelas =====
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $slotJam = [
            ['jam_ke' => [1, 2], 'mulai' => '07:00', 'selesai' => '08:30'],
            ['jam_ke' => [3, 4], 'mulai' => '08:30', 'selesai' => '10:00'],
            ['jam_ke' => [5, 6], 'mulai' => '10:15', 'selesai' => '11:45'],
            ['jam_ke' => [7, 8], 'mulai' => '12:30', 'selesai' => '14:00'],
        ];

        foreach ($kelasList as $kelas) {
            $putaran = 0;
            foreach ($hariList as $hari) {
                foreach ($slotJam as $slot) {
                    $mapel = $mapelList[$putaran % count($mapelList)];
                    JadwalPelajaran::create([
                        'kelas_id' => $kelas->id,
                        'mata_pelajaran_id' => $mapel->id,
                        'guru_id' => $mapel->guru_id,
                        'hari' => $hari,
                        'jam_ke' => $slot['jam_ke'],
                        'jam_mulai' => $slot['mulai'],
                        'jam_selesai' => $slot['selesai'],
                    ]);
                    $putaran++;
                }
            }
        }

        // ===== SPP: setiap siswa dibuatkan tagihan 3 bulan terakhir =====
        // 2 bulan pertama LUNAS (sudah dibayar), 1 bulan terakhir masih BELUM LUNAS
        // supaya halaman SPP langsung punya contoh kedua status sejak awal.
        $bulanSpp = ['Juli', 'Agustus', 'September'];
        $tahunSpp = '2026';
        foreach ($siswaList as $siswa) {
            foreach ($bulanSpp as $bi => $bulan) {
                $lunas = $bi < 2; // Juli & Agustus lunas, September belum.
                Spp::create([
                    'siswa_id' => $siswa->id,
                    'bulan' => $bulan,
                    'tahun' => $tahunSpp,
                    'jumlah' => 150000,
                    'status' => $lunas ? 'Lunas' : 'Belum Lunas',
                    'tanggal_bayar' => $lunas ? now()->subMonths(3 - $bi)->startOfMonth()->addDays(4) : null,
                ]);
            }
        }

        // ===== Prestasi: sebagian siswa (tiap kelipatan ke-6) diberi 1 prestasi =====
        $daftarPrestasi = [
            ['nama' => 'Olimpiade Sains Nasional (OSN) Matematika', 'tingkat' => 'Nasional', 'peringkat' => 'Juara 2'],
            ['nama' => 'Lomba Debat Bahasa Inggris Tingkat Kota', 'tingkat' => 'Kota', 'peringkat' => 'Juara 1'],
            ['nama' => 'Lomba Cipta Puisi Bahasa Indonesia', 'tingkat' => 'Sekolah', 'peringkat' => 'Juara 1'],
            ['nama' => 'Kompetisi Sains IPA Tingkat Provinsi', 'tingkat' => 'Provinsi', 'peringkat' => 'Juara 3'],
            ['nama' => 'Lomba Cerdas Cermat IPS', 'tingkat' => 'Kota', 'peringkat' => 'Juara 2'],
            ['nama' => 'Turnamen Futsal Antar Sekolah', 'tingkat' => 'Kota', 'peringkat' => 'Juara 1'],
            ['nama' => 'Festival Seni & Budaya Pelajar', 'tingkat' => 'Sekolah', 'peringkat' => 'Juara 2'],
            ['nama' => 'Lomba Karya Prakarya Daur Ulang', 'tingkat' => 'Provinsi', 'peringkat' => 'Juara 3'],
        ];
        foreach ($siswaList as $i => $siswa) {
            if ($i % 6 === 0) {
                $p = $daftarPrestasi[intdiv($i, 6) % count($daftarPrestasi)];
                Prestasi::create([
                    'siswa_id' => $siswa->id,
                    'nama_prestasi' => $p['nama'],
                    'tingkat' => $p['tingkat'],
                    'peringkat' => $p['peringkat'],
                    'tanggal' => now()->subMonths(2)->subDays($i)->toDateString(),
                ]);
            }
        }

        // ===== Kotak Aspirasi: beberapa aspirasi dari siswa, status & anonim bervariasi =====
        $daftarAspirasi = [
            ['judul' => 'Kantin Sekolah Perlu Jam Buka Lebih Awal', 'isi' => 'Mohon kantin dibuka lebih awal (jam 06.30) karena banyak siswa yang datang pagi belum sarapan.', 'anonim' => false, 'status' => 'Selesai', 'tanggapan' => 'Terima kasih masukannya, kantin sudah mulai buka jam 06.30 mulai minggu depan.'],
            ['judul' => 'Usul Penambahan Kipas Angin di Kelas', 'isi' => 'Beberapa ruang kelas di lantai 2 cukup panas pada siang hari, mohon dipertimbangkan penambahan kipas angin.', 'anonim' => true, 'status' => 'Diproses', 'tanggapan' => null],
            ['judul' => 'Jadwal Ekstrakurikuler Bertabrakan', 'isi' => 'Jadwal ekskul basket dan paduan suara bertabrakan di hari yang sama, mohon diatur ulang.', 'anonim' => false, 'status' => 'Baru', 'tanggapan' => null],
            ['judul' => 'Apresiasi untuk Guru Matematika', 'isi' => 'Terima kasih untuk Bapak Budi yang selalu sabar menjelaskan ulang materi yang sulit dipahami.', 'anonim' => false, 'status' => 'Selesai', 'tanggapan' => 'Masukan positif ini sudah kami sampaikan ke guru yang bersangkutan.'],
            ['judul' => 'Toilet Siswa Perlu Perbaikan', 'isi' => 'Beberapa keran air di toilet lantai 1 sudah rusak dan perlu diperbaiki.', 'anonim' => true, 'status' => 'Diproses', 'tanggapan' => null],
            ['judul' => 'Usul Perpustakaan Buka Sampai Sore', 'isi' => 'Mohon perpustakaan bisa buka sampai jam 16.00 untuk yang mau belajar kelompok sepulang sekolah.', 'anonim' => false, 'status' => 'Baru', 'tanggapan' => null],
        ];
        foreach ($daftarAspirasi as $i => $a) {
            $siswaPengirim = $siswaList[$i * 7 % count($siswaList)];
            Aspirasi::create([
                'user_id' => $siswaPengirim->user_id,
                'judul' => $a['judul'],
                'isi' => $a['isi'],
                'anonim' => $a['anonim'],
                'status' => $a['status'],
                'tanggapan' => $a['tanggapan'],
            ]);
        }

        // ===== Bank Soal: 5 soal pilihan ganda per mapel (45 soal total) =====
        $tingkatKesulitanUrutan = ['Mudah', 'Sedang', 'Sulit'];
        foreach ($mapelList as $mi => $mapel) {
            for ($s = 1; $s <= 5; $s++) {
                BankSoal::create([
                    'mata_pelajaran_id' => $mapel->id,
                    'pertanyaan' => "Contoh soal {$s} untuk mata pelajaran {$mapel->nama_mapel} (tingkat kesulitan bervariasi).",
                    'pilihan_a' => 'Pilihan A',
                    'pilihan_b' => 'Pilihan B',
                    'pilihan_c' => 'Pilihan C',
                    'pilihan_d' => 'Pilihan D',
                    'pilihan_e' => 'Pilihan E',
                    'jawaban_benar' => ['A', 'B', 'C', 'D', 'E'][$s % 5],
                    'tingkat_kesulitan' => $tingkatKesulitanUrutan[$s % 3],
                ]);
            }
        }

        $this->command->info('===== Data dummy berhasil dibuat =====');
        $this->command->info('9 kelas, 9 guru (merata 1:1 dengan mapel & wali kelas), 9 mata pelajaran,');
        $this->command->info((count($kelasList) * 8) . ' siswa (8 per kelas, merata di semua kelas), dan jadwal Senin-Jumat untuk semua kelas.');
        $this->command->info('');
        $this->command->info('Semua akun login pakai email NAMA MASING-MASING (nama.belakang@eduvision.test), password: password.');
        $this->command->info('Admin -> admin@eduvision.test / password');
        $this->command->info('Contoh guru -> budi.santoso@eduvision.test / password');
        $this->command->info('Contoh siswa -> cek kolom "Email" di halaman Data Siswa/Data Guru untuk daftar lengkap.');
        $this->command->info('');
        $jumlahPrestasi = (int) floor((count($siswaList) - 1) / 6) + 1;
        $this->command->info('Data tambahan: ' . (count($siswaList) * 3) . ' tagihan SPP, ' . $jumlahPrestasi . ' prestasi siswa,');
        $this->command->info(count($daftarAspirasi) . ' kotak aspirasi, dan ' . (count($mapelList) * 5) . ' soal bank soal (5 per mapel).');
    }

    /**
     * Ubah nama orang menjadi email berbasis nama: "Budi Santoso" -> budi.santoso@eduvision.test.
     * Kalau hasilnya sudah terpakai (ada nama kembar), ditambah angka di belakang
     * (budi.santoso2@eduvision.test, dst) supaya tetap unik.
     */
    private function emailDariNama(string $nama): string
    {
        $slug = strtolower(trim($nama));
        $slug = preg_replace('/[^a-z0-9\s]/', '', $slug);
        $slug = preg_replace('/\s+/', '.', trim($slug));

        $kandidat = $slug;
        $suffix = 1;
        while (isset($this->emailTerpakai[$kandidat])) {
            $suffix++;
            $kandidat = $slug . $suffix;
        }
        $this->emailTerpakai[$kandidat] = true;

        return $kandidat . '@eduvision.test';
    }
}
