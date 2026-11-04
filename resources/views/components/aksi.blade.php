{{--
    Tiga tombol aksi BAKU di semua daftar: Detail, Edit, Hapus — bentuk, warna, ikon, dan urutannya
    sama di seluruh aplikasi. Tombol yang tidak diizinkan untuk peran tertentu cukup tidak diberi alamatnya.
    Pemakaian: <x-aksi :detail="route('x.show',$x)" :edit="route('x.edit',$x)" :hapus="route('x.destroy',$x)" />
--}}
@props(['detail' => null, 'edit' => null, 'hapus' => null, 'konfirmasi' => 'Hapus data ini? Tindakan ini tidak dapat dibatalkan.', 'besar' => false])
<div class="aksi-grup {{ $besar ? 'aksi-besar' : '' }}">
    @if($detail)
        <a href="{{ $detail }}" data-popup class="btn-aksi btn-detail"><x-ikon nama="eye" class="{{ $besar ? 'w-4 h-4' : 'w-3.5 h-3.5' }}" />Detail</a>
    @endif
    @if($edit)
        <a href="{{ $edit }}" data-popup class="btn-aksi btn-edit"><x-ikon nama="pencil" class="{{ $besar ? 'w-4 h-4' : 'w-3.5 h-3.5' }}" />Edit</a>
    @endif
    @if($hapus)
        <form method="POST" action="{{ $hapus }}" class="aksi-form" data-konfirmasi="{{ $konfirmasi }}">
            @csrf @method('DELETE')
            <button type="submit" class="btn-aksi btn-hapus"><x-ikon nama="trash" class="{{ $besar ? 'w-4 h-4' : 'w-3.5 h-3.5' }}" />Hapus</button>
        </form>
    @endif
</div>
