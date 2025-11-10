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
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/dataTables.bootstrap5.css') }}"/>
	<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/virtual-select.min.css') }}">
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/select2.min.css') }}" />
	<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/snackbar.css?v=8.1') }}">
	<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/sweetalert2.min.css') }}">
    @yield('extra_css')
</head>
<body>
    <div id="loadingSpinner" class="loading-overlay d-none">
        <div class="spinner-border text-company" role="status">
            <span class="visually-hidden">Processing...</span>
        </div>
        <p class="mt-2 text-company">Processing...</p>
    </div>
    <div class="fullscreen-container">
        <div class="fullscreen-header">
            <button type="button" class="btn btn-back-white fullscreenHeaderCloseBtn"><i class="fa-solid fa-arrow-left"></i></button>
            <span class="fullscreen-title"></span>
            <button type="button" class="btn btn-close-white fullscreenHeaderCloseBtn"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="fullscreen-content" id="fullscreen-content-container">
            <div class="center-container">
                <div class="stripes-red-blue stripes-red-blue-md"></div>
                <div class="d-block fs-7 mt-2">Loading...</div>
            </div>
        </div>
    </div>
    <div class="fullscreen-container-aside">
        <div class="fullscreen-header">
            {{-- <button type="button" class="btn btn-back-white fullscreenHeaderCloseBtn" style="visibility:hidden"><i class="fa-solid fa-arrow-left"></i></button> --}}
            <span class="fullscreen-title"></span>
            <div>
                <button type="button" class="btn btn-close-white fullscreenHeaderRotateCW" title="Rotate Clockwise"><i class="fa-regular fa-arrow-rotate-right"></i></button>
                <button type="button" class="btn btn-close-white fullscreenHeaderRotateCCW" title="Rotate Counterclockwise"><i class="fa-regular fa-arrow-rotate-left"></i></button>
                <button type="button" class="btn btn-close-white fullscreenHeaderDownload" title="Download Document"><i class="fa-regular fa-download"></i></button>
                <button type="button" class="btn btn-close-white fullscreenHeaderPrint" title="Print Document"><i class="fa-solid fa-print"></i></button>
                <button type="button" class="btn btn-close-white fullscreenHeaderHideAsideBtn" title="Hide Document"><i class="fa-solid fa-chevrons-right"></i></button>
            </div>
        </div>
        <div class="fullscreen-content" id="fullscreen-content-aside">
            <div class="center-container">
                <div class="stripes-red-blue stripes-red-blue-md"></div>
                <div class="d-block fs-7 mt-2">Loading...</div>
            </div>
        </div>
    </div>
    <div class="wrapper d-flex flex-column min-vh-100">
        <header class="header header-sticky justify-content-center p-0 mb-1">
            <div class="container-fluid border-bottom px-3">
                <button class="header-toggler d-md-flex align-items-center" type="button" data-current="hide" style="margin-inline-start: -12px; visibility:hidden !important">
                    <i class="fa-solid fa-bars icon icon-menu icon-md"></i><span style="font-size: 14px; margin-left: 8px;">Approval List</span>
                </button>
                <img  class="sidebar-brand-full justify-content-center" src="{{ asset('assets/images/logisteed-logo-white-min.png')}}" width="210" height="28" alt="LOGISTEED"/>
                <a href="{{ route('login') }}" class="d-none d-md-inline header-link" style="font-size: 14px; margin-left: 8px;"><span style="text-decoration:underline">Login</span> to access all menu <i class="fa-solid fa-arrow-right-to-bracket"></i></a>
            </div>
        </header>
        @include('layouts.includes.aside')
        <div class="body flex-grow-1">
            @yield('content')
        </div>
    </div>

</body>
<script src="{{ asset('js/vendor/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/vendor/app-bundle.min.js') }}"></script>
<script src="{{ asset('js/vendor/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('js/vendor/jquery-ui-1.10.3.min.js') }}"></script>
<script src="{{ asset('js/vendor/dataTables.js') }}"></script>
<script src="{{ asset('js/vendor/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('js/cui/config.js') }}"></script>
<script src="{{ asset('js/locale/dayjs.min.js') }}"></script>
<script src="{{ asset('js/locale/customParseFormat.js') }}"></script>
<script src="{{ asset('js/locale/moment-with-locales.js') }}"></script>
<script src="{{ asset('js/vendor/virtual-select.min.js') }}"></script>
<script src="{{ asset('js/vendor/select2.min.js') }}"></script>
<script src="{{ asset('js/vendor/snackbar.js') }}"></script>
<script src="{{ asset('js/vendor/ellipsis.js') }}"></script>
<script src="{{ asset('js/vendor/autosize.js') }}"></script>
<script src="{{ asset('js/vendor/popper.min.js') }}"></script>
<script src="{{ asset('js/vendor/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('js/app/app.js?v=13.1') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.autosize').autosize();
        $('.select2').select2({ allowClear: false, placeholder: '-- Select --'});
    });
</script>
@yield('extra_js')
</html>