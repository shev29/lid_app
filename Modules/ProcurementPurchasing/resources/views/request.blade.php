@extends('layouts.app')

@section('extra_css')
{{-- <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/bootstrap-table.min.css') }}">
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/bootstrap-table-fixed-columns.min.css') }}"> --}}

<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/app/scrollbar-custom.css') }}">
<style>
    @media (min-width: 991.99px) {
        body {
            overflow: hidden !important;
        }
    }

    @media (max-width: 767.98px) {
        .card-container {
            width: 92vw !important;
        }
    }

    body {
        --height-content-full: calc(100vh - (var(--cui-header-height) + 1rem));
    }

    .actionBtnHeader {
        background-color: #fff;
    }

    .cardHoverBtn {
        style="background-color:#fff"
    }

    #body-container {
        /* height: var(--height-content-full); */
        height: calc((var(--vh, 1vh) * 100) - (var(--cui-header-height) + 1.5rem));
        padding-bottom: 1rem;
        display: flex;
    }

    .row-content {
        height: 100%;
        /* overflow: hidden; */
        display: flex;
        flex-wrap: nowrap;
        width: max-content;
    }

    .left-column-wrapper, .full-height-column-wrapper {
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .right-column-wrapper {
        padding-left: 0 !important;
    }

    .left-column-filter {
        padding-right: 1rem;
        margin-bottom: .75rem;
    }

    .search-form-filter {
        /* top: 100%; */
        /* left: 0; */
        width: 100%; /* Adjust the width as needed */
        background-color: #fff;
        padding: 0.5rem 1rem;
        /* box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); */
        /* border: var(--cui-border-width) solid var(--cui-border-color); */
        /* border-radius: var(--cui-border-radius); */
        z-index: 2;
    }

    .search-filter {
        display: flex;
        align-items: center;
    }

    .left-column-card, .right-column-card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .left-column-card-body, .right-column-card-body {
        flex: 1;
        /* overflow: hidden; */
        position: relative;
    }

    .order-column, .application-ongoing-column, .end-column, .openOrderContainer, .goodsReceivedContainer {
        /* height: calc(100% - 90px); */
        height: 100%;
        scrollbar-width: none;
        -ms-overflow-style: none;
        display: block;
    }

    .order-column::-webkit-scrollbar, .application-ongoing-column::-webkit-scrollbar, .end-column::-webkit-scrollbar, .openOrderContainer::-webkit-scrollbar, .goodsReceivedContainer::-webkit-scrollbar {
        display: none;
    }

    .card-footer-fixed {
        bottom: 0;
        width: 100%;
        background: white;
        z-index: 1;
        padding-top: 0.75rem !important;
        padding-bottom: 0.75rem !important;
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
        line-height: 1;
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

    #context-menu {
        min-width: 150px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    #context-menu .list-group-item {
        cursor: pointer;
    }

    #pdfPrintArea, #pdfPrintArea * {
        display: none;
    }

    @media print {
        body * {
            display: none;
        }

        #pdfPrintArea, #pdfPrintArea * {
            display: block;
        }

        #pdfPrintArea {
            position: absolute;
            top: 0;
            left: 0;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            height: auto;
        }
        .pdf-page {
            top: 0;
            left: 0;
            margin: 0;
            padding: 0;
            float: left;
            /* display: none; */
            border: none;
            box-shadow: none;
            background-clip: content-box;
            background-color: rgba(255, 255, 255, 1);
            width: 100%;
            height: auto;
        }

        #pdfPrintArea > .pdf-page :is(canvas, img){
            max-width:100%;
            max-height:100%;

            direction:ltr;
            display:block;
        }

        .page[data-loaded] {
            display: block;
        }

        @page {
            top: 0;
            left: 0;
            margin: 0;
            padding: 0;
            size: auto;
        }
    }

    .filterButton {
        position: relative;
    }

    .row-checkbox {
        cursor: pointer;
    }
    #vendorReferenceTable tbody tr {
        cursor: pointer;
    }
</style>
@endsection

@section('content')
    <aside tabindex="-1" class="form-aside aside-lg p-0" id="asideLg">
        <div class="aside-header">
            <span class="aside-title" id="asideLgTitle">Title</span>
            <button type="button" class="btn btn-close-white asideHeaderCloseBtn"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="aside-content" id="asideLgContent">
        </div>
        <div class="aside-footer" id="asideLgFooter">
            <div class="container-fluid p-0">
                <div class="row justify-content-center w-100 mx-0 asideFooterBtn">
                    <div class="col-6 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                        <div class="skeleton mb-0 w-100 w-md-auto me-2"></div>
                    </div>
                    <div class="col-6 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                        <div class="skeleton mb-0 w-100 w-md-auto"></div>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <aside tabindex="-1" class="form-aside p-0" id="filterAside">
        <div class="aside-header">
            <span class="aside-title">Filter</span>
            <button type="button" class="btn btn-close-white asideHeaderCloseBtn"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="notclear-aside py-3" id="filterAsideForm">
        </div>
        <div class="aside-footer">
            <div class="container-fluid p-0">
                <div class="row justify-content-center w-100 mx-0">
                    {{-- <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                        <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
                    </div> --}}
                    <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                        <button type="button" class="btn btn-default w-100 w-md-auto me-2 resetFilter" data-form="" title="Reset Filter">Reset Filter</button>
                    </div>
                    <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                        <button type="button" class="btn btn-info w-100 w-md-auto me-2 showResultFilter" data-form="" title="Show Filter Result">Show Result</button>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <div class="px-3" id="body-container">
        <div id="context-menu" class="position-fixed bg-white shadow rounded p-2" style="display: none; z-index: 1000;">
            <div class="list-group list-group-flush" id="context-menu-list"></div>
        </div>
        <div class="row row-content">
            <div class="col-md-4 card-container full-height-column-wrapper">
                <div class="card left-column-card" data-scroll-top="">
                    <div class="card-header mb-2" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color)">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-2">
                                <div class="card-title m-0">Order Form Received<span class="start-100 ms-1 badge rounded-pill bg-company py-015 px-3 fs-8" id="badgeCountOrderFormRequest"><i class="fa-regular fa-circle-notch fa-spin"></i></span></div>
                            </div>
                        </div>
                    </div>
                    <form role="form" class="form-horizontal" enctype="multipart/form-data" id="filterOrderFormRequest">
                        <div id="searchOrderFormContainer" class="search-form-filter pt-0">
                            <div class="d-flex align-items-center position-relative mt-2">
                                <div class="form-group d-flex align-items-center position-relative w-100">
                                    <input type="text" class="form-control d-flex searchInput" name="search" data-iscleared="true" data-form="ORDER_FORM_REQUEST" data-type="" spellcheck="false" autocomplete="off" placeholder="Search..." style="padding-right: 66px;">
                                    <select class="d-none" name="applicant"><option value="ALL">-- ALL --</option></select>
                                    <select class="d-none" name="company">
                                        @foreach ($arrCompany as $row)
                                            @php
                                                $selected = ($row['selected'] == true) ? 'selected' : '';
                                            @endphp
                                            <option value="{{ $row['id'] }}" {{$selected}}>{{ $row['text'] }}</option>
                                        @endforeach
                                    </select>

                                    <select class="d-none" name="receiver">
                                        @foreach ($arrSelection as $row)
                                            @php
                                                $selected = ($row['selected'] == true) ? 'selected' : '';
                                            @endphp
                                            <option value="{{ $row['id'] }}" {{$selected}}>{{ $row['text'] }}</option>
                                        @endforeach
                                    </select>

                                    <input type="hidden" name="status" value="FULLY_APPROVED">
                                    <input type="hidden" name="priority" value="ALL">
                                    <input type="hidden" name="dept" value="ALL">
                                    <input type="hidden" name="location" value="ALL">
                                    <input type="hidden" name="startDateRange" value="">
                                    <input type="hidden" name="endDateRange" value="">
                                    <input type="hidden" name="sortBy" value="LATEST">
                                    <div class="position-absolute end-0 top-0 h-100 d-flex align-items-center px-1">
                                        <button class="btn btn-sm btn-transparent searchButtonClear d-none" type="button" data-form="ORDER_FORM_REQUEST" title="Clear Order Form Search">
                                            <i class="fa-solid fa-times fa-lg"></i>
                                        </button>
                                        <button class="btn btn-sm btn-transparent searchButton" type="button" data-form="ORDER_FORM_REQUEST" title="Search Order Form Request">
                                            <i class="fa-solid fa-search fa-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center position-relative ms-1">
                                    <button class="btn btn-md btn-transparent filterButton" type="button" data-form="ORDER_FORM_REQUEST" title="Filter Order Form Request"><i class="fa-regular fa-filter-list fa-lg"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="card-body full-height-column-wrapper pt-2">
                        <div class="order-column px-0" id="orderFormRequest" style="height:100% !important" data-scroll-top="" data-type="ORDER_FORM_REQUEST">
                            <div class="full-height-column-wrapper">
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="center-container">
                                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                                        <div class="d-block fs-7 mt-2">Loading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 card-container full-height-column-wrapper">
                <div class="card right-column-card">
                    <div class="card-header mb-2" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color)">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-2">
                                <div class="card-title m-0">Ongoing Purchasing Approval<span class="start-100 ms-1 badge rounded-pill bg-company py-015 px-3 fs-8" id="badgeCountApplicationOngoing"><i class="fa-regular fa-circle-notch fa-spin"></i></span></div>
                            </div>
                        </div>
                    </div>
                    <form role="form" class="form-horizontal" enctype="multipart/form-data" id="filterApplicationFormRequest">
                        <div id="searchApplicationFormContainer" class="search-form-filter pt-0">
                            <div class="d-flex align-items-center position-relative mt-2">
                                <div class="form-group d-flex align-items-center position-relative w-100">
                                    <input type="text" class="form-control d-flex searchInput" id="searchInputAppOngoing" name="search" data-iscleared="true" data-form="APPLICATION_ONGOING" data-type="" spellcheck="false" autocomplete="off" placeholder="Search..." style="padding-right: 66px;">
                                    <select class="d-none" name="company">
                                        @foreach ($arrCompany as $row)
                                            @php
                                                $selected = ($row['selected'] == true) ? 'selected' : '';
                                            @endphp
                                            <option value="{{ $row['id'] }}" {{$selected}}>{{ $row['text'] }}</option>
                                        @endforeach
                                    </select>

                                    <select class="d-none" name="receiver">
                                        @foreach ($arrSelection as $row)
                                            @php
                                                $selected = ($row['selected'] == true) ? 'selected' : '';
                                            @endphp
                                            <option value="{{ $row['id'] }}" {{$selected}}>{{ $row['text'] }}</option>
                                        @endforeach
                                    </select>

                                    <input type="hidden" name="status" value="ALL">
                                    <input type="hidden" name="priority" value="ALL">
                                    <input type="hidden" name="startDateRange" value="">
                                    <input type="hidden" name="endDateRange" value="">
                                    <input type="hidden" name="sortBy" value="LATEST">
                                    <div class="position-absolute end-0 top-0 h-100 d-flex align-items-center px-1">
                                        <button class="btn btn-sm btn-transparent searchButtonClear d-none" type="button" data-form="APPLICATION_ONGOING" title="Clear Application Form Search">
                                            <i class="fa-solid fa-times fa-lg"></i>
                                        </button>
                                        <button class="btn btn-sm btn-transparent searchButton" type="button" data-form="APPLICATION_ONGOING" title="Search Application Form">
                                            <i class="fa-solid fa-search fa-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center position-relative ms-1">
                                    <button class="btn btn-md btn-transparent filterButton" type="button" data-form="APPLICATION_ONGOING" title="Filter Ongoing Purchasing Approval"><i class="fa-regular fa-filter-list fa-lg"></i></button>
                                    <button class="btn btn-md btn-transparent maximizeApprovalProgress" type="button" data-form="APPLICATION_ONGOING" title="Expand Approval Progress">
                                        <i class="fa-solid fa-arrow-up-right fa-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="card-body full-height-column-wrapper pt-2">
                        <div class="application-ongoing-column px-0" id="applicationOngoing" style="height:100% !important" data-scroll-top="" data-type="APPLICATION_ONGOING">
                            <div class="full-height-column-wrapper">
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="center-container">
                                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                                        <div class="d-block fs-7 mt-2">Loading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 card-container full-height-column-wrapper">
                <div class="card right-column-card">
                    <div class="card-header mb-2" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color)">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-2">
                                <div class="card-title m-0">Completed Purchasing Approval<span class="start-100 ms-1 badge rounded-pill bg-company py-015 px-3 fs-8" id="badgeCountApplicationCompleted"><i class="fa-regular fa-circle-notch fa-spin"></i></span></div>
                            </div>
                        </div>
                    </div>
                    <form role="form" class="form-horizontal" enctype="multipart/form-data" id="filterApplicationCompletedForm">
                        <div id="searchApplicationCompletedContainer" class="search-form-filter pt-0">
                            <div class="d-flex align-items-center position-relative mt-2">
                                <div class="form-group d-flex align-items-center position-relative w-100">
                                    <input type="text" class="form-control d-flex searchInput" id="searchInputAppCompleted" name="search" data-iscleared="true" data-form="COMPLETED_APPROVAL" data-type="" spellcheck="false" autocomplete="off" placeholder="Search..." style="padding-right: 66px;">
                                    <input type="hidden" name="type" value="COMPLETED_APPROVAL">
                                    <select class="d-none" name="company">
                                        @foreach ($arrCompany as $row)
                                            @php
                                                $selected = ($row['selected'] == true) ? 'selected' : '';
                                            @endphp
                                            <option value="{{ $row['id'] }}" {{$selected}}>{{ $row['text'] }}</option>
                                        @endforeach
                                    </select>

                                    <select class="d-none" name="receiver">
                                        @foreach ($arrSelection as $row)
                                            @php
                                                $selected = ($row['selected'] == true) ? 'selected' : '';
                                            @endphp
                                            <option value="{{ $row['id'] }}" {{$selected}}>{{ $row['text'] }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="priority" value="ALL">
                                    <input type="hidden" name="status" value="ALL">
                                    <input type="hidden" name="startDateRange" value="">
                                    <input type="hidden" name="endDateRange" value="">
                                    <input type="hidden" name="sortBy" value="LATEST">
                                    <div class="position-absolute end-0 top-0 h-100 d-flex align-items-center px-1">
                                        <button class="btn btn-sm btn-transparent searchButtonClear d-none" type="button" data-form="COMPLETED_APPROVAL" title="Clear Application Completed Search">
                                            <i class="fa-solid fa-times fa-lg"></i>
                                        </button>
                                        <button class="btn btn-sm btn-transparent searchButton" type="button" data-form="COMPLETED_APPROVAL" title="Search Application Completed">
                                            <i class="fa-solid fa-search fa-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center position-relative ms-1">
                                    <button class="btn btn-md btn-transparent filterButton" type="button" data-form="COMPLETED_APPROVAL" title="Filter Completed Purchasing Approval"><i class="fa-regular fa-filter-list fa-lg"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="card-body full-height-column-wrapper pt-2">
                        <div class="end-column px-0" id="applicationCompleted" style="height:100% !important" data-scroll-top="" data-type="APPLICATION_COMPLETED">
                            <div class="full-height-column-wrapper">
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="center-container">
                                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                                        <div class="d-block fs-7 mt-2">Loading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 card-container full-height-column-wrapper">
                <div class="card right-column-card">
                    <div class="card-header mb-2" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color)">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-2">
                                <div class="card-title m-0">Open Order<span class="start-100 ms-1 badge rounded-pill bg-company py-015 px-3 fs-8" id="badgeCountOpenOrder"><i class="fa-regular fa-circle-notch fa-spin"></i></span></div>
                            </div>
                        </div>
                    </div>
                    <form role="form" class="form-horizontal" enctype="multipart/form-data" id="filterOpenOrderForm">
                        <div id="searchOpenOrderContainer" class="search-form-filter pt-0">
                            <div class="d-flex align-items-center position-relative mt-2">
                                <div class="form-group d-flex align-items-center position-relative w-100">
                                    <input type="text" class="form-control d-flex searchInput" id="searchInputOpenOrder" name="search" data-iscleared="true" data-form="OPEN_ORDER" data-type="" spellcheck="false" autocomplete="off" placeholder="Search..." style="padding-right: 66px;">
                                    <input type="hidden" name="type" value="OPEN_ORDER">
                                    <select class="d-none" name="company">
                                        @foreach ($arrCompany as $row)
                                            @php
                                                $selected = ($row['selected'] == true) ? 'selected' : '';
                                            @endphp
                                            <option value="{{ $row['id'] }}" {{$selected}}>{{ $row['text'] }}</option>
                                        @endforeach
                                    </select>

                                    <select class="d-none" name="receiver">
                                        @foreach ($arrSelection as $row)
                                            @php
                                                $selected = ($row['selected'] == true) ? 'selected' : '';
                                            @endphp
                                            <option value="{{ $row['id'] }}" {{$selected}}>{{ $row['text'] }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="priority" value="ALL">
                                    <input type="hidden" name="status" value="OPEN_ORDER">
                                    <input type="hidden" name="startDateRange" value="">
                                    <input type="hidden" name="endDateRange" value="">
                                    <input type="hidden" name="sortBy" value="LATEST">
                                    <div class="position-absolute end-0 top-0 h-100 d-flex align-items-center px-1">
                                        <button class="btn btn-sm btn-transparent searchButtonClear d-none" type="button" data-form="OPEN_ORDER" title="Clear Open Order Search">
                                            <i class="fa-solid fa-times fa-lg"></i>
                                        </button>
                                        <button class="btn btn-sm btn-transparent searchButton" type="button" data-form="OPEN_ORDER" title="Search Open Order">
                                            <i class="fa-solid fa-search fa-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center position-relative ms-1">
                                    <button class="btn btn-md btn-transparent filterButton" type="button" data-form="OPEN_ORDER" title="Filter Open Order"><i class="fa-regular fa-filter-list fa-lg"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="card-body full-height-column-wrapper pt-2">
                        <div class="openOrderContainer px-0" id="openOrderContainer" style="height:100% !important" data-scroll-top="" data-type="OPEN_ORDER">
                            <div class="full-height-column-wrapper">
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="center-container">
                                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                                        <div class="d-block fs-7 mt-2">Loading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 card-container full-height-column-wrapper">
                <div class="card right-column-card">
                    <div class="card-header mb-2" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color)">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-2">
                                <div class="card-title m-0">Goods Received Not Invoiced<span class="start-100 ms-1 badge rounded-pill bg-company py-015 px-3 fs-8" id="badgeCountGoodsReceived"><i class="fa-regular fa-circle-notch fa-spin"></i></span></div>
                            </div>
                        </div>
                    </div>
                    <form role="form" class="form-horizontal" enctype="multipart/form-data" id="filterGoodsReceivedForm">
                        <div id="searchGoodsReceivedContainer" class="search-form-filter pt-0">
                            <div class="d-flex align-items-center position-relative mt-2">
                                <div class="form-group d-flex align-items-center position-relative w-100">
                                    <input type="text" class="form-control d-flex searchInput" id="searchInputGoodsReceived" name="search" data-iscleared="true" data-form="GOODS_RECEIVED" data-type="" spellcheck="false" autocomplete="off" placeholder="Search..." style="padding-right: 66px;">
                                    <input type="hidden" name="type" value="GOODS_RECEIVED">
                                    <select class="d-none" name="company">
                                        @foreach ($arrCompany as $row)
                                            @php
                                                $selected = ($row['selected'] == true) ? 'selected' : '';
                                            @endphp
                                            <option value="{{ $row['id'] }}" {{$selected}}>{{ $row['text'] }}</option>
                                        @endforeach
                                    </select>

                                    <select class="d-none" name="receiver">
                                        @foreach ($arrSelection as $row)
                                            @php
                                                $selected = ($row['selected'] == true) ? 'selected' : '';
                                            @endphp
                                            <option value="{{ $row['id'] }}" {{$selected}}>{{ $row['text'] }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="priority" value="ALL">
                                    <input type="hidden" name="status" value="GOODS_RECEIVED">
                                    <input type="hidden" name="startDateRange" value="">
                                    <input type="hidden" name="endDateRange" value="">
                                    <input type="hidden" name="sortBy" value="LATEST">
                                    <div class="position-absolute end-0 top-0 h-100 d-flex align-items-center px-1">
                                        <button class="btn btn-sm btn-transparent searchButtonClear d-none" type="button" data-form="GOODS_RECEIVED" title="Clear Goods Received Search">
                                            <i class="fa-solid fa-times fa-lg"></i>
                                        </button>
                                        <button class="btn btn-sm btn-transparent searchButton" type="button" data-form="GOODS_RECEIVED" title="Search Goods Received">
                                            <i class="fa-solid fa-search fa-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center position-relative ms-1">
                                    <button class="btn btn-md btn-transparent filterButton" type="button" data-form="GOODS_RECEIVED" title="Filter Goods Received Not Invoiced"><i class="fa-regular fa-filter-list fa-lg"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="card-body full-height-column-wrapper pt-2">
                        <div class="goodsReceivedContainer px-0" id="goodsReceivedContainer" style="height:100% !important" data-scroll-top="" data-type="GOODS_RECEIVED">
                            <div class="full-height-column-wrapper">
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <div class="center-container">
                                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                                        <div class="d-block fs-7 mt-2">Loading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <aside class="form-aside d-md-none p-0" id="right-column-aside">
                <div class="aside-header">
                    <span class="aside-title">Detail Form</span>
                    <button type="button" class="btn btn-close-white asideHeaderCloseBtn"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="aside-content" id="asideDetailForm" style="z-index: 1061">
                </div>
                <div class="aside-footer">
                </div>
            </aside>
        </div>
    </div>

    <div class="modal fade" id="modalDocumentForm" data-coreui-backdrop="static" data-coreui-keyboard="false" data-coreui-focus="false" tabindex="-1"
        aria-labelledby="modalDocumentForm" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-xl my-md-2-2">
            <div class="modal-content h-100">
                <div class="modal-header" id="modalDocumentFormHeader">
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
{{-- <script src="{{ asset('js/vendor/bootstrap-table.min.js') }}"></script>
<script src="{{ asset('js/vendor/bootstrap-table-fixed-columns.min.js') }}"></script> --}}

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf_viewer.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf_viewer.min.js"></script>


<script src="{{ asset('js/app/scrollbar-custom.js?v=11.5') }}"></script>
<script src="{{ asset('js/app/procpur/purchasing_request.js?v=15.1') }}"></script>
<script>
    // const container = document.querySelector('.container-content.scroll');

    // // Define the scroll step size (width of one card-container)
    // const scrollStep = document.querySelector('.card-container')?.offsetWidth || 300;

    // // Add event listener for keydown events on the document
    // document.addEventListener('keydown', function(event) {
    //     // Check if the key pressed is arrow left or arrow right
    //     if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
    //         // Prevent default behavior (like page scrolling)
    //         event.preventDefault();

    //         // Calculate the scroll amount
    //         const scrollAmount = event.key === 'ArrowLeft' ? -scrollStep : scrollStep;

    //         // Scroll the container horizontally
    //         container.scrollBy({
    //             left: scrollAmount,
    //             behavior: 'smooth'
    //         });
    //     }
    // });

    $(document).on('contextmenu', '.card-link-content', function (e) {
        e.preventDefault();
        let $this = $(this);
        let dataForm = $this.parent().parent('div').attr('data-form');
        let dataType = $this.parent().parent('div').attr('data-type');
        $('#context-menu-list').html('');
        if(dataType == 'ORDER_FORM_REQUEST') {
            if($this.attr('data-type') == 'VIEW_MULTIPLE') {
                $('#context-menu-list').html(`<button class="list-group-item list-group-item-action actionBtn" data-type="VIEW" data-doc="1" data-form="ORDER_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-files me-2"></i> View Documents</button>`);
            }
            else if($this.attr('data-type') == 'VIEW_ONGOING') {
                $('#context-menu-list').html(`<button class="list-group-item list-group-item-action actionBtn" data-type="VIEW_ONGOING" data-doc="1" data-form="ORDER_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-files me-2"></i> View Documents</button>`);
            }
            else if($this.attr('data-form') == 'COMPARISON_FORM') {
                $('#context-menu-list').html(`<button class="list-group-item list-group-item-action actionBtn" data-type="VIEW" data-doc="1" data-form="ORDER_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-files me-2"></i> View Order Form</button>
                                            <button class="list-group-item list-group-item-action actionBtn" data-source="WEB" data-type="VIEW" data-form="COMPARISON_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-files me-2"></i> View Comparison Form</button>
                                            <button class="list-group-item list-group-item-action nextNewForm" data-type="REVISE" data-form="COMPARISON_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-list-ol me-2"></i> Revise Comparison Form</button>
                                            <button class="list-group-item list-group-item-action nextNewForm" data-type="NEW" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-file-import me-2"></i> Create Application Form</button>
                                            <button class="list-group-item list-group-item-action actionBtn" data-type="SEND_BACK" data-form="ORDER_FORM" data-token="${$this.attr('data-token')}"><i class="fa-solid fa-arrow-turn-left me-2"></i> Send Back to User</button>`);
            }
            else if($this.attr('data-type') == 'VIEW') {
                $('#context-menu-list').html(`<button class="list-group-item list-group-item-action actionBtn" data-type="VIEW" data-doc="1" data-form="ORDER_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-files me-2"></i> View Documents</button>
                                            <button class="list-group-item list-group-item-action nextNewForm" data-type="NEW" data-form="COMPARISON_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-list-ol me-2"></i> Create Comparison Form</button>
                                            <button class="list-group-item list-group-item-action actionBtn" data-type="SEND_BACK" data-form="ORDER_FORM" data-token="${$this.attr('data-token')}"><i class="fa-solid fa-arrow-turn-left me-2"></i> Send Back to User</button>`);
            }
        }
        else if(dataType == 'APPLICATION_ONGOING') {
            $('#context-menu-list').html(`<button class="list-group-item list-group-item-action actionBtn" data-type="VIEW" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-files me-2"></i> View Documents</button>
                                        <button class="list-group-item list-group-item-action actionBtnHeader" data-type="PRINT" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-solid fa-print me-2"></i> Print Documents</button>
                                        <button class="list-group-item list-group-item-action actionBtnHeader" data-type="DOWNLOAD" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-solid fa-arrow-down-to-line me-2"></i> Download Documents</button>
                                        <button class="list-group-item list-group-item-action actionBtn" data-type="PROGRESS" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-circle-info me-2"></i> View Progress</button>
                                        <button class="list-group-item list-group-item-action actionBtn" data-type="CANCEL" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-arrow-turn-left me-2"></i> Cancel Approval Form</button>`);
        }
        else if(dataType == 'APPLICATION_COMPLETED') {
            if($this.attr('data-completed') == 'CANCELED' || $this.attr('data-completed') == 'NEED_REVISE') {
                let poNo = $this.find('.card-body').find('.card_po_number').html();
                let moveToArchive = '';
                if($this.attr('data-completed') == 'CANCELED') {
                    moveToArchive = `<button class="list-group-item list-group-item-action actionBtn" data-type="ARCHIVE" data-doc="2" data-no="${poNo}" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-cabinet-filing me-2"></i> Move to Archive</button>`;
                }
                else if($this.attr('data-completed') == 'NEED_REVISE') {
                    moveToArchive = `<button class="list-group-item list-group-item-action actionBtn" data-type="CANCEL_TO_ARCHIVE" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-cabinet-filing me-2"></i> Cancel & Move to Archive</button>`;
                }

                $('#context-menu-list').html(`<button class="list-group-item list-group-item-action actionBtn" data-type="VIEW" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-files me-2"></i> View Documents</button>
                                            <button class="list-group-item list-group-item-action actionBtnHeader" data-type="PRINT" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-solid fa-print me-2"></i> Print Documents</button>
                                            <button class="list-group-item list-group-item-action actionBtnHeader" data-type="DOWNLOAD" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-solid fa-arrow-down-to-line me-2"></i> Download Documents</button>
                                            <button class="list-group-item list-group-item-action actionBtn" data-type="PROGRESS" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-circle-info me-2"></i> View Progress</button>

                                            <button class="list-group-item list-group-item-action actionBtn" data-type="SEND_BACK" data-no="${poNo}" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-solid fa-arrow-turn-left me-2"></i> Send Back to User</button>

                                            <button class="list-group-item list-group-item-action actionBtn" data-type="REVISE" data-doc="2" data-no="${poNo}" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-pen-to-square me-2"></i> Revise Application Form</button>

                                            ${moveToArchive}`);
            }
            else if($this.attr('data-completed') == 'REJECTED') {
                let poNo = $this.find('.card-body').find('.card_po_number').html();
                $('#context-menu-list').html(`<button class="list-group-item list-group-item-action actionBtn" data-type="VIEW" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-files me-2"></i> View Documents</button>
                                            <button class="list-group-item list-group-item-action actionBtn" data-type="PROGRESS" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-circle-info me-2"></i> View Progress</button>

                                            <button class="list-group-item list-group-item-action actionBtn" data-type="ARCHIVE" data-doc="2" data-no="${poNo}" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-cabinet-filing me-2"></i> Move to Archive</button>`);
            }
            else if($this.attr('data-completed') == 'APPROVED') {
                let poNo = $this.find('.card-body').find('.card_po_number').html();
                $('#context-menu-list').html(`<button class="list-group-item list-group-item-action actionBtn" data-type="VIEW" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-files me-2"></i> View Documents</button>
                                            <button class="list-group-item list-group-item-action actionBtnHeader" data-type="PRINT" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-solid fa-print me-2"></i> Print Documents</button>
                                            <button class="list-group-item list-group-item-action actionBtnHeader" data-type="DOWNLOAD" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-solid fa-arrow-down-to-line me-2"></i> Download Documents</button>
                                            <button class="list-group-item list-group-item-action actionBtn" data-type="PROGRESS" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-circle-info me-2"></i> View Progress</button>
                                            <button class="list-group-item list-group-item-action actionBtn" data-type="REVISE" data-doc="2" data-no="${poNo}" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-pen-to-square me-2"></i> Revise Application Form</button>
                                            <button class="list-group-item list-group-item-action actionBtn" data-type="OPEN_ORDER" data-form="APPLICATION_FORM" data-no="${poNo}" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-cart-shopping me-2"></i> Set Open Order</button>`);
            }
        }
        else if(dataType == 'OPEN_ORDER') {
            let poNo = $this.find('.card-body').find('.card_po_number').html();
            $('#context-menu-list').html(`<button class="list-group-item list-group-item-action actionBtn" data-type="VIEW" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-files me-2"></i> View Documents</button>
                                        <button class="list-group-item list-group-item-action actionBtnHeader" data-type="PRINT" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-solid fa-print me-2"></i> Print Documents</button>
                                        <button class="list-group-item list-group-item-action actionBtnHeader" data-type="DOWNLOAD" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-solid fa-arrow-down-to-line me-2"></i> Download Documents</button>
                                        <button class="list-group-item list-group-item-action actionBtn" data-type="PROGRESS" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-circle-info me-2"></i> View Progress</button>
                                        <button class="list-group-item list-group-item-action actionBtn" data-type="REVISE" data-doc="2" data-no="${poNo}" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-pen-to-square me-2"></i> Revise Application Form</button>
                                        <button class="list-group-item list-group-item-action actionBtn" data-type="GOODS_RECEIVED" data-form="APPLICATION_FORM" data-no="${poNo}" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-cube me-2"></i> Goods Received</button>`);
        }
        else if(dataType == 'GOODS_RECEIVED') {
            let poNo = $this.find('.card-body').find('.card_po_number').html();
            $('#context-menu-list').html(`<button class="list-group-item list-group-item-action actionBtn" data-type="VIEW" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-files me-2"></i> View Documents</button>
                                        <button class="list-group-item list-group-item-action actionBtnHeader" data-type="PRINT" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-solid fa-print me-2"></i> Print Documents</button>
                                        <button class="list-group-item list-group-item-action actionBtnHeader" data-type="DOWNLOAD" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-solid fa-arrow-down-to-line me-2"></i> Download Documents</button>
                                        <button class="list-group-item list-group-item-action actionBtn" data-type="PROGRESS" data-doc="2" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-circle-info me-2"></i> View Progress</button>
                                        <button class="list-group-item list-group-item-action actionBtn" data-type="REVISE" data-doc="2" data-no="${poNo}" data-form="APPLICATION_FORM" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-pen-to-square me-2"></i> Revise Application Form</button>
                                        <button class="list-group-item list-group-item-action actionBtn" data-type="INVOICED" data-form="APPLICATION_FORM" data-no="${poNo}" data-ccy="${$this.attr('data-ccy')}" data-token="${$this.attr('data-token')}"><i class="fa-regular fa-receipt me-2"></i> Invoiced Purchase</button>`);
        }

        let contextMenu = $('#context-menu');
        const menuWidth = contextMenu.outerWidth();
        const menuHeight = contextMenu.outerHeight();
        const winWidth = $(window).width();
        const winHeight = $(window).height();

        let posX = e.pageX;
        let posY = e.pageY;

        if ((posX + menuWidth) > winWidth) {
            posX = winWidth - menuWidth - 10;
        }

        if ((posY + menuHeight) > winHeight) {
            posY = winHeight - menuHeight - 10;
        }

        contextMenu.css({
            top: posY,
            left: posX,
        }).fadeIn(200);

        contextMenu.data('row-id', $(this).find('.row-checkbox').val());
    });

    $(document).on('click', function() {
        $('#context-menu').hide();
    });

    // document.addEventListener('keydown', function (e) {
    //     if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
    //         return;
    //     }

    //     const container = document.querySelector('.container-content');

    //     // Cek jika modal atau aside terbuka
    //     const modalOpen = document.querySelector('.modal.show, .modal[style*="display: block"]');
    //     const asideOpen = document.querySelector('.overlay-aside.show');


    //     if (modalOpen || asideOpen || !container) {
    //         console.log(asideOpen);
    //         return;
    //     }

    //     // const card = container.querySelector('.card-container');
    //     // if (!card) return;

    //     // // const cardWidth = card.offsetWidth + parseInt(getComputedStyle(card).marginRight || 0);

    //     // if (e.key === 'ArrowRight') {
    //     //     container.scrollBy({ left: cardWidth, behavior: 'smooth' });
    //     // } else if (e.key === 'ArrowLeft') {
    //     //     container.scrollBy({ left: -cardWidth, behavior: 'smooth' });
    //     // }
    //     const scrollAmount = 1000; // Sesuaikan jarak scroll

    //     // if (e.key === 'ArrowRight') {
    //     //     container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    //     // } else if (e.key === 'ArrowLeft') {
    //     //     container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    //     // }
    // });

    dayjs.locale('id');
    dayjs.extend(window.dayjs_plugin_customParseFormat);

    // Context Menu Actions
    // $('#context-menu button').on('click', function() {
    //     let action = $(this).data('action');
    //     let rowId = $('#context-menu').data('row-id');

    //     // Handle different actions
    //     switch(action) {
    //         case 'edit':
    //             // Implement edit action
    //             console.log('Edit application:', rowId);
    //             break;
    //         case 'view':
    //             // Implement view action
    //             console.log('View application details:', rowId);
    //             break;
    //         case 'approve':
    //             // Implement approve action
    //             console.log('Approve application:', rowId);
    //             break;
    //     }

    //     $('#context-menu').hide();
    // });
</script>
@endsection
