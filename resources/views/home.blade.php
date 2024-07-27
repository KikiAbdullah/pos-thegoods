@extends('layouts.header')

@section('customcss')
@endsection

@section('content')
    <!-- Page header -->
    <div class="page-header">
        <div class="page-header-content container d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                </h4>

                <a href="#page_header"
                    class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
                    data-bs-toggle="collapse">
                    <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- /page header -->


    <!-- Content area -->
    <div class="content container pt-0">

        <div class="row">
            <div class="col-lg-12">
                <div class="card p-3" style="border-radius: 25px !important;">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6">
                                <div class="d-flex align-items-center mb-3 mb-lg-0">
                                    <a href="#" class="bg-primary bg-opacity-10 text-primary lh-1 rounded-pill p-2">
                                        <i class="ph ph-receipt ph-2x"></i>
                                    </a>
                                    <div class="ms-3">
                                        <h5 class="mb-0">{{ $total['all'] ?? 0 }} Transaksi</h5>
                                        <span class="text-muted">Jumlah Transaksi</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">

                                <div class="d-flex align-items-center mb-3 mb-lg-0">
                                    <a href="#" class="bg-danger bg-opacity-10 text-danger lh-1 rounded-pill p-2">
                                        <i class="ph ph-warning ph-2x"></i>
                                    </a>
                                    <div class="ms-3">
                                        <h5 class="mb-0">{{ $total['open'] ?? 0 }} Customer</h5>
                                        <span class="text-muted">Belum dilayani</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="d-flex align-items-center mb-3 mb-lg-0">
                                    <a href="#" class="bg-warning bg-opacity-10 text-warning lh-1 rounded-pill p-2">
                                        <i class="ph ph-users-three ph-2x"></i>
                                    </a>
                                    <div class="ms-3">
                                        <h5 class="mb-0">{{ $total['ordered'] ?? 0 }} Customer</h5>
                                        <span class="text-muted">Sedang Dilayani</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">

                                <div class="d-flex align-items-center mb-3 mb-lg-0">
                                    <a href="#" class="bg-success bg-opacity-10 text-success lh-1 rounded-pill p-2">
                                        <i class="ph ph-check ph-2x"></i>
                                    </a>
                                    <div class="ms-3">
                                        <h5 class="mb-0">{{ $total['verify'] ?? 0 }} Transaksi</h5>
                                        <span class="text-muted">Selesai</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blocks with chart -->
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="mb-0">Scan QR Code for Whatsapp</h5>

                        <img src="./assets/loader.gif" alt="loading" id="qrcode" class="img-fluid m-2"
                            style="width: 200px" />

                        <form id="messageForm" class="mt-3">
                            <div class="row mb-3">
                                <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Number</label>
                                <div class="col-lg-9">
                                    {!! Form::text('number', null, [
                                        'class' => 'form-control',
                                        'data-placeholder' => 'Number',
                                        'id' => 'number',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Message</label>
                                <div class="col-lg-9">
                                    {!! Form::text('message', null, [
                                        'class' => 'form-control',
                                        'data-placeholder' => 'Message',
                                        'id' => 'message',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="d-flex justify-content-end align-items-center">
                                <button type="submit" class="btn btn-secondary btn-labeled btn-labeled-start rounded-pill">
                                    <span class="btn-labeled-icon bg-black bg-opacity-20 m-1 rounded-pill">
                                        <i class="ph-magnifying-glass" id="submit_loader"></i>
                                    </span>
                                    Send
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="mb-0">Recent Activity</h5>
                    </div>

                    <div class="card-body">
                        @foreach ($userlog as $log)
                            <div class="d-flex mb-3">
                                <div class="me-3">
                                    <div class="bg-primary bg-opacity-10 text-primary lh-1 rounded-pill p-2">
                                        <i class="ph-activity"></i>
                                    </div>
                                </div>
                                <div class="flex-fill">
                                    {!! $log->message ?? '' !!}
                                    <div class="text-muted fs-sm">{{ time_elapsed_string($log->created_at) }}</div>
                                </div>
                            </div>
                        @endforeach


                    </div>
                </div>
            </div>
        </div>
        <!-- /blocks with chart -->

    </div>
    <!-- /content area -->
@endsection

@section('customjs')
    <script src="{{ asset('app_local/js/swal.js') }}"></script>
    <script src="https://cdn.socket.io/4.1.2/socket.io.min.js"></script>
    <script>
        $(document).ready(function() {
            const socket = io('http://localhost:3000');

            socket.on("qr", (src) => {
                qrcode.setAttribute("src", src);
                qrcode.setAttribute("alt", "qrcode");
            });
            socket.on("qrstatus", (src) => {
                qrcode.setAttribute("src", src);
                qrcode.setAttribute("alt", "loading");
            });

            socket.on("log", (log) => {
                console.log(log);
            });


            $('body').on("submit", '#messageForm', function(e) {
                e.preventDefault();

                const number = $('#number').val();
                const message = $('#message').val();

                $.ajax({
                    type: 'POST',
                    url: 'http://localhost:3000/send-message',
                    data: JSON.stringify({
                        number,
                        message
                    }),
                    contentType: 'application/json',
                    success: function(response) {
                        swalInit.fire({
                            title: "Success",
                            text: 'Berhasil',
                            icon: "success",
                        });
                        $('#number').val('');
                        $('#message').val('');
                    },
                    error: function(error) {

                        swalInit.fire({
                            title: "Error",
                            text: 'Failed to send message: ' + error.responseText,
                            icon: "error",
                        });
                    }
                });
            });
        });
    </script>
@endsection

@section('notification')
    @include('layouts.notification')
@endsection
