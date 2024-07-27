<div class="card">
    <div class="card-header bg-success text-white d-sm-flex">
        <div>
            <h6 class="mb-sm-0" id="trans-no">
                #{{ $id }}
            </h6>
        </div>

        <div class="d-sm-flex align-items-sm-center text-end flex-sm-nowrap ms-sm-auto">
            <div class="fs-sm">Created By: <br><span class="fs-lg fw-bold">{{ ucwords($item->createdBy->name) }}</span>
            </div>
        </div>
    </div>
    <input type="hidden" name="id" value="{{ $id }}">
    <div class="table-responsive">
        <table class="table table-xxs table-bordered" id="dtable">
            <thead>
                <tr>
                    <th class="text-center" width="3%">#</th>
                    <th>No</th>
                    <th class="text-center">Service Time</th>
                    <th>Customer</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalTunai = $item->transaction
                        ->where('status', 'verify')
                        ->where('tipe_pembayaran_id', 1)
                        ->sum('total');

                    $totalTransaksiToday = $item->transaction->where('status', 'verify')->sum('total');

                    $saldoAwal = $item->saldo_awal;
                    $totalTunaiDiKasir = $item->total_transaksi;
                    $selisih = $totalTunaiDiKasir - $totalTunai;
                @endphp
                @foreach ($item->transaction->where('status', 'verify') as $trans)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $trans->no }}</td>
                        <td class="text-center">{{ $trans->service_time }}</td>
                        <td>{{ ucwords($trans->customer_name) }}</td>
                        <td>{{ ucwords($trans->tipePembayaran->name) }}</td>
                        <td class="text-end">{{ cleanNumber($trans->total) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th class="text-end" colspan="5">Total Tunai</th>
                    <th class="text-end">
                        {{ cleanNumber($totalTunai) }}
                    </th>
                </tr>
                <tr>
                    <th class="text-end" colspan="5">Total Non Tunai</th>
                    <th class="text-start">
                        {{ cleanNumber($totalNonTunai) }}
                    </th>
                </tr>
                <tr>
                    <th class="text-end" colspan="5">Total Transaksi Hari Ini</th>
                    <th class="text-end">{{ cleanNumber($totalTransaksiToday) }}
                    </th>
                </tr>
                <tr>
                    <td class="bg-success fs-lg fw-semibold" colspan="6">Rekap Transaksi</td>
                </tr>
                <tr>
                    <th class="text-end" colspan="5">Saldo Awal Kasir</th>
                    <th class="text-end">{{ cleanNumber($saldoAwal) }}</th>
                </tr>
                <tr>
                    <th class="text-end" colspan="5">Total Transaksi Tunai</th>
                    <th class="text-end">{{ cleanNumber($totalTunai) }}</th>
                </tr>
                <tr>
                    <th class="text-end" colspan="5">Total Kasir (Saldo Awal + Total Tunai)</th>
                    <th class="text-end">{{ cleanNumber($saldoAwal + $totalTunai) }}</th>
                </tr>
                <tr>
                    <th class="text-end" colspan="5">Saldo Akhir Tunai di Kasir</th>
                    <th class="text-end">{{ cleanNumber($totalTunaiDiKasir) }}</th>
                </tr>
                <tr>
                    <th class="text-end" colspan="5">Selisih</th>
                    <th class="text-end">{{ ($selisih > 0 ? '(+) ' : '(-) ') . cleanNumber($selisih) }}
                    </th>
                </tr>
            </tbody>
        </table>
    </div>
</div>
