<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 6px 20px 5px 20px;
            line-height: 1.2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 5px;
            font-size: 11pt;
        }

        thead th {
            text-align: center;
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .no-border {
            border: none !important;
        }

        .title-2 {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            margin-top: 5px;
        }

        .title-3 {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            margin-top: 5px;
            margin-bottom: 12px;
        }

        .meta-small {
            font-size: 11pt;
            margin: 3px 0 10px 0;
            margin-bottom: 5px;
        }

        .border-bottom-header {
            border-bottom: 1px solid;
        }

        /* Lebar kolom agar mirip contoh */
        colgroup col.c1 {
            width: 12%;
        }

        colgroup col.c2,
        colgroup col.c3,
        colgroup col.c4,
        colgroup col.c5,
        colgroup col.c6 {
            width: 17.6%;
        }
    </style>
</head>

<body>
    <table class="border-bottom-header">
        <tr>
            <td class="no-border" style="width:15%; text-align:center;">
                <img src="{{ public_path('img/ptpria.png') }}" style="width:100px;">
            </td>
            <td class="no-border" style="width:85%; text-align:center;">
                <div style="font-weight:bold; font-size:13pt;">PT PUTRA RESTU IBU ABADI</div>
                <div style="font-size:11pt; font-weight:bold;">
                    Pusat Pengangkutan, Pengolahan, Pemanfaatan dan Pengumpulan Limbah B3
                </div>
                <div style="font-size:10pt;">
                    Jl. Raya Lakardowo Ds. Lakardowo, Kec. Jetis, Kab. Mojokerto Jawa Timur
                </div>
                <div style="font-size:10pt;">Telepon (0321)361212, Fax. (0321) 365322</div>
                <div style="font-size:10pt;">Laman: www.ptpria.co.id</div>
            </td>
        </tr>
    </table>

    <div class="title-2">NERACA PENGOLAHAN LIMBAH</div>
    <div class="title-3">BAHAN BERBAHAYA DAN BERACUN</div>
    
    <div class="meta-small"><b>Kegiatan : Pengolahan dengan Insinerator</b></div>
    <div class="meta-small"><b>Bulan: {{ $namaBulan }}</b></div>
    <div class="meta-small"><b>Tahun: {{ $tahun }}</b></div>

    <table>
        <colgroup>
            <col class="c1">
            <col class="c2">
            <col class="c3">
            <col class="c4">
            <col class="c5">
            <col class="c6">
        </colgroup>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Total Limbah Masuk (Kg)</th>
                <th>Total Limbah Diolah (Kg)</th>
                <th>Sisa Limbah (Kg)</th>
                <th>Total Residu (Kg)</th>
                <th>Total Pengiriman Residu (Kg)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $r)
                <tr>
                    <td class="text-center">{{ $r['hari'] }}</td>
                    <td class="text-right">{{ $r['masuk'] }}</td>
                    <td class="text-right">{{ $r['diolah'] }}</td>
                    <td class="text-right">{{ $r['sisa'] }}</td>
                    <td class="text-right">{{ $r['residu'] }}</td>
                    <td class="text-right">{{ $r['kirim'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-size:10pt; text-align:right; margin-top:10px;">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </p>

    <table class="no-border" style="margin-top:30px; width:100%;">
        <tr class="no-border">
            <td class="no-border" style="width:65%"></td>
            <td class="no-border text-center">
                Mengetahui,<br>Kepala Manajer<br><br><br><br><br>
                (...........................................)
            </td>
        </tr>
    </table>
</body>

</html>
