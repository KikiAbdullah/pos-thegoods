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
                    <th width="10%">No</th>
                    <th width="15%" class="text-center">Service Time</th>
                    <th>Customer</th>
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
                        <td class="text-center">{{ $trans->service_time }}</td>
                        <td>{{ ucwords($trans->customer_name) }}</td>
                        @foreach ($data['list_tipe_pembayaran'] as $idPembayaran => $tipePembayaran)
                            <td class="text-end">
                                {{ $trans->tipe_pembayaran_id == $idPembayaran ? cleanNumber($trans->total) : '' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                <tr>
                    <th class="text-end" colspan="4">Total (Rp)</th>
                    @foreach ($data['list_tipe_pembayaran']->sortKeysDesc() as $idPembayaran => $tipePembayaran)
                        <th class="text-end">
                            {{ cleanNumber($item->transaction->where('status', 'verify')->where('tipe_pembayaran_id', $idPembayaran)->sum('total')) }}
                        </th>
                    @endforeach
                </tr>
                <tr>
                    <td class="bg-success fs-lg fw-semibold" colspan="7">Rekap Transaksi</td>
                </tr>
                <tr>
                    <th class="text-end" colspan="{{ count($data['list_tipe_pembayaran']) + 3 }}">Total
                        Kasir (Saldo Awal + Total Tunai)</th>
                    <th class="text-end">{{ cleanNumber($saldoAwal + $totalTunai) }}</th>
                </tr>
                <tr>
                    <th class="text-end" colspan="{{ count($data['list_tipe_pembayaran']) + 3 }}">Saldo
                        Awal Kasir</th>
                    <th class="text-end">{{ cleanNumber($saldoAwal) }}</th>
                </tr>
                <tr>
                    <th class="text-end" colspan="{{ count($data['list_tipe_pembayaran']) + 3 }}">Total
                        Transaksi Tunai</th>
                    <th class="text-end">{{ cleanNumber($totalTunai) }}</th>
                </tr>



                @if (!empty($totalTunaiDiKasir))
                    <tr>
                        <th class="text-end" colspan="{{ count($data['list_tipe_pembayaran']) + 3 }}">Saldo
                            Akhir Tunai di Kasir</th>
                        <th class="text-end">{{ cleanNumber($totalTunaiDiKasir) }}</th>
                    </tr>
                    <tr>
                        <th class="text-end" colspan="{{ count($data['list_tipe_pembayaran']) + 3 }}">
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
</div>
