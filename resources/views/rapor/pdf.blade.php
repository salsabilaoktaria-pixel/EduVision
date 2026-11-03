<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #1e293b; }
        .header { text-align: center; border-bottom: 3px solid #0d9488; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #0f766e; font-size: 20px; }
        .header p { margin: 2px 0; color: #64748b; font-size: 11px; }
        .info-table { width: 100%; margin-bottom: 15px; }
        .info-table td { padding: 3px 0; font-size: 12px; }
        .kartu { display: block; width: 32%; float: left; margin-right: 1%; background: #f0fdfa; border: 1px solid #ccfbf1; border-radius: 6px; padding: 10px; text-align: center; margin-bottom: 15px; }
        .kartu p.label { font-size: 10px; color: #64748b; margin: 0; }
        .kartu p.value { font-size: 18px; font-weight: bold; color: #0f766e; margin: 4px 0 0; }
        .clear { clear: both; }
        table.nilai { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.nilai th, table.nilai td { border: 1px solid #cbd5e1; padding: 6px 8px; font-size: 11px; text-align: left; }
        table.nilai th { background: #0f766e; color: white; }
        .motivasi { background: #f0fdfa; border-left: 4px solid #0d9488; padding: 10px 14px; margin-top: 20px; font-style: italic; font-size: 12px; }
        .footer { margin-top: 40px; font-size: 10px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <h1>EDUVISION — SISTEM AKADEMIK SEKOLAH</h1>
        <p>Rapor Akademik Siswa</p>
    </div>

    <table class="info-table">
        <tr><td width="120"><b>Nama Siswa</b></td><td>: {{ $siswa->nama }}</td></tr>
        <tr><td><b>NISN</b></td><td>: {{ $siswa->nisn }}</td></tr>
        <tr><td><b>Kelas</b></td><td>: {{ $siswa->kelas->nama_kelas ?? '-' }}</td></tr>
        <tr><td><b>Tanggal Cetak</b></td><td>: {{ now()->format('d F Y') }}</td></tr>
    </table>

    <div class="kartu">
        <p class="label">RATA-RATA NILAI</p>
        <p class="value">{{ $rataRata }}</p>
    </div>
    <div class="kartu">
        <p class="label">PERSENTASE KEHADIRAN</p>
        <p class="value">{{ $kehadiran }}%</p>
    </div>
    <div class="kartu">
        <p class="label">PREDIKSI</p>
        <p class="value" style="font-size:13px;">{{ $prediksi['status'] }}</p>
    </div>
    <div class="clear"></div>

    <table class="nilai">
        <thead>
            <tr><th>Mata Pelajaran</th><th>Semester</th><th>Tugas</th><th>UTS</th><th>UAS</th><th>Nilai Akhir</th></tr>
        </thead>
        <tbody>
        @forelse($siswa->nilai as $n)
            <tr>
                <td>{{ $n->mataPelajaran->nama_mapel ?? '-' }}</td>
                <td>{{ $n->semester }} {{ $n->tahun_ajaran }}</td>
                <td>{{ $n->nilai_tugas }}</td>
                <td>{{ $n->nilai_uts }}</td>
                <td>{{ $n->nilai_uas }}</td>
                <td><b>{{ $n->nilai_akhir }}</b></td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;">Belum ada nilai tercatat.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="motivasi">
        💬 <b>Catatan untuk siswa:</b> {{ $motivasi }}
    </div>

    <div class="footer">
        Dokumen ini dicetak otomatis oleh sistem EduVision pada {{ now()->format('d/m/Y H:i') }}.
        Prediksi dihitung dari rata-rata nilai & persentase kehadiran (algoritma dasar), bukan keputusan akhir kelulusan.
    </div>
</body>
</html>
