@extends('layouts.app')
@section('title', 'Kumpulkan Tugas')
@section('content')
{{-- href dipaksa ke tugas.saya (bukan previous default): setelah mengumpulkan,
     halaman ini redirect ke dirinya sendiri supaya riwayat langsung kelihatan,
     yang akan membuat tombol kembali versi default hanya mereload halaman ini. --}}
<x-tombol-kembali :href="route('tugas.saya')" />

<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-xl kartu"
     x-data="{
        tipe: '{{ old('tipe', $pengumpulanSaya->tipe ?? 'file') }}',
        filesPreview: [],
        modalOpen: false,
        modalUrl: null,
        modalNama: null,
        modalGambar: false,
        modalVideo: false,
        onFilesChange(event) {
            this.filesPreview = [];
            const files = Array.from(event.target.files || []);
            files.forEach(f => {
                const ext = f.name.split('.').pop().toLowerCase();
                const gambar = ['jpg','jpeg','png','gif','webp'].includes(ext);
                const video = ['mp4','mov','avi','webm','mkv'].includes(ext);
                this.filesPreview.push({
                    nama: f.name + ' (' + Math.round(f.size/1024) + ' KB)',
                    url: (gambar || video) ? URL.createObjectURL(f) : null,
                    gambar, video,
                });
            });
        },
        bukaPratinjau(p) {
            this.modalUrl = p.url;
            this.modalNama = p.nama;
            this.modalGambar = p.gambar;
            this.modalVideo = p.video;
            this.modalOpen = true;
        },
     }">
    <h2 class="text-lg font-extrabold text-ink-900">{{ $tugas->judul }}</h2>
    <p class="text-sm text-slate-500 mt-1">{{ $tugas->mataPelajaran->nama_mapel ?? '-' }} &middot; Deadline: {{ $tugas->deadline->translatedFormat('d M Y, H:i') }}</p>
    @if($tugas->deskripsi)<p class="text-sm text-slate-600 mt-3 bg-slate-50 rounded-xl p-3">{{ $tugas->deskripsi }}</p>@endif

    @if($tugas->lampiran->isNotEmpty())
        <div class="mt-3">
            <p class="text-xs font-semibold text-slate-500 mb-1.5">📎 Lampiran dari Guru:</p>
            <div class="flex flex-wrap gap-2">
                @foreach($tugas->lampiran as $l)
                    @if($l->tipe === 'file')
                        <a href="{{ asset('storage/'.$l->file_path) }}" target="_blank" class="text-xs font-semibold text-teal-600 bg-teal-50 px-3 py-1.5 rounded-lg hover:bg-teal-100">⬇️ {{ $l->file_nama_asli }}</a>
                    @else
                        <a href="{{ $l->link }}" target="_blank" class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg hover:bg-indigo-100">🔗 Buka Link</a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    @if($tugas->sudahLewatDeadline())
        <div class="mt-4 bg-amber-50 border-2 border-amber-200 text-amber-700 text-sm font-semibold rounded-xl px-4 py-3">
            ⚠️ Deadline tugas ini sudah lewat. Kamu masih bisa mengumpulkan, tapi akan ditandai "Terlambat".
        </div>
    @endif

    @if($pengumpulanSaya)
        <div class="mt-4 bg-teal-50 border-2 border-teal-200 text-teal-700 text-sm font-semibold rounded-xl px-4 py-3">
            Kamu sudah pernah mengumpulkan tugas ini pada {{ $pengumpulanSaya->dikumpulkan_pada->translatedFormat('d M Y, H:i') }}. Mengumpulkan ulang akan MENGGANTI status terkini (tapi riwayat lama tetap tersimpan di bawah).
            @if($pengumpulanSaya->tipe === 'file' && $pengumpulanSaya->files->isNotEmpty())
                <div class="mt-2 flex flex-wrap gap-1.5">
                    @foreach($pengumpulanSaya->files as $f)
                        <a href="{{ asset('storage/'.$f->file_path) }}" target="_blank" class="text-xs font-bold text-teal-800 bg-white/70 px-2.5 py-1 rounded-lg hover:bg-white">⬇️ {{ $f->file_nama_asli }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <form method="POST" action="{{ route('tugas.prosesKumpulkan', $tugas) }}" enctype="multipart/form-data" class="space-y-4 mt-5">
        @csrf
        <div>
            <label class="text-sm font-semibold text-slate-700">Jenis Pengumpulan</label>
            <div class="flex gap-2 mt-1">
                <label class="flex-1 flex items-center justify-center rounded-xl border-2 border-slate-300 py-2.5 text-sm font-semibold cursor-pointer transition"
                       :class="tipe === 'file' ? 'bg-teal-600 text-white border-teal-600' : ''">
                    <input type="radio" name="tipe" value="file" x-model="tipe" class="hidden"> 📎 Upload File
                </label>
                <label class="flex-1 flex items-center justify-center rounded-xl border-2 border-slate-300 py-2.5 text-sm font-semibold cursor-pointer transition"
                       :class="tipe === 'link' ? 'bg-teal-600 text-white border-teal-600' : ''">
                    <input type="radio" name="tipe" value="link" x-model="tipe" class="hidden"> 🔗 Link
                </label>
            </div>
        </div>

        <div x-show="tipe === 'file'">
            <label class="text-sm font-semibold text-slate-700">Berkas Tugas (boleh lebih dari 1, maks 5 file)</label>
            <p class="text-xs text-slate-400 mb-1">.pdf, .docx, gambar, .txt, video, atau .zip/.rar — maks 25MB per file.</p>
            <input type="file" name="file[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.txt,.mp4,.mov,.avi,.webm,.zip,.rar"
                   @change="onFilesChange($event)"
                   class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            @error('file')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            @error('file.*')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror

            {{-- Pratinjau SEBELUM dikirim: setiap file bisa DIKLIK untuk menampilkan
                 hasilnya secara penuh (gambar/video tampil langsung, file lain
                 tetap bisa diklik untuk melihat info & membukanya). --}}
            <div x-show="filesPreview.length > 0" x-cloak class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-2">
                <template x-for="(p, idx) in filesPreview" :key="idx">
                    <button type="button" @click="bukaPratinjau(p)"
                            class="border-2 border-dashed border-slate-200 rounded-xl p-2 hover:border-teal-400 transition text-left">
                        <template x-if="p.gambar">
                            <img :src="p.url" class="h-20 w-full object-cover rounded-lg">
                        </template>
                        <template x-if="p.video">
                            <div class="h-20 w-full rounded-lg bg-slate-800 flex items-center justify-center text-white text-2xl">🎬</div>
                        </template>
                        <template x-if="!p.gambar && !p.video">
                            <div class="h-20 w-full rounded-lg bg-slate-100 flex items-center justify-center text-2xl">📄</div>
                        </template>
                        <p class="text-[11px] text-slate-500 mt-1 truncate" x-text="p.nama"></p>
                        <p class="text-[10px] text-teal-600 font-semibold">Klik untuk pratinjau</p>
                    </button>
                </template>
            </div>
        </div>

        <div x-show="tipe === 'link'">
            <label class="text-sm font-semibold text-slate-700">Link Tugas</label>
            <input type="url" name="link" value="{{ old('link') }}" placeholder="https://..." class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            @error('link')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="text-sm font-semibold text-slate-700">Catatan (opsional)</label>
            <textarea name="catatan" rows="3" class="mt-1 w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">{{ old('catatan') }}</textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button class="bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">{{ $pengumpulanSaya ? 'Kumpulkan Ulang' : 'Kumpulkan Tugas' }}</button>
            <a href="{{ route('tugas.saya') }}" class="bg-slate-100 rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>

    {{-- Modal pratinjau penuh: klik salah satu thumbnail di atas untuk melihat
         hasilnya sebelum benar-benar dikirim. --}}
    <div x-show="modalOpen" x-cloak @click.self="modalOpen = false"
         class="fixed inset-0 bg-ink-900/70 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-4 max-w-lg w-full max-h-[85vh] overflow-auto">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-bold text-ink-900 truncate" x-text="modalNama"></p>
                <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold px-2">✕</button>
            </div>
            <template x-if="modalGambar">
                <img :src="modalUrl" class="w-full rounded-xl">
            </template>
            <template x-if="modalVideo">
                <video :src="modalUrl" controls class="w-full rounded-xl"></video>
            </template>
            <template x-if="!modalGambar && !modalVideo">
                <p class="text-sm text-slate-500">File ini tidak bisa ditampilkan langsung di sini, tapi akan ikut terkirim saat kamu menekan "Kumpulkan Tugas".</p>
            </template>
        </div>
    </div>

    {{-- Riwayat semua pengumpulan sebelumnya untuk tugas ini --}}
    <div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 kartu mt-5" x-data="{}">
        <p class="font-bold text-ink-900 mb-3">🕑 Riwayat Pengumpulan Saya</p>
        @forelse($riwayat as $r)
            <div class="flex items-start justify-between gap-3 py-2.5 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-ink-900">{{ $r->dikumpulkan_pada->translatedFormat('d M Y, H:i') }}</p>
                    @if($r->catatan)<p class="text-xs text-slate-500 truncate">{{ $r->catatan }}</p>@endif
                </div>
                <div class="shrink-0 flex flex-col items-end gap-1">
                    @if($r->tipe === 'file' && $r->files->isNotEmpty())
                        @foreach($r->files as $f)
                            <a href="{{ asset('storage/'.$f->file_path) }}" target="_blank" class="text-xs font-semibold text-teal-600 hover:underline">⬇️ {{ $f->ekstensiFile() }}</a>
                        @endforeach
                    @elseif($r->tipe === 'link')
                        <a href="{{ $r->link }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">🔗 Buka Link</a>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400 text-center py-6">Belum ada riwayat pengumpulan untuk tugas ini.</p>
        @endforelse
    </div>
</div>
@endsection
