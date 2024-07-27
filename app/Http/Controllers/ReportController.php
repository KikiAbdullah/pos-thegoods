<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function transaksiIndex()
    {
        $view = [
            'title' => 'Laporan Transaksi',
            'data'  => [
                'list_user'         => $this->listUser(),
                'list_package'      => $this->listPackage(),
                'list_status'       => $this->listStatusTransaksi(),
            ],
        ];

        return view('report.transaksi.index', $view);
    }

    public function transaksiResult(Request $request)
    {
        $data = $this->makeRequest($request->all());

        $items = $this->makeDataTransaksi($data);
        $response                   = responseFailed('Data Tidak Ditemukan');

        if (!$items->isEmpty()) {

            $view                   = [
                'title'                 => "Laporan Transaksi",
                'items'                 => $items,
                'data'                  => [
                    'list_tipe_pembayaran' => $this->listTipePembayaran(),
                ],
                'tanggal_range'         => formatDate('Y-m-d', 'd F Y', $data['tanggal_awal']) . ' s/d ' . formatDate('Y-m-d', 'd F Y', $data['tanggal_akhir']),
            ];

            $response               = responseSuccess(view('report.transaksi.result', $view)->render(), 'Berhasil');
        }

        return response()->json($response);
    }

    public function makeDataTransaksi($data)
    {
        $transaksi = Transaction::with(['tipePembayaran', 'packages', 'addons', 'kasir', 'createdBy'])
            ->whereBetween('tanggal', [$data['tanggal_awal'], $data['tanggal_akhir']])
            ->where('status', $data['status'])
            ->get();

        return $transaksi;
    }

    public function makeRequest($data)
    {
        ['tanggal_awal' => $data['tanggal_awal'], 'tanggal_akhir' => $data['tanggal_akhir']]         = $this->explodeTanggal($data['tanggal']);

        unset($data['tanggal']);

        return $data;
    }

    public function explodeTanggal($tanggal)
    {
        $explode                = explode(' - ', $tanggal);

        return [
            'tanggal_awal'      => formatDate('d/m/Y', 'Y-m-d', $explode[0]),
            'tanggal_akhir'     => formatDate('d/m/Y', 'Y-m-d', $explode[1]),
        ];
    }
}
