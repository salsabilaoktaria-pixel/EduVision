{{--
    Tombol kembali universal.
    - Default (tanpa prop apapun): kembali ke halaman SEBELUMNYA (seperti biasa).
    - :href="route('sesuatu')"  -> paksa ke URL tertentu (dipakai di beberapa halaman khusus).
    - :first-page="true"        -> ini halaman PERTAMA/landing dari sebuah fitur, jadi tombol
                                    kembali diarahkan ke Dashboard, bukan ke halaman sebelumnya.
--}}
@props(['href' => null, 'firstPage' => false])
@php
    $tujuan = $href ?? ($firstPage ? route('dashboard') : url()->previous());
@endphp
<<<<<<< HEAD
<div class="tombol-kembali-bar mb-4 flex flex-wrap items-center justify-between gap-3">
    <a href="{{ $tujuan }}" class="tombol-aktif btn-sekunder"><x-ikon nama="arrow-left" class="w-4 h-4" /> Kembali</a>
    @if(!$slot->isEmpty())<div class="flex flex-wrap items-center gap-2">{{ $slot }}</div>@endif
=======
<div class="mb-4">
    <a href="{{ $tujuan }}"
<<<<<<< HEAD
       class="inline-flex items-center gap-2 text-sm font-bold text-white bg-gradient-to-r from-teal-500 to-teal-700 hover:from-teal-600 hover:to-teal-800 pl-2 pr-4 py-2 rounded-full shadow-sm shadow-teal-700/25 border-2 border-teal-400/30 transition group">
=======
       class="inline-flex items-center gap-2 text-sm font-bold text-white bg-gradient-to-r from-teal-500 to-teal-700 hover:from-teal-600 hover:to-teal-800 pl-2 pr-4 py-2 rounded-full shadow-sm shadow-teal-900/20 border-2 border-teal-400/30 transition group">
>>>>>>> ec84a08003e73a5ed32437e14f179dd6dd2214e8
        <span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center group-hover:bg-white/30 transition">←</span>
        Kembali
    </a>
>>>>>>> fec47dc2b440329cea55aa66de621f7c0558f476
</div>
