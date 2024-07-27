@extends('layouts.pos')

@section('customcss')
@endsection

@section('content')
    <!-- Page header -->
    <div class="page-header">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <div class="page-title">
                    <h4 class="fw-semibold"></h4>
                </div>

                <a href="#page_header"
                    class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
                    data-bs-toggle="collapse">
                    <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                </a>
            </div>

            <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page_header">
                <div class="hstack gap-0 mb-3 mb-lg-0">
                    @if ($item->status == 'open')
                        <a href="{{ route('pos.index') }}"
                            class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold"><i
                                class="ph-arrow-left ph-2x text-warning"></i>Kembali</a>
                    @endif

                    <a href="{{ route('siteurl') }}"
                        class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold"><i
                            class="ph-house ph-2x text-indigo"></i>Home</a>


                    <span class="menuoption"></span>
                </div>
            </div>
        </div>
    </div>
    <!-- /page header -->


    <!-- Content area -->
    <div class="content pt-0">
        @include('layouts.alert')
        <div class="row ">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">{{ $title }}</h6>
                    </div>

                    <div class="card-body">
                        @if ($item->status == 'open')
                            {!! Form::model($item, ['route' => [$url['closed'], $id], 'method' => 'POST', 'id' => 'dform']) !!}
                            <div class="row mb-3">
                                <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Total Tunai</label>
                                <div class="col-lg-9">
                                    {!! Form::text('total_transaksi', null, [
                                        'class' => 'form-control uang',
                                        'id' => 'uang',
                                        'placeholder' => 'Total Tunai Kasir',
                                        'disabled' => isset($item) && $item->status == 'closed' ? true : false,
                                    ]) !!}
                                    <cite class="fs-sm text-muted">Total Tunai Kasir adalah nominal yang dipegang kasir hari
                                        ini</cite>
                                </div>
                            </div>
                            {!! Form::hidden('status', 'closed') !!}
                            <div class="d-flex justify-content-end align-items-center">
                                <button type="submit" class="btn btn-primary btn-labeled btn-labeled-start rounded-pill">
                                    <span class="btn-labeled-icon bg-black bg-opacity-20 m-1 rounded-pill">
                                        <i class="ph-paper-plane-tilt submit_loader"></i>
                                    </span>
                                    Submit
                                </button>
                            </div>
                            {!! Form::close() !!}
                        @else
                            <div class="row mb-3">
                                <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Kasir</label>
                                <div class="col-lg-9">
                                    <div class="form-control">
                                        {{ ucwords($item->createdBy->name) ?? '' }}
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Saldo Awal</label>
                                <div class="col-lg-9">
                                    <div class="form-control">
                                        Rp. {{ cleanNumber($item->saldo_awal) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Saldo Akhir</label>
                                <div class="col-lg-9">
                                    <div class="form-control">
                                        Rp. {{ cleanNumber($item->total_transaksi) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Buka</label>
                                <div class="col-lg-4">
                                    <div class="form-control">
                                        {{ formatDate('Y-m-d H:i:s', 'H:i:s', $item->open) }}
                                    </div>
                                </div>
                                <label class="col-lg-2 col-form-label text-lg-end d-none d-lg-block">Tutup</label>
                                <div class="col-lg-3">
                                    <div class="form-control">
                                        {{ formatDate('Y-m-d H:i:s', 'H:i:s', $item->close) }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                @if ($item->status == 'closed')
                    <div class="card w-100 table-responsive">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">Transaksi</h6>
                        </div>
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
                @endif
            </div>
        </div>
    </div>
    <!-- /content area -->
@endsection

@section('customjs')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var amountInput = document.getElementById('uang');

            amountInput.addEventListener('input', function(event) {
                var value = this.value.replace(/,/g, '').replace(/[^0-9.]/g, '');
                this.value = formatCurrency(value);
            });

            function formatCurrency(value) {
                if (value === '') return '';
                var parts = value.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                return parts.join('.');
            }
        });
    </script>
@endsection
