@extends('blank_page')

@section('extra_css')
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/app/scrollbar-custom.css') }}">
<style>
    body {
        overflow: hidden !important;
        --height-content-full: calc(100vh - (var(--cui-header-height) + 1rem));
    }

    .container-content {
        height: var(--height-content-full);
        padding-bottom: 1rem;
    }

    .row-content {
        height: 100%;
        overflow: hidden;
    }

    .left-column-wrapper, .full-height-column-wrapper {
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .left-column-filter {
        padding-right: 1rem;
        margin-bottom: .75rem;
    }

    .search-form-filter {
        top: 100%;
        left: 0;
        width: 100%; /* Adjust the width as needed */
        background-color: #fff;
        padding: 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: var(--cui-border-width) solid var(--cui-border-color);
        border-radius: var(--cui-border-radius);
        z-index: 2;
    }

    .search-filter {
        display: flex;
        align-items: center;
    }

    .left-column {
        height: calc(100% - 50px);
        overflow-y: auto;
        padding-right: 1rem;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .right-column-card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .right-column-card-body {
        flex: 1;
        overflow: hidden;
        position: relative;
    }

    .right-column {
        height: 100%;
        overflow-y: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .card-footer-fixed {
        bottom: 0;
        width: 100%;
        background: white;
        z-index: 1;
        padding-top: 0.75rem !important;
        padding-bottom: 0.75rem !important;
    }

    .left-column::-webkit-scrollbar, .right-column::-webkit-scrollbar {
        display: none;
    }

    @media only screen and (max-width: 768px) {
        .left-column-filter, .left-column {
            padding-right: 0;
        }
    }

    .data-details {
        display: grid;
        grid-template-columns: auto auto 1fr;
        gap: 0.5rem;
        align-items: start;
        line-height: 1 !important;
    }

    .data-row {
        display: contents;
    }

    .data-label {
        font-weight: 600;
        white-space: nowrap;
    }

    .data-separator {
        justify-self: center;
    }

    .data-value {
        overflow: hidden;
        text-wrap: wrap;
    }

    .data-item-list {
        list-style-type: none;
        padding: 0;
        margin: 0;
        line-height: 1.5 !important;
    }

    .data-item-list li {
        padding-left: 1.5em;
        text-indent: -1.5em;
        word-wrap: break-word;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 1;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endsection

@section('content')
<div class="container-content px-2 py-0">
    <div class="row row-content justify-content-center">
        <input type="hidden" id="token" value="{{$token}}">
        <div class="d-none d-md-block col-md-8 full-height-column-wrapper right-column-wrapper ps-0">
            <div class="card right-column-card">
                {{-- <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color); background-color: var(--color-company);"> --}}
                <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-2">
                            <div class="card-title card-title-right-column m-0 d-flex align-items-center gap-1"><div class="skeleton mb-0" style="width: 300px; height: 24px"></div></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center actionHeaderContainer">
                            <div class="skeleton mb-0" style="width: 100px; height: 24px"></div>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-md-4 left-column-wrapper" id="leftColumnApprovalWrapper" style="display:none">
                    <div class="left-column-wrapper left-column" id="leftColumnApproval" data-scroll-top="" style="display:none">
                        <div class="card full-height-column-wrapper">
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <div class="text-center">
                                    <i class="fa-regular fa-circle-notch fa-spin fs-5"></i>
                                    <div class="d-block fs-7 mt-2">Loading...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <div class="card-body right-column-card-body py-0 px-0">
                    <div class="right-column overflow-hidden" data-scroll-top="">
                        <div class="full-height-column-wrapper">
                            <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                <div class="text-center">
                                    <i class="fa-regular fa-circle-notch fa-spin fs-5"></i>
                                    <div class="d-block fs-7 mt-2">Loading...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer card-footer card-footer-fixed d-flex gap-2 border-top-0">
                    <div class="col-sm-3 mb-sm-0">
                        <div class="skeleton mb-0"></div>
                    </div>
                    <div class="col-sm-3 mb-sm-0">
                        <div class="skeleton mb-0"></div>
                    </div>
                    <div class="col-sm-3 mb-sm-0">
                        <div class="skeleton mb-0"></div>
                    </div>
                </div>
            </div>
            <div class="popper-area"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMessage" data-coreui-focus="false" tabindex="-1" aria-labelledby="modalMessage" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-md my-md-2-2" id="modalMessageDialog">
        <div class="modal-content h-100">
            <div class="modal-header" id="modalMessageHeader" style="background-color:#fff !important; border-bottom: none !important;">
                <h6 class="modal-title modal-title-black" id="modalMessageTitle"></h6>
                <button type="button" class="btn-close" data-coreui-dismiss="modal"  title="Close" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0 pb-3" id="modalMessageBody">
            </div>
            <div class="modal-footer" id="modalMessageFooter" style="background-color:#fff !important; border-top: none !important;">
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_js')
{{-- <script src="{{ asset('js/vendor/print.min.js') }}"></script> --}}
<script src="{{ asset('js/app/scrollbar-custom.js?v=9.1') }}"></script>
<script src="{{ asset('js/app/docapproval/view-form.js?v=9.1') }}"></script>
@endsection
