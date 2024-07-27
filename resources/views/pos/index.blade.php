@extends('layouts.pos')

@section('customcss')
    <style>
        .horizontal-scrollable {
            overflow-x: auto;
            white-space: nowrap;
        }

        .horizontal-scrollable .row {
            display: flex;
            flex-wrap: nowrap;
        }
    </style>
@endsection

@section('content')
    <!-- Page header -->
    <div class="page-header">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <div class="page-title">
                    <h4 class="fw-semibold">Hai {{ $data['kasir']->user->name ?? '' }},</h4>
                </div>

                <a href="#page_header"
                    class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
                    data-bs-toggle="collapse">
                    <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                </a>
            </div>

            <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page_header">
                <div class="hstack gap-0 mb-3 mb-lg-0">
                    <a href="{{ route('siteurl') }}"
                        class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold"><i
                            class="ph-house ph-2x text-indigo"></i>Home</a>

                    <a href="#!" onclick="newCustomer()"
                        class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold"><i
                            class="ph-plus-circle ph-2x text-indigo"></i>Customer Baru</a>
                    @if (!empty($data['kasir']->transaction))
                        @if ($data['kasir']->transaction->where('status', 'verify')->isNotEmpty())
                            <a href="{{ route('pos.closed', $data['kasir']->id ?? 0) }}"
                                class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold"><i
                                    class="ph-math-operations ph-2x text-warning"></i>Tutup Kasir</a>
                        @endif
                    @endif


                    <span class="menuoption"></span>
                </div>
            </div>
        </div>
    </div>
    <!-- /page header -->


    <!-- Content area -->
    <div class="content pt-0">
        @include('layouts.alert')
        <div class="row">
            <div class="col-md-8">
                <div class="horizontal-scrollable" id="trans-today">
                </div>


                <div id="pos-system">
                    {!! Form::hidden('transaction_id', null, [
                        'id' => 'trans-id',
                    ]) !!}
                    <div class="card" style="border-radius:15px;">
                        <div class="card-body">
                            <h4 class="fw-semibold mb-3">Packages</h4>
                            <div class="row mb-3">
                                @foreach ($data['list_package'] as $package)
                                    <div class="col-lg-3">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <i class="ph-package text-indigo fw-bold ph-2x mb-3"></i>

                                                <h6 class="mb-0">{{ $package->name }}</h6>
                                                <span class="opacity-75">Rp {{ cleanNumber($package->harga) }}</span>

                                                <div class="d-flex justify-content-center mt-3">
                                                    <button type="button" class="btn w-100 btn-primary"
                                                        onclick="ChoosePackage({{ $package->id }},'package')">
                                                        <i class="ph-plus-circle me-2"></i>
                                                        Add
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <h4 class="fw-semibold mb-3">Add On</h4>
                            <div class="row mb-3">
                                @foreach ($data['list_addon'] as $addon)
                                    <div class="col-lg-3">

                                        <div class="card">
                                            <div class="card-body text-center">
                                                <i class="ph-puzzle-piece text-success fw-bold ph-2x mb-3"></i>

                                                <h6 class="mb-0">{{ $addon->name }}</h6>
                                                <span class="opacity-75">Rp {{ cleanNumber($addon->harga) }}</span>

                                                <div class="d-flex justify-content-center mt-3">
                                                    <button type="button" class="btn w-100 btn-primary"
                                                        onclick="ChoosePackage({{ $addon->id }},'addon')">
                                                        <i class="ph-plus-circle me-2"></i>
                                                        Add
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div id="notready">
                    <div class="card" style="border-radius:15px;">
                        <div class="card-body text-center">
                            <img src="{{ asset('app_local/img/cust.png') }}" width="50%" class="img-fluid" alt=""
                                srcset="">
                            <div class="text-muted">Silahkan memilih customer terlebih dahulu</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <ul class="nav nav-tabs nav-justified mb-0" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="#tab-new-order" class="nav-link fw-semibold active" data-bs-toggle="tab"
                                aria-selected="true" role="tab">New Order</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#tab-history" class="nav-link fw-semibold" data-bs-toggle="tab" aria-selected="false"
                                role="tab" tabindex="-1">History</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="tab-new-order" role="tabpanel">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <h4 class="mb-0 text-white" id="trans-no"></h4>
                                <div class="w-50">
                                    {!! Form::select('transaction_id', [], null, [
                                        'class' => 'select-cust',
                                        'data-placeholder' => 'Pilih Customer',
                                        'onchange' => 'changeTrans(this, event)',
                                    ]) !!}
                                </div>
                            </div>

                            <table class="table table-xxs table-borderless mt-2" id="transaction-detail">
                                <tbody>
                                    <tr>
                                        <td width="20%">Name</td>
                                        <td class="fw-semibold" id="trans-cust-name">-</td>
                                    </tr>
                                    <tr>
                                        <td>Whatsapp</td>
                                        <td class="fw-semibold" id="trans-cust-wa">-</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="order-list"></div>
                        </div>

                        <div class="tab-pane fade" id="tab-history" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-xxs table-striped" id="hTable">
                                    <thead>
                                        <tr>
                                            <th width="5%">ID</th>
                                            <th>No</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /content area -->
@endsection

@section('customjs')
    <script src="{{ asset('app_local/js/swal.js') }}"></script>
    <script>
        var hTable;
        let kasirId = '{{ $data['kasir']->id ?? '' }}'
        const urlCustomer = '{{ route('pos.get-customer') }}';
        const urlTransaction = '{{ route('pos.get-transaction') }}';
        const urlOrderList = '{{ route('pos.order-list') }}';
        const urlTransToday = '{{ route('pos.trans-today') }}';
        const urlChoosePackage = '{{ route('pos.choose-package') }}';
        const urlFormCustomer = '{{ route('pos.customer.create') }}';
        const urlBukaKasir = '{{ route('pos.open') }}';
        const urlHistoryTable = '{{ route('transaction.get-data-today') }}'
        const urlDetailTransaction = '{{ route('pos.detail-transaction') }}';


        //GLOBAL JS
        function toggleDivs(showId, hideId) {
            $(`#${showId}`).show();
            $(`#${hideId}`).hide();
        }
    </script>

    @if (empty($data['kasir']))
        <script>
            $(document).ready(function() {
                toggleDivs('notready', 'pos-system');

                SelectRemoteDataCustomer('.select-cust', urlCustomer);
                // $('.select-cust').val(null).trigger('change');

                $("#mymodal").find('.modal-body').html(
                    '<center><i class="ph-spinner spinner"></i></center>');
                $("#mymodal").find('.modal-title').html('Buka Kasir');
                $("#mymodal").find('.modal-header').removeClass('bg-indigo').addClass('bg-success')
                $("#mymodal").find('.modal-dialog').removeAttr('class').attr('class',
                    'modal-dialog');
                $("#mymodal").modal({
                    backdrop: 'static',
                    keyboard: false // Mencegah penutupan modal dengan menekan tombol ESC
                }).modal('show').find('.btn-close').remove();

                $.ajax({
                    url: urlBukaKasir,
                    type: 'GET',
                    success: function(response) {
                        $("#mymodal").find('.modal-body').html(response);

                        $('.select').select2();

                        $("input.uang").keyup(function(event) {

                            // skip for arrow keys
                            if (event.which >= 37 && event.which <= 40) {
                                event.preventDefault();
                            }

                            $(this).val(function(index, value) {
                                value = value.replace(/,/g, '');
                                return numberWithCommas(value);
                            });
                        });
                    }
                });

                $('body').on("submit", '#l-modal-form', function(e) {
                    e.preventDefault();

                    let urlForm = $(this).attr('action');
                    let dataForm = $(this).serialize();

                    SwalConfirmAjax('INSERT', urlForm, 'POST', dataForm, () => {
                        window.location.href = "";
                    });
                });
            });
        </script>
    @else
        <script>
            $(document).ready(function() {
                SelectRemoteDataCustomer('.select-cust', urlCustomer);
                $('.select-cust').val(null).trigger('change');

                TransToday();
                toggleDivs('notready', 'pos-system');

                $("input.uang").keyup(function(event) {

                    // skip for arrow keys
                    if (event.which >= 37 && event.which <= 40) {
                        event.preventDefault();
                    }

                    $(this).val(function(index, value) {
                        value = value.replace(/,/g, '');
                        return numberWithCommas(value);
                    });
                });

                hTable = $('#hTable').DataTable({
                    "select": {
                        style: "single",
                        info: false
                    },
                    "serverSide": true,
                    "stateSave": true,
                    "sServerMethod": "GET",
                    "deferRender": true,
                    "rowId": 'id',
                    "ajax": urlHistoryTable,
                    "columns": [{
                            data: 'id',

                        },
                        {
                            data: 'no'
                        },
                        {
                            data: 'customer_name'
                        },
                        {
                            data: 'total'
                        },
                        {
                            data: 'status_formatted'
                        },
                    ],
                    "columnDefs": [{
                        "targets": [0],
                        "visible": false,
                        "searchable": false
                    }],
                    "order": [
                        [0, "desc"]
                    ],
                    "dom": '<"datatable-header"Bf<"toolbar">><"datatable-scroll"t><"datatable-footer"p>',

                });
                //set class for page length
                $("#hTable_length").addClass('d-none d-lg-block');

                hTable.on('select', function(e, dt, type, indexes) {
                    var rowArrayHtable = hTable.rows('.selected').data().toArray();
                    var rowData = hTable.rows(indexes).data().toArray();
                    var id = rowData[0].id;

                    $("#mymodal").find('.modal-body').html(
                            '<center><i class="ph-spinner spinner"></i></center>')
                        .end().find('.modal-title').text('Detail Transaksi')
                        .end().find('.modal-header').removeClass('bg-indigo').addClass('bg-success')
                        .end().find('.modal-dialog').attr('class', 'modal-dialog modal-dialog-scrollable')
                        .end().modal('show');

                    $.ajax({
                        type: 'GET',
                        url: urlDetailTransaction,
                        data: {
                            id: id,
                        },
                        success: function(response) {
                            if (response.status) {
                                $("#mymodal").find('.modal-body').html(response.data.view);
                            }
                        }
                    });
                });
                hTable.on('deselect', function(e, dt, type, indexes) {
                    if (type === 'row') {
                        // backtoCreate();
                    }
                });
            });

            $('body').on("submit", '#l-modal-form', function(e) {
                e.preventDefault();
                CustomAjax($(this).attr('action'), 'POST', $(this).serialize(), (response) => {
                    if (response) {
                        transId = response.data.id ?? '';
                        swalInit.fire({
                            title: "Success",
                            text: response.msg,
                            icon: "success",
                            didClose: () => {
                                $("#mymodal").modal('hide');
                                hTable.ajax.reload(null, false);
                            },
                        });
                    } else {
                        swalInit.fire({
                            title: "Error",
                            text: response.msg,
                            icon: "error",
                        });
                    }


                });
            });

            function changeTrans(el) {
                clearTransactionDetails();
                const transId = $(el).val();
                if (transId) fetchTransaction(transId);
            }

            function ChooseTrans(id) {
                clearTransactionDetails();
                $('.select-cust').val(null).trigger('change');
                if (id) fetchTransaction(id);
            }

            function fetchTransaction(transId) {
                $.ajax({
                    url: urlTransaction,
                    type: 'GET',
                    data: {
                        id: transId
                    },
                    success: function(response) {
                        if (response.status) {
                            $('#trans-id').val(response.data.id);
                            $('#trans-no').html(response.data.no);
                            $('#trans-cust-name').html(response.data.customer_name);
                            $('#trans-cust-wa').html(response.data.customer_whatsapp);
                            OrderList(response.data.id);
                            TransToday();
                            toggleDivs('pos-system', 'notready');
                            hTable.ajax.reload(null, false);
                        }
                    }
                });
            }

            function ChoosePackage(id, type) {
                const transId = $('#trans-id').val();
                $.ajax({
                    url: urlChoosePackage,
                    type: 'GET',
                    data: {
                        id: id,
                        transaction_id: transId,
                        type: type
                    },
                    success: function(response) {
                        OrderList(response.data.id);
                        TransToday();
                    }
                });
            }

            function OrderList(id) {
                $.ajax({
                    url: urlOrderList,
                    type: 'GET',
                    data: {
                        id
                    },
                    success: function(response) {
                        $(".order-list").html(response.view);
                    }
                });
            }

            function TransToday() {
                $.ajax({
                    url: urlTransToday,
                    type: 'GET',
                    success: function(response) {
                        $("#trans-today").html(response.view);
                    }
                });
            }

            function newCustomer() {
                $("#mymodal").find('.modal-body').html('<center><i class="ph-spinner spinner"></i></center>')
                    .end().find('.modal-title').text('New Customer')
                    .end().find('.modal-header').removeClass('bg-indigo').addClass('bg-success')
                    .end().find('.modal-dialog').attr('class', 'modal-dialog modal-dialog-scrollable')
                    .end().modal('show');

                $.ajax({
                    url: urlFormCustomer,
                    type: 'GET',
                    success: function(response) {
                        $("#mymodal").find('.modal-body').html(response);

                        $('.select').select2();

                        $(".daterange-single").daterangepicker({
                            singleDatePicker: true,
                            locale: {
                                format: "DD-MM-YYYY"
                            },
                        });
                    }
                });
            }

            function clearPos() {
                clearTransactionDetails();
                toggleDivs('notready', 'pos-system');
            }

            function clearTransactionDetails() {
                $('#trans-no').html('');
                $('#trans-cust-name').html('-');
                $('#trans-cust-wa').html('-');
                $('#trans-id').val('');
                $(".order-list").html('');
            }

            const RemoveLines = (url) => {
                const transId = $('#trans-id').val();
                CustomAjax(url, 'GET', {}, () => {
                    OrderList(transId);
                    TransToday();
                });
            }



            const BtnOption = (title, el, e) => {
                e.preventDefault();
                SwalConfirmAjax(title, $(el).data('url'), 'GET', {
                    status: title
                }, () => {
                    clearPos();
                    TransToday();
                    hTable.ajax.reload(null, false);
                });
            }

            const BtnOptionVerify = (title, el, e) => {
                e.preventDefault();

                textHtml = '{!! Form::text('qty_fix', null, [
                    'class' => 'form-control uang mt-2 mb-2 field-paid',
                    'placeholder' => 'Nominal yang dibayarkan',
                    'autofocus' => true,
                ]) !!}';

                textHtml += '{!! Form::select('tipe_pembayaran_id', $data['list_tipe_pembayaran'], null, [
                    'class' => 'form-select field-tipe',
                    'data-placeholder' => 'Pilih Tipe Pembayaran',
                ]) !!}';

                swalInit.fire({
                    icon: 'question',
                    title: 'Verifikasi Pembayaran',
                    html: textHtml,
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                    reverseButtons: true,
                    showLoaderOnConfirm: true,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        // The textfield element
                        textField = Swal.getPopup().querySelector(".field-paid")
                        textField?.focus();

                        $("input.uang").keyup(function(event) {

                            // skip for arrow keys
                            if (event.which >= 37 && event.which <= 40) {
                                event.preventDefault();
                            }

                            $(this).val(function(index, value) {
                                value = value.replace(/,/g, '');
                                return numberWithCommas(value);
                            });
                        });
                    },
                    preConfirm: (value) => {
                        let paid = Swal.getPopup().querySelector(".field-paid")?.value;
                        let tipePembayaran = Swal.getPopup().querySelector(".field-tipe")?.value;

                        return $.ajax({
                            type: 'GET',
                            url: $(el).data('url'),
                            data: {
                                status: title,
                                amount_paid: paid,
                                tipe_pembayaran_id: tipePembayaran,
                            },
                            dataType: "json",
                        }).done(function(data) {
                            return data;
                        }).fail(function(jqXHR, textStatus, errorThrown) {
                            if (jqXHR.status == 422) {
                                var xhr = JSON.stringify(JSON.parse(jqXHR.responseText)
                                    .errors);
                            } else {
                                var xhr = JSON.stringify(JSON.parse(jqXHR
                                    .responseText));
                            }
                            swalInit.fire({
                                title: 'Request Error',
                                text: xhr.substring(0, 160),
                                icon: 'error',
                            })
                        })
                    },
                }).then((result) => {
                    if (result.value != null)
                        if (result.value.status) {
                            swalInit.fire({
                                title: 'Success',
                                text: result.value.msg,
                                icon: 'success',
                                didClose: () => {
                                    clearPos();
                                    TransToday();
                                    hTable.ajax.reload(null, false);
                                    $("#mymodal").modal('hide');
                                }
                            })
                        } else {
                            swalInit.fire({
                                title: 'Error',
                                text: result.value.msg.substring(0, 160),
                                icon: 'error',
                            })
                        }
                });
            }

            const BtnOptionReject = (title, el, e) => {
                e.preventDefault();

                textHtml = '{!! Form::text('qty_fix', null, [
                    'class' => 'form-control mt-2 field-qty',
                    'placeholder' => 'Keterangan',
                    'autofocus' => true,
                ]) !!}';

                swalInit.fire({
                    icon: 'question',
                    title: 'Batalkan Transaksi',
                    html: textHtml,
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                    reverseButtons: true,
                    showLoaderOnConfirm: true,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        // The textfield element
                        textField = Swal.getPopup().querySelector(".field-qty")
                        textField?.focus()
                    },
                    preConfirm: (value) => {
                        let keterangan = Swal.getPopup().querySelector(".field-qty")?.value;

                        return $.ajax({
                            type: 'GET',
                            url: $(el).data('url'),
                            data: {
                                status: title,
                                keterangan: keterangan,
                            },
                            dataType: "json",
                        }).done(function(data) {
                            return data;
                        }).fail(function(jqXHR, textStatus, errorThrown) {
                            if (jqXHR.status == 422) {
                                var xhr = JSON.stringify(JSON.parse(jqXHR.responseText)
                                    .errors);
                            } else {
                                var xhr = JSON.stringify(JSON.parse(jqXHR
                                    .responseText));
                            }
                            swalInit.fire({
                                title: 'Request Error',
                                text: xhr.substring(0, 160),
                                icon: 'error',
                            })
                        })
                    },
                }).then((result) => {
                    if (result.value != null)
                        if (result.value.status) {
                            swalInit.fire({
                                title: 'Success',
                                text: result.value.msg,
                                icon: 'success',
                                didClose: () => {
                                    clearPos();
                                    TransToday();
                                    hTable.ajax.reload(null, false);
                                    $("#mymodal").modal('hide');
                                }
                            })
                        } else {
                            swalInit.fire({
                                title: 'Error',
                                text: result.value.msg.substring(0, 160),
                                icon: 'error',
                            })
                        }
                });
            }

            const ClearFormLines = (formId) => {
                $(formId).find('select:not([dont-clear])').val('').trigger('change');
                $(formId).find('input:not([disabled])').val('');
            }

            setInterval(function() {
                TransToday();
                hTable.rows().deselect();
                hTable.ajax.reload(null, false);
            }, 10000);
        </script>
    @endif
@endsection

@section('appmodal')
    <!-- Basic modal -->
    <div id="mymodal" class="modal" data-bs-focus="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-indigo text-white">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>
    <!-- /basic modal -->
@endsection

@section('notification')
    @include('layouts.notification')
@endsection
