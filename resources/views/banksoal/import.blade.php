@extends('layouts.app')
@section('title', 'Import Soal dari File')
@section('content')
<x-tombol-kembali :href="route('banksoal.index')" />

<div class="flex gap-2 mb-5 flex-wrap">
    <a href="{{ route('banksoal.create') }}" class="px-4 py-2.5 text-sm font-bold rounded-xl bg-white border-2 border-slate-300 text-slate-600 hover:bg-slate-50">✍️ Buat Manual</a>
    <span class="px-4 py-2.5 text-sm font-bold rounded-xl bg-teal-600 text-white">📁 Import File</span>
</div>

<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-2xl kartu">
    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 mb-4 text-xs text-indigo-700">
        <p class="font-bold mb-1">Format yang didukung: .docx, .txt, dan .pdf — fleksibel, tidak harus persis</p>
        <p class="mb-2">Sistem akan mencoba mengenali soal secara otomatis. Yang wajib ada cuma <b>4 pilihan berlabel A/B/C/D</b> untuk tiap soal. Selebihnya bebas:</p>
        <ul class="list-disc pl-4 space-y-0.5 mb-2">
            <li>Boleh dipisah baris kosong antar soal, boleh juga <b>tidak</b> dipisah sama sekali.</li>
            <li>Label pilihan boleh <b>A.</b> / <b>A)</b> / <b>A:</b> / bahkan cuma <b>A</b> + spasi, huruf besar atau kecil.</li>
            <li>Nomor soal boleh ada ("1.", "Soal 2)") atau tidak ada sama sekali.</li>
            <li>Kunci jawaban boleh ditulis "JAWABAN:", "Kunci:", "Kunci Jawaban:", "Jawaban Benar:", dll — atau <b>tidak ditulis sama sekali</b> (nanti tinggal dilengkapi manual lewat tombol Edit).</li>
            <li>Pertanyaan/pilihan boleh menyambung ke beberapa baris, tidak harus satu baris.</li>
            <li><b>Pilihan boleh TANPA label sama sekali</b> (langsung 4 atau 5 baris jawaban berurutan setelah kalimat soal yang diakhiri "?" atau "…."), lalu baris <b>JAWABAN: C</b>. Mendukung <b>5 pilihan (A–E)</b> sesuai standar ujian.</li>
            <li>Kop surat/petunjuk pengerjaan di awal dokumen otomatis diabaikan.</li>
        </ul>
        <pre class="bg-white rounded-lg p-2 mt-2 text-[11px] whitespace-pre-wrap">1. Ibu kota Indonesia adalah?
A. Bandung
B. Jakarta
C. Surabaya
D. Medan
JAWABAN: B</pre>
    </div>

    <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 mb-4 text-xs text-amber-700 space-y-1">
        <p>📌 <b>.txt</b> — paling akurat, dibaca langsung apa adanya.</p>
        <p>📌 <b>.docx</b> — dibaca otomatis termasuk isi tabel, tidak butuh install apapun. Pastikan filenya benar-benar Word (bukan hasil "Save As PDF" lalu diganti nama ekstensinya).</p>
        <p>📌 <b>.pdf</b> — dibaca pakai library pembaca PDF khusus, akurat untuk PDF yang dibuat langsung dari Word/Google Docs/LibreOffice maupun PDF dengan font ter-embed. PDF hasil <b>scan atau foto</b> tetap tidak bisa dibaca (isinya gambar, bukan teks asli, butuh OCR) — untuk kasus itu gunakan .docx atau .txt.</p>
    </div>

    <form method="POST" action="{{ route('banksoal.import') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div><label class="text-sm font-semibold text-slate-700">Mata Pelajaran</label>
            <select name="mata_pelajaran_id" required class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="">-- Pilih Mapel --</option>
                @foreach($mapel as $m)<option value="{{ $m->id }}" @selected(old('mata_pelajaran_id')==$m->id)>{{ $m->nama_mapel }}</option>@endforeach
            </select>
        </div>
        <div><label class="text-sm font-semibold text-slate-700">Pilih File (.docx / .txt / .pdf)</label><input type="file" name="file_soal" accept=".docx,.txt,.pdf" required class="mt-1 w-full text-sm"></div>
        <button class="bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Import Soal</button>
    </form>
</div>
@endsection
