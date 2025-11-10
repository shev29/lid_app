<!DOCTYPE html>
<html lang="en">
<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        {{ config('app.name') }}
    </title>
    <meta name="theme-color" content="#ffffff">
    <link rel="shortcut icon" href="{{ asset('assets/images/logisteed-favicon-48.png') }}">
    <link rel="icon" href="{{ asset('assets/images/logisteed-favicon-48.png') }}">
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/style.css?v=14.1') }}">
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/fontawesome6/css/fontawesome.css') }}">
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/fontawesome6/css/regular.css') }}">
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/fontawesome6/css/light.css') }}">
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/fontawesome6/css/solid.css') }}">
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/fontawesome6/css/duotone.css') }}">
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/fontawesome6/css/v4-font-face.css') }}">
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/fontawesome6/css/v4-shims.css') }}"/>
    <style>
        @media (max-width: 991.98px) {
            body {
                --height-content-full: calc(100vh - var(--cui-header-height));
            }

            .container-content {
                height: var(--height-content-full);
                height: calc((var(--vh, 1vh) * 100) - var(--cui-header-height));
                padding-bottom: 1rem;
            }
        }
        @media (min-width: 991.99px) {
            body {
                overflow: hidden !important;
                --height-content-full: calc(100vh - var(--cui-header-height));
            }

            .container-content {
                height: var(--height-content-full);
                height: calc((var(--vh, 1vh) * 100) - var(--cui-header-height));
                padding-bottom: 1rem;
            }
        }

        .row-content {
            height: 100%;
            overflow: hidden;
        }

        .full-height-column-wrapper {
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
    </style>
</head>
<body style="overflow: hidden !important">
    <div class="wrapper d-flex flex-column min-vh-100">
        <header class="header header-sticky justify-content-center p-0 border-bottom-0">
            <div class="container-fluid px-3 w-auto">
                <img  class="sidebar-brand-full justify-content-center" src="{{ asset('assets/images/logisteed-logo-white-min.png')}}" width="210" height="28" alt="LOGISTEED"/>
            </div>
        </header>
        <div class="body flex-grow-1">
            <div class="container-content p-0">
                <object class="w-100 h-100" id="subfile_frame" data="/framePdf?page=1&zoom=100&token={{$token}}" type="text/html"><param name="allowfullscreen" value="true"></object>
            </div>
        </div>
    </div>

</body>
{{-- <script src="{{ asset('js/vendor/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/vendor/app-bundle.min.js') }}"></script>
<script src="{{ asset('js/vendor/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('js/vendor/jquery-ui-1.10.3.min.js') }}"></script>
<script src="{{ asset('js/cui/config.js') }}"></script> --}}
@yield('extra_js')
</html>