<div class="d-flex flex-wrap mb-4 wmin-lg-400">
    <ul class="list list-unstyled mb-0">
        <li>Tanggal:</li>
        <li>Customer:</li>
        <li>Whatsapp:</li>
        <li>Email:</li>
        <li>Tipe Pembayaran:</li>
        <li>Keterangan:</li>
        @if ($item->status == 'rejected')
            <li>Keterangan Reject:</li>
        @endif
        <li>Status:</li>
    </ul>

    <ul class="list list-unstyled text-end mb-0 ms-auto">
        <li><span class="fw-semibold">{{ formatDate('Y-m-d', 'd/m/Y', $item->tanggal) }}</span></li>
        <li><span class="fw-semibold">{{ $item->customer_name ?? '-' }}</span></li>
        <li>{{ $item->customer_whatsapp ?? '-' }}</li>
        <li>{{ $item->customer_email ?? '-' }}</li>
        <li>{{ $item->tipePembayaran->name ?? '-' }}</li>
        <li>{{ $item->text ?? '-' }}</li>
        @if ($item->status == 'rejected')
            <li>{{ $item->text_rejected ?? '-' }}</li>
        @endif
        <li>{!! $item->status_formatted !!}</li>
    </ul>
</div>

<div class="table-responsive">
    <table class="table table-xxs table-bordered" id="ctable">
        <thead>
            <tr>
                <th class="text-center" width="3%">#</th>
                <th>Package Name</th>
                <th class="text-end">Qty</th>
                <th class="text-end" width="15%">Harga</th>
            </tr>
        </thead>
        <tbody id="tbody">
            @forelse ($item->packages as $package)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td colspan="2">
                        {{ $package->package_name ?? '-' }}
                        {!! !empty($package->url) && $package->url != '#'
                            ? '<a href="' . $package->url . '" target="_blank"><i class="ph-link-simple text-primary"></i></a>'
                            : '' !!}
                    </td>
                    <td class="text-end">{{ cleanNumber($package->harga) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak Ada</td>
                </tr>
            @endforelse
            @if ($item->addons->isNotEmpty())
                <tr>
                    <th class="text-center" width="3%">#</th>
                    <th colspan="4">Add On</th>
                </tr>
                @forelse ($item->addons as $addon)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $addon->addon_name ?? '-' }}</td>
                        <td class="text-end">{{ cleanNumber($addon->qty) ?? '-' }}</td>
                        <td class="text-end">{{ cleanNumber($addon->harga) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak Ada</td>
                        @if ($item->status == 'open' || $item->status == 'payment')
                            <td></td>
                        @endif
                    </tr>
                @endforelse
            @endif
            <tr>
                <th class="text-end" colspan="3">Total</th>
                <th class="text-end">{{ cleanNumber($grandTotal) ?? 0 }}</th>
            </tr>
        </tbody>
    </table>
</div>

@if ($grandTotal > 0)
    <div class="d-flex justify-content-end align-items-center mt-4">
        @if (!in_array($item->status, ['open', 'verify', 'rejected']))
            <button type="button" data-url="{{ route($button['change-status'], $id) }}"
                onclick="BtnOptionReject('rejected',this, event)"
                class="btn btn-danger btn-labeled btn-labeled-start btn-lg w-100">
                <span class="btn-labeled-icon bg-black bg-opacity-20">
                    <i class="ph-x-circle ph-lg"></i>
                </span>
                Batalkan Transaksi
            </button>
        @endif
    </div>
@endif
