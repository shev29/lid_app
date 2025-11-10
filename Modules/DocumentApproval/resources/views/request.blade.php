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

{{-- @section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb my-0">
            <li class="breadcrumb-item"><span>Document Approval</span></li>
            <li class="breadcrumb-item active"><span>Ongoing</span></li>
        </ol>
    </nav>
@endsection --}}

@section('content')
    <button type="button" class="btn btn-floating btn-company d-block d-md-none createNewRequest" style="z-index:2; right:20px; bottom:30px"><i class="fa-solid fa-plus-large"></i></button>
    <div class="col-12 mb-3 px-3">
        <div class="card">
            <div class="card-body px-2 py-2">
                <div class="row align-items-center">
                    <div class="col-12 col-md-4 d-flex align-items-center">
                        <div class="flex-grow-1">
                            <ul class="nav nav-pills p-1" id="pillsTabFilterForm" role="tablist" style="background-color: var(--cui-gray-300); border-radius: var(--cui-nav-pills-border-radius); border-bottom: 1px solid #dbdfe6;">
                                <input type="hidden" id="filterStatus" value="ONGOING">
                                <li class="nav-item col-6 m-0" role="presentation">
                                    <button class="nav-link filterTab px-3 py-1 w-100 active" id="pillsOngoingTab" data-coreui-toggle="pill" data-coreui-target="#pillsOngoingForm" data-status="ONGOING" type="button" role="tab" aria-controls="pillsOngoingForm" aria-selected="false" tabindex="-1">My Request Ongoing</button>
                                </li>
                                <li class="nav-item col-6 m-0" role="presentation">
                                    <button class="nav-link filterTab px-3 py-1 w-100" id="pillsHistoryTab" data-coreui-toggle="pill" data-coreui-target="#pillsHistoryForm" data-status="HISTORY" type="button" role="tab" aria-controls="pillsHistoryForm" aria-selected="true">My Request History</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-8">
                        <div class="d-flex justify-content-end align-items-center gap-3">
                            <div class="tab-content w-100" id="pillsTabFilterContentForm">
                                <div class="tab-pane fade active show" id="pillsOngoingForm" role="tabpanel" aria-labelledby="pillsOngoingTab" tabindex="0">
                                    <form role="form" class="form-horizontal form-horizontal d-flex flex-wrap justify-content-end gap-2 pt-2 pt-md-0" enctype="multipart/form-data" id="filterOngoing">
                                        <div class="d-flex align-items-center justify-content-end col-12 col-md-6 px-0 gap-2">
                                            <button type="button" class="btn btn-company ps-3 pe-3 d-none d-md-block createNewRequest" style="text-align:left"><i class="fa-solid fa-plus-large fa-fw"></i> New Request</button>
                                            <div id="searchFormOngoing" class="search-form-filter w-sm-100">
                                                <div class="form-group d-flex align-items-center position-relative">
                                                    <input type="text" class="form-control d-flex search" name="search" data-iscleared="true" spellcheck="false" autocomplete="off" placeholder="My Request Ongoing..." style="padding-right: 66px;">
                                                    <div class="position-absolute end-0 top-0 h-100 d-flex align-items-center px-1">
                                                        <button class="btn btn-sm btn-transparent searchFilterClear d-none" type="button" title="Clear"><i class="fa-solid fa-times fa-lg"></i></button>
                                                        <button class="btn btn-sm btn-transparent searchFilter" type="button" title="Search"><i class="fa-solid fa-search fa-lg"></i></button>
                                                        <button class="btn btn-sm btn-transparent filterButton" type="button" data-type="MY_REQUEST_ONGOING" title="Filter"><i class="fa-regular fa-filter-list fa-lg"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between justify-content-md-end col-12 col-md-4 gap-1">
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
                                                <button class="btn btn-md btn-transparent fw-normal dropdown-toggle" data-bs-boundary="viewport" id="dropdownMenuReqType" type="button" data-coreui-toggle="dropdown" data-coreui-auto-close="outside" aria-expanded="false" title="Ongoing Request Type">Request Type</button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuReqType">
                                                    <li>
                                                        <label class="dropdown-item d-flex align-items-center">
                                                            <input type="checkbox" class="form-check-input me-2 filterCheckAllRequest" id="filterCheckAllRequestOngoing" data-status="ONGOING" name="requestTypeAll" value="FALSE"> ALL REQUEST TYPE
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
                                                            <input type="radio" class="form-check-input me-2" name="sortBy" value="PRIORITY_LEVEL">Priority Level
                                                        </label>
                                                    </li>
                                                    <li>
                                                        <label class="dropdown-item d-flex align-items-center sortBySelectOngoing">
                                                            <input type="radio" class="form-check-input me-2" name="sortBy" value="LATEST_SUBMITTED" checked="">Latest Submitted
                                                        </label>
                                                    </li>
                                                    <li>
                                                        <label class="dropdown-item d-flex align-items-center sortBySelectOngoing">
                                                            <input type="radio" class="form-check-input me-2" name="sortBy" value="OLDEST_SUBMITTED">Oldest Submitted
                                                        </label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </form>
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
            <div class="col-md-4 left-column-wrapper d-none" id="leftColumnHistoryWrapper">
                <div class="left-column-wrapper left-column" id="leftColumnHistory" data-scroll-top="">
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
                                <div class="card-title m-0 d-flex align-items-center gap-1">My Request History</div>
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
                                <input type="hidden" name="type" value="REQUEST_HISTORY">
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
                                        <th>Doc. Number</th>
                                        <th>Company</th>
                                        <th>Department</th>
                                        <th>Priority</th>
                                        <th>Items</th>
                                        <th>Doc. Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNewDocument" data-coreui-backdrop="static" data-coreui-keyboard="false" data-coreui-focus="false" aria-labelledby="modalNewDocument" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header flex-wrap">
                    <h6 class="modal-title" id="modalNewDocumentTitle">New Approval Request</h6>
                    <button type="button" class="btn-close btn-close-modal" data-coreui-dismiss="modal"  title="Close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3" id="documentTypeContainer">
                            <div id="newDocumentTypeFeedback" class="invalid-feedback">Select document type</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="d-flex flex-column flex-md-row justify-content-between w-100 gap-2 mb-4">
                        <button type="button" class="btn btn-default flex-grow-1 mb-2" data-coreui-dismiss="modal" title="Close">
                            <i class="fas fa-xmark"></i> Close
                        </button>
                        <button type="button" class="btn btn-info flex-grow-1 mb-2" id="newRequestNext" data-request-type="" data-token="">
                            Next <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDocumentForm" data-coreui-backdrop="static" data-coreui-keyboard="false" data-coreui-focus="false" tabindex="-1"
        aria-labelledby="modalDocumentForm" aria-hidden="true" tabindex="-1">
            <div class="modal-dialog modal-dialog-scrollable modal-xl my-md-2-2">
            <div class="modal-content">
                <div class="modal-header flex-wrap">
                    <h6 class="modal-title" id="modalDocumentFormTitle"><div class="skeleton mb-0" style="width: 250px; height: 24px"></div></h6>
                    <button type="button" class="btn-close btn-close-modal" data-coreui-dismiss="modal"  title="Close" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalDocumentFormBody">
                    <form role="form" class="form-horizontal" enctype="multipart/form-data" id="formDocumentForm">
                        @csrf
                        <input type="hidden" name="documentType" id="documentTypeForm" value="">
                        <div id="headerContainer">
                        </div>
                        <div id="itemContainer"></div>
                        <div id="itemContainerFooter"></div>
                        <div id="formContainerFooter"></div>
                    </form>
                </div>
                <div class="modal-footer" id="modalDocumentFormFooter">
                    <div class="container-fluid p-0">
                        <div class="row justify-content-center w-100 mx-0">
                            <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                    <i class="fas fa-xmark"></i> Close
                                </button>
                            </div>
                            <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 saveForm" data-type="SUBMIT" id="submitDocumentForm">
                                    <i class="fa-regular fa-file-import"></i> Submit Form
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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
    <script src="{{ asset('js/app/scrollbar-custom.js?v=11.5') }}"></script>
    <script src="{{ asset('js/app/docapproval/request.js?v=14.9') }}"></script>
    {{-- <script>
        const apiUrl = 'http://127.0.0.1:8000/api/purchasingCarton/sendPrePo';

        const data = {
            po_number: 'PO123456',
            vendor: 'PT. Contoh',
            items: [
            { item_code: 'ABC123', qty: 10 },
            { item_code: 'XYZ456', qty: 5 }
            ]
        };

        fetch(apiUrl, {
            method: 'POST',
            headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-API-KEY': '9efc314b-f7a1-4cde-a01f-738ba2a62b37' // API Key kamu
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
            throw new Error(`HTTP error ${response.status}`);
            }
            return response.json();
        })
        .then(result => {
            console.log('Success:', result);
        })
        .catch(error => {
            console.error('Error:', error);
        });
    </script> --}}
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
