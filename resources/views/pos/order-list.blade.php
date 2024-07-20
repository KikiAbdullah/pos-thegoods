<div class="table-responsive">
    <table class="table table-xxs table-striped" id="ctable">
        <thead>
            <tr>
                <th class="text-center" width="3%">#</th>
                <th>Package Name</th>
                <th class="text-end">Qty</th>
                <th class="text-end" width="15%">Harga</th>
                <th width="3%"></th>
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
                    @if ($item->status == 'open' || $item->status == 'payment')
                        <td>
                            <a href="#!"
                                onclick="RemoveLines('{{ route('transaction.package-delete', $package->id) }}', event)">
                                <i class="ph-minus-circle text-danger"></i>
                            </a>
                        </td>
                    @endif
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
                        @if ($item->status == 'open' || $item->status == 'payment')
                            <td>
                                <a href="#!"
                                    onclick="RemoveLines('{{ route('pos.addon-delete', $addon->id) }}', event)">
                                    <i class="ph-minus-circle text-danger"></i>
                                </a>
                            </td>
                        @endif
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

    @if ($item->status == 'open')
        <div class="text-center m-2">
            <button type="button" data-url="{{ route($button['change-status'], $id) }}"
                onclick="BtnOption('ordered',this, event)"
                class="btn btn-success btn-labeled btn-labeled-start btn-lg w-100">
                <span class="btn-labeled-icon bg-black bg-opacity-20">
                    <i class="ph-paper-plane-tilt ph-lg"></i>
                </span>
                Lanjutkan Pemesanan
            </button>
        </div>
    @endif

    @if ($item->status == 'payment')
        <div class="text-center m-2">
            <button type="button" data-url="{{ route($button['change-status'], $id) }}"
                onclick="BtnOption('verify',this, event)"
                class="btn btn-success btn-labeled btn-labeled-start btn-lg w-100">
                <span class="btn-labeled-icon bg-black bg-opacity-20">
                    <i class="ph-check ph-lg"></i>
                </span>
                Verifikasi Pembayaran
            </button>
        </div>
    @endif


@endif
