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
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <a href="{{ $tujuan }}" class="tombol-aktif btn-sekunder"><x-ikon nama="arrow-left" class="w-4 h-4" /> Kembali</a>
    @if(!$slot->isEmpty())<div class="flex flex-wrap items-center gap-2">{{ $slot }}</div>@endif
</div>
