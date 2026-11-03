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
<div class="mb-4">
    <a href="{{ $tujuan }}"
       class="inline-flex items-center gap-2 text-sm font-bold text-white bg-gradient-to-r from-teal-500 to-teal-700 hover:from-teal-600 hover:to-teal-800 pl-2 pr-4 py-2 rounded-full shadow-sm shadow-teal-900/20 border-2 border-teal-400/30 transition group">
        <span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center group-hover:bg-white/30 transition">←</span>
        Kembali
    </a>
</div>
