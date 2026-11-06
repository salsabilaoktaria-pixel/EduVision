<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Belajar - {{ $siswa->nama }}</title>
    <style>
        @page { margin: 42px 48px; }
        body { font-family: 'Times', 'DejaVu Serif', serif; font-size: 12px; color: #111; line-height: 1.45; }
        .kop { border-bottom: 3px double #111; padding-bottom: 8px; margin-bottom: 14px; }
        .instansi { font-size: 15px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
        .sub { font-size: 10.5px; margin-top: 2px; }
        h2.judul { text-align: center; font-size: 14px; margin: 0 0 12px; text-transform: uppercase; text-decoration: underline; }
        table.identitas { width: 100%; margin-bottom: 12px; }
        table.identitas td { padding: 1.5px 0; vertical-align: top; }
        table.nilai { width: 100%; border-collapse: collapse; }
        table.nilai th, table.nilai td { border: 1px solid #111; padding: 5px 7px; font-size: 11.5px; }
        table.nilai th { background: #e9e9e9; text-align: center; }
        table.nilai td.c { text-align: center; }
        table.nilai tr.jumlah td { font-weight: bold; background: #f4f4f4; }
        table.rekap { width: 100%; border-collapse: collapse; margin-top: 14px; }
        table.rekap td { border: 1px solid #111; padding: 5px 7px; font-size: 11.5px; }
        .catatan { border: 1px solid #111; margin-top: 14px; padding: 7px 10px; min-height: 42px; }
        .catatan b { display: block; margin-bottom: 3px; }
        table.ttd { width: 100%; margin-top: 30px; text-align: center; }
        table.ttd td { width: 33%; vertical-align: top; font-size: 11.5px; }
        .nama-ttd { margin-top: 56px; font-weight: bold; text-decoration: underline; }
        .catatan-kaki { margin-top: 22px; font-size: 9.5px; color: #444; border-top: 1px solid #999; padding-top: 5px; }
    </style>
</head>
<body>
    @php
        $predikat = function ($n) { $n = (float) $n; return $n >= 90 ? 'A' : ($n >= 80 ? 'B' : ($n >= 70 ? 'C' : ($n >= 60 ? 'D' : 'E'))); };
        $waliKelas = $siswa->kelas?->waliKelas?->nama;
    @endphp

    <table class="kop" width="100%">
        <tr>
            <td width="14%" style="text-align:left;">@if($logoData)<img src="{{ $logoData }}" style="width:64px;height:64px;">@endif</td>
            <td style="text-align:center;">
                <div class="instansi">{{ $sekolah->nama }}</div>
                <div class="sub">
                    @if($sekolah->alamat){{ $sekolah->alamat }}@if($sekolah->kota), {{ $sekolah->kota }}@endif<br>@endif
                    @if($sekolah->telepon)Telp. {{ $sekolah->telepon }}@endif @if($sekolah->telepon && $sekolah->email) | @endif @if($sekolah->email){{ $sekolah->email }}@endif
                    @if($sekolah->npsn)<br>NPSN {{ $sekolah->npsn }}@endif
                </div>
            </td>
            <td width="14%"></td>
        </tr>
    </table>

    <h2 class="judul">Laporan Hasil Belajar Siswa</h2>
    <p style="text-align:center;margin:2px 0 10px;font-size:12px;">{{ $semesterLabel ?? "" }}</p>

    <table class="identitas">
        <tr><td width="22%">Nama Siswa</td><td width="3%">:</td><td width="40%">{{ $siswa->nama }}</td><td width="14%">Kelas</td><td width="3%">:</td><td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td></tr>
        <tr><td>NISN</td><td>:</td><td>{{ $siswa->nisn }}</td><td>Tanggal Cetak</td><td>:</td><td>{{ now()->translatedFormat('d F Y') }}</td></tr>
    </table>

    <table class="nilai">
        <thead>
            <tr><th width="5%">No</th><th>Mata Pelajaran</th><th width="16%">Semester</th><th width="9%">Tugas</th><th width="8%">UTS</th><th width="8%">UAS</th><th width="10%">Nilai Akhir</th><th width="8%">Predikat</th></tr>
        </thead>
        <tbody>
        @forelse($siswa->nilai as $i => $n)
            <tr>
                <td class="c">{{ $i + 1 }}</td>
                <td>{{ $n->mataPelajaran->nama_mapel ?? '-' }}</td>
                <td class="c">{{ $n->semester }} {{ $n->tahun_ajaran }}</td>
                <td class="c">{{ $n->nilai_tugas }}</td>
                <td class="c">{{ $n->nilai_uts }}</td>
                <td class="c">{{ $n->nilai_uas }}</td>
                <td class="c"><b>{{ $n->nilai_akhir }}</b></td>
                <td class="c">{{ $predikat($n->nilai_akhir) }}</td>
            </tr>
        @empty
            <tr><td colspan="8" class="c">Belum ada nilai tercatat.</td></tr>
        @endforelse
        @if($siswa->nilai->count())
            <tr class="jumlah"><td colspan="6" style="text-align:right;">Rata-rata Nilai Akhir</td><td class="c">{{ $rataRata }}</td><td class="c">{{ $predikat($rataRata) }}</td></tr>
        @endif
        </tbody>
    </table>

    <table class="rekap">
        <tr><td width="30%">Persentase Kehadiran</td><td width="20%">{{ $kehadiran }}%</td><td width="20%">Keterangan Akademik</td><td>{{ $prediksi['status'] }}</td></tr>
    </table>

    <div class="catatan">
        <b>Catatan Wali Kelas</b>
        {{ $motivasi }}
    </div>

    <table class="ttd">
        <tr>
            <td>Orang Tua / Wali<div class="nama-ttd">{{ $siswa->nama_wali ?: '..............................' }}</div></td>
            <td>Wali Kelas<div class="nama-ttd">{{ $waliKelas ?: '..............................' }}</div></td>
            <td>Kepala Sekolah<div class="nama-ttd">{{ $sekolah->kepala_sekolah ?: '..............................' }}</div>@if($sekolah->nip_kepala_sekolah)<div style="font-size:10px;">NIP. {{ $sekolah->nip_kepala_sekolah }}</div>@endif</td>
        </tr>
    </table>

    <div class="catatan-kaki">
        Predikat: A (90-100), B (80-89), C (70-79), D (60-69), E (&lt;60). Keterangan akademik dihitung dari rata-rata nilai dan persentase kehadiran,
        bukan merupakan keputusan akhir kenaikan kelas. Dicetak melalui sistem EduVision, {{ now()->format('d/m/Y H:i') }}.
    </div>
</body>
</html>
