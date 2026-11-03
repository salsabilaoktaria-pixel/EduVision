@extends('layouts.app')
@section('title', 'Latihan Soal')
@section('content')
<x-tombol-kembali :first-page="true" />
<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-6 kartu">
    <form method="GET" class="flex gap-2 mb-6">
        <select name="mapel_id" onchange="this.form.submit()" class="rounded-xl border-2 border-slate-300 px-4 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            <option value="">-- Pilih Mata Pelajaran --</option>
            @foreach($mapel as $m)<option value="{{ $m->id }}" @selected(request('mapel_id')==$m->id)>{{ $m->nama_mapel }}</option>@endforeach
        </select>
    </form>

    @if($soal && $soal->count() > 0)
    <form method="POST" action="{{ route('banksoal.koreksi') }}" class="space-y-6">
        @csrf
        @foreach($soal as $i => $s)
        <div class="border-2 border-slate-200 rounded-xl p-4">
            <p class="font-semibold text-ink-900 mb-3">{{ $i+1 }}. {{ $s->pertanyaan }}</p>
            @if($s->gambar)<img src="{{ asset('storage/'.$s->gambar) }}" class="mb-3 max-h-56 rounded-xl border-2 border-slate-200">@endif
            <div class="space-y-2 text-sm">
                @foreach(['A'=>'pilihan_a','B'=>'pilihan_b','C'=>'pilihan_c','D'=>'pilihan_d','E'=>'pilihan_e'] as $huruf => $field)
                @continue(empty($s->$field)) {{-- soal 4 pilihan: pilihan E kosong, jangan ditampilkan --}}
                <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-slate-50 cursor-pointer">
                    <input type="radio" name="jawaban[{{ $s->id }}]" value="{{ $huruf }}" required class="text-teal-600">
                    {{ $huruf }}. {{ $s->$field }}
                </label>
                @endforeach
            </div>
        </div>
        @endforeach
        <button class="bg-teal-600 text-white rounded-xl px-6 py-3 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Selesai & Lihat Skor</button>
    </form>
    @elseif(request('mapel_id'))
        <p class="text-center py-10 text-slate-400">Belum ada soal untuk mapel ini.</p>
    @endif
</div>
@endsection
