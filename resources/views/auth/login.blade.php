<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - {{ env('APP_NAME') }}</title>

    <!-- Global stylesheets -->
    <link href="{{ asset('assets/fonts/inter/inter.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/icons/phosphor/styles.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/ltr/all.min.css') }}" id="stylesheet" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/logo-font.css') }}" id="stylesheet" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/style.css') }}" id="stylesheet" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->

    <!-- Core JS files -->
    <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <!-- /core JS files -->

    <!-- Theme JS files -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('app_local/js/theme.js') }}"></script>
    <!-- /theme JS files -->

</head>

<body
    style="background-image: url('app_local/img/bg.png'); background-position: center; background-repeat: no-repeat; background-size: cover;">

    <!-- Page content -->
    <div class="page-content">

        <!-- Main content -->
        <div class="content-wrapper">

            <!-- Inner content -->
            <div class="content-inner">

                <!-- Content area -->
                <div class="content d-flex justify-content-center align-items-center">

                    <!-- Login card -->
                    {!! Form::open(['route' => 'login', 'class' => 'login-form']) !!}
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="text-center mt-3 mb-3">
                                <img src="{{ asset('app_local/img/logo.png') }}" class="img-fluid" alt="">
                            </div>
                            <div class="text-center mb-3 fw-semibold">LOGIN PAGE</div>
                            <div class="text-center mb-3">
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        @foreach ($errors->all() as $error)
                                            {!! $error . '<br/>' !!}
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <div class="form-control-feedback form-control-feedback-start">
                                    {!! Form::text('username', null, [
                                        'class' => 'form-control',
                                        'placeholder' => 'Username',
                                        'autofocus' => true,
                                        'onfocus' => 'this.selectionStart = this.selectionEnd = this.value.length;',
                                    ]) !!}
                                    <div class="form-control-feedback-icon">
                                        <i class="ph-user-circle text-muted"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="form-control-feedback form-control-feedback-start">
                                    <input type="password" name="password" class="form-control"
                                        placeholder="•••••••••••">
                                    <div class="form-control-feedback-icon">
                                        <i class="ph-lock text-muted"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                                <label class="form-check">
                                    <input type="checkbox" name="remember" class="form-check-input" checked>
                                    <span class="form-check-label">Remember</span>
                                </label>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary w-100">Sign in</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                    <!-- /login card -->

                </div>
                <!-- /content area -->

            </div>
            <!-- /inner content -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->

</body>

</html>
