@extends('layouts.app')
@section('title', 'Detail Prestasi')
@section('content')
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 max-w-3xl kartu">
    <div class="flex items-start justify-between mb-6">
        <div>
            <h2 class="text-xl font-extrabold text-ink-900">Detail Prestasi</h2>
            <p class="text-sm text-slate-500 mt-1">{{ $prestasi->siswa->nama ?? '-' }} - {{ $prestasi->nama_prestasi }}</p>
        </div>
        <a href="{{ route('prestasi.index') }}" class="bg-slate-100 text-slate-700 rounded-xl px-4 py-2 text-sm font-semibold hover:bg-slate-200 transition">← Kembali</a>
    </div>

    <div class="grid sm:grid-cols-2 gap-4 mb-6">
        <div>
            <label class="text-xs text-slate-500 font-semibold uppercase tracking-wide">Siswa</label>
            <p class="text-sm font-medium text-ink-900 mt-1">{{ $prestasi->siswa->nama ?? '-' }}</p>
        </div>
        <div>
            <label class="text-xs text-slate-500 font-semibold uppercase tracking-wide">Kelas</label>
            <p class="text-sm font-medium text-ink-900 mt-1">{{ $prestasi->siswa->kelas->nama_kelas ?? '-' }}</p>
        </div>
        <div>
            <label class="text-xs text-slate-500 font-semibold uppercase tracking-wide">Nama Prestasi</label>
            <p class="text-sm font-medium text-ink-900 mt-1">{{ $prestasi->nama_prestasi }}</p>
        </div>
        <div>
            <label class="text-xs text-slate-500 font-semibold uppercase tracking-wide">Tingkat</label>
            <p class="text-sm font-medium text-ink-900 mt-1">{{ $prestasi->tingkat }}</p>
        </div>
        <div>
            <label class="text-xs text-slate-500 font-semibold uppercase tracking-wide">Peringkat</label>
            <p class="text-sm font-medium text-ink-900 mt-1">{{ $prestasi->peringkat ?? '-' }}</p>
        </div>
        <div>
            <label class="text-xs text-slate-500 font-semibold uppercase tracking-wide">Tanggal</label>
            <p class="text-sm font-medium text-ink-900 mt-1">{{ $prestasi->tanggal }}</p>
        </div>
        <div class="sm:col-span-2">
            <label class="text-xs text-slate-500 font-semibold uppercase tracking-wide">File Bukti</label>
            @if($prestasi->file_bukti)
                <div class="mt-2">
                    @if(Str::endsWith($prestasi->file_bukti, '.pdf'))
                        <a href="{{ asset('storage/' . $prestasi->file_bukti) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-700 rounded-xl border border-red-100 hover:bg-red-100 transition">
                            <span>📄</span> Lihat PDF
                        </a>
                    @else
                        <img src="{{ asset('storage/' . $prestasi->file_bukti) }}" alt="Bukti Prestasi" class="max-w-xs max-h-64 rounded-lg border-2 border-slate-300 cursor-pointer" onclick="showImageModal('{{ asset('storage/' . $prestasi->file_bukti) }}')">
                    @endif
                </div>
            @else
                <p class="text-sm text-slate-400 mt-1">Tidak ada file bukti</p>
            @endif
        </div>
    </div>

    <div class="flex gap-3 mt-6 pt-4 border-t border-slate-100">
        <a href="{{ route('prestasi.edit', $prestasi) }}" class="bg-amber-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-amber-700 transition tombol-aktif">Edit</a>
        <form method="POST" action="{{ route('prestasi.destroy', $prestasi) }}" onsubmit="return confirm('Hapus prestasi ini?')">
            @csrf @method('DELETE')
            <button class="bg-rose-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-rose-700 transition tombol-aktif">Hapus</button>
        </form>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black/80 z-50 hidden items-center justify-center" onclick="hideImageModal()">
    <img id="modalImage" src="" alt="Bukti Prestasi" class="max-w-[90%] max-h-[90%] rounded-lg shadow-2xl">
</div>

<script>
function showImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.remove('hidden');
    document.getElementById('imageModal').classList.add('flex');
}

function hideImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.getElementById('imageModal').classList.remove('flex');
    document.getElementById('modalImage').src = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideImageModal();
    }
});
</script>
@endsection