@extends('layouts.header')
@section('customcss')
@endsection

@section('customjs')
    <script src="https://cdn.socket.io/4.1.2/socket.io.min.js"></script>
    <script>
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
    </script>
@endsection

@section('content')
    <!-- Page header -->
    <div class="page-header">
        <div class="page-header-content container d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Home - <span class="fw-normal">Dashboard</span>
                </h4>

                <a href="#page_header"
                    class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
                    data-bs-toggle="collapse">
                    <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                </a>
            </div>

            <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page_header">
                <div class="d-sm-flex align-items-center mb-3 mb-lg-0 ms-lg-3">
                    <div class="dropdown w-100 w-sm-auto">
                        <a href="#" class="d-flex align-items-center text-body lh-1 dropdown-toggle py-sm-2"
                            data-bs-toggle="dropdown" data-bs-display="static">
                            <img src="{{ asset('assets/images/brands/tesla.svg') }}" class="w-32px h-32px me-2"
                                alt="">
                            <div class="me-auto me-lg-1">
                                <div class="fs-sm text-muted mb-1">Customer</div>
                                <div class="fw-semibold">Tesla Motors Inc</div>
                            </div>
                        </a>

                        <div class="dropdown-menu dropdown-menu-lg-end w-100 w-lg-auto wmin-300 wmin-sm-350 pt-0">
                            <div class="d-flex align-items-center p-3">
                                <h6 class="fw-semibold mb-0">Customers</h6>
                                <a href="#" class="ms-auto">
                                    View all
                                    <i class="ph-arrow-circle-right ms-1"></i>
                                </a>
                            </div>
                            <a href="#" class="dropdown-item active py-2">
                                <img src="{{ asset('assets/images/brands/tesla.svg') }}" class="w-32px h-32px me-2"
                                    alt="">
                                <div>
                                    <div class="fw-semibold">Tesla Motors Inc</div>
                                    <div class="fs-sm text-muted">42 users</div>
                                </div>
                            </a>
                            <a href="#" class="dropdown-item py-2">
                                <img src="{{ asset('assets/images/brands/debijenkorf.svg') }}" class="w-32px h-32px me-2"
                                    alt="">
                                <div>
                                    <div class="fw-semibold">De Bijenkorf</div>
                                    <div class="fs-sm text-muted">49 users</div>
                                </div>
                            </a>
                            <a href="#" class="dropdown-item py-2">
                                <img src="{{ asset('assets/images/brands/klm.svg') }}" class="w-32px h-32px me-2"
                                    alt="">
                                <div>
                                    <div class="fw-semibold">Royal Dutch Airlines</div>
                                    <div class="fs-sm text-muted">18 users</div>
                                </div>
                            </a>
                            <a href="#" class="dropdown-item py-2">
                                <img src="{{ asset('assets/images/brands/shell.svg') }}" class="w-32px h-32px me-2"
                                    alt="">
                                <div>
                                    <div class="fw-semibold">Royal Dutch Shell</div>
                                    <div class="fs-sm text-muted">54 users</div>
                                </div>
                            </a>
                            <a href="#" class="dropdown-item py-2">
                                <img src="{{ asset('assets/images/brands/bp.svg') }}" class="w-32px h-32px me-2"
                                    alt="">
                                <div>
                                    <div class="fw-semibold">BP plc</div>
                                    <div class="fs-sm text-muted">23 users</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="vr d-none d-sm-block flex-shrink-0 my-2 mx-3"></div>

                    <div class="d-inline-flex mt-3 mt-sm-0">
                        <a href="#" class="status-indicator-container ms-1">
                            <img src="{{ asset('assets/images/demo/users/face24.jpg') }}" class="w-32px h-32px rounded-pill"
                                alt="">
                            <span class="status-indicator bg-warning"></span>
                        </a>
                        <a href="#" class="status-indicator-container ms-1">
                            <img src="{{ asset('assets/images/demo/users/face1.jpg') }}" class="w-32px h-32px rounded-pill"
                                alt="">
                            <span class="status-indicator bg-success"></span>
                        </a>
                        <a href="#" class="status-indicator-container ms-1">
                            <img src="{{ asset('assets/images/demo/users/face3.jpg') }}" class="w-32px h-32px rounded-pill"
                                alt="">
                            <span class="status-indicator bg-danger"></span>
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-icon w-32px h-32px rounded-pill ms-3">
                            <i class="ph-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /page header -->


    <!-- Content area -->
    <div class="content container pt-0">

        <!-- Blocks with chart -->
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h1>Scan QR Code</h1>
                        <img src="./assets/loader.gif" alt="loading" id="qrcode" style="width: 250px" />
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header d-flex border-bottom-0 pb-1 mb-0">
                        <div>
                            <span class="fw-medium mb-1">New opportunities</span>
                            <h2 class="fw-bold mb-0">2,785 <small class="text-success fs-base fw-normal ms-2">+
                                    5.9%</small></h2>
                        </div>

                        <div class="dropdown ms-auto">
                            <button type="button" data-bs-toggle="dropdown"
                                class="btn btn-outline-light btn-icon btn-sm text-body border-transparent rounded-pill">
                                <i class="ph-dots-three-vertical"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="#" class="dropdown-item">
                                    <i class="ph-eye me-2"></i>
                                    View report
                                </a>
                                <a href="#" class="dropdown-item">
                                    <i class="ph-printer me-2"></i>
                                    Print report
                                </a>
                                <a href="#" class="dropdown-item">
                                    <i class="ph-file-pdf me-2"></i>
                                    Export report
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item">
                                    <i class="ph-gear me-2"></i>
                                    Configure
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="chart-container">
                        <div class="chart" id="area_gradient_orange" style="height: 100px"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header d-flex border-bottom-0 pb-1 mb-0">
                        <div>
                            <span class="fw-medium mb-1">New leads</span>
                            <h2 class="fw-bold mb-0">1,589 <small class="text-danger fs-base fw-normal ms-2">-
                                    1.8%</small></h2>
                        </div>

                        <div class="dropdown ms-auto">
                            <button type="button" data-bs-toggle="dropdown"
                                class="btn btn-outline-light btn-icon btn-sm text-body border-transparent rounded-pill">
                                <i class="ph-dots-three-vertical"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="#" class="dropdown-item">
                                    <i class="ph-eye me-2"></i>
                                    View report
                                </a>
                                <a href="#" class="dropdown-item">
                                    <i class="ph-printer me-2"></i>
                                    Print report
                                </a>
                                <a href="#" class="dropdown-item">
                                    <i class="ph-file-pdf me-2"></i>
                                    Export report
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item">
                                    <i class="ph-gear me-2"></i>
                                    Configure
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="chart-container">
                        <div class="chart" id="area_gradient_green" style="height: 100px"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /blocks with chart -->

    </div>
    <!-- /content area -->
@endsection
@section('notification')
    @include('layouts.notification')
@endsection
