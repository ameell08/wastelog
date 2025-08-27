<?php

namespace App\Http\Controllers;

use App\Models\LimbahMasuk;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Facades\Excel;

class DataLimbahMasukController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $allowedSorts = ['tanggal', 'total_kg'];
        $sort = $request->input('sort', 'tanggal');
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'tanggal';
        }
        $direction = strtolower($request->input('direction', 'desc'));
        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';

        $query = LimbahMasuk::query();
        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        }

        $limbahMasuk = $query->selectRaw('DATE(tanggal) as tanggal, SUM(total_kg) as total_kg')
            ->groupBy('tanggal')
            ->orderBy($sort, $direction)
            ->paginate(20)
            ->withQueryString();

        $breadcrumb = (object)[
            'title' => 'Data Limbah Masuk',
            'list' => ['Input Limbah Masuk', 'Data Limbah Masuk']
        ];


        return view('admin1.DataLimbahMasuk', compact('limbahMasuk', 'tanggal', 'breadcrumb'))->with('activeMenu', 'datalimbahmasuk');
    }

    public function show($id)
    {
        $limbahMasuk = LimbahMasuk::findOrFail($id);
        $detail = $limbahMasuk->detailLimbahMasuk()->with(['truk', 'kodeLimbah'])->get();

        return response()->json([
            'tanggal' => $limbahMasuk->tanggal,
            'data' => $detail,
        ]);
    }

    public function export_excel(Request $request)
    {
        try {
            $tanggal = $request->input('tanggal');

            $allowedSorts = ['tanggal', 'total_kg'];
            $sort = $request->input('sort', 'tanggal');
            if (!in_array($sort, $allowedSorts)) {
                $sort = 'tanggal';
            }

            $direction = strtolower($request->input('direction', 'desc'));
            $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';

            $groupQuery = LimbahMasuk::query();
            if ($tanggal) {
                $groupQuery->whereDate('tanggal', $tanggal);
            }

            $tanggalOrdered = $groupQuery
                ->selectRaw('DATE(tanggal) as tanggal, SUM(total_kg) as total_kg')
                ->groupBy('tanggal')
                ->orderBy($sort, $direction)
                ->get();

            if ($tanggalOrdered->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data untuk diekspor.');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'DATA LIMBAH MASUK');
            $sheet->getStyle('A1')->getFont()->setBold(true);

            $headers = [
                'A2' => 'No',
                'B2' => 'Tanggal',
                'C2' => 'Plat Nomor Truk',
                'D2' => 'Kode Limbah (Deskripsi)',
                'E2' => 'Sumber',
                'F2' => 'Berat (Kg)',
                'G2' => 'Kode Festronik',
            ];
            foreach ($headers as $cell => $text) {
                $sheet->setCellValue($cell, $text);
                $sheet->getStyle($cell)->getFont()->setBold(true);
            }

            $no  = 1;
            $row = 3;

            foreach ($tanggalOrdered as $g) {
                $list = LimbahMasuk::whereDate('tanggal', $g->tanggal)
                    ->with(['detailLimbahMasuk.truk', 'detailLimbahMasuk.kodeLimbah', 'detailLimbahMasuk.sumber'])
                    ->orderBy('tanggal', 'asc') 
                    ->get();

                foreach ($list as $item) {
                    foreach ($item->detailLimbahMasuk as $detail) {
                        $sheet->setCellValue('A' . $row, $no);
                        $sheet->setCellValue('B' . $row, \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y'));
                        $sheet->setCellValue('C' . $row, $detail->truk->plat_nomor ?? '-');

                        $kode = $detail->kodeLimbah->kode ?? '-';
                        $desk = $detail->kodeLimbah->deskripsi ?? '-';
                        $sheet->setCellValue('D' . $row, $kode . ' (' . $desk . ')');

                        $sheet->setCellValue('E' . $row, $detail->sumber->nama_sumber ?? '-');
                        $sheet->setCellValue('F' . $row, $detail->berat_kg ?? 0);
                        $sheet->setCellValue('G' . $row, $detail->kode_festronik ?: '-');

                        $row++;
                        $no++;
                    }
                }
            }
            $lastRow = $row - 1;
            $sheet->getStyle("A2:G{$lastRow}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $sheet->setTitle('Data Limbah Masuk');

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $filename = 'Data Limbah Masuk ' . date('d-m-y H.i.s') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');

            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    public function showByTanggal($tanggal)
    {
        $parsedTanggal = \Carbon\Carbon::createFromFormat('d-m-Y', $tanggal)->toDateString();

        $limbahMasuk = LimbahMasuk::whereDate('tanggal', $parsedTanggal)
            ->with(['detailLimbahMasuk.truk', 'detailLimbahMasuk.kodeLimbah', 'detailLimbahMasuk.sumber'])
            ->get();

        $detail = [];

        foreach ($limbahMasuk as $item) {
            foreach ($item->detailLimbahMasuk as $d) {
                $detail[] = [
                    'plat_nomor' => $d->truk->plat_nomor,
                    'kode_limbah' => [
                        'kode' => $d->kodeLimbah->kode,
                        'deskripsi' => $d->kodeLimbah->deskripsi ?? '-',
                    ],
                    'sumber' => $d->sumber->nama_sumber ?? '-',
                    'berat_kg' => $d->berat_kg,
                    'kode_festronik' => $d->kode_festronik ?: null,
                ];
            }
        }

        return response()->json([
            'tanggal' => $parsedTanggal,
            'data' => $detail,
        ]);
    }

    public function detailexportexcel($tanggal)
    {
        try {
            $parsedTanggal = Carbon::parse($tanggal)->toDateString();

            $limbahMasukList = LimbahMasuk::whereDate('tanggal', $parsedTanggal)
                ->with(['detailLimbahMasuk.truk', 'detailLimbahMasuk.kodeLimbah', 'detailLimbahMasuk.sumber'])
                ->get();

            if ($limbahMasukList->isEmpty()) {
                return redirect()->back()->with('error', 'Data tidak ditemukan untuk tanggal tersebut.');
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('B1', 'DETAIL LIMBAH MASUK ');
            $sheet->setCellValue('B2', 'TANGGAL : ' . Carbon::createFromFormat('d-m-Y', $tanggal)->format('d/m/Y'));
            $sheet->getStyle(('B1'))->getFont()->setBold(true);
            $sheet->getStyle(('B2'))->getFont()->setBold(true);

            $headers = [
                'A4' => 'Plat Nomor Truk',
                'B4' => 'Kode Limbah',
                'C4' => 'Sumber',
                'D4' => 'Berat (Kg)',
                'E4' => 'Kode Festronik',
            ];
            foreach ($headers as $cell => $text) {
                $sheet->setCellValue($cell, $text);
                $sheet->getStyle($cell)->getFont()->setBold(true);
            }

            $row = 5;
            foreach ($limbahMasukList as $limbahMasuk) {
                foreach ($limbahMasuk->detailLimbahMasuk as $detail) {
                    $platNomor = $detail->truk->plat_nomor ?? '-';
                    $kode = $detail->kodeLimbah->kode ?? '-';
                    $deskripsi = $detail->kodeLimbah->deskripsi ?? '-';
                    $sumber = $detail->sumber->nama_sumber ?? '-';
                    $berat = $detail->berat_kg ?? 0;

                    $sheet->setCellValue('A' . $row, $platNomor);
                    $sheet->setCellValue('B' . $row, $kode . ' (' . $deskripsi . ')');
                    $sheet->setCellValue('C' . $row, $sumber);
                    $sheet->setCellValue('D' . $row, $berat);
                    $sheet->setCellValue('E' . $row, $detail->kode_festronik ?? '-');
                    $row++;
                }
            }

            $lastRow = $row - 1;
            $sheet->getStyle("A4:E{$lastRow}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            foreach (range('A', 'E') as $columID) {
                $sheet->getColumnDimension($columID)->setAutoSize(true);
            }

            $tanggalFormatted = Carbon::parse($parsedTanggal)->format('d-m-Y');
            $sheet->setTitle('Detail Limbah Masuk ' . $tanggalFormatted);

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $filename = 'Detail Limbah Masuk ' . $tanggalFormatted . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');

            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }
}
