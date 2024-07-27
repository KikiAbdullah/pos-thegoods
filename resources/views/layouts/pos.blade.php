<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Apple devices fullscreen -->
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <!-- Apple devices fullscreen -->
    <meta names="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <title>{{ $title . ' - ' . env('APP_NAME') }}</title>

    <link rel="icon" href="{{ asset('assets/images/logo_icon.svg') }}" />

    <!-- Global stylesheets -->
    <link href="{{ asset('assets/fonts/inter/inter.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/icons/phosphor/styles.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/ltr/all.min.css') }}" id="stylesheet" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->
    <link href="{{ asset('css/logo-font.css') }}" id="stylesheet" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/style.css') }}" id="stylesheet" rel="stylesheet" type="text/css">
    @yield('customcss')

    <!-- Core JS files -->
    <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/notifications/sweet_alert.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/forms/selects/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/tables/datatables/extensions/select.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/ui/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/pickers/daterangepicker.js') }}"></script>
    <!-- /core JS files -->

    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script type="text/javascript">
        const _csrf_token = "{{ csrf_token() }}";
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': _csrf_token
                }
            });
        });
    </script>

    <script src="{{ asset('app_local/js/theme.js') }}"></script>
    <script src="{{ asset('app_local/js/settings.js') }}"></script>
</head>

<body>
    <!-- Page content -->
    <div class="page-content">
        <!-- Main content -->
        <div class="content-wrapper">
            <!-- Inner content -->
            <div class="content-inner">

                @yield('content')
                @include('layouts.inner-footer')

            </div>
            <!-- /inner content -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->
    @yield('notification')

    @include('layouts.footer')
    @yield('appmodal')
    @yield('customjs')
</body>

</html>
