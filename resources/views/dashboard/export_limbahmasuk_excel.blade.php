<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Truk</th>
            <th>Kode Limbah</th>
            <th>Jumlah (kg)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($limbahMasuk as $item)
            @foreach ($item->detailLimbahMasuk as $detail)
                <tr>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $detail->truk->plat_nomor ?? '-' }}</td>
                    <td>{{ $detail->kodeLimbah->kode ?? '-' }}</td>
                    <td>{{ $detail->berat_kg }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
