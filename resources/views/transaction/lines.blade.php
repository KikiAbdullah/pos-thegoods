<div class="card">
    <div class="card-header bg-success text-white d-sm-flex">
        <div>
            <h6 class="mb-sm-0" id="trans-no">
                {{ $item['no'] ?? '' }}
            </h6>
            <div class="fs-sm">Customer Name: <span class="fw-semibold">{{ ucwords($item->customer_name) }}</span>
            </div>
            <div class="fs-sm mb-2">No WA: <span class="fw-semibold">{{ $item->customer_whatsapp }}</span></div>
            <cite class="fs-sm mb-2">{{ $item->text }}</cite>
        </div>

        <div class="d-sm-flex align-items-sm-center text-end flex-sm-nowrap ms-sm-auto">
            <div class="fs-sm">Created By: <br><span class="fs-lg fw-bold">{{ ucwords($item->createdBy->name) }}</span>
            </div>
        </div>
    </div>
    <input type="hidden" name="id" value="{{ $id }}">
    <div class="table-responsive">
        <table class="table table-xxs table-striped" id="ctable">
            <thead>
                <tr>
                    <th class="text-center" width="3%">#</th>
                    <th>Package Name</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end" width="15%">Harga</th>
                    @can('transaction_create')
                        @if ($item->status == 'open')
                            <th class="text-center" width="3%">
                                <a href="#!" onclick="addLines({{ $item->id }})"><i
                                        class="ph-plus-circle text-primary"></i></a>
                            </th>
                        @endif
                    @endcan
                </tr>
            </thead>
            <tbody id="tbody">
                @forelse ($item->packages as $package)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td colspan="2">{{ $package->package_name ?? '-' }}</td>
                        <td class="text-end">{{ cleanNumber($package->harga) }}</td>
                        @can('transaction_create')
                            @if ($item->status == 'open')
                                <td>
                                    <a href="#!"
                                        onclick="RemoveLines('{{ route('transaction.package-delete', $package->id) }}', event)">
                                        <i class="ph-minus-circle text-danger"></i>
                                    </a>
                                </td>
                            @endif
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak Ada</td>
                    </tr>
                @endforelse
                @if (!empty($item->addons))
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
                            @can('transaction_create')
                                @if ($item->status == 'open')
                                    <td>
                                        <a href="#!"
                                            onclick="RemoveLines('{{ route('transaction.addon-delete', $addon->id) }}', event)">
                                            <i class="ph-minus-circle text-danger"></i>
                                        </a>
                                    </td>
                                @endif
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak Ada</td>
                        </tr>
                    @endforelse
                @endif
                <tr>
                    <th class="text-end" colspan="3">Total</th>
                    <th class="text-end">{{ cleanNumber($grandTotal) ?? 0 }}</th>
                    @can('transaction_create')
                        @if ($item->status == 'open')
                            <th></th>
                        @endif
                    @endcan
                </tr>
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript">
    function addLines(id) {
        $("#mymodal").find('.modal-body').html(
            '<center><i class="ph-spinner spinner"></i></center>');
        $("#mymodal").find('.modal-title').html('Add Package/Add-on');
        $("#mymodal").find('.modal-header').removeClass('bg-indigo').addClass('bg-success');
        $("#mymodal").find('.modal-dialog').removeAttr('class').attr('class',
            'modal-dialog modal-lg modal-dialog-scrollable m-4');
        $("#mymodal").modal('show');
        // $(".modal-backdrop.show").css('opacity', .05);
        $.ajax({
            url: '{{ route('transaction.lines-form') }}',
            type: 'GET',
            data: {
                id: id
            },
            success: function(response) {
                $("#mymodal").find('.modal-body').html(response);
                $(".select-m").select2({
                    dropdownParent: $('#mymodal')
                });
            }
        });
    }

    function SumQty(el, stokClass, totalClass) {
        let total = parseFloat($(el).val()) + parseFloat($("." + stokClass).val());

        if (isNaN(total)) {
            total = 0
        }

        $("." + totalClass).val(total);
    }
</script>
