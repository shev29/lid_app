@extends('blank_page_approval')

@section('extra_css')
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/app/scrollbar-custom.css') }}">
<style>
    body {
        overflow: hidden !important;
        --height-content-full: calc(100vh - (var(--cui-header-height) + 1.18rem));
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
        margin-bottom: .75rem;
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
        .left-column {
            padding-right: 0;
        }

        .actionHeaderContainer {
            position: relative !important;
            margin-top: 10px;
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
        <div class="col-md-4 left-column-wrapper" id="leftColumnApprovalWrapper">
            <div class="left-column-filter">
                <div class="card">
                    <div class="card-header" style="">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-2">
                                <div class="card-title m-0 d-flex align-items-center gap-1">My Approval Ongoing<span class="start-100 ms-1 badge rounded-pill bg-danger py-015 px-3 fs-8" id="badgeCountOngoing"><i class="fa-regular fa-circle-notch fa-spin"></i></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-2 pt-0 pb-1">
                        <form role="form" class="form-horizontal d-flex flex-wrap" enctype="multipart/form-data" id="filterLeftColumnFormOngoing">
                            <input type="hidden" id="filterStatus" value="ONGOING">
                            <div class="col-12 mb-1">
                                <div id="searchFormOngoing" class="search-form-filter w-100">
                                    <div class="form-group d-flex align-items-center position-relative">
                                        <input type="text" class="form-control d-flex search" name="search" data-iscleared="true" spellcheck="false" autocomplete="off" placeholder="My Approval Ongoing..." style="padding-right: 66px;">
                                        <div class="position-absolute end-0 top-0 h-100 d-flex align-items-center px-1">
                                            <button class="btn btn-sm btn-transparent searchFilterClear d-none" type="button" title="Clear">
                                                <i class="fa-solid fa-times fa-lg"></i>
                                            </button>
                                            <button class="btn btn-sm btn-transparent searchFilter" type="button" title="Search">
                                                <i class="fa-solid fa-search fa-lg"></i>
                                            </button>
                                            <button class="btn btn-sm btn-transparent advancedSearch" type="button" data-type="MY_APPROVAL_ONGOING" title="Filter">
                                                <i class="fa-regular fa-filter-list fa-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-end col-12 my-1 gap-2">
                                <div class="dropdown">
                                    <button class="btn btn-md btn-transparent fw-normal dropdown-toggle" data-bs-boundary="viewport" id="dropdownMenuCompany" type="button" data-coreui-toggle="dropdown" data-coreui-auto-close="outside" aria-expanded="false" title="Company">Company</button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuCompany">
                                        @foreach ($arrCompany as $row)
                                            <li>
                                                <label class="dropdown-item d-flex align-items-center">
                                                    <input type="checkbox" class="form-check-input me-2 filterCheckRequestCompany" data-status="ONGOING" name="company[]" value="{{ $row['id'] }}" checked>{{ $row['text'] }}
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-md btn-transparent fw-normal dropdown-toggle" data-bs-boundary="viewport" id="dropdownMenuReqType" type="button" data-coreui-toggle="dropdown" data-coreui-auto-close="outside" aria-expanded="false" title="Ongoing Request Type">Approval Type</button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuReqType">
                                        <li>
                                            <label class="dropdown-item d-flex align-items-center">
                                                <input type="checkbox" class="form-check-input me-2 filterCheckAllRequest" id="filterCheckAllRequestOngoing" data-status="ONGOING" name="requestTypeAll" value="FALSE"> ALL APPROVAL TYPE
                                            </label>
                                        </li>
                                        @php
                                            $arrDocGroup = [];
                                            foreach ($getDocumentType as $row) {
                                                if(!in_array($row->doc_group_id, $arrDocGroup)){
                                                    echo '<li class="dropdown-header  py-1 px-2 m-0 fs-8 fw-medium">'.$row->group_name.'</li>';
                                                    $arrDocGroup[] = $row->doc_group_id;
                                                }

                                                echo '<li>
                                                        <label class="dropdown-item d-flex align-items-center">
                                                            <input type="checkbox" class="form-check-input me-2 filterCheckRequest filterCheckRequestOngoing" data-status="ONGOING" name="requestType[]" value="'.$row->doc_type_id.'"> '.$row->doc_name.'
                                                        </label>
                                                    </li>';
                                            }
                                        @endphp
                                    </ul>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-md btn-transparent fw-normal dropdown-toggle" data-bs-boundary="viewport" id="dropdownMenuSortOngoing" data-coreui-toggle="dropdown" aria-expanded="false" title="Ongoing Sort By">
                                            Sort by
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuSortOngoing">
                                            <li>
                                                <label class="dropdown-item d-flex align-items-center sortBySelectOngoing sortByPriority active">
                                                    <input type="radio" class="form-check-input me-2" name="sortBy" value="PRIORITY_LEVEL" checked="">Priority Level
                                                </label>
                                            </li>
                                            <li>
                                                <label class="dropdown-item d-flex align-items-center sortBySelectOngoing">
                                                    <input type="radio" class="form-check-input me-2" name="sortBy" value="LATEST_RECEIVED">Latest Received
                                                </label>
                                            </li>
                                            <li>
                                                <label class="dropdown-item d-flex align-items-center sortBySelectOngoing">
                                                    <input type="radio" class="form-check-input me-2" name="sortBy" value="OLDEST_RECEIVED">Oldest Received
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="left-column-wrapper left-column" id="leftColumnOngoing" data-scroll-top="">
                <div class="card full-height-column-wrapper">
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <div class="center-container">
                            <div class="stripes-red-blue stripes-red-blue-md"></div>
                            <div class="d-block fs-7 mt-2">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="col-12 col-md-8 full-height-column-wrapper right-column-wrapper px-1"> --}}
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
                <div class="card-body right-column-card-body py-0 px-0">
                    <div class="right-column overflow-hidden" data-scroll-top="">
                        <div class="full-height-column-wrapper">
                            <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                <div class="center-container">
                                    <div class="stripes-red-blue stripes-red-blue-md"></div>
                                    <div class="d-block fs-7 mt-2">Loading...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer card-footer-fixed d-flex gap-2 border-top-0">
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

<div class="modal fade" id="modalDocumentForm" data-coreui-backdrop="static" data-coreui-keyboard="false" data-coreui-focus="false" tabindex="-1"
        aria-labelledby="modalDocumentForm" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-xl my-md-2-2">
        <div class="modal-content h-100">
            <div class="modal-header flex-wrap" id="modalDocumentFormHeader">
                <h6 class="modal-title d-flex align-items-center gap-1" id="modalDocumentFormTitle">Form Title</h6>
                <div class="position-absolute end-0 me-5 actionHeaderContainer">
                </div>
                <button type="button" class="btn-close btn-close-modal" data-coreui-dismiss="modal"  title="Close" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0 pb-5" id="modalDocumentFormBody">
            </div>
            <div class="modal-footer" id="modalDocumentFormFooter"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMessage" data-coreui-focus="false" tabindex="-1" aria-labelledby="modalMessage" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-md my-md-2-2" id="modalMessageDialog">
        <div class="modal-content h-100">
            <div class="modal-header flex-wrap" id="modalMessageHeader" style="background-color:#fff !important; border-bottom: none !important;">
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
<script src="{{ asset('js/app/scrollbar-custom.js?v=11.4') }}"></script>
<script src="{{ asset('js/app/docapproval/view-form.js?v=14.5') }}"></script>
@endsection
