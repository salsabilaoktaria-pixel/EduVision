@extends('layouts.app')
@section('title', 'Hasil Latihan Soal')
@section('content')
<x-tombol-kembali :href="route('banksoal.latihan')" />

<div class="bg-white rounded-2xl shadow-sm border-2 border-slate-200 p-8 max-w-2xl mx-auto text-center mb-6 kartu">
    <p class="text-slate-500 text-sm">Skor Kamu</p>
    <p class="text-6xl font-extrabold {{ $skor >= 70 ? 'text-emerald-600' : ($skor >= 50 ? 'text-amber-500' : 'text-rose-600') }} my-3">{{ $skor }}</p>
    <p class="text-sm text-slate-600">Benar {{ $benar }} dari {{ $total }} soal</p>
    <p class="text-sm text-slate-500 mt-2">
        @if($skor >= 85) Mantap! Pemahamanmu terhadap materi ini sudah sangat baik. 🎉
        @elseif($skor >= 60) Lumayan bagus, tapi masih ada beberapa bagian yang perlu diulang lagi. 💪
        @else Jangan berkecil hati, coba pelajari lagi bagian yang salah di bawah ini ya. 📚
        @endif
    </p>
    <a href="{{ route('banksoal.latihan') }}" class="mt-6 inline-block bg-teal-600 text-white rounded-xl px-6 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif">Latihan Mapel Lain</a>
</div>

<div class="max-w-2xl mx-auto space-y-4">
    <h3 class="font-bold text-ink-900">Pembahasan Jawaban</h3>
    @foreach($detail as $i => $d)
    <div class="bg-white rounded-2xl shadow-sm border {{ $d['benar'] ? 'border-emerald-200' : 'border-rose-200' }} p-4">
        <div class="flex justify-between items-start mb-2">
            <p class="font-semibold text-ink-900">{{ $i+1 }}. {{ $d['pertanyaan'] }}</p>
            @if($d['gambar'])<img src="{{ asset('storage/'.$d['gambar']) }}" class="my-2 max-h-48 rounded-xl border-2 border-slate-200">@endif
            <span class="shrink-0 ml-3 px-2 py-1 rounded-full text-xs font-bold {{ $d['benar'] ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                {{ $d['benar'] ? 'Benar' : 'Salah' }}
            </span>
        </div>
        <div class="grid sm:grid-cols-2 gap-1 text-sm">
            @foreach(['A'=>'pilihan_a','B'=>'pilihan_b','C'=>'pilihan_c','D'=>'pilihan_d','E'=>'pilihan_e'] as $huruf => $field)
            @continue(empty($d[$field]))
                <p class="px-2 py-1 rounded-lg
                    {{ $huruf === $d['jawaban_benar'] ? 'bg-emerald-50 text-emerald-700 font-semibold' : ($huruf === $d['jawaban_user'] && !$d['benar'] ? 'bg-rose-50 text-rose-700 font-semibold' : 'text-slate-500') }}">
                    {{ $huruf }}. {{ $d[$field] }}
                    @if($huruf === $d['jawaban_benar']) ✓ @endif
                    @if($huruf === $d['jawaban_user'] && !$d['benar']) (Jawabanmu) @endif
                </p>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection
