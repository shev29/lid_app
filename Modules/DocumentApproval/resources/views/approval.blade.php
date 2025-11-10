@extends('layouts.app')

@section('extra_css')
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/app/scrollbar-custom.css') }}">
<style>
    @media (max-width: 991.98px) {
        body {
            --height-content-full: calc(100vh - (var(--cui-header-height) + 1rem + 115px));
        }

        .container-content {
            height: var(--height-content-full);
            height: calc((var(--vh, 1vh) * 100) - (var(--cui-header-height) + 1rem + 115px));
            padding-bottom: 1rem;
        }
    }
    @media (min-width: 991.99px) {
        body {
            overflow: hidden !important;
            --height-content-full: calc(100vh - (var(--cui-header-height) + 1rem + 70px));
        }

        .container-content {
            height: var(--height-content-full);
            height: calc((var(--vh, 1vh) * 100) - (var(--cui-header-height) + 1rem + 70px));
            padding-bottom: 1rem;
        }
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
        /* overflow: hidden; */
    }

    .right-column-wrapper {
        padding-left: 0 !important;
    }

    .left-column-filter {
        padding-right: 1rem;
        margin-bottom: .75rem;
    }

    .left-column {
        /* height: calc(100% - 50px); */
        height: 100%;
        /* overflow-y: auto; */
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
        /* overflow-y: auto; */
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

    .dropZone {
        cursor: pointer;
        border: 2px dashed #6c757d !important;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
</style>
@endsection

@section('content')
    <div class="col-12 mb-3 px-3">
        <div class="card">
            <div class="card-body px-2 py-2">
                <div class="row align-items-center">
                    <div class="col-12 col-md-5 d-flex align-items-center">
                        <div class="flex-grow-1">
                            <ul class="nav nav-pills p-1" id="pillsTabFilterForm" role="tablist" style="background-color: var(--cui-gray-300); border-radius: var(--cui-nav-pills-border-radius); border-bottom: 1px solid #dbdfe6;">
                                <input type="hidden" id="filterStatus" value="ONGOING">
                                <li class="nav-item col-6 m-0" role="presentation">
                                    <button class="nav-link filterTab px-3 py-1 w-100 active" id="pillsOngoingTab" data-coreui-toggle="pill" data-coreui-target="#pillsOngoingForm" data-status="ONGOING" type="button" role="tab" aria-controls="pillsOngoingForm" aria-selected="false" tabindex="-1">My Approval Ongoing<span class="start-100 ms-1 badge rounded-pill bg-danger py-015 px-3 fs-8" id="badgeCountOngoing"><i class="fa-regular fa-circle-notch fa-spin"></i></span></button>
                                </li>
                                <li class="nav-item col-6 m-0" role="presentation">
                                    <button class="nav-link filterTab px-3 py-1 w-100" id="pillsHistoryTab" data-coreui-toggle="pill" data-coreui-target="#pillsHistoryForm" data-status="HISTORY" type="button" role="tab" aria-controls="pillsHistoryForm" aria-selected="true">My Approval History</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-7">
                        <div class="d-flex justify-content-end align-items-center gap-3">
                            <div class="tab-content" id="pillsTabFilterContentForm">
                                <div class="tab-pane fade active show" id="pillsOngoingForm" role="tabpanel" aria-labelledby="pillsOngoingTab" tabindex="0">
                                    <form role="form" class="form-horizontal form-horizontal d-flex align-items-center gap-2" enctype="multipart/form-data" id="filterLeftColumnFormOngoing">
                                        <div id="searchFormOngoing" class="search-form-filter">
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
                                        <div class="d-flex align-items-center justify-content-between col-12 col-md-5 mb-2">
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
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-md btn-transparent fw-normal dropdown-toggle" data-bs-boundary="viewport" id="dropdownMenuSortOngoing" data-coreui-toggle="dropdown" aria-expanded="false" title="Ongoing Sort By">Sort by</button>
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
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="pillsHistoryForm" role="tabpanel" aria-labelledby="pillsHistoryTab" tabindex="1">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-content px-3 d-block" id="ongoingContainer">
        <div class="row row-content">
            <div class="col-md-4 left-column-wrapper d-block" id="leftColumnRequestWrapper">
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
            <div class="d-none d-md-block col-md-8 full-height-column-wrapper right-column-wrapper">
                <div class="card right-column-card">
                    <div class="card-header" style="border-bottom: var(--cui-card-border-width) solid var(--cui-card-border-color)">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-2">
                                <div class="card-title card-title-right-column m-0 d-flex align-items-center gap-1">Detail Request</div>
                            </div>
                            <div class="d-flex gap-1 justify-content-between align-items-center actionHeaderContainer">
                            </div>
                        </div>
                    </div>
                    <div class="card-body right-column-card-body py-0 px-0">
                        <div class="right-column" data-scroll-top="">
                            <div class="full-height-column-wrapper">
                                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                    <div class="text-center">
                                        <i class="fa-light fa-file fs-1"></i>
                                        <div class="d-block fs-7 mt-2">Select on left item to view</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer card-footer-fixed d-flex gap-2 border-top-0"></div>
                </div>
                <div class="popper-area"></div>
            </div>
        </div>
    </div>

    <div class="container-content px-3 d-none" id="historyContainer">
        <div class="row row-content">
            <div class="col-12 h-100">
                <div class="card full-height-column-wrapper" id="historyTableContainer">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fs-7 fw-medium">
                                <div class="card-title m-0 d-flex align-items-center gap-1">My Approval History</div>
                            </div>
                            <div class="d-flex gap-1 justify-content-between align-items-center">
                                <button class="btn btn-sm btn-success actionBtnHeader" type="button" data-type="EXPORT_DATA_TABLE" data-table="historyTable" title="Export Data"><i class="fa-solid fa-arrow-up-right fa-lg fa-fw"></i> Export Data</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="table-container table-secondary table-responsive h-100" style="overflow-y: hidden;">
                            <form role="form" class="form-horizontal d-sm-block d-md-flex justify-content-between align-items-center mb-2 formSearchTable" enctype="multipart/form-data">
                                <div class="me-md-2">
                                    <div class="form-group mb-2 mb-md-0 w-sm-100" style="width:250px">
                                        <select class="select2" name="company" id="historyCompany">
                                            @foreach ($arrCompany as $row)
                                                @php
                                                    $selected = ($row['selected'] == true) ? 'selected' : '';
                                                @endphp
                                                <option value="{{ $row['id'] }}" {{$selected}}>{{ $row['text'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="type" value="APPROVAL_HISTORY">
                                <input type="hidden" name="status" value="ALL">
                                <input type="hidden" name="submittedStartDate" value="">
                                <input type="hidden" name="submittedEndDate" value="">
                                <input type="hidden" name="completedStartDate" value="">
                                <input type="hidden" name="completedEndDate" value="">
                                <input type="hidden" name="sortBy" value="LATEST">

                                <div class="d-flex align-items-center">
                                    <div class="form-group d-flex align-items-center position-relative w-100">
                                        <input type="text" class="form-control d-flex searchInputTable" name="search" data-iscleared="true" spellcheck="false" autocomplete="off" placeholder="Search..." style="padding-right: 66px;">
                                        <div class="position-absolute end-0 top-0 h-100 d-flex align-items-center px-1">
                                            <button class="btn btn-sm btn-transparent searchButtonClearTable d-none" type="button" title="Clear Search">
                                                <i class="fa-solid fa-times fa-lg"></i>
                                            </button>
                                            <button class="btn btn-sm btn-transparent searchButtonTable" type="button" title="Search">
                                                <i class="fa-solid fa-search fa-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center position-relative ms-1">
                                        <button class="btn btn-md btn-transparent filterButtonTable" type="button" title="Filter Table" data-table="historyTable"><i class="fa-regular fa-filter-list fa-lg"></i></button>
                                    </div>
                                </div>
                            </form>
                            <table id="historyTable" class="table table-striped table-hover" style="width:100%">
                                <thead class="table-secondary">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Doc. Type</th>
                                        <th>Applied By</th>
                                        <th>Doc. Number</th>
                                        <th>Company</th>
                                        <th>Department</th>
                                        <th>Priority</th>
                                        <th>Items</th>
                                        <th>My Decision</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
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

    <div class="modal fade" id="modalDocumentFormHistory" data-coreui-backdrop="static" data-coreui-keyboard="false" data-coreui-focus="false" tabindex="-1"
        aria-labelledby="modalDocumentFormHistory" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-xl my-md-2-2">
            <div class="modal-content h-100">
                <div class="modal-header flex-wrap" id="modalDocumentFormHeaderHistory">
                    <h6 class="modal-title d-flex align-items-center gap-1" id="modalDocumentFormTitleHistory">Form Title</h6>
                    <div class="position-absolute end-0 me-5 actionHeaderContainerHistory">
                    </div>
                    <button type="button" class="btn-close btn-close-modal" data-coreui-dismiss="modal"  title="Close" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0 pb-5" id="modalDocumentFormBodyHistory">
                </div>
                <div class="modal-footer" id="modalDocumentFormFooterHistory"></div>
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
    <script src="{{ asset('js/app/scrollbar-custom.js?v=11.2') }}"></script>
    <script src="{{ asset('js/app/docapproval/approval.js?v=14.6') }}"></script>
    <script type="text/javascript">
        dayjs.locale('id');
        dayjs.extend(window.dayjs_plugin_customParseFormat);
        const optionsRequiredDatePicker = {
            locale: 'en-US',
            inputDateFormat: date => dayjs(date).locale('en').format('DD-MM-YYYY'),
            inputDateParse: date => dayjs(date, 'DD-MM-YYYY', 'id').toDate(),
            // minDate: dayjs(new Date()),
            showAdjacementDays: false,
        }

        $('#historyCompany').select2({
            allowClear: false,
            minimumResultsForSearch: Infinity,
        }).on('select2:select', function (e) {
            if ($.fn.DataTable.isDataTable('#historyTable')) {
                let table = $('#historyTable').DataTable();
                let scrollBody = $(table.table().node()).parent();
                let currentScrollTop = scrollBody.scrollTop();
                table.ajax.reload(function () {
                    scrollBody.scrollTop(currentScrollTop);
                }, false);
            }
        });
    </script>
@endsection
