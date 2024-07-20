@extends('layouts.header')

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
                    <h4 class="fw-semibold">{{ $title ?? '' }}</h4>
                </div>

                <a href="#page_header"
                    class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
                    data-bs-toggle="collapse">
                    <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                </a>
            </div>

            <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page_header">
                <div class="hstack gap-0 mb-3 mb-lg-0">
                    <a href="#!" onclick="newCustomer()"
                        class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold"><i
                            class="ph-plus-circle ph-2x text-indigo"></i>NEW CUSTOMER</a>

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
                                    <div class="col-lg-4">
                                        <div class="card" onclick="ChoosePackage({{ $package->id }},'package')">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <a href="#"
                                                        class="bg-success bg-opacity-10 text-success lh-1 rounded-pill p-2">
                                                        <i class="ph ph-users ph-2x"></i>
                                                    </a>
                                                    <div class="ms-3">
                                                        <span class="fw-bold mb-0">{{ $package->name }}</span>
                                                        <br>
                                                        <span class="text-muted">Rp
                                                            {{ cleanNumber($package->harga) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <h4 class="fw-semibold mb-3">Add On</h4>
                            <div class="row mb-3">
                                @foreach ($data['list_addon'] as $addon)
                                    <div class="col-lg-4">
                                        <div class="card" onclick="ChoosePackage({{ $addon->id }},'addon')">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <a href="#"
                                                        class="bg-primary bg-opacity-10 text-primary lh-1 rounded-pill p-2">
                                                        <i class="ph ph-users ph-2x"></i>
                                                    </a>
                                                    <div class="ms-3">
                                                        <span class="fw-bold mb-0">{{ $addon->name }}</span>
                                                        <br>
                                                        <span class="text-muted">Rp
                                                            {{ cleanNumber($addon->harga) }}</span>
                                                    </div>
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
                            <img src="{{ asset('app_local/img/logo.png') }}" style="max-width: 400px;" class="img-fluid m-5"
                                alt="" srcset="">
                            <div class="text-muted fs-lg">Silahkan memilih customer terlebih dahulu</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-success d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 text-white" id="trans-no"></h4>
                        <div class="w-50">
                            {!! Form::select('transaction_id', $data['list_customer_today'], null, [
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
            </div>
        </div>

    </div>
    <!-- /content area -->
@endsection

@section('customjs')
    <script src="{{ asset('app_local/js/swal.js') }}"></script>
    <script>
        const urlCustomer = '{{ route('pos.get-customer') }}';

        $(document).ready(function() {
            SelectRemoteData('.select-cust', urlCustomer);
            $('.select-cust').val(null).trigger('change');

            TransToday();

            hideDiv('pos-system');
            showDiv('notready');


            $('body').on("submit", '#l-modal-form', function(e) {
                e.preventDefault();

                let urlForm = $(this).attr('action');
                let dataForm = $(this).serialize();

                CustomAjax(urlForm, 'POST', dataForm, (response) => {
                    $('.select-cust').val(response.data.id).trigger('change');
                    $("#mymodal").modal('hide');
                });
            });

        });

        function changeTrans(el, e) {
            $('#trans-no').html('');
            $('#trans-cust-name').html('-');
            $('#trans-cust-wa').html('-');
            $('#trans-id').val('');
            $(".order-list").html('');

            hideDiv('pos-system');
            showDiv('notready');

            transId = $(el).val();
            if (transId != '') {
                $.ajax({
                    url: '{{ route('pos.get-transaction') }}',
                    type: 'GET',
                    data: {
                        id: transId
                    },
                    success: function(response) {
                        if (response.status) {
                            showDiv('pos-system');
                            hideDiv('notready');

                            $('#trans-id').val(response.data.id);
                            $('#trans-no').html(response.data.no);
                            $('#trans-cust-name').html(response.data.customer_name);
                            $('#trans-cust-wa').html(response.data.customer_whatsapp);

                            OrderList(response.data.id);
                            TransToday();
                        }

                    }
                });
            }
        }

        function ChooseTrans(id) {
            $('#trans-no').html('');
            $('#trans-cust-name').html('-');
            $('#trans-cust-wa').html('-');
            $('#trans-id').val('');
            $(".order-list").html('');

            hideDiv('pos-system');
            showDiv('notready');

            $('.select-cust').val(null).trigger('change');

            transId = id;
            if (transId != '') {
                $.ajax({
                    url: '{{ route('pos.get-transaction') }}',
                    type: 'GET',
                    data: {
                        id: transId
                    },
                    success: function(response) {
                        if (response.status) {
                            showDiv('pos-system');
                            hideDiv('notready');

                            $('#trans-id').val(response.data.id);
                            $('#trans-no').html(response.data.no);
                            $('#trans-cust-name').html(response.data.customer_name);
                            $('#trans-cust-wa').html(response.data.customer_whatsapp);

                            OrderList(response.data.id);
                            TransToday();
                        }

                    }
                });
            }
        }

        function ChoosePackage(id, type) {
            const transId = $('#trans-id').val();

            $.ajax({
                url: '{{ route('pos.choose-package') }}',
                type: 'GET',
                data: {
                    id: id,
                    transaction_id: transId,
                    type: type,
                },
                success: function(response) {
                    OrderList(response.data.id);
                    TransToday();
                }
            });
        }

        function OrderList(id) {
            $.ajax({
                url: '{{ route('pos.order-list') }}',
                type: 'GET',
                data: {
                    id: id
                },
                success: function(response) {
                    $(".order-list").html(response.view);
                }
            });
        }

        function TransToday() {
            $.ajax({
                url: '{{ route('pos.trans-today') }}',
                type: 'GET',
                success: function(response) {
                    $("#trans-today").html(response.view);
                }
            });
        }

        function newCustomer() {
            $("#mymodal").find('.modal-body').html('<center><i class="ph-spinner spinner"></i></center>');
            $("#mymodal").find('.modal-title').html('New Customer');
            $("#mymodal").find('.modal-header').removeClass('bg-indigo').addClass('bg-success');
            $("#mymodal").find('.modal-dialog').removeAttr('class').attr('class',
                'modal-dialog modal-dialog-scrollable');
            $("#mymodal").modal('show');
            // $(".modal-backdrop.show").css('opacity', .05);

            $.ajax({
                url: '{{ route('pos.form-customer') }}',
                type: 'GET',
                success: function(response) {
                    $("#mymodal").find('.modal-body').html(response);

                    $(".daterange-single").daterangepicker({
                        singleDatePicker: true,
                        locale: {
                            format: "DD-MM-YYYY",
                        },
                    });

                }
            });
        }

        function clearPos() {
            $('#trans-no').html('');
            $('#trans-cust-name').html('-');
            $('#trans-cust-wa').html('-');
            $('#trans-id').val('');
            $(".order-list").html('');

            hideDiv('pos-system');
            showDiv('notready');
        }

        const RemoveLines = (url, lines_id, e) => {
            const transId = $('#trans-id').val();

            CustomAjax(url, 'GET', {}, () => {
                OrderList(transId);
                TransToday();
            });
        }

        function showDiv(id) {
            document.getElementById(id).style.display = 'block';
        }

        function hideDiv(id) {
            document.getElementById(id).style.display = 'none';
        }

        const BtnOption = (title, el, e) => {
            e.preventDefault();

            SwalConfirmAjax(title, $(el).data('url'), 'GET', {
                'status': title,
            }, () => {
                clearPos();
                TransToday();
            });
        }

        const ClearFormLines = (formId) => {
            $(formId).find('select:not([dont-clear])').val('').trigger('change');
            $(formId).find('input:not([disabled])').val('');
        }
    </script>
@endsection

@section('appmodal')
    <!-- Basic modal -->
    <div id="mymodal" class="modal" tabindex="-1">
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
