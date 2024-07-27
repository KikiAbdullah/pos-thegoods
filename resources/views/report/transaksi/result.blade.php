<div class="card w-100 mx-auto">
    <div class="card-header d-flex align-items-center">
        <h5 class="card-title fw-semibold">Result</h5>
        <div class="ms-auto">
            <button type="button" id="btnexport" class="btn btn-success mr-2 mb-2"
                onclick="ExportExcel('{{ $title }}')">
                <i class="icon-file-excel mr-1"></i>
                Save Excel
            </button>
        </div>
    </div>
    <div class="content_isi">
        <div class="card-body">
            <table>
                <tbody>
                    <tr>
                        <td class="pr-2">
                            <span
                                class="{{ env('APP_WARNA') }} logo-text me-2 fw-semibold d-none d-sm-inline-block">{{ env('APP_NAME') }}</span>
                        </td>
                        <td>
                            <div class="fw-bold font-size-lg">{{ $title }}</div>
                            <div>Tanggal: {{ $tanggal_range }}</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-responsive">
            <table class="table table-xxs table-bordered" id="dtable">
                <thead>
                    <tr>
                        <th class="text-center" width="3%">#</th>
                        <th class="text-center" width="8%">No</th>
                        <th>Customer</th>
                        <th class="text-center" width="8%">Tanggal</th>
                        <th class="text-center" width="5%">In</th>
                        <th class="text-center" width="5%">Out</th>
                        <th class="text-center" width="5%">Service Time</th>
                        <th class="text-center">Package</th>
                        <th class="text-center">Addon</th>
                        @foreach ($data['list_tipe_pembayaran'] as $tipePembayaran)
                            <th class="text-end">Total {{ $tipePembayaran }} (Rp)</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $trans)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $trans->no }}</td>
                            <td>{{ $trans->customer_name }}</td>
                            <td class="text-center">{{ formatDate('Y-m-d', 'd-m-Y', $trans->tanggal) }}</td>
                            <td class="text-center">{{ formatDate('Y-m-d H:i:s', 'H:i:s', $trans->ordered_at) }}</td>
                            <td class="text-center">{{ formatDate('Y-m-d H:i:s', 'H:i:s', $trans->verify_at) }}</td>
                            <td class="text-center">{{ $trans->service_time }}</td>
                            <td>{!! implode(',<br>', $trans->packages->pluck('package_name', 'package_id')->toArray()) !!}</td>
                            <td>{!! implode(',<br>', $trans->addons->pluck('addon_name', 'addon_id')->toArray()) !!}</td>
                            @foreach ($data['list_tipe_pembayaran'] as $idTipe => $tipePembayaran)
                                <td class="text-end">
                                    {{ $trans->tipe_pembayaran_id == $idTipe ? cleanNumber($trans->total) : '' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="9" class="text-end fw-semibold">Total</td>
                        @foreach ($data['list_tipe_pembayaran'] as $idTipe => $tipePembayaran)
                            <td class="text-end fw-semibold">
                                {{ cleanNumber($items->where('tipe_pembayaran_id', $idTipe)->sum('total')) }}
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
