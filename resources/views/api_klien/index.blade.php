@extends('layouts.app')
@section('title', 'Integrasi API')
@section('content')
<div class="max-w-4xl space-y-5">
    <div class="rounded-2xl bg-teal-50 border border-teal-100 text-teal-800 px-5 py-4 text-sm">
        <p class="font-bold">API untuk aplikasi Komite</p>
        <p class="text-xs mt-0.5">Buat satu kunci per aplikasi. Aplikasi luar mengirim kunci lewat header <code class="font-semibold">X-API-Key</code> ke <code class="font-semibold">{{ url('/api/v1') }}</code>. Dokumentasi endpoint ada di berkas <code>API.md</code>.</p>
    </div>

    @if(session('kunci_baru'))
    <div class="rounded-2xl bg-brand-50 border border-brand-200 px-5 py-4" x-data="{ salin: false }">
        <p class="text-sm font-bold text-ink-900">Kunci API baru (tampil sekali saja)</p>
        <div class="mt-2 flex gap-2 items-center flex-wrap">
            <code class="flex-1 min-w-0 break-all bg-white border border-brand-200 rounded-lg px-3 py-2 text-xs" id="kunciBaru">{{ session('kunci_baru') }}</code>
            <button type="button" @click="navigator.clipboard.writeText(document.getElementById('kunciBaru').innerText); salin = true"
                    class="bg-brand-400 text-ink-900 rounded-lg px-4 py-2 text-xs font-bold"><span x-text="salin ? 'Tersalin' : 'Salin'"></span></button>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 p-5 kartu">
        <form method="POST" action="{{ route('apiklien.store') }}" class="flex gap-2 flex-wrap">
            @csrf
            <input type="text" name="nama" required maxlength="80" placeholder="Nama aplikasi, mis. Aplikasi Komite"
                   class="flex-1 min-w-0 rounded-xl border-2 px-4 py-2.5 text-sm outline-none">
            <button class="bg-teal-600 text-white rounded-xl px-5 py-2.5 text-sm font-bold hover:bg-teal-700 transition tombol-aktif"><x-ikon nama="key" class="w-4 h-4" /> Buat Kunci</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 kartu">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs uppercase"><th class="py-2">Aplikasi</th><th>Awalan</th><th>Status</th><th>Terakhir dipakai</th><th></th></tr></thead>
            <tbody>
            @forelse($klien as $k)
                <tr class="border-t border-slate-100">
                    <td class="py-3 font-semibold text-ink-900">{{ $k->nama }}</td>
                    <td><code class="text-xs">{{ $k->awalan_kunci }}…</code></td>
                    <td><span class="px-2 py-1 rounded-lg text-xs font-bold {{ $k->aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $k->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td class="text-xs text-slate-500">{{ $k->terakhir_dipakai?->diffForHumans() ?? 'Belum pernah' }}</td>
                    <td class="text-right whitespace-nowrap">
                        <form method="POST" action="{{ route('apiklien.toggle', $k) }}" class="inline">@csrf @method('PATCH')
                            <button class="btn-aksi btn-detail">{{ $k->aktif ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                        </form>
                        <form method="POST" action="{{ route('apiklien.destroy', $k) }}" class="inline" data-konfirmasi="Hapus kunci ini? Aplikasi yang memakainya langsung ditolak.">@csrf @method('DELETE')
                            <button class="btn-aksi btn-hapus">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-8 text-slate-400">Belum ada kunci API.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
