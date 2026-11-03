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
<<<<<<< HEAD
use App\Models\Nilai;
use App\Models\TugasHarian;
=======
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
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

<<<<<<< HEAD
        // ===== Bank Soal: 5 soal pilihan ganda NYATA per mapel (45 soal total) =====
        // Soal sungguhan (bukan placeholder) sesuai mata pelajaran masing-masing; kunci jawaban & tingkat kesulitan sudah diisi.
        $dataBankSoal = [
            'Matematika' => [
                ['Hasil dari 2³ × 3² adalah ...', ['36', '48', '72', '54', '64'], 'C', 'Mudah'],
                ['Nilai x yang memenuhi persamaan 2x + 7 = 19 adalah ...', ['4', '5', '6', '7', '8'], 'C', 'Mudah'],
                ['Akar-akar persamaan kuadrat x² − 5x + 6 = 0 adalah ...', ['1 dan 6', '2 dan 3', '−2 dan −3', '2 dan −3', '−1 dan 6'], 'B', 'Sedang'],
                ['Jika f(x) = 3x − 2, maka nilai f(4) adalah ...', ['8', '12', '10', '14', '6'], 'C', 'Mudah'],
                ['Turunan pertama dari f(x) = x³ + 2x² adalah ...', ['3x² + 4x', '3x² + 2x', 'x² + 4x', '3x + 4', '2x² + 4x'], 'A', 'Sulit'],
            ],
            'Bahasa Indonesia' => [
                ['Kalimat yang penggunaan huruf kapitalnya benar adalah ...', ['Dia lahir di kota bandung.', 'Adik membaca buku di Perpustakaan.', 'Ibu pergi ke Pasar Minggu hari Senin.', 'Kami berlibur ke pulau Bali.', 'Ayah bekerja di jakarta.'], 'C', 'Mudah'],
                ['Penulisan kata baku yang benar adalah ...', ['apotik', 'apotek', 'apoteck', 'appotek', 'apotheek'], 'B', 'Mudah'],
                ['Majas pada kalimat "Angin malam berbisik lembut di telingaku" adalah ...', ['Hiperbola', 'Personifikasi', 'Metafora', 'Litotes', 'Ironi'], 'B', 'Sedang'],
                ['Gagasan pokok sebuah paragraf biasanya terdapat pada ...', ['kalimat penjelas', 'kalimat utama', 'kalimat penutup saja', 'judul bacaan', 'kata sambung'], 'B', 'Mudah'],
                ['Antonim dari kata "transparan" adalah ...', ['bening', 'tembus pandang', 'jelas', 'buram', 'cerah'], 'D', 'Sedang'],
            ],
            'Bahasa Inggris' => [
                ['She ___ to school every morning.', ['go', 'goes', 'going', 'gone', 'went'], 'B', 'Mudah'],
                ['They ___ playing football when it started to rain.', ['was', 'is', 'were', 'are', 'be'], 'C', 'Sedang'],
                ['What is the past tense of the verb "write"?', ['writed', 'written', 'wrote', 'writes', 'writing'], 'C', 'Mudah'],
                ['If I ___ rich, I would travel around the world.', ['am', 'was', 'be', 'were', 'will be'], 'D', 'Sulit'],
                ['The synonym of the word "enormous" is ...', ['tiny', 'huge', 'narrow', 'quiet', 'ancient'], 'B', 'Sedang'],
            ],
            'IPA' => [
                ['Organel sel yang berfungsi sebagai tempat berlangsungnya respirasi sel adalah ...', ['Ribosom', 'Mitokondria', 'Kloroplas', 'Lisosom', 'Nukleus'], 'B', 'Mudah'],
                ['Satuan SI untuk gaya adalah ...', ['Joule', 'Watt', 'Pascal', 'Newton', 'Ampere'], 'D', 'Mudah'],
                ['Rumus kimia dari air adalah ...', ['CO₂', 'O₂', 'NaCl', 'H₂O₂', 'H₂O'], 'E', 'Mudah'],
                ['Sebuah benda bermassa 2 kg dipercepat 3 m/s². Besar gaya yang bekerja pada benda adalah ...', ['1,5 N', '5 N', '6 N', '9 N', '12 N'], 'C', 'Sedang'],
                ['Proses pembuatan makanan pada tumbuhan hijau dengan bantuan cahaya matahari disebut ...', ['respirasi', 'transpirasi', 'fotosintesis', 'fermentasi', 'osmosis'], 'C', 'Sedang'],
            ],
            'IPS' => [
                ['Proklamasi kemerdekaan Indonesia dibacakan pada tanggal ...', ['17 Agustus 1945', '18 Agustus 1945', '1 Juni 1945', '28 Oktober 1928', '10 November 1945'], 'A', 'Mudah'],
                ['Kegiatan menyalurkan barang dari produsen ke konsumen disebut ...', ['produksi', 'distribusi', 'konsumsi', 'investasi', 'ekspor'], 'B', 'Mudah'],
                ['Organisasi pergerakan nasional pertama di Indonesia adalah ...', ['Sarekat Islam', 'Indische Partij', 'Budi Utomo', 'PNI', 'Muhammadiyah'], 'C', 'Sedang'],
                ['Menurut hukum permintaan, jika harga suatu barang naik maka jumlah barang yang diminta cenderung ...', ['naik', 'tetap', 'turun', 'tak terbatas', 'berubah-ubah'], 'C', 'Sedang'],
                ['Benua terluas di dunia adalah ...', ['Afrika', 'Amerika Utara', 'Eropa', 'Asia', 'Antartika'], 'D', 'Mudah'],
            ],
            'PPKn' => [
                ['Sila ketiga Pancasila berbunyi ...', ['Ketuhanan Yang Maha Esa', 'Kemanusiaan yang adil dan beradab', 'Persatuan Indonesia', 'Kerakyatan yang dipimpin oleh hikmat kebijaksanaan', 'Keadilan sosial bagi seluruh rakyat Indonesia'], 'C', 'Mudah'],
                ['Lambang sila kedua Pancasila adalah ...', ['bintang', 'rantai', 'pohon beringin', 'kepala banteng', 'padi dan kapas'], 'B', 'Mudah'],
                ['UUD 1945 disahkan oleh PPKI pada tanggal ...', ['17 Agustus 1945', '18 Agustus 1945', '19 Agustus 1945', '1 Juni 1945', '22 Juni 1945'], 'B', 'Sedang'],
                ['Berikut ini yang termasuk kewajiban warga negara adalah ...', ['mendapat pendidikan', 'memeluk agama', 'membayar pajak', 'mengemukakan pendapat', 'mendapat pekerjaan'], 'C', 'Sedang'],
                ['Semboyan "Bhinneka Tunggal Ika" memiliki arti ...', ['Berbeda-beda tetapi tetap satu', 'Satu nusa satu bangsa', 'Bersatu kita teguh', 'Tut Wuri Handayani', 'Gotong royong'], 'A', 'Mudah'],
            ],
            'Penjaskes' => [
                ['Dalam permainan sepak bola, jumlah pemain satu tim di lapangan adalah ...', ['9 orang', '10 orang', '11 orang', '12 orang', '15 orang'], 'C', 'Mudah'],
                ['Start yang dipakai dalam lari jarak pendek (sprint) adalah start ...', ['melayang', 'jongkok', 'berdiri', 'duduk', 'berjalan'], 'B', 'Mudah'],
                ['Ukuran lapangan bola voli adalah ...', ['18 × 9 meter', '20 × 10 meter', '15 × 8 meter', '28 × 15 meter', '24 × 12 meter'], 'A', 'Sedang'],
                ['Olahraga yang paling melatih daya tahan jantung dan paru-paru adalah ...', ['angkat beban', 'lari jarak jauh', 'lompat jauh', 'tolak peluru', 'lempar lembing'], 'B', 'Sedang'],
                ['Pemanasan sebelum berolahraga bertujuan untuk ...', ['mengurangi tenaga', 'mencegah cedera', 'menambah kelelahan', 'memperlambat detak jantung', 'membuat mengantuk'], 'B', 'Mudah'],
            ],
            'Seni Budaya' => [
                ['Alat musik tradisional Jawa Barat yang terbuat dari bambu adalah ...', ['sasando', 'angklung', 'gamelan', 'kolintang', 'tifa'], 'B', 'Mudah'],
                ['Tari Kecak berasal dari daerah ...', ['Jawa Barat', 'Bali', 'Aceh', 'Sumatera Barat', 'Papua'], 'B', 'Mudah'],
                ['Warna primer dalam seni rupa adalah ...', ['merah, kuning, biru', 'merah, hijau, biru', 'hijau, oranye, ungu', 'hitam, putih, abu-abu', 'merah, jingga, kuning'], 'A', 'Sedang'],
                ['Teknik membatik yang menggunakan cap dari tembaga disebut batik ...', ['tulis', 'cap', 'lukis', 'ikat celup', 'printing'], 'B', 'Sedang'],
                ['Tangga nada diatonis terdiri dari ... nada dasar.', ['5', '6', '7', '8', '12'], 'C', 'Sulit'],
            ],
            'Prakarya' => [
                ['Kerajinan dari tanah liat yang dibentuk lalu dibakar disebut ...', ['anyaman', 'gerabah', 'batik', 'kriya kayu', 'rajut'], 'B', 'Mudah'],
                ['Tahap pertama dalam proses membuat produk kerajinan adalah ...', ['pengemasan', 'produksi', 'perencanaan dan penentuan ide', 'pemasaran', 'evaluasi'], 'C', 'Sedang'],
                ['Bahan yang umum dipakai untuk membuat anyaman adalah ...', ['rotan, bambu, dan pandan', 'besi', 'kaca', 'plastik cair', 'beton'], 'A', 'Mudah'],
                ['Pengeringan bahan pangan dilakukan dengan tujuan ...', ['menambah kadar air', 'mengawetkan', 'mempercepat pembusukan', 'memperbanyak jumlah', 'mengubah warna'], 'B', 'Sedang'],
                ['Kemasan produk yang baik sebaiknya ...', ['menutupi seluruh produk', 'melindungi dan menarik minat pembeli', 'dibuat semahal mungkin', 'tanpa informasi apa pun', 'berukuran sebesar mungkin'], 'B', 'Mudah'],
            ],
        ];
        $jumlahSoal = 0;
        foreach ($mapelList as $mapel) {
            foreach ($dataBankSoal[$mapel->nama_mapel] ?? [] as [$tanya, $opsi, $kunci, $tingkat]) {
                BankSoal::create([
                    'mata_pelajaran_id' => $mapel->id,
                    'pertanyaan' => $tanya,
                    'pilihan_a' => $opsi[0],
                    'pilihan_b' => $opsi[1],
                    'pilihan_c' => $opsi[2],
                    'pilihan_d' => $opsi[3],
                    'pilihan_e' => $opsi[4],
                    'jawaban_benar' => $kunci,
                    'tingkat_kesulitan' => $tingkat,
                ]);
                $jumlahSoal++;
            }
        }

        // ===== Nilai + Tugas Harian: setiap siswa diberi nilai untuk SEMUA mapel =====
        // Tiap baris Nilai punya 4 TUGAS HARIAN (tabel nilai_tugas_harian). Nilai Tugas = rata-rata
        // 4 skor tugas itu, lalu Nilai Akhir = (Tugas + UTS + UAS) / 3 lewat Nilai::hitungNilaiAkhir()
        // — alur yang SAMA dengan input manual guru, jadi angkanya konsisten dengan halaman
        // "Kelola Tugas Harian". Nilai dibuat deterministik dari indeks siswa & mapel supaya tiap
        // siswa punya pola berbeda (bukan nilai sama rata).
        $semesterAktif = 'Ganjil';
        $tahunAjaranAktif = '2026/2027';
        $namaTugasHarian = ['Latihan Soal Bab 1', 'Kuis Pekan ke-3', 'Tugas Mandiri Bab 2', 'Presentasi / Praktik'];
        $offsetSkor = [-4, 2, -1, 5];
        $jumlahNilai = 0;
        $jumlahTugasHarian = 0;
        foreach ($siswaList as $si => $siswa) {
            foreach ($mapelList as $mi => $mapel) {
                $basis = 65 + (($si * 7 + $mi * 11) % 26); // sebaran dasar 65-90

                $skorTugas = [];
                foreach ($namaTugasHarian as $ti => $namaTugas) {
                    $skorTugas[$ti] = min(100, max(50, $basis + $offsetSkor[($ti + $si + $mi) % 4]));
                }
                $tugas = round(array_sum($skorTugas) / count($skorTugas), 2);
                $uts = min(100, max(50, $basis - 2 + (($mi * 3) % 8)));
                $uas = min(100, max(50, $basis + 3 + (($si * 2) % 7)));

                $nilai = Nilai::create([
                    'siswa_id' => $siswa->id,
                    'mata_pelajaran_id' => $mapel->id,
                    'semester' => $semesterAktif,
                    'tahun_ajaran' => $tahunAjaranAktif,
                    'nilai_tugas' => $tugas,
                    'nilai_uts' => $uts,
                    'nilai_uas' => $uas,
                    'nilai_akhir' => Nilai::hitungNilaiAkhir($tugas, $uts, $uas),
                ]);
                $jumlahNilai++;

                foreach ($namaTugasHarian as $ti => $namaTugas) {
                    TugasHarian::create([
                        'nilai_id' => $nilai->id,
                        'nama_tugas' => $namaTugas,
                        'skor' => $skorTugas[$ti],
                        'tanggal' => now()->subWeeks(10 - ($ti * 2))->toDateString(),
                    ]);
                    $jumlahTugasHarian++;
                }
=======
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
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
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
<<<<<<< HEAD
        $this->command->info(count($daftarAspirasi) . ' kotak aspirasi, ' . $jumlahSoal . ' soal bank soal asli (5 per mapel),');
        $this->command->info('dan ' . $jumlahNilai . ' data Nilai (semua siswa x semua mapel, semester ' . $semesterAktif . ' ' . $tahunAjaranAktif . ')');
        $this->command->info('beserta ' . $jumlahTugasHarian . ' nilai tugas harian (4 per mapel per siswa).');
=======
        $this->command->info(count($daftarAspirasi) . ' kotak aspirasi, dan ' . (count($mapelList) * 5) . ' soal bank soal (5 per mapel).');
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
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
