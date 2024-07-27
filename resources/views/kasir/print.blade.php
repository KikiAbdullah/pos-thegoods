<html>

<head>
    <title>LAPORAN TRANSAKSI</title>
    <style>
        @page {
            margin: 0.2cm;
        }

        body {
            font-size: 8pt;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0.2cm;
        }

        .title {
            font-size: 12pt;
            font-weight: bold;
        }

        p {
            margin: 0;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .table-item thead tr th,
        .table-item tbody tr td {
            padding: 4px 8px;
        }

        .table-item tbody tr th {
            padding: 4px 8px;
        }

        .table-item thead {
            background: #ccc;
            border-bottom: 0.5px solid #000;
            border-top: 0.5px solid #000;
        }

        .table-item tbody {
            border: 0.5px solid #000;
        }

        .table-ttd tr td {
            border: 0.5px solid #000;
        }

        ul li {
            font-size: 0.655rem;
            list-style: decimal;
        }
    </style>
</head>

<body>

    <table width="100%">
        <tr>
            <td>
                <div class="title">LAPORAN TRANSAKSI</div>
                <table width="50%">
                    <tr>
                        <td>Tanggal</td>
                        <td>: {{ $item->tanggal_formatted }}</td>
                    </tr>
                    <tr>
                        <td>Kasir</td>
                        <td>: {{ $item->createdBy->name }}</td>
                    </tr>
                </table>
            </td>
            <td></td>
            <td width="25%" valign="top" style="text-align: right;">
                <img src="{{ public_path('app_local/img/logo.png') }}" width="100" style="margin-bottom: 10px;">
                <h3>The Good Studios</h3>
                <small>Ruko Pandaan Square Blok C-4, Kluncing, Kec. Pandaan, Pasuruan 67156</small>
            </td>
        </tr>
    </table>
    <br />
    <div style="min-height: 200px;vertical-align: top;">
        <table width="100%" class="table-item" cellspacing="0" cellspadding="0" border="0.5">
            <thead>
                <tr>
                    <th class="text-center" width="3%">#</th>
                    <th width="10%">No</th>
                    <th>Customer</th>
                    <th>Packages</th>
                    <th>Add ons</th>
                    @foreach ($data['list_tipe_pembayaran']->sortKeysDesc() as $tipePembayaran)
                        <th class="text-center" width="15%">Total {{ $tipePembayaran }} (Rp)</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $totalTunai = $item->transaction
                        ->where('status', 'verify')
                        ->where('tipe_pembayaran_id', 1)
                        ->sum('total');

                    $totalNonTunai = $item->transaction
                        ->where('status', 'verify')
                        ->where('tipe_pembayaran_id', 2)
                        ->sum('total');

                    $totalTransaksiToday = $totalTunai + $totalNonTunai;

                    $saldoAwal = $item->saldo_awal;
                    $totalTunaiDiKasir = $item->total_transaksi;
                    $selisih = $totalTunaiDiKasir - $totalTunai;
                @endphp
                @foreach ($item->transaction->where('status', 'verify') as $trans)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $trans->no }}</td>
                        <td>{{ ucwords($trans->customer_name) }}</td>
                        <td>{!! implode(',<br>', $trans->packages->pluck('package_name')->toArray()) !!}</td>
                        <td>{!! implode(',<br>', $trans->addons->pluck('addon_name')->toArray()) !!}</td>
                        @foreach ($data['list_tipe_pembayaran'] as $idPembayaran => $tipePembayaran)
                            <td class="text-end">
                                {{ $trans->tipe_pembayaran_id == $idPembayaran ? cleanNumber($trans->total) : '' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                <tr>
                    <th class="text-end" colspan="{{ count($data['list_tipe_pembayaran']) + 3 }}">Total (Rp)</th>
                    @foreach ($data['list_tipe_pembayaran']->sortKeysDesc() as $idPembayaran => $tipePembayaran)
                        <th class="text-end">
                            {{ cleanNumber($item->transaction->where('status', 'verify')->where('tipe_pembayaran_id', $idPembayaran)->sum('total')) }}
                        </th>
                    @endforeach
                </tr>
                <tr>
                    <td style="background: #ccc;" colspan="{{ count($data['list_tipe_pembayaran']) + 5 }}">
                        Rekap Transaksi</td>
                </tr>
                <tr>
                    <th class="text-end" colspan="{{ count($data['list_tipe_pembayaran']) + 4 }}">Total
                        Kasir (Saldo Awal + Total Tunai)</th>
                    <th class="text-end">{{ cleanNumber($saldoAwal + $totalTunai) }}</th>
                </tr>
                <tr>
                    <th class="text-end" colspan="{{ count($data['list_tipe_pembayaran']) + 4 }}">Saldo
                        Awal Kasir</th>
                    <th class="text-end">{{ cleanNumber($saldoAwal) }}</th>
                </tr>
                <tr>
                    <th class="text-end" colspan="{{ count($data['list_tipe_pembayaran']) + 4 }}">Total
                        Transaksi Tunai</th>
                    <th class="text-end">{{ cleanNumber($totalTunai) }}</th>
                </tr>



                @if (!empty($totalTunaiDiKasir))
                    <tr>
                        <th class="text-end" colspan="{{ count($data['list_tipe_pembayaran']) + 4 }}">Saldo
                            Akhir Tunai di Kasir</th>
                        <th class="text-end">{{ cleanNumber($totalTunaiDiKasir) }}</th>
                    </tr>
                    <tr>
                        <th class="text-end" colspan="{{ count($data['list_tipe_pembayaran']) + 4 }}">
                            Selisih
                        </th>
                        <th class="text-end">
                            {{ ($selisih > 0 ? '(+) ' : '(-) ') . cleanNumber(abs($selisih)) }}
                        </th>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>

</html>
