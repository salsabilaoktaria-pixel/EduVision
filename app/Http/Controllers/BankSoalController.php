<?php

namespace App\Http\Controllers;

use App\Models\BankSoal;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BankSoalController extends Controller
{
    public function index(Request $request)
    {
        $query = BankSoal::with('mataPelajaran');
        if ($request->filled('mapel_id')) {
            $query->where('mata_pelajaran_id', $request->mapel_id);
        }
        $soal = $query->latest()->paginate(10)->withQueryString();
        $mapel = MataPelajaran::all();
        return view('banksoal.index', compact('soal', 'mapel'));
    }

    public function create()
    {
        $mapel = MataPelajaran::all();
        return view('banksoal.create', compact('mapel'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'pertanyaan' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'pilihan_a' => 'required|string',
            'pilihan_b' => 'required|string',
            'pilihan_c' => 'required|string',
            'pilihan_d' => 'required|string',
            'pilihan_e' => 'nullable|string',
            'jawaban_benar' => 'required|in:A,B,C,D,E',
            'tingkat_kesulitan' => 'required|string',
        ]);
        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('banksoal', 'public');
        }
        BankSoal::create($validated);
        return redirect()->route('banksoal.index')->with('sukses', 'Soal berhasil ditambahkan ke bank soal.');
    }

    public function edit(BankSoal $banksoal)
    {
        $mapel = MataPelajaran::all();
        return view('banksoal.edit', compact('banksoal', 'mapel'));
    }

    public function update(Request $request, BankSoal $banksoal)
    {
        $validated = $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'pertanyaan' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'hapus_gambar' => 'nullable|boolean',
            'pilihan_a' => 'required|string',
            'pilihan_b' => 'required|string',
            'pilihan_c' => 'required|string',
            'pilihan_d' => 'required|string',
            'pilihan_e' => 'nullable|string',
            'jawaban_benar' => 'required|in:A,B,C,D,E',
            'tingkat_kesulitan' => 'required|string',
        ]);
        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('banksoal', 'public');
        } elseif ($request->boolean('hapus_gambar')) {
            $validated['gambar'] = null;
        }
        unset($validated['hapus_gambar']);
        $banksoal->update($validated);
        return redirect()->route('banksoal.index')->with('sukses', 'Soal berhasil diperbarui.');
    }

    public function destroy(BankSoal $banksoal)
    {
        $banksoal->delete();
        return back()->with('sukses', 'Soal berhasil dihapus.');
    }

    // Halaman terpisah untuk import file
    public function importForm()
    {
        $mapel = MataPelajaran::all();
        return view('banksoal.import', compact('mapel'));
    }

    /**
     * IMPORT SOAL DARI FILE .docx / .txt / .pdf.
     *
     * PEMBARUAN: sebelumnya import GAGAL TOTAL kalau teks di file tidak persis
     * mengikuti template ("baris A./B./C./D./JAWABAN:", dipisah baris kosong per
     * soal). Sekarang parser jauh lebih fleksibel (lihat parseDanSimpanSoal):
     * - Boleh ada baris kosong pemisah soal, boleh juga TIDAK ADA sama sekali.
     * - Label pilihan boleh A./A)/A: huruf besar atau kecil.
     * - Kata kunci jawaban boleh "JAWABAN:", "Kunci:", "Kunci Jawaban:", "Jawaban Benar:", dst.
     * - Kalau file TIDAK punya kunci jawaban sama sekali, soal tetap diimpor
     *   (kunci jawabannya bisa dilengkapi belakangan lewat halaman Edit).
     * - Pertanyaan/pilihan boleh menyambung ke beberapa baris (word-wrap).
     * - Nomor soal ("1.", "Soal 2)") boleh ada, boleh juga tidak ada.
     *
     * Ekstraksi teks:
     * - .txt  -> dibaca langsung, paling akurat.
     * - .docx -> dibaca dari word/document.xml (docx = file ZIP), termasuk isi tabel.
     * - .pdf  -> dibaca pakai library smalot/pdfparser (jauh lebih akurat dari
     *            parser manual, termasuk PDF dengan font ter-embed/terkompresi).
     *            PDF hasil SCAN/FOTO (gambar, bukan teks asli) tetap tidak bisa
     *            dibaca — itu butuh OCR, di luar cakupan fitur ini.
     */
    public function import(Request $request)
    {
        // Sengaja TIDAK pakai rule 'mimes:docx,pdf,txt' bawaan Laravel karena file .docx
        // sering salah terdeteksi sebagai "application/zip" oleh pengecekan isi konten,
        // sehingga file yang valid malah ditolak. Makanya di sini dicek dari EKSTENSI saja.
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'file_soal' => ['required', 'file', 'max:10240', function ($attribute, $value, $fail) {
                $ext = strtolower($value->getClientOriginalExtension());
                if (!in_array($ext, ['docx', 'txt', 'pdf'])) {
                    $fail('File harus berformat .docx, .txt, atau .pdf (yang diupload: .' . $ext . ').');
                }
            }],
        ]);

        $file = $request->file('file_soal');
        $ext = strtolower($file->getClientOriginalExtension());
        $teks = '';

        try {
            if ($ext === 'txt') {
                $teks = file_get_contents($file->getRealPath());
            } elseif ($ext === 'docx') {
                $teks = $this->ekstrakTeksDocx($file->getRealPath());
            } elseif ($ext === 'pdf') {
                $teks = $this->ekstrakTeksPdf($file->getRealPath());
            }
        } catch (\Exception $e) {
            Log::error('Import soal gagal baca file: ' . $e->getMessage());
            return back()->withInput()->with('gagal', 'Gagal membaca file: ' . $e->getMessage());
        }

        if (trim((string) $teks) === '') {
            return back()->withInput()->with('gagal', "Tidak ada teks yang berhasil dibaca dari file .$ext ini. Kalau file PDF hasil scan/foto, sistem tidak bisa membacanya (bukan teks asli) — coba pakai file .docx atau .txt.");
        }

        $hasil = $this->parseDanSimpanSoal($teks, $request->mata_pelajaran_id);

        if ($hasil['jumlah'] === 0) {
            Log::info('Import soal: 0 soal terbaca. Isi teks hasil ekstraksi: ' . substr($teks, 0, 2000));
            // Tampilkan CUPLIKAN teks hasil ekstraksi supaya kelihatan PERSIS apa
            // yang berhasil dibaca sistem dari file ini — kalau isinya kosong/
            // acak-acakan, itu tandanya masalahnya di ekstraksi file (mis. PDF
            // hasil scan), bukan di format penulisan soalnya.
            $cuplikan = trim(substr($teks, 0, 400));
            $cuplikan = $cuplikan === '' ? '(kosong — tidak ada teks yang terbaca sama sekali)' : $cuplikan;
            return back()->withInput()->with('gagal', "File berhasil dibaca, tapi tidak ada soal yang berhasil dikenali. Pastikan tiap soal minimal punya 4 pilihan berlabel A/B/C/D (nomor soal & kunci jawaban boleh ada, boleh juga tidak).\n\nCuplikan teks yang berhasil dibaca dari file Anda (400 karakter pertama), supaya bisa dicek bagian mana yang tidak dikenali:\n\"$cuplikan\"");
        }

        $pesan = "{$hasil['jumlah']} soal berhasil diimpor dari file .$ext.";
        if ($hasil['tanpa_jawaban'] > 0) {
            $pesan .= " {$hasil['tanpa_jawaban']} di antaranya belum punya kunci jawaban terdeteksi — silakan lengkapi lewat tombol Edit.";
        }

        return redirect()->route('banksoal.index')->with('sukses', $pesan);
    }

    /**
     * Baca satu file dari dalam arsip ZIP TANPA ekstensi PHP "zip" (ZipArchive).
     * Cukup pakai ekstensi "zlib" (gzinflate) untuk dekompresi — ekstensi ini
     * jauh lebih sering aktif di hosting dibanding ext-zip. Dipakai untuk
     * baca file .docx (yang formatnya adalah arsip ZIP) tanpa bergantung pada
     * ext-zip sama sekali. Sudah diuji terhadap file .docx asli dari
     * LibreOffice/Word dan hasilnya identik dengan `unzip` biasa.
     */
    private function bacaFileDariZipManual(string $zipPath, string $namaFileDicari): ?string
    {
        $data = @file_get_contents($zipPath);
        if ($data === false || $data === '') return null;

        // 1) Cari End Of Central Directory record (signature PK\x05\x06) dari
        //    BELAKANG file, karena boleh ada "comment" dengan panjang bervariasi.
        $posEocd = strrpos($data, "\x50\x4b\x05\x06");
        if ($posEocd === false) return null;

        $jumlahEntri = unpack('v', substr($data, $posEocd + 10, 2))[1] ?? 0;
        $offsetCentralDir = unpack('V', substr($data, $posEocd + 16, 4))[1] ?? 0;

        // 2) Jalan di Central Directory, cari entri dengan nama yang cocok persis
        $pos = $offsetCentralDir;
        for ($i = 0; $i < $jumlahEntri; $i++) {
            if (substr($data, $pos, 4) !== "\x50\x4b\x01\x02") break;

            $metodeKompresi = unpack('v', substr($data, $pos + 10, 2))[1];
            $ukuranTerkompresi = unpack('V', substr($data, $pos + 20, 4))[1];
            $panjangNama = unpack('v', substr($data, $pos + 28, 2))[1];
            $panjangExtra = unpack('v', substr($data, $pos + 30, 2))[1];
            $panjangKomentar = unpack('v', substr($data, $pos + 32, 2))[1];
            $offsetLocalHeader = unpack('V', substr($data, $pos + 42, 4))[1];
            $nama = substr($data, $pos + 46, $panjangNama);

            if ($nama === $namaFileDicari) {
                // 3) Baca Local File Header di offset-nya untuk tahu persis di
                //    mana data mentahnya mulai (panjang field di local header
                //    bisa berbeda dari central directory).
                $panjangNamaLocal = unpack('v', substr($data, $offsetLocalHeader + 26, 2))[1];
                $panjangExtraLocal = unpack('v', substr($data, $offsetLocalHeader + 28, 2))[1];
                $offsetData = $offsetLocalHeader + 30 + $panjangNamaLocal + $panjangExtraLocal;
                $isiTerkompresi = substr($data, $offsetData, $ukuranTerkompresi);

                if ($metodeKompresi === 0) {
                    return $isiTerkompresi; // 0 = disimpan mentah tanpa kompresi
                } elseif ($metodeKompresi === 8) {
                    return @gzinflate($isiTerkompresi) ?: null; // 8 = deflate
                }
                return null; // metode kompresi lain (sangat jarang dipakai docx)
            }

            $pos += 46 + $panjangNama + $panjangExtra + $panjangKomentar;
        }

        return null;
    }

    // Ekstrak teks dari file .docx TANPA bergantung ke ekstensi PHP "zip"
    // (ZipArchive) — sebelumnya kalau ext-zip tidak aktif di hosting, import
    // langsung gagal total. File .docx sebenarnya cuma arsip ZIP biasa, jadi
    // di sini kita baca strukturnya secara manual (cari Central Directory,
    // temukan entri "word/document.xml", lalu dekompresi pakai ekstensi
    // "zlib" yang jauh lebih sering aktif dibanding ext-zip di hosting).
    // Sudah diuji dengan file .docx asli dari LibreOffice — hasilnya identik
    // dengan `unzip` biasa.
    private function ekstrakTeksDocx($path)
    {
        $xml = $this->bacaFileDariZipManual($path, 'word/document.xml');

        if ($xml === null && class_exists(\ZipArchive::class)) {
            // Fallback kalau ext-zip TERNYATA aktif dan parser manual di atas
            // entah kenapa gagal (mis. format ZIP yang tidak lazim) — masih
            // dicoba lewat ZipArchive sebagai jaring pengaman kedua.
            $zip = new \ZipArchive();
            if ($zip->open($path) === true) {
                $xml = $zip->getFromName('word/document.xml') ?: null;
                $zip->close();
            }
        }

        if ($xml === null) {
            throw new \Exception('File .docx tidak bisa dibaca — kemungkinan file rusak, terenkripsi/dikunci password, atau bukan format .docx asli (coba buka & simpan ulang filenya dari Word/LibreOffice).');
        }

        // Ganti penanda struktur (akhir paragraf, baris baru, tab, akhir baris/sel tabel)
        // jadi baris baru SEBELUM tag di-strip, supaya struktur baris (buat parsing soal)
        // tetap terjaga — termasuk kalau soalnya ditulis di dalam tabel Word.
        $xml = str_replace(
            ['</w:p>', '<w:br/>', '<w:br />', '<w:tab/>', '</w:tr>', '</w:tc>'],
            ["\n", "\n", "\n", "\t", "\n", "\t"],
            $xml
        );
        $teks = strip_tags($xml);
        $teks = html_entity_decode($teks, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $teks = str_replace("\xC2\xA0", ' ', $teks); // spasi non-breaking dari Word

        return $teks;
    }

    // Ekstrak teks dari file .pdf memakai library smalot/pdfparser.
    // Jauh lebih andal dibanding parser manual: menangani stream terkompresi,
    // font ter-embed/CID, dan berbagai encoding teks yang umum dipakai
    // Word/LibreOffice/Google Docs saat "Export/Save as PDF".
    private function ekstrakTeksPdf($path)
    {
        if (!class_exists(\Smalot\PdfParser\Parser::class)) {
            throw new \Exception('Library pembaca PDF (smalot/pdfparser) belum terpasang di server. Jalankan "composer install" dulu, lalu coba lagi.');
        }

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($path);
            $teks = $pdf->getText();
        } catch (\Throwable $e) {
            throw new \Exception('PDF tidak bisa dibaca (kemungkinan PDF hasil scan/gambar, terenkripsi/dikunci, atau file rusak). Coba pakai file .docx atau .txt.');
        }

        return $teks;
    }

    /**
     * Pecah SATU baris yang ternyata berisi beberapa pilihan sekaligus (mis.
     * "A. 6   B. 7   C. 8   D. 9" jadi 4 baris terpisah). Aturan biar tidak
     * salah tangkap kata biasa di tengah kalimat:
     * - Huruf BESAR (A-D) boleh dipisah cuma 1 spasi, karena pola "X) teks"
     *   sangat khas penanda pilihan dan jarang muncul kebetulan di kalimat biasa.
     * - Huruf KECIL (a-d) harus dipisah TAB atau 2-atau-lebih spasi, supaya
     *   tidak salah menangkap kata seperti "a." atau "b." di tengah kalimat.
     * - Kalau cuma ketemu 0-1 kecocokan, baris dikembalikan apa adanya (tidak
     *   ada yang perlu dipecah).
     */
    private function pecahBarisJikaMultiOpsi(string $baris): array
    {
        $pola = '/(?:^|\t|(?<=  )|(?<= ))([A-E][\.\)\:])\s*|(?:^|\t|(?<=  ))([a-e][\.\)\:])\s*/u';
        if (!preg_match_all($pola, $baris, $m, PREG_OFFSET_CAPTURE) || count($m[0]) < 2) {
            return [$baris];
        }

        $potongan = [];
        for ($i = 0; $i < count($m[0]); $i++) {
            $mulai = $m[0][$i][1];
            $akhir = $i + 1 < count($m[0]) ? $m[0][$i + 1][1] : strlen($baris);
            $potongan[] = trim(substr($baris, $mulai, $akhir - $mulai));
        }
        return $potongan;
    }

    /**
     * Parser soal yang FLEKSIBEL — dibaca baris per baris seperti orang membaca,
     * bukan mencocokkan satu pola kaku ke seluruh blok teks sekaligus. Karena itu
     * bisa menangani berbagai gaya penulisan soal sekaligus (lihat komentar di
     * method import() untuk daftar variasi yang didukung).
     */
    private function parseDanSimpanSoal($teks, $mapelId)
    {
        $teks = str_replace(["\r\n", "\r"], "\n", $teks);
        // Pecah dulu baris yang berisi BEBERAPA pilihan sekaligus dalam satu
        // baris (mis. "A. 6   B. 7   C. 8   D. 9" — umum kalau soal ditulis
        // pakai tabel Word lalu diekstrak jadi teks) supaya tiap pilihan tetap
        // bisa dikenali satu-per-satu oleh parser di bawah, yang aturannya
        // satu baris = satu pilihan.
        $lines = [];
        foreach (explode("\n", $teks) as $baris) {
            foreach ($this->pecahBarisJikaMultiOpsi($baris) as $potongan) {
                $lines[] = $potongan;
            }
        }

        $reOption = '/^\s*(?:pilihan\s*)?([A-Ea-e])(?:[\.\)\:]|\s)+(.+)$/u';
        $reAnswer = '/^\s*(?:JAWABAN(?:\s*BENAR)?(?:NYA)?|KUNCI(?:\s*JAWABAN)?|ANSWER|ANS|KEY)\s*[:\-\.]?\s*([A-Ea-e])\b/iu';
        $reQuestionStart = '/^\s*(?:no\.?|soal|pertanyaan)?\s*(\d{1,3})\s*[\.\)\-\:]\s+(.*)$/iu';
        $huruf5 = ['A', 'B', 'C', 'D', 'E'];

        // TEMUAN PENTING dari soal ujian ASLI sekolah: standar soal Indonesia itu
        // 5 PILIHAN (A-E) dan seringkali ditulis TANPA label sama sekali — cuma
        // baris teks pilihan berurutan, lalu baris "JAWABAN: <huruf>". Contoh nyata:
        //   Kata sejarah berasal dari Bahasa Arab yaitu syajaratun yang berarti ….
        //   Masa lalu
        //   Pohon
        //   Asal usul
        //   Kenangan
        //   silsilah
        // Makanya parser ini punya DUA cara mengenali pilihan sekaligus:
        // (1) berlabel A/B/C/D/E seperti sebelumnya, ATAU
        // (2) tanpa label — dikumpulkan berurutan (4 atau 5 baris) begitu baris
        //     PERTANYAAN berakhir dengan "?", ":", atau "…" (titik-titik) — pertanda
        //     baris berikutnya adalah daftar pilihan, bukan lanjutan kalimat.
        $akhirPertanyaan = fn ($t) => (bool) preg_match('/[\?:]$|\.{2,}$|…\.?$/u', rtrim($t));

        $freshCurrent = fn () => [
            'pertanyaan' => [],
            'opsi' => ['A' => null, 'B' => null, 'C' => null, 'D' => null, 'E' => null],
            'opsi_urut' => [], // dipakai kalau pilihannya TANPA label
            'jawaban' => null,
        ];

        $isComplete = function ($c) {
            if (trim(implode(' ', $c['pertanyaan'])) === '') return false;
            $labelLengkap = true;
            foreach (['A', 'B', 'C', 'D'] as $h) { // E opsional (soal 4 pilihan tetap sah)
                if ($c['opsi'][$h] === null || trim($c['opsi'][$h]) === '') { $labelLengkap = false; break; }
            }
            $urutLengkap = count($c['opsi_urut']) >= 4 && count($c['opsi_urut']) <= 5;
            return $labelLengkap || $urutLengkap;
        };

        $jumlah = 0;
        $tanpaJawaban = 0;
        $current = $freshCurrent();
        $state = 'question'; // question | options | opsi_polos
        $lastOptionLetter = null;

        $simpanJikaLengkap = function () use (&$current, $isComplete, $mapelId, $huruf5, &$jumlah, &$tanpaJawaban) {
            if ($current !== null && $isComplete($current)) {
                // Kalau dikumpulkan tanpa label, petakan opsi_urut ke A/B/C/D/(E) dulu.
                if (!array_filter($current['opsi']) && count($current['opsi_urut']) >= 4) {
                    foreach (array_slice($current['opsi_urut'], 0, 5) as $i => $isiOpsi) {
                        $current['opsi'][$huruf5[$i]] = $isiOpsi;
                    }
                }

                BankSoal::create([
                    'mata_pelajaran_id' => $mapelId,
                    'pertanyaan' => trim(implode(' ', $current['pertanyaan'])),
                    'pilihan_a' => trim($current['opsi']['A']),
                    'pilihan_b' => trim($current['opsi']['B']),
                    'pilihan_c' => trim($current['opsi']['C']),
                    'pilihan_d' => trim($current['opsi']['D']),
                    'pilihan_e' => $current['opsi']['E'] !== null ? trim($current['opsi']['E']) : null,
                    'jawaban_benar' => $current['jawaban'],
                    'tingkat_kesulitan' => 'Sedang',
                ]);
                $jumlah++;
                if ($current['jawaban'] === null) $tanpaJawaban++;
            }
            $current = null;
        };

        foreach ($lines as $line) {
            $trim = trim($line);
            if ($trim === '') continue; // baris kosong dilewati, BUKAN wajib jadi pemisah soal

            // 1) Baris kunci jawaban -> menutup soal yang sedang dikumpulkan
            if (preg_match($reAnswer, $trim, $m)) {
                if ($current !== null) {
                    $current['jawaban'] = strtoupper($m[1]);
                    $simpanJikaLengkap();
                }
                $current = $freshCurrent();
                $state = 'question';
                $lastOptionLetter = null;
                continue;
            }

            // 2) Baris pilihan berlabel A/B/C/D/E
            if (preg_match($reOption, $trim, $m)) {
                $letter = strtoupper($m[1]);
                $isi = trim($m[2]);

                // Kalau opsi "A" muncul lagi padahal soal saat ini sudah lengkap
                // (tanpa ada baris kunci jawaban di antaranya), berarti ini soal BARU.
                if ($letter === 'A' && $current !== null && $isComplete($current)) {
                    $simpanJikaLengkap();
                    $current = $freshCurrent();
                }
                if ($current === null) $current = $freshCurrent();

                $current['opsi'][$letter] = $isi;
                $state = 'options';
                $lastOptionLetter = $letter;
                continue;
            }

            // 3) Baris nomor soal baru (mis. "1.", "2)", "Soal 3.")
            if (preg_match($reQuestionStart, $trim, $m)) {
                if ($current !== null && (trim(implode(' ', $current['pertanyaan'])) !== '' || $current['opsi']['A'] !== null || count($current['opsi_urut']) > 0)) {
                    $simpanJikaLengkap();
                    $current = $freshCurrent();
                }
                if ($current === null) $current = $freshCurrent();
                $current['pertanyaan'][] = trim($m[2]);
                $state = 'question';
                $lastOptionLetter = null;
                continue;
            }

            // 4) Baris polos: lanjutan pilihan berlabel (word-wrap), pilihan TANPA
            //    label, atau bagian pertanyaan — tergantung state saat ini.
            if ($current === null) $current = $freshCurrent();

            if ($state === 'options' && $lastOptionLetter !== null) {
                $current['opsi'][$lastOptionLetter] = trim($current['opsi'][$lastOptionLetter] . ' ' . $trim);
            } elseif ($state === 'opsi_polos') {
                if (count($current['opsi_urut']) >= 5) {
                    // Sudah 5 opsi tapi masih ada baris polos lagi & belum ketemu
                    // JAWABAN — berarti ini soal BARU tanpa nomor. Finalisasi yang
                    // lama, baris ini jadi AWAL PERTANYAAN BARU (bukan opsi lagi).
                    $simpanJikaLengkap();
                    $current = $freshCurrent();
                    $current['pertanyaan'][] = $trim;
                    $state = $akhirPertanyaan($trim) ? 'opsi_polos' : 'question';
                } else {
                    $current['opsi_urut'][] = $trim;
                }
            } else {
                // state === 'question': cegah kop surat/instruksi panjang di awal
                // dokumen ikut menumpuk jadi bagian pertanyaan — kalau sudah lebih
                // dari 3 baris tanpa tanda ini pertanyaan sungguhan, buang yang lama.
                if (count($current['pertanyaan']) >= 3) {
                    $current['pertanyaan'] = [];
                }
                // Baris sebelumnya yang sudah berupa kalimat lengkap (berakhir satu
                // titik, mis. "Petunjuk: Pilihlah jawaban yang paling benar.") bukan
                // bagian dari soal berikutnya — soal yang terpotong di tengah kalimat
                // (word-wrap) tidak berakhir dengan titik, jadi tidak terpengaruh.
                if (!empty($current['pertanyaan'])) {
                    $terakhir = rtrim(end($current['pertanyaan']));
                    if (preg_match('/[^\.]\.$/u', $terakhir) && !preg_match('/…\.?$/u', $terakhir)) {
                        $current['pertanyaan'] = [];
                    }
                }
                $current['pertanyaan'][] = $trim;
                if ($akhirPertanyaan($trim)) {
                    // Baris pertanyaan berakhir dengan "?"/":"/"…" -> baris
                    // berikutnya kemungkinan besar adalah pilihan TANPA label.
                    $state = 'opsi_polos';
                }
            }
        }

        // Soal terakhir di file mungkin tidak diikuti baris kunci jawaban
        $simpanJikaLengkap();

        return ['jumlah' => $jumlah, 'tanpa_jawaban' => $tanpaJawaban];
    }

    // Latihan ujian: siswa mengerjakan soal acak dari 1 mapel.
    // Soal yang kunci jawabannya belum dilengkapi (hasil import tanpa kunci)
    // sengaja TIDAK diikutkan di sini, supaya siswa tidak dirugikan oleh
    // soal yang jawaban benarnya belum ada.
    public function latihan(Request $request)
    {
        $mapel = MataPelajaran::all();
        $soal = null;
        if ($request->filled('mapel_id')) {
            $soal = BankSoal::where('mata_pelajaran_id', $request->mapel_id)
                ->whereNotNull('jawaban_benar')
                ->inRandomOrder()->limit(10)->get();
        }
        return view('banksoal.latihan', compact('mapel', 'soal'));
    }

    public function koreksi(Request $request)
    {
        $jawabanUser = $request->input('jawaban', []); // [soal_id => 'A']
        $benar = 0;
        $detail = [];

        foreach ($jawabanUser as $soalId => $jawaban) {
            $soal = BankSoal::find($soalId);
            if (!$soal) continue;
            $isBenar = $soal->jawaban_benar === $jawaban;
            if ($isBenar) $benar++;
            $detail[] = [
                'pertanyaan' => $soal->pertanyaan,
                'gambar' => $soal->gambar,
                'pilihan_a' => $soal->pilihan_a, 'pilihan_b' => $soal->pilihan_b,
                'pilihan_c' => $soal->pilihan_c, 'pilihan_d' => $soal->pilihan_d, 'pilihan_e' => $soal->pilihan_e,
                'jawaban_user' => $jawaban,
                'jawaban_benar' => $soal->jawaban_benar,
                'benar' => $isBenar,
            ];
        }

        $total = count($jawabanUser);
        $skor = $total > 0 ? round(($benar / $total) * 100, 2) : 0;

        session(['hasil_latihan' => compact('benar', 'total', 'skor', 'detail')]);

        return redirect()->route('banksoal.hasil');
    }

    public function hasil()
    {
        $data = session('hasil_latihan');
        if (!$data) {
            return redirect()->route('banksoal.latihan');
        }
        return view('banksoal.hasil', $data);
    }
}
