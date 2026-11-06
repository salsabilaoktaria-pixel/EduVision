@extends('layouts.app')
@section('title', 'Bank Soal')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-5 kartu">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <form method="GET" class="flex gap-2">
            <select name="mapel_id" onchange="this.form.submit()" class="rounded-xl border-2 border-slate-300 px-4 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                <option value="">-- Semua Mapel --</option>
                @foreach($mapel as $m)<option value="{{ $m->id }}" @selected(request('mapel_id')==$m->id)>{{ $m->nama_mapel }}</option>@endforeach
            </select>
        </form>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('banksoal.create') }}" data-popup class="bg-teal-600 text-white rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-teal-700 transition shadow-lg shadow-teal-600/20 text-center tombol-aktif"><x-ikon nama="edit" class="w-4 h-4 inline-block align-[-2px] mr-1" /> Manual</a>
            <a href="{{ route('banksoal.importForm') }}" class="bg-slate-700 text-white rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-800 transition text-center"><x-ikon nama="upload" class="w-4 h-4 inline-block align-[-2px] mr-1" /> Import File</a>
        </div>
    </div>
    <div class="space-y-3">
    @forelse($soal as $s)
        <div class="border-2 border-slate-200 rounded-xl p-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-slate-400 font-semibold">{{ $s->mataPelajaran->nama_mapel ?? '-' }} • {{ $s->tingkat_kesulitan }}</p>
                    <p class="font-semibold text-ink-900 mt-1">{{ $s->pertanyaan }}</p>
                    @if($s->gambar)<img src="{{ asset('storage/'.$s->gambar) }}" class="mt-2 max-h-40 rounded-xl border-2 border-slate-200">@endif
                    <div class="grid sm:grid-cols-2 gap-1 mt-2 text-sm text-slate-600">
                        <p>A. {{ $s->pilihan_a }}</p><p>B. {{ $s->pilihan_b }}</p>
                        <p>C. {{ $s->pilihan_c }}</p><p>D. {{ $s->pilihan_d }}</p>
                        @if($s->pilihan_e)<p>E. {{ $s->pilihan_e }}</p>@endif
                    </div>
                    @if($s->jawaban_benar)
                        <p class="text-xs text-emerald-600 font-bold mt-2">Jawaban Benar: {{ $s->jawaban_benar }}</p>
                    @else
                        <p class="text-xs text-amber-600 font-bold mt-2 inline-flex items-center gap-1"><x-ikon nama="alert" class="w-4 h-4 inline-block align-[-2px] mr-0.5" /> Kunci jawaban belum diisi — lengkapi lewat Edit</p>
                    @endif
                </div>
                <div class="shrink-0"><x-aksi :detail="route('banksoal.show', $s)" :edit="route('banksoal.edit', $s)" :hapus="route('banksoal.destroy', $s)" konfirmasi="Hapus soal ini?" /></div>
            </div>
        </div>
    @empty
        <p class="text-center py-10 text-slate-400">Belum ada soal.</p>
    @endforelse
    </div>
    <div class="mt-4">{{ $soal->links() }}</div>
</div>
@endsection
