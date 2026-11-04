<?php

namespace Database\Seeders;

use App\Models\Aspirasi;
use App\Models\BankSoal;
use App\Models\Guru;
use App\Models\Kurikulum;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Materi;
use App\Models\Nilai;
use App\Models\PengumpulanTugas;
use App\Models\Prestasi;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\Tugas;
use App\Models\TugasHarian;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

/**
 * Data contoh MULTI-SEKOLAH:
 *  - 1 akun super admin (kelola semua sekolah)
 *  - SMA Negeri 1 Harapan Bangsa (9 kelas, 9 guru, 72 siswa)
 *  - SMA Negeri 2 Nusantara (3 kelas, 5 guru, 18 siswa)
 * Identitas sekolah, guru, kelas, siswa, prestasi (+ gambar sertifikat) dibaca dari
 * database/seeders/data/sekolah.json; gambar ada di storage/app/public/seed/.
 * Materi, tugas, pengumpulan tugas, dan nilai saling terhubung (nilai tugas harian
 * otomatis berasal dari nilai pengumpulan tugas, sama seperti alur guru menilai).
 * Password semua akun: "password".
 */
class DatabaseSeeder extends Seeder
{
    private array $emailTerpakai = [];

    public function run(): void
    {
        $this->pastikanStorageLink();

        User::create(['name' => 'Super Admin EduVision', 'email' => 'superadmin@eduvision.test', 'password' => Hash::make('password'), 'role' => 'super_admin']);

        $data = json_decode(File::get(database_path('seeders/data/sekolah.json')), true);
        $materiSumber = $data['sekolah'][0]['materi'];
        $ringkas = [];

        foreach ($data['sekolah'] as $n => $d) {
            $m = $d['meta'];
            $sekolah = Sekolah::create([
                'nama' => $m['nama'], 'npsn' => $m['npsn'], 'jenjang' => $m['jenjang'], 'alamat' => $m['alamat'],
                'kota' => $m['kota'], 'telepon' => $m['telepon'], 'email' => $m['email'],
                'kepala_sekolah' => $m['kepala'], 'nip_kepala_sekolah' => $m['nip'], 'logo' => $m['logo'],
                'latitude' => $m['lat'], 'longitude' => $m['lng'], 'radius_meter' => 100,
            ]);

            Tenant::paksa($sekolah->id);
            try {
                $ringkas[] = $this->isiSekolah($sekolah, $d, $materiSumber, $n === 0 ? 'admin@eduvision.test' : 'admin@nusantara.eduvision.test');
            } finally {
                Tenant::lepas();
            }
        }

        $this->command->info('===== Data contoh berhasil dibuat =====');
        foreach ($ringkas as $r) {
            $this->command->info($r);
        }
        $this->command->info('');
        $this->command->info('Super admin : superadmin@eduvision.test / password');
        $this->command->info('Admin SMAN 1: admin@eduvision.test / password');
        $this->command->info('Admin SMAN 2: admin@nusantara.eduvision.test / password');
        $this->command->info('Guru & siswa : lihat kolom Email di halaman Data Guru / Data Siswa (password: password).');
    }

    private function isiSekolah(Sekolah $sekolah, array $d, array $materiSumber, string $emailAdmin): string
    {
        $this->emailTerpakai = [];

        User::create(['name' => 'Administrator ' . $sekolah->nama, 'email' => $emailAdmin, 'password' => Hash::make('password'), 'role' => 'admin']);

        // ----- Kurikulum sekolah -----
        $kurikulumAktif = Kurikulum::create([
            'nama' => 'Kurikulum Merdeka', 'kode' => 'KM', 'tahun_berlaku' => '2022/2023', 'aktif' => true,
            'deskripsi' => 'Kurikulum berbasis capaian pembelajaran dengan Projek Penguatan Profil Pelajar Pancasila dan pembelajaran terdiferensiasi.',
        ]);
        if ($sekolah->id === Sekolah::withoutGlobalScopes()->min('id')) {
            Kurikulum::create([
                'nama' => 'Kurikulum 2013', 'kode' => 'K13', 'tahun_berlaku' => '2013/2014', 'aktif' => false,
                'deskripsi' => 'Kurikulum sebelumnya (arsip) berbasis kompetensi inti dan kompetensi dasar.',
            ]);
        }
        $semesterMapel = []; // semua mapel berjalan setahun penuh, jadi jumlah mapel Ganjil = Genap

        // ----- Guru + mapel (1 guru = 1 mapel) -----
        $guruList = [];
        $mapelList = [];
        foreach ($d['guru'] as $i => $g) {
            $u = User::create(['name' => $g['nama'], 'email' => $g['email'], 'password' => Hash::make('password'), 'password_awal' => User::enkripsiAwal('password'), 'role' => 'guru', 'foto' => $g['foto']]);
            $guru = Guru::create([
                'user_id' => $u->id, 'nip' => $g['nip'], 'nama' => $g['nama'], 'jenis_kelamin' => $g['jk'],
                'no_hp' => '0812' . str_pad((string) (3000000 + $i), 8, '0', STR_PAD_LEFT),
                'alamat' => 'Jl. Pendidikan No. ' . ($i + 1) . ', ' . $sekolah->kota, 'foto' => $g['foto'],
            ]);
            $mapelList[] = MataPelajaran::create(['kode_mapel' => $g['kode'], 'nama_mapel' => $g['mapel'], 'guru_id' => $guru->id, 'kkm' => 75, 'kurikulum_id' => $kurikulumAktif->id, 'semester' => $semesterMapel[$g['mapel']] ?? 'Semua']);
            $guruList[] = $guru;
        }

        // ----- Kelas -----
        $kelasList = [];
        foreach ($d['kelas'] as $k) {
            $kelas = Kelas::create(['nama_kelas' => $k['nama'], 'tingkat' => $k['tingkat'], 'wali_kelas_id' => $guruList[$k['wali']]->id]);
            $kelas->mataPelajaran()->attach(collect($mapelList)->pluck('id'));
            $kelasList[] = $kelas;
        }

        // ----- Siswa -----
        $siswaList = [];
        foreach ($d['siswa'] as $s) {
            $u = User::create(['name' => $s['nama'], 'email' => $s['email'], 'password' => Hash::make('password'), 'password_awal' => User::enkripsiAwal('password'), 'role' => 'siswa', 'foto' => $s['foto']]);
            $siswaList[] = Siswa::create([
                'user_id' => $u->id, 'nisn' => $s['nisn'], 'nama' => $s['nama'], 'kelas_id' => $kelasList[$s['kelas']]->id,
                'jenis_kelamin' => $s['jk'], 'nama_wali' => $s['wali'], 'no_hp_wali' => $s['hp'], 'foto' => $s['foto'],
            ]);
        }

        $this->jadwal($kelasList, collect($mapelList)->where('semester', '!=', 'Genap')->values()->all());
        $this->spp($siswaList);

        // ----- Prestasi + gambar sertifikat -----
        foreach ($d['prestasi'] as $p) {
            Prestasi::create([
                'siswa_id' => $siswaList[$p['siswa']]->id, 'nama_prestasi' => $p['nama'], 'tingkat' => $p['tingkat'],
                'peringkat' => $p['peringkat'], 'tanggal' => $p['tanggal'], 'file_bukti' => $p['bukti'],
            ]);
        }

        $this->aspirasi($siswaList);
        $jumlahSoal = $this->bankSoal($mapelList);
        $jumlahMateri = $this->materi($mapelList, $guruList, $materiSumber);
        [$jumlahTugas, $jumlahKumpul, $jumlahNilai] = $this->tugasDanNilai($kelasList, $mapelList, $siswaList);
        $this->semesterGanjil2025($kelasList, $mapelList, $guruList, $siswaList, $materiSumber);

        return sprintf(
            '%s: %d kelas, %d guru, %d siswa, %d prestasi bersertifikat, %d soal, %d materi, %d tugas, %d pengumpulan, %d data nilai.',
            $sekolah->nama, count($kelasList), count($guruList), count($siswaList), count($d['prestasi']), $jumlahSoal, $jumlahMateri, $jumlahTugas, $jumlahKumpul, $jumlahNilai
        );
    }

    private function jadwal(array $kelasList, array $mapelList, string $semester = 'Ganjil', string $tahun = '2026/2027'): void
    {
        $slotJam = [
            [[1, 2], '07:00', '08:30'], [[3, 4], '08:30', '10:00'], [[5, 6], '10:15', '11:45'], [[7, 8], '12:30', '14:00'],
        ];
        foreach ($kelasList as $ki => $kelas) {
            $putaran = $ki; // digeser per kelas supaya guru yang sama tidak bentrok di jam yang sama
            foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari) {
                foreach ($slotJam as [$jam, $mulai, $selesai]) {
                    $mapel = $mapelList[$putaran % count($mapelList)];
                    JadwalPelajaran::create([
                        'kelas_id' => $kelas->id, 'mata_pelajaran_id' => $mapel->id, 'guru_id' => $mapel->guru_id,
                        'hari' => $hari, 'jam_ke' => $jam, 'jam_mulai' => $mulai, 'jam_selesai' => $selesai,
                        'semester' => $semester, 'tahun_ajaran' => $tahun,
                    ]);
                    $putaran++;
                }
            }
        }
    }

    /** Semester Ganjil 2025/2026 (Jul-Des 2025): jadwal, materi, nilai, tugas, SPP, aspirasi, dan prestasi. */
    private function semesterGanjil2025(array $kelasList, array $mapelList, array $guruList, array $siswaList, array $materiSumber): void
    {
        $mapelGanjil = array_values(array_filter($mapelList, fn ($m) => $m->semester !== 'Genap'));
        $this->jadwal($kelasList, $mapelGanjil, 'Ganjil', '2025/2026');

        foreach ($mapelList as $i => $mapel) {
            foreach ($materiSumber as $m) {
                if ($m['mapel'] !== $mapel->nama_mapel) {
                    continue;
                }
                Materi::create([
                    'mata_pelajaran_id' => $mapel->id, 'guru_id' => $guruList[$i]->id, 'kelas_id' => null,
                    'judul' => $m['judul'] . ' (Ganjil 2025/2026)', 'deskripsi' => $m['deskripsi'], 'tipe' => 'file',
                    'file_path' => $m['file'], 'file_nama_asli' => $m['nama_asli'],
                    'semester' => 'Ganjil', 'tahun_ajaran' => '2025/2026',
                ]);
            }
            $this->materiLink($mapel, $guruList[$i], 'Ganjil', '2025/2026');
        }

        $daftar = $this->dataTugas();
        foreach ($siswaList as $si => $siswa) {
            foreach ($mapelGanjil as $mi => $mapel) {
                $basis = 62 + (($si * 3 + $mi * 17) % 29);
                $tg = min(100, max(55, $basis + 2));
                $ut = min(100, max(50, $basis - 2));
                $ua = min(100, max(50, $basis + 1));
                Nilai::create([
                    'siswa_id' => $siswa->id, 'mata_pelajaran_id' => $mapel->id, 'semester' => 'Ganjil', 'tahun_ajaran' => '2025/2026',
                    'nilai_tugas' => $tg, 'nilai_uts' => $ut, 'nilai_uas' => $ua, 'nilai_akhir' => Nilai::hitungNilaiAkhir($tg, $ut, $ua),
                ]);
            }
        }
        foreach ($kelasList as $kelas) {
            foreach ($mapelGanjil as $mapel) {
                foreach (array_slice($daftar[$mapel->nama_mapel] ?? [], 0, 2) as $ti => [$judul, $deskripsi]) {
                    Tugas::create([
                        'mata_pelajaran_id' => $mapel->id, 'guru_id' => $mapel->guru_id, 'kelas_id' => $kelas->id,
                        'semester' => 'Ganjil', 'tahun_ajaran' => '2025/2026', 'judul' => $judul, 'deskripsi' => $deskripsi,
                        'deadline' => \Carbon\Carbon::create(2025, $ti === 0 ? 9 : 11, 12 + $ti)->setTime(23, 59),
                    ]);
                }
            }
        }

        foreach ($siswaList as $si => $siswa) {
            foreach (['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bi => $bulan) {
                Spp::create([
                    'siswa_id' => $siswa->id, 'bulan' => $bulan, 'tahun' => '2025', 'jumlah' => 150000, 'status' => 'Lunas',
                    'tanggal_bayar' => \Carbon\Carbon::create(2025, 7 + $bi, 3 + ($si % 6)),
                ]);
            }
        }

        $aspirasi = [
            ['Usul Lapangan Basket Diperbaiki', 'Ring dan lantai lapangan basket perlu diperbaiki agar aman dipakai latihan.', 'Selesai', 'Terima kasih, perbaikan lapangan sudah dijadwalkan dan selesai bulan lalu.', [2025, 8, 21]],
            ['Wifi Sekolah Sering Putus', 'Wifi di area perpustakaan sering putus saat jam belajar mandiri.', 'Selesai', 'Router tambahan sudah dipasang di perpustakaan.', [2025, 10, 8]],
        ];
        foreach ($aspirasi as $i => [$judul, $isi, $status, $tanggapan, $t]) {
            $pengirim = $siswaList[($i * 11 + 3) % count($siswaList)];
            $a = Aspirasi::create(['user_id' => $pengirim->user_id, 'judul' => $judul, 'isi' => $isi, 'anonim' => false, 'status' => $status, 'tanggapan' => $tanggapan]);
            $w = \Carbon\Carbon::create($t[0], $t[1], $t[2], 10, 30);
            $a->forceFill(['created_at' => $w, 'updated_at' => $w])->saveQuietly();
        }

        $prestasi = [
            ['Lomba Karya Tulis Ilmiah Tingkat Kota', 'Kota', 'Juara 2', '2025-09-18'],
            ['Turnamen Futsal Antar SMA', 'Kabupaten', 'Juara 3', '2025-10-25'],
            ['Festival Seni dan Budaya Pelajar', 'Provinsi', 'Juara Harapan 1', '2025-11-14'],
        ];
        foreach ($prestasi as $i => [$nama, $tingkat, $peringkat, $tanggal]) {
            Prestasi::create(['siswa_id' => $siswaList[($i * 9 + 4) % count($siswaList)]->id, 'nama_prestasi' => $nama, 'tingkat' => $tingkat, 'peringkat' => $peringkat, 'tanggal' => $tanggal]);
        }
    }

    private function spp(array $siswaList): void
    {
        // Semester Genap 2025/2026 (Jan-Jun 2026): seluruh tagihan sudah lunas.
        foreach ($siswaList as $si => $siswa) {
            foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'] as $bi => $bulan) {
                Spp::create([
                    'siswa_id' => $siswa->id, 'bulan' => $bulan, 'tahun' => '2026', 'jumlah' => 150000, 'status' => 'Lunas',
                    'tanggal_bayar' => \Carbon\Carbon::create(2026, $bi + 1, 3 + ($si % 6)),
                ]);
            }
        }

        foreach ($siswaList as $si => $siswa) {
            foreach (['Juli', 'Agustus', 'September'] as $bi => $bulan) {
                $lunas = $bi < 2 || ($bi === 2 && $si % 3 === 0);
                Spp::create([
                    'siswa_id' => $siswa->id, 'bulan' => $bulan, 'tahun' => '2026', 'jumlah' => 150000,
                    'status' => $lunas ? 'Lunas' : 'Belum Lunas',
                    'tanggal_bayar' => $lunas ? now()->subMonths(3 - $bi)->startOfMonth()->addDays(3 + ($si % 6)) : null,
                ]);
            }
        }
    }

    private function aspirasi(array $siswaList): void
    {
        $daftarAspirasi = [
            ['judul' => 'Kantin Sekolah Perlu Jam Buka Lebih Awal', 'isi' => 'Mohon kantin dibuka lebih awal (jam 06.30) karena banyak siswa yang datang pagi belum sarapan.', 'anonim' => false, 'status' => 'Selesai', 'tanggapan' => 'Terima kasih masukannya, kantin sudah mulai buka jam 06.30 mulai minggu depan.'],
            ['judul' => 'Usul Penambahan Kipas Angin di Kelas', 'isi' => 'Beberapa ruang kelas di lantai 2 cukup panas pada siang hari, mohon dipertimbangkan penambahan kipas angin.', 'anonim' => true, 'status' => 'Diproses', 'tanggapan' => null],
            ['judul' => 'Jadwal Ekstrakurikuler Bertabrakan', 'isi' => 'Jadwal ekskul basket dan paduan suara bertabrakan di hari yang sama, mohon diatur ulang.', 'anonim' => false, 'status' => 'Baru', 'tanggapan' => null],
            ['judul' => 'Apresiasi untuk Guru Matematika', 'isi' => 'Terima kasih untuk guru Matematika kami yang selalu sabar menjelaskan ulang materi yang sulit dipahami.', 'anonim' => false, 'status' => 'Selesai', 'tanggapan' => 'Masukan positif ini sudah kami sampaikan ke guru yang bersangkutan.'],
            ['judul' => 'Toilet Siswa Perlu Perbaikan', 'isi' => 'Beberapa keran air di toilet lantai 1 sudah rusak dan perlu diperbaiki.', 'anonim' => true, 'status' => 'Diproses', 'tanggapan' => null],
            ['judul' => 'Usul Perpustakaan Buka Sampai Sore', 'isi' => 'Mohon perpustakaan bisa buka sampai jam 16.00 untuk yang mau belajar kelompok sepulang sekolah.', 'anonim' => false, 'status' => 'Baru', 'tanggapan' => null],
        ];
        foreach ($daftarAspirasi as $i => $a) {
            $pengirim = $siswaList[$i * 7 % count($siswaList)];
            $baru = Aspirasi::create(['user_id' => $a['anonim'] ? null : $pengirim->user_id, 'pemilik_hash' => $a['anonim'] ? Aspirasi::hashPemilik($pengirim->user_id) : null, 'judul' => $a['judul'], 'isi' => $a['isi'], 'anonim' => $a['anonim'], 'status' => $a['status'], 'tanggapan' => $a['tanggapan']]);
            // Aspirasi yang sudah selesai ditanggapi dibuat pada semester Genap sebelumnya; sisanya semester berjalan.
            $waktu = $a['status'] === 'Selesai' ? \Carbon\Carbon::create(2026, 3 + $i, 10, 9, 15) : now()->subDays(3 + $i * 4);
            $baru->forceFill(['created_at' => $waktu, 'updated_at' => $waktu])->saveQuietly();
        }
    }

    private function bankSoal(array $mapelList): int
    {
        $data = $this->dataBankSoal();
        $jumlah = 0;
        foreach ($mapelList as $mapel) {
            foreach ($data[$mapel->nama_mapel] ?? [] as [$tanya, $opsi, $kunci, $tingkat, $bahas]) {
                BankSoal::create([
                    'mata_pelajaran_id' => $mapel->id, 'pertanyaan' => $tanya,
                    'pilihan_a' => $opsi[0], 'pilihan_b' => $opsi[1], 'pilihan_c' => $opsi[2], 'pilihan_d' => $opsi[3], 'pilihan_e' => $opsi[4],
                    'jawaban_benar' => $kunci, 'tingkat_kesulitan' => $tingkat, 'pembahasan' => $bahas,
                ]);
                $jumlah++;
            }
        }

        return $jumlah;
    }

    /** Materi berupa file PDF sungguhan (storage/app/public/seed/materi), berlaku untuk semua kelas. */
    /** Materi berupa tautan video pembelajaran (pencarian YouTube per mapel, jadi tautannya selalu valid). */
    private function materiLink(MataPelajaran $mapel, $guru, string $semester, string $tahun): void
    {
        $topik = [
            ['Video Pembelajaran', 'Kumpulan video penjelasan materi untuk dipelajari ulang di rumah.', ' kelas 10 SMA materi lengkap'],
            ['Latihan Soal dan Pembahasan', 'Video pembahasan contoh soal agar siap menghadapi ulangan.', ' SMA latihan soal dan pembahasan'],
        ];
        foreach ($topik as [$judul, $deskripsi, $kata]) {
            Materi::create([
                'mata_pelajaran_id' => $mapel->id, 'guru_id' => $guru->id, 'kelas_id' => null,
                'judul' => $mapel->nama_mapel . ': ' . $judul, 'deskripsi' => $deskripsi, 'tipe' => 'link_youtube',
                'link' => 'https://www.youtube.com/results?search_query=' . rawurlencode($mapel->nama_mapel . $kata),
                'semester' => $semester, 'tahun_ajaran' => $tahun,
            ]);
        }
    }

    private function materi(array $mapelList, array $guruList, array $sumber): int
    {
        $jumlah = 0;
        foreach ($mapelList as $i => $mapel) {
            foreach ($sumber as $m) {
                if ($m['mapel'] !== $mapel->nama_mapel) {
                    continue;
                }
                Materi::create([
                    'mata_pelajaran_id' => $mapel->id, 'guru_id' => $guruList[$i]->id, 'kelas_id' => null,
                    'judul' => $m['judul'], 'deskripsi' => $m['deskripsi'], 'tipe' => 'file',
                    'file_path' => $m['file'], 'file_nama_asli' => $m['nama_asli'],
                    'semester' => $mapel->semester === 'Genap' ? 'Genap' : 'Ganjil',
                    'tahun_ajaran' => $mapel->semester === 'Genap' ? '2025/2026' : '2026/2027',
                ]);
                $jumlah++;
            }
            $this->materiLink($mapel, $guruList[$i], $mapel->semester === 'Genap' ? 'Genap' : 'Ganjil', $mapel->semester === 'Genap' ? '2025/2026' : '2026/2027');
            $jumlah += 2;
        }

        return $jumlah;
    }

    /**
     * Tiap mapel di tiap kelas mendapat 3 tugas (2 sudah lewat deadline + 1 akan datang).
     * Siswa mengumpulkan; tugas yang sudah dikumpulkan dinilai guru, lalu nilainya masuk ke
     * "Tugas Harian" pada data Nilai lewat alur yang sama dengan TugasController::beriNilai.
     * UTS & UAS diisi terpisah, nilai akhir dihitung Nilai::hitungNilaiAkhir().
     */
    private function tugasDanNilai(array $kelasList, array $mapelList, array $siswaList): array
    {
        $semester = 'Ganjil';
        $tahun = '2026/2027';
        $daftar = $this->dataTugas();
        $catatan = [
            [90, 'Pekerjaan sangat baik dan rapi, pertahankan.'], [80, 'Sudah baik, tingkatkan ketelitian pada bagian akhir.'],
            [70, 'Cukup baik. Perhatikan lagi langkah pengerjaan.'], [0, 'Perlu belajar lebih giat, silakan konsultasi dengan guru.'],
        ];

        // Semester Genap 2025/2026 (sudah selesai): nilai akhir semua mapel yang berlaku di semester Genap.
        foreach ($siswaList as $si => $siswa) {
            foreach ($mapelList as $mi => $mapel) {
                if ($mapel->semester === 'Ganjil') {
                    continue;
                }
                $basis = 64 + (($si * 5 + $mi * 13) % 27);
                $tg = min(100, max(55, $basis + 3));
                $ut = min(100, max(50, $basis - 1));
                $ua = min(100, max(50, $basis + 2));
                Nilai::create([
                    'siswa_id' => $siswa->id, 'mata_pelajaran_id' => $mapel->id, 'semester' => 'Genap', 'tahun_ajaran' => '2025/2026',
                    'nilai_tugas' => $tg, 'nilai_uts' => $ut, 'nilai_uas' => $ua, 'nilai_akhir' => Nilai::hitungNilaiAkhir($tg, $ut, $ua),
                ]);
            }
        }

        // Semester Ganjil 2026/2027 (berjalan): hanya mapel yang berlaku di semester Ganjil.
        $mapelList = array_values(array_filter($mapelList, fn ($m) => $m->semester !== 'Genap'));

        // Nilai (siswa x mapel) dibuat lebih dulu dengan UTS/UAS.
        $nilaiMap = [];
        foreach ($siswaList as $si => $siswa) {
            foreach ($mapelList as $mi => $mapel) {
                $basis = 66 + (($si * 7 + $mi * 11) % 25);
                $uts = min(100, max(50, $basis - 2 + (($mi * 3) % 8)));
                $uas = min(100, max(50, $basis + 3 + (($si * 2) % 7)));
                $nilaiMap[$siswa->id][$mapel->id] = [Nilai::create([
                    'siswa_id' => $siswa->id, 'mata_pelajaran_id' => $mapel->id, 'semester' => $semester, 'tahun_ajaran' => $tahun,
                    'nilai_tugas' => 0, 'nilai_uts' => $uts, 'nilai_uas' => $uas, 'nilai_akhir' => 0,
                ]), $basis];
            }
        }

        $jTugas = $jKumpul = 0;
        $siswaPerKelas = collect($siswaList)->groupBy('kelas_id');
        foreach ($kelasList as $kelas) {
            foreach ($mapelList as $mi => $mapel) {
                foreach ($daftar[$mapel->nama_mapel] as $ti => [$judul, $deskripsi]) {
                    $deadline = match ($ti) { 0 => now()->subWeeks(6), 1 => now()->subWeeks(2), default => now()->addDays(8) };
                    $tugas = Tugas::create([
                        'mata_pelajaran_id' => $mapel->id, 'guru_id' => $mapel->guru_id, 'kelas_id' => $kelas->id,
                        'semester' => $semester, 'tahun_ajaran' => $tahun, 'judul' => $judul, 'deskripsi' => $deskripsi,
                        'deadline' => $deadline->setTime(23, 59),
                    ]);
                    $jTugas++;

                    foreach ($siswaPerKelas[$kelas->id] ?? [] as $si => $siswa) {
                        $kunci = $siswa->id * 3 + $ti * 5 + $mi;
                        // Tugas terakhir (belum jatuh tempo) baru dikumpulkan sebagian siswa; yang lain terkadang terlewat.
                        if ($ti === 2 && $kunci % 3 !== 0) {
                            continue;
                        }
                        if ($ti < 2 && $kunci % 13 === 0) {
                            continue;
                        }
                        $telat = $ti < 2 && $kunci % 7 === 0;
                        $kumpul = $telat
                            ? $deadline->copy()->addHours(5 + $kunci % 20)
                            : ($ti === 2 ? now()->subHours(3 + $kunci % 40) : $deadline->copy()->subDays(1 + $kunci % 4)->setTime(7 + $kunci % 12, ($kunci * 7) % 60));

                        $pengumpulan = PengumpulanTugas::create([
                            'tugas_id' => $tugas->id, 'siswa_id' => $siswa->id, 'tipe' => 'file',
                            'catatan' => $kunci % 5 === 0 ? 'Mohon maaf Bu/Pak, tugas saya kumpulkan dalam satu berkas.' : null,
                            'dikumpulkan_pada' => $kumpul,
                        ]);
                        $berkas = 'seed/tugas/lembar-' . preg_replace('/\s+/', '-', strtolower($mapel->nama_mapel)) . '.pdf';
                        $pengumpulan->files()->create(['file_path' => $berkas, 'file_nama_asli' => 'Tugas ' . $mapel->nama_mapel . ' - ' . $siswa->nama . '.pdf']);
                        $jKumpul++;

                        if ($ti === 2) {
                            continue; // belum dinilai, menunggu guru
                        }

                        [$nilai, $basis] = $nilaiMap[$siswa->id][$mapel->id];
                        $skor = min(100, max(55, $basis + [-4, 2, -1, 5, 0, 3][$kunci % 6] - ($telat ? 5 : 0)));
                        $komentar = collect($catatan)->first(fn ($c) => $skor >= $c[0])[1];
                        $pengumpulan->update(['nilai' => $skor, 'catatan_guru' => $komentar]);
                        TugasHarian::create([
                            'nilai_id' => $nilai->id, 'tugas_id' => $tugas->id, 'nama_tugas' => $tugas->judul,
                            'skor' => $skor, 'tanggal' => $kumpul->toDateString(),
                        ]);
                    }
                }
            }
        }

        // Hitung ulang Nilai Tugas (rata-rata tugas harian) & Nilai Akhir
        $jNilai = 0;
        foreach ($nilaiMap as $perMapel) {
            foreach ($perMapel as [$nilai]) {
                $rata = $nilai->rataRataTugasHarian();
                $nilai->nilai_tugas = $rata;
                $nilai->nilai_akhir = Nilai::hitungNilaiAkhir($rata, $nilai->nilai_uts, $nilai->nilai_uas);
                $nilai->save();
                $jNilai++;
            }
        }

        return [$jTugas, $jKumpul, $jNilai];
    }

    private function dataTugas(): array
    {
        return [
            'Matematika' => [
                ['Latihan Soal Persamaan Kuadrat', 'Kerjakan soal nomor 1–10 pada Lembar Kerja Siswa halaman 24. Tuliskan langkah penyelesaian secara runtut.'],
                ['Menentukan Gradien Garis Singgung', 'Tentukan turunan dan gradien garis singgung dari 8 fungsi aljabar pada lembar soal.'],
                ['Soal Cerita Aplikasi Turunan', 'Selesaikan 5 soal cerita tentang nilai maksimum dan minimum. Sertakan model matematikanya.'],
            ],
            'Bahasa Indonesia' => [
                ['Menyusun Teks Eksposisi', 'Tulis teks eksposisi 3 paragraf bertema "Manfaat Membaca bagi Pelajar" lengkap dengan tesis, argumentasi, dan penegasan ulang.'],
                ['Menemukan Majas dalam Puisi', 'Analisis 3 puisi yang dibagikan, tuliskan majas yang digunakan beserta kutipannya.'],
                ['Resensi Buku Fiksi', 'Buat resensi satu buku fiksi yang sudah kamu baca (minimal 400 kata).'],
            ],
            'Bahasa Inggris' => [
                ['Exercise: Simple Present vs Present Continuous', 'Complete the 20 sentences in the worksheet using the correct form of the verbs.'],
                ['Write a Short Descriptive Text', 'Write a descriptive text (100–150 words) about your favourite place in your town.'],
                ['Conditional Sentences Worksheet', 'Complete 15 conditional sentences (type 1, 2, and 3) and make 3 sentences of your own.'],
            ],
            'IPA' => [
                ['Soal Hukum Newton', 'Kerjakan 8 soal perhitungan gaya, massa, dan percepatan. Tuliskan diketahui, ditanya, dan jawab.'],
                ['Laporan Pengamatan Fotosintesis', 'Buat laporan singkat percobaan fotosintesis pada tanaman air (tujuan, alat bahan, hasil, kesimpulan).'],
                ['Rangkuman Sistem Pernapasan', 'Buat rangkuman dan gambar bagan sistem pernapasan manusia beserta fungsi tiap organ.'],
            ],
            'IPS' => [
                ['Kronologi Proklamasi Kemerdekaan', 'Susun lini masa peristiwa menjelang proklamasi 1945 dalam bentuk tabel dan jelaskan tiap peristiwa.'],
                ['Analisis Permintaan dan Penawaran', 'Dari data harga dan jumlah barang pada lembar soal, gambarkan kurva dan tentukan harga keseimbangan.'],
                ['Wawancara Pelaku Usaha Sekitar', 'Wawancarai satu pedagang di sekitar rumahmu tentang rantai distribusi barangnya dan tulis hasilnya.'],
            ],
            'PPKn' => [
                ['Nilai-nilai Pancasila dalam Kehidupan Sehari-hari', 'Berikan 5 contoh pengamalan tiap sila Pancasila di lingkungan sekolah dan rumah.'],
                ['Hak dan Kewajiban Warga Negara', 'Buat tabel hak dan kewajiban warga negara beserta pasal UUD 1945 yang mengaturnya.'],
                ['Studi Kasus Toleransi Antarumat Beragama', 'Analisis satu kasus toleransi dari berita dan tuliskan pendapatmu (maks. 1 halaman).'],
            ],
            'Penjaskes' => [
                ['Tabel Pengamatan Kebugaran Pribadi', 'Catat hasil lari 12 menit dan denyut nadi sebelum–sesudah olahraga selama seminggu.'],
                ['Peraturan Permainan Bola Voli', 'Rangkum peraturan dan teknik dasar bola voli (servis, passing bawah, passing atas, smash).'],
                ['Menyusun Program Latihan Mingguan', 'Susun program latihan kebugaran 1 minggu dengan prinsip FITT.'],
            ],
            'Seni Budaya' => [
                ['Mengenal Alat Musik Daerah', 'Pilih 5 alat musik tradisional dari daerah berbeda, tulis asal daerah dan cara memainkannya.'],
                ['Karya Gambar Komposisi Warna', 'Buat gambar dengan komposisi warna primer dan sekunder di kertas A4 (difoto/pindai).'],
                ['Apresiasi Pertunjukan Tari Daerah', 'Tonton satu pertunjukan tari daerah dan tulis apresiasimu (gerak, iringan, kostum, makna).'],
            ],
            'Prakarya' => [
                ['Rancangan Produk Kerajinan', 'Buat sketsa dan rencana pembuatan satu produk kerajinan dari bahan limbah beserta alat dan bahan.'],
                ['Laporan Pembuatan Produk Pangan Awet', 'Tuliskan proses pembuatan satu produk pengawetan pangan (misalnya manisan atau keripik) disertai foto.'],
                ['Rencana Pengemasan dan Pemasaran', 'Rancang kemasan dan strategi pemasaran sederhana untuk produk hasil karyamu.'],
            ],
        ];
    }

    private function dataBankSoal(): array
    {
        return [
            'Matematika' => [
                ['Hasil dari 2³ × 3² adalah ...', ['36', '48', '72', '54', '64'], 'C', 'Mudah', '2³ = 8 dan 3² = 9, sehingga 8 × 9 = 72.'],
                ['Nilai x yang memenuhi persamaan 2x + 7 = 19 adalah ...', ['4', '5', '6', '7', '8'], 'C', 'Mudah', '2x + 7 = 19 → 2x = 19 − 7 = 12 → x = 12 : 2 = 6.'],
                ['Akar-akar persamaan kuadrat x² − 5x + 6 = 0 adalah ...', ['1 dan 6', '2 dan 3', '−2 dan −3', '2 dan −3', '−1 dan 6'], 'B', 'Sedang', 'x² − 5x + 6 = 0 difaktorkan menjadi (x − 2)(x − 3) = 0, sehingga x = 2 atau x = 3.'],
                ['Jika f(x) = 3x − 2, maka nilai f(4) adalah ...', ['8', '12', '10', '14', '6'], 'C', 'Mudah', 'f(4) = 3(4) − 2 = 12 − 2 = 10.'],
                ['Turunan pertama dari f(x) = x³ + 2x² adalah ...', ['3x² + 4x', '3x² + 2x', 'x² + 4x', '3x + 4', '2x² + 4x'], 'A', 'Sulit', 'Turunan xⁿ adalah n·xⁿ⁻¹. Maka turunan x³ = 3x² dan turunan 2x² = 4x, jadi f′(x) = 3x² + 4x.'],
            ],
            'Bahasa Indonesia' => [
                ['Kalimat yang penggunaan huruf kapitalnya benar adalah ...', ['Dia lahir di kota bandung.', 'Adik membaca buku di Perpustakaan.', 'Ibu pergi ke Pasar Minggu hari Senin.', 'Kami berlibur ke pulau Bali.', 'Ayah bekerja di jakarta.'], 'C', 'Mudah', 'Huruf kapital dipakai untuk nama tempat dan hari. “Pasar Minggu” dan “Senin” sudah benar, sedangkan “kota bandung”, “pulau Bali”, “Perpustakaan”, dan “jakarta” salah penulisannya.'],
                ['Penulisan kata baku yang benar adalah ...', ['apotik', 'apotek', 'apoteck', 'appotek', 'apotheek'], 'B', 'Mudah', 'Bentuk baku menurut KBBI adalah “apotek”, bukan “apotik”.'],
                ['Majas pada kalimat "Angin malam berbisik lembut di telingaku" adalah ...', ['Hiperbola', 'Personifikasi', 'Metafora', 'Litotes', 'Ironi'], 'B', 'Sedang', 'Personifikasi memberi sifat manusia pada benda mati. Angin “berbisik” seolah-olah manusia yang berbicara.'],
                ['Gagasan pokok sebuah paragraf biasanya terdapat pada ...', ['kalimat penjelas', 'kalimat utama', 'kalimat penutup saja', 'judul bacaan', 'kata sambung'], 'B', 'Mudah', 'Gagasan pokok (ide utama) paragraf terletak pada kalimat utama, sedangkan kalimat penjelas hanya mendukungnya.'],
                ['Antonim dari kata "transparan" adalah ...', ['bening', 'tembus pandang', 'jelas', 'buram', 'cerah'], 'D', 'Sedang', 'Transparan berarti tembus pandang atau bening. Lawan katanya adalah buram, yaitu tidak tembus pandang.'],
            ],
            'Bahasa Inggris' => [
                ['She ___ to school every morning.', ['go', 'goes', 'going', 'gone', 'went'], 'B', 'Mudah', 'Subjek “She” adalah orang ketiga tunggal dalam simple present tense, sehingga kata kerjanya memakai tambahan -s/-es: “goes”.'],
                ['They ___ playing football when it started to rain.', ['was', 'is', 'were', 'are', 'be'], 'C', 'Sedang', 'Kalimat ini memakai past continuous (kejadian yang sedang berlangsung di masa lalu). Subjek “They” jamak sehingga memakai “were playing”.'],
                ['What is the past tense of the verb "write"?', ['writed', 'written', 'wrote', 'writes', 'writing'], 'C', 'Mudah', 'Bentuk lampau (past tense) dari “write” adalah “wrote”. “Written” adalah past participle.'],
                ['If I ___ rich, I would travel around the world.', ['am', 'was', 'be', 'were', 'will be'], 'D', 'Sulit', 'Pada pengandaian (conditional type 2) yang tidak nyata, setelah “if” dipakai “were” untuk semua subjek: “If I were rich...”.'],
                ['The synonym of the word "enormous" is ...', ['tiny', 'huge', 'narrow', 'quiet', 'ancient'], 'B', 'Sedang', '“Enormous” berarti sangat besar, sinonimnya “huge”. “Tiny” justru berarti sangat kecil.'],
            ],
            'IPA' => [
                ['Organel sel yang berfungsi sebagai tempat berlangsungnya respirasi sel adalah ...', ['Ribosom', 'Mitokondria', 'Kloroplas', 'Lisosom', 'Nukleus'], 'B', 'Mudah', 'Mitokondria disebut “pembangkit tenaga sel” karena di sinilah respirasi sel menghasilkan energi (ATP).'],
                ['Satuan SI untuk gaya adalah ...', ['Joule', 'Watt', 'Pascal', 'Newton', 'Ampere'], 'D', 'Mudah', 'Satuan gaya dalam SI adalah Newton (N). Joule untuk energi, Watt untuk daya, Pascal untuk tekanan, Ampere untuk arus listrik.'],
                ['Rumus kimia dari air adalah ...', ['CO₂', 'O₂', 'NaCl', 'H₂O₂', 'H₂O'], 'E', 'Mudah', 'Satu molekul air terdiri dari dua atom hidrogen dan satu atom oksigen, rumusnya H₂O. H₂O₂ adalah hidrogen peroksida.'],
                ['Sebuah benda bermassa 2 kg dipercepat 3 m/s². Besar gaya yang bekerja pada benda adalah ...', ['1,5 N', '5 N', '6 N', '9 N', '12 N'], 'C', 'Sedang', 'Hukum II Newton: F = m × a = 2 kg × 3 m/s² = 6 N.'],
                ['Proses pembuatan makanan pada tumbuhan hijau dengan bantuan cahaya matahari disebut ...', ['respirasi', 'transpirasi', 'fotosintesis', 'fermentasi', 'osmosis'], 'C', 'Sedang', 'Fotosintesis adalah proses tumbuhan hijau membuat makanan (glukosa) dari air dan CO₂ dengan bantuan cahaya matahari dan klorofil.'],
            ],
            'IPS' => [
                ['Proklamasi kemerdekaan Indonesia dibacakan pada tanggal ...', ['17 Agustus 1945', '18 Agustus 1945', '1 Juni 1945', '28 Oktober 1928', '10 November 1945'], 'A', 'Mudah', 'Proklamasi kemerdekaan Indonesia dibacakan Ir. Soekarno pada 17 Agustus 1945. Tanggal 18 Agustus 1945 adalah pengesahan UUD 1945.'],
                ['Kegiatan menyalurkan barang dari produsen ke konsumen disebut ...', ['produksi', 'distribusi', 'konsumsi', 'investasi', 'ekspor'], 'B', 'Mudah', 'Distribusi adalah kegiatan menyalurkan barang dari produsen ke konsumen. Produksi membuat barang, konsumsi menggunakannya.'],
                ['Organisasi pergerakan nasional pertama di Indonesia adalah ...', ['Sarekat Islam', 'Indische Partij', 'Budi Utomo', 'PNI', 'Muhammadiyah'], 'C', 'Sedang', 'Budi Utomo (didirikan 20 Mei 1908) adalah organisasi pergerakan nasional pertama, sehingga tanggal berdirinya diperingati sebagai Hari Kebangkitan Nasional.'],
                ['Menurut hukum permintaan, jika harga suatu barang naik maka jumlah barang yang diminta cenderung ...', ['naik', 'tetap', 'turun', 'tak terbatas', 'berubah-ubah'], 'C', 'Sedang', 'Hukum permintaan: harga naik, jumlah barang yang diminta turun (ceteris paribus), karena konsumen mencari alternatif atau mengurangi pembelian.'],
                ['Benua terluas di dunia adalah ...', ['Afrika', 'Amerika Utara', 'Eropa', 'Asia', 'Antartika'], 'D', 'Mudah', 'Asia adalah benua terluas di dunia, dengan luas sekitar 44 juta km².'],
            ],
            'PPKn' => [
                ['Sila ketiga Pancasila berbunyi ...', ['Ketuhanan Yang Maha Esa', 'Kemanusiaan yang adil dan beradab', 'Persatuan Indonesia', 'Kerakyatan yang dipimpin oleh hikmat kebijaksanaan', 'Keadilan sosial bagi seluruh rakyat Indonesia'], 'C', 'Mudah', 'Sila ketiga Pancasila berbunyi “Persatuan Indonesia”, berlambang pohon beringin.'],
                ['Lambang sila kedua Pancasila adalah ...', ['bintang', 'rantai', 'pohon beringin', 'kepala banteng', 'padi dan kapas'], 'B', 'Mudah', 'Sila kedua (Kemanusiaan yang adil dan beradab) berlambang rantai yang melambangkan hubungan antarmanusia.'],
                ['UUD 1945 disahkan oleh PPKI pada tanggal ...', ['17 Agustus 1945', '18 Agustus 1945', '19 Agustus 1945', '1 Juni 1945', '22 Juni 1945'], 'B', 'Sedang', 'UUD 1945 disahkan oleh PPKI sehari setelah proklamasi, yaitu 18 Agustus 1945.'],
                ['Berikut ini yang termasuk kewajiban warga negara adalah ...', ['mendapat pendidikan', 'memeluk agama', 'membayar pajak', 'mengemukakan pendapat', 'mendapat pekerjaan'], 'C', 'Sedang', 'Membayar pajak adalah kewajiban warga negara. Mendapat pendidikan, beragama, berpendapat, dan bekerja adalah hak.'],
                ['Semboyan "Bhinneka Tunggal Ika" memiliki arti ...', ['Berbeda-beda tetapi tetap satu', 'Satu nusa satu bangsa', 'Bersatu kita teguh', 'Tut Wuri Handayani', 'Gotong royong'], 'A', 'Mudah', '“Bhinneka Tunggal Ika” berasal dari kitab Sutasoma, artinya berbeda-beda tetapi tetap satu.'],
            ],
            'Penjaskes' => [
                ['Dalam permainan sepak bola, jumlah pemain satu tim di lapangan adalah ...', ['9 orang', '10 orang', '11 orang', '12 orang', '15 orang'], 'C', 'Mudah', 'Satu tim sepak bola bermain dengan 11 pemain di lapangan, termasuk satu penjaga gawang.'],
                ['Start yang dipakai dalam lari jarak pendek (sprint) adalah start ...', ['melayang', 'jongkok', 'berdiri', 'duduk', 'berjalan'], 'B', 'Mudah', 'Pada lari jarak pendek (sprint) dipakai start jongkok agar tubuh dapat meluncur dengan daya dorong maksimal.'],
                ['Ukuran lapangan bola voli adalah ...', ['18 × 9 meter', '20 × 10 meter', '15 × 8 meter', '28 × 15 meter', '24 × 12 meter'], 'A', 'Sedang', 'Lapangan bola voli berukuran 18 × 9 meter, dibagi dua oleh net.'],
                ['Olahraga yang paling melatih daya tahan jantung dan paru-paru adalah ...', ['angkat beban', 'lari jarak jauh', 'lompat jauh', 'tolak peluru', 'lempar lembing'], 'B', 'Sedang', 'Lari jarak jauh adalah latihan aerobik yang paling melatih daya tahan jantung dan paru-paru.'],
                ['Pemanasan sebelum berolahraga bertujuan untuk ...', ['mengurangi tenaga', 'mencegah cedera', 'menambah kelelahan', 'memperlambat detak jantung', 'membuat mengantuk'], 'B', 'Mudah', 'Pemanasan menaikkan suhu otot dan melancarkan peredaran darah sehingga tubuh siap bergerak dan risiko cedera berkurang.'],
            ],
            'Seni Budaya' => [
                ['Alat musik tradisional Jawa Barat yang terbuat dari bambu adalah ...', ['sasando', 'angklung', 'gamelan', 'kolintang', 'tifa'], 'B', 'Mudah', 'Angklung adalah alat musik tradisional Jawa Barat yang terbuat dari bambu dan dimainkan dengan cara digoyangkan.'],
                ['Tari Kecak berasal dari daerah ...', ['Jawa Barat', 'Bali', 'Aceh', 'Sumatera Barat', 'Papua'], 'B', 'Mudah', 'Tari Kecak berasal dari Bali dan dibawakan oleh banyak penari laki-laki yang bersuara “cak”.'],
                ['Warna primer dalam seni rupa adalah ...', ['merah, kuning, biru', 'merah, hijau, biru', 'hijau, oranye, ungu', 'hitam, putih, abu-abu', 'merah, jingga, kuning'], 'A', 'Sedang', 'Warna primer adalah warna dasar yang tidak bisa dibuat dari campuran warna lain: merah, kuning, dan biru.'],
                ['Teknik membatik yang menggunakan cap dari tembaga disebut batik ...', ['tulis', 'cap', 'lukis', 'ikat celup', 'printing'], 'B', 'Sedang', 'Batik cap dibuat menggunakan stempel (cap) dari tembaga, berbeda dengan batik tulis yang digambar dengan canting.'],
                ['Tangga nada diatonis terdiri dari ... nada dasar.', ['5', '6', '7', '8', '12'], 'C', 'Sulit', 'Tangga nada diatonis terdiri dari 7 nada dasar (do, re, mi, fa, sol, la, si); nada kedelapan adalah do oktaf.'],
            ],
            'Prakarya' => [
                ['Kerajinan dari tanah liat yang dibentuk lalu dibakar disebut ...', ['anyaman', 'gerabah', 'batik', 'kriya kayu', 'rajut'], 'B', 'Mudah', 'Gerabah adalah kerajinan dari tanah liat yang dibentuk lalu dibakar, misalnya kendi dan pot.'],
                ['Tahap pertama dalam proses membuat produk kerajinan adalah ...', ['pengemasan', 'produksi', 'perencanaan dan penentuan ide', 'pemasaran', 'evaluasi'], 'C', 'Sedang', 'Proses kerajinan selalu diawali dengan perencanaan dan penentuan ide sebelum bahan disiapkan dan diproduksi.'],
                ['Bahan yang umum dipakai untuk membuat anyaman adalah ...', ['rotan, bambu, dan pandan', 'besi', 'kaca', 'plastik cair', 'beton'], 'A', 'Mudah', 'Anyaman dibuat dari bahan alam yang lentur dan mudah dianyam, seperti rotan, bambu, dan pandan.'],
                ['Pengeringan bahan pangan dilakukan dengan tujuan ...', ['menambah kadar air', 'mengawetkan', 'mempercepat pembusukan', 'memperbanyak jumlah', 'mengubah warna'], 'B', 'Sedang', 'Pengeringan menurunkan kadar air sehingga pertumbuhan jamur dan bakteri terhambat dan bahan pangan lebih awet.'],
                ['Kemasan produk yang baik sebaiknya ...', ['menutupi seluruh produk', 'melindungi dan menarik minat pembeli', 'dibuat semahal mungkin', 'tanpa informasi apa pun', 'berukuran sebesar mungkin'], 'B', 'Mudah', 'Kemasan yang baik melindungi produk dari kerusakan sekaligus menarik minat pembeli dan memuat informasi produk.'],
            ],
        ];
    }

    private function pastikanStorageLink(): void
    {
        if (!file_exists(public_path('storage'))) {
            try {
                Artisan::call('storage:link');
            } catch (\Throwable $e) {
                // abaikan; bisa dijalankan manual: php artisan storage:link
            }
        }
    }
}
