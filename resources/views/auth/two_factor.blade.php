<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Two Factor Auth - {{ env('APP_NAME') }}</title>

    <!-- Global stylesheets -->
    <link href="{{ asset('assets/fonts/inter/inter.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/icons/phosphor/styles.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/ltr/all.min.css') }}" id="stylesheet" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/logo-font.css') }}" id="stylesheet" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/style.css') }}" id="stylesheet" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->

    <!-- Core JS files -->
    <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery/jquery.min.js') }}"></script>
    <!-- /core JS files -->

    <!-- Theme JS files -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('app_local/js/theme.js') }}"></script>
    <!-- /theme JS files -->
    <style>
        body {
            background-image: url('{{ asset('assets/slide-1.jpg') }}');
            background-size: 140%;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: top;
            margin-bottom: 40px;
        }

        .input {
            width: 50px;
            border: none;
            border: 3px solid rgba(0, 0, 0, 0.1);
            margin: 0 6px 6px 0;
            text-align: center;
            font-size: 36px;
            border-radius: 12px;
            font-family: 'Rubik';
        }

        @media only screen and (max-width: 600px) {
            .input {
                width: 30px;
                height: 40px;
                border: 2px solid rgba(0, 0, 0, 0.1);
                border-radius: 8px;
                font-size: 26px;
            }
        }

        .input:focus {
            border: 3px solid orange;
            outline: none;
        }

        .input:nth-child(1) {
            cursor: pointer;
            pointer-events: all;
        }
    </style>

</head>

<body>

    <!-- Page content -->
    <div class="page-content">

        <!-- Main content -->
        <div class="content-wrapper">

            <!-- Inner content -->
            <div class="content-inner">

                <!-- Content area -->
                <div class="content d-flex justify-content-center mt-lg-5">

                    {!! Form::open(['route' => 'verifyTwoFactor', 'class' => 'login-form']) !!}
                    <!-- Unlock form -->
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="text-center">
                                <i class="ph-lock ph-3x text-primary"></i>
                            </div>

                            <div class="text-center mb-3">
                                <h6 class="font-weight-semibold mb-0">Two Factor Authentication</h6>
                                @if (!empty($nowa))
                                    <span class="d-block">Please enter OTP Code that we've sent to
                                        <b>{{ $nowa ?? '' }}</b></span>
                                @endif
                            </div>

                            <div class="text-center mb-3">
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        @foreach ($errors->all() as $error)
                                            {!! $error . '<br/>' !!}
                                        @endforeach
                                    </div>
                                @endif
                                @if (empty($nowa))
                                    <div class="alert alert-danger">
                                        WhatsApp number not found, please contact Administrator.
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <div class="container">
                                    <div id="inputs" class="inputs">
                                        <input class="input" type="text" inputmode="numeric" maxlength="1" autofocus />
                                        <input class="input" type="text" inputmode="numeric" maxlength="1" />
                                        <input class="input" type="text" inputmode="numeric" maxlength="1" />
                                        <input class="input" type="text" inputmode="numeric" maxlength="1" />
                                        <input class="input" type="text" inputmode="numeric" maxlength="1" />
                                    </div>
                                </div>
                                <input type="hidden" name="otp">
                                <input type="hidden" name="redirect" value="{{ request()['redirect'] }}">
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-2"><i class="ph-lock-simple-open mr-4"></i>&nbsp;Confirm</button>
                            <a href="#!" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();" class="btn btn-light w-100"> Back to Login</a>
                        </div>
                    </div>
                    <!-- /unlock form -->
                    {!! Form::close() !!}
                    <form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>

                </div>
                <!-- /content area -->

            </div>
            <!-- /inner content -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->
    <script type="text/javascript">
        const inputs = document.getElementById("inputs");

        inputs.addEventListener("input", function(e) {
            const target = e.target;
            const val = target.value;

            if (isNaN(val)) {
                target.value = "";
                return;
            }

            if (val != "") {
                const next = target.nextElementSibling;
                if (next) {
                    next.value = "";
                    next.focus();
                }
            }
        });

        inputs.addEventListener("keyup", function(e) {
            const target = e.target;
            const key = e.key.toLowerCase();

            if (key == "backspace" || key == "delete") {
                target.value = "";
                const prev = target.previousElementSibling;
                if (prev) {
                    prev.focus();
                }
                return;
            }
        });

        $(function() {
            $("body").on("submit", ".login-form", function(e) {
                const inputs = document.querySelectorAll("input.input");
                let otp = "";
                inputs.forEach(el => {
                    otp += el.value;
                });
                $('input[name="otp"]').val(otp);
                return true;
            });
        });
    </script>
</body>

</html>
