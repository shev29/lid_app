<!DOCTYPE html>
<html lang="en">
<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/select2.min.css?v=11.4') }}" />
	<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/snackbar.css?v=8.1') }}">
	<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/sweetalert2.min.css') }}">

    {{-- @stack('before-styles') --}}
    {{-- @vite('resources/sass/app.scss') --}}
    {{-- @stack('after-styles') --}}
    @yield('extra_css')
</head>
<body>
    <div id="pdfPrintArea"></div>
    <div id="loadingSpinner" class="loading-overlay d-none">
        <div class="center-container">
            <div class="stripes-red-blue stripes-red-blue-lg"></div>
            <div class="d-block fs-6 mt-2">Processing...</div>
        </div>
        {{-- <div class="spinner-border text-company" role="status">
            <span class="visually-hidden">Processing...</span>
        </div>
        <p class="mt-2 text-company">Processing...</p> --}}
    </div>
    <div class="fullscreen-container">
        <div class="fullscreen-header">
            <button type="button" class="btn btn-back-white fullscreenHeaderCloseBtn"><i class="fa-solid fa-arrow-left"></i></button>
            <span class="fullscreen-title"></span>
            <button type="button" class="btn btn-close-white fullscreenHeaderCloseBtn"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="fullscreen-content" id="fullscreen-content-container">
            <div class="loading-content">
                <div class="spinner-border text-company" role="status"></div>
                <p class="mt-2 text-company fs-7">Loading...</p>
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
            <div class="loading-content">
                <div class="spinner-border text-company" role="status"></div>
                <p class="mt-2 text-company fs-7">Loading...</p>
            </div>
        </div>
    </div>
    <div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
        <div class="sidebar-header border-bottom">
            <div class="sidebar-brand">
                <img  class="sidebar-brand-full" src="{{ asset('assets/images/logisteed-logo-white-min.png')}}" alt="LOGISTEED"/>
                <img  class="sidebar-brand-narrow" src="{{ asset('assets/images/logisteed-icon-48.png')}}" width="37" height="28" alt="LOGISTEED"/>
            </div>
        </div>
        @include('layouts.includes.sidebar_nav')       
    </div>
    <div class="wrapper d-flex flex-column min-vh-100">
        @include('layouts.includes.header')
        @include('layouts.includes.aside')
        <div class="body flex-grow-1">
            @yield('content')            
        </div>
        @include('layouts.includes.footer')
    </div>

    <div class="modal fade" id="modalMdGeneral" data-coreui-backdrop="static" data-coreui-keyboard="false" data-coreui-focus="false" tabindex="-1"
        aria-labelledby="modalMdGeneral" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-md my-md-2-2" id="modalMdGeneralDialog">
            <div class="modal-content h-100">
                <div class="modal-header" id="modalMdGeneralHeader" style="background-color:#fff !important; border-bottom: none !important;">
                    <h6 class="modal-title modal-title-black" id="modalMdGeneralTitle"></h6>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal"  title="Close" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0 pb-3" id="modalMdGeneralBody">
                </div>
                <div class="modal-footer" id="modalMdGeneralFooter" style="background-color:#fff !important; border-top: none !important;">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalLgGeneral" data-coreui-backdrop="static" data-coreui-keyboard="false" data-coreui-focus="false" tabindex="-1"
        aria-labelledby="modalLgGeneral" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-lg my-md-2-2" id="modalLgGeneralDialog">
            <div class="modal-content h-100">
                <div class="modal-header" id="modalLgGeneralHeader" style="background-color:#fff !important; border-bottom: none !important;">
                    <h6 class="modal-title modal-title-black" id="modalLgGeneralTitle"></h6>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal"  title="Close" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0 pb-3" id="modalLgGeneralBody">
                </div>
                <div class="modal-footer" id="modalLgGeneralFooter" style="background-color:#fff !important; border-top: none !important;">
                </div>
            </div>
        </div>
    </div>
    
</body>
<script src="{{ asset('js/vendor/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/vendor/app-bundle.min.js') }}"></script>
<script src="{{ asset('js/vendor/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('js/vendor/jquery-ui-1.10.3.min.js') }}"></script>
<script src="{{ asset('js/vendor/dataTables.js') }}"></script>
<script src="{{ asset('js/vendor/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('js/vendor/dataTables.simple_numbers_no_ellipses.js') }}"></script>
<script src="{{ asset('js/vendor/config.js') }}"></script>
{{-- <script src="{{ asset('js/vendor/color-modes.js') }}"></script> --}}
<script src="{{ asset('js/locale/dayjs.min.js') }}"></script>
<script src="{{ asset('js/locale/customParseFormat.js') }}"></script>
{{-- <script src="{{ asset('js/locale/id.js') }}"></script> --}}
<script src="{{ asset('js/locale/moment-with-locales.js') }}"></script>
<script src="{{ asset('js/vendor/virtual-select.min.js') }}"></script>
<script src="{{ asset('js/vendor/select2.full.min.js') }}"></script>
<script src="{{ asset('js/vendor/snackbar.js') }}"></script>
<script src="{{ asset('js/vendor/ellipsis.js') }}"></script>
<script src="{{ asset('js/vendor/autosize.js?v=7.1') }}"></script>
<script src="{{ asset('js/vendor/popper.min.js') }}"></script>
<script src="{{ asset('js/vendor/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('js/app/app.js?v=15.5') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {


        // var obj = {};
        // $.fn.serializeObject = function(){
        //     $.each( this.serializeArray(), function(i,o){
        //         var n = o.name, v = o.value;
        //         obj[n] = obj[n] === undefined ? v : $.isArray( obj[n] ) ? obj[n].concat( v ) : [obj[n], v];
        //     });
        //     return obj;
        // };
        // $('.autosize').autosize();
        // $('.select2').select2({ allowClear: false, placeholder: '-- Select --'});

    });
</script>
@yield('extra_js')
{{-- <script src="js/cui/main.js"></script> --}}
    {{-- @stack('before-scripts') --}}
    {{-- <script src="node_modules/@coreui/coreui/dist/js/coreui.bundle.min.js"></script>
    <script src="node_modules/simplebar/dist/simplebar.min.js"></script> --}}
    {{-- @vite('resources/js/app.js') --}}
    {{-- @stack('after-scripts') --}}
</html>