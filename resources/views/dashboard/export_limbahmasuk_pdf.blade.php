<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 6px 20px 5px 20px;
            line-height: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 4px 3px;
        }

        th {
            text-align: left;
        }

        .d-block {
            display: block;
        }

        img.image {
            width: auto;
            height: 80px;
            max-width: 150px;
            max-height: 150px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .p-1 {
            padding: 5px 1px 5px 1px;
        }

        .font-10 {
            font-size: 10pt;
        }

        .font-11 {
            font-size: 11pt;
        }

        .font-12 {
            font-size: 12pt;
        }

        .font-13 {
            font-size: 13pt;
        }

        .border-bottom-header {
            border-bottom: 1px solid;
        }

        .border-all,
        .border-all th,
        .border-all td {
            border: 1px solid;
        }

        .footer {
            margin-top: 40px;
        }

        .ttd {
            margin-top: 60px;
        }

        .footer-table {
            width: 100%;
            margin-top: 30px;
        }

        .footer-table td {
            vertical-align: top;
        }

        .tanggal-cetak {
            text-align: right;
            font-size: 10pt;
        }
    </style>
</head>

<body>
    <table class="border-bottom-header">
        <tr>
            <td width="15%" class="text-center">
                <img src="{{ public_path('img/ptpria.png') }}" style="width: 100px;">

            </td>
            <td width="85%">
                <span class="text-center d-block font-13 font-bold mb-1">PT PUTRA RESTU IBU ABADI</span>
                <span class="text-center d-block font-11 font-bold mb-1">Pusat Pengangkutan, Pengolahan, Pemanfaatan dan
                    Pengumpulan Limbah B3</span>
                <span class="text-center d-block font-10">Jl. Raya Lakardowo Ds. Lakardowo, Kec. Jetis, Kab. Mojokerto
                    Jawa Timur</span>
                <span class="text-center d-block font-10">Telepon (0321)361212, Fax. (0321) 365322</span>
                <span class="text-center d-block font-10">Laman: www.ptpria.co.id</span>
            </td>
        </tr>
    </table>
    <h3 class="text-center">DAFTAR LIMBAH MASUK</h4>
        <p class="font-12" style="margin-bottom:8px;">
            <b>Bulan: {{ $namaBulan }}</b>
        </p>
        <table class="border-all">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Tanggal</th>
                    <th>Plat Nomor Truk</th>
                    <th>Kode Limbah</th>
                    <th>Berat (Kg)</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($limbahMasuk as $m)
                    @foreach ($m->detailLimbahMasuk as $detail)
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ \Carbon\Carbon::parse($m->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $detail->truk->plat_nomor ?? '-' }}</td>
                            <td>{{ $detail->kodeLimbah->kode ?? '-' }}</td>
                            <td>{{ $detail->berat_kg }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <p class="tanggal-cetak">
            Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
        </p>

        <table class="footer-table">
            <tr>
                <td width="70%"></td>
                <td class="text-center">
                    Mengetahui,<br>
                    Kepala Manajer<br><br><br><br><br>
                    (...........................................)
                </td>
            </tr>
        </table>
</body>

</html>
