{{--
    Foto profil bulat. Kalau belum ada foto -> lingkaran berwarna berisi INISIAL nama (warna tetap per nama).
    Pemakaian: <x-avatar :nama="$siswa->nama" :foto="$siswa->foto" class="w-10 h-10" teks="text-sm" />
--}}
@props(['nama' => '', 'foto' => null, 'teks' => 'text-sm'])
@php
    $kata = preg_split('/\s+/', trim((string) $nama)) ?: [''];
    $inisial = mb_strtoupper(mb_substr($kata[0] ?? '', 0, 1) . (count($kata) > 1 ? mb_substr(end($kata), 0, 1) : ''));
    $warna = [['#e0e9f6', '#34507c'], ['#fde9de', '#7a2f56'], ['#fff4c9', '#7a4120'], ['#e4efdf', '#215a43'], ['#fff3c9', '#6a4d10'], ['#e4ecf8', '#3b588a']];
    [$latar, $huruf] = $warna[crc32((string) $nama) % count($warna)];
    $ukuran = $attributes->get('class') ?: 'w-10 h-10';
@endphp
@if($foto)
    <img src="{{ asset('storage/' . $foto) }}" alt="Foto {{ $nama }}" loading="lazy" class="{{ $ukuran }} rounded-full object-cover shrink-0 border-2 border-white ring-1 ring-slate-300 bg-slate-100">
@else
    <span class="{{ $ukuran }} {{ $teks }} rounded-full shrink-0 inline-flex items-center justify-center font-bold select-none ring-1 ring-slate-300" style="background:{{ $latar }};color:{{ $huruf }}">{{ $inisial }}</span>
@endif
