@extends('layouts.app')

@section('extra_css')
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/app/scrollbar-custom.css') }}">
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/rowReorder.dataTables.min.css') }}">
<style>
    @media (max-width: 991.98px) {
        body {
            --height-content-full: calc(100vh - (var(--cui-header-height) + 1rem + 70px));
        }

        .container-content {
            height: var(--height-content-full);
            height: calc((var(--vh, 1vh) * 100) - (var(--cui-header-height) + 1rem + 70px));
            padding-bottom: 1rem;
        }
    }
    @media (min-width: 991.99px) {
        body {
            overflow: hidden !important;
            --height-content-full: calc(100vh - (var(--cui-header-height) + 1.5rem + 70px));
        }

        .container-content {
            height: var(--height-content-full);
            height: calc((var(--vh, 1vh) * 100) - (var(--cui-header-height) + 1.5rem + 70px));
            padding-bottom: 1rem;
            display: flex;
        }
    }

    .row-content {
        height: 100%;
        /* overflow: hidden; */
        display: flex;
        flex-wrap: nowrap;
        /* width: max-content; */
        width: 100%;
    }

    .full-height-column-wrapper {
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
</style>
@endsection
@section('content')
    <div class="col-12 mb-3 px-3">
        <div class="card">
            <div class="card-body px-2 py-2">
                <div class="d-flex flex-grow-1">
                    <ul class="nav nav-pills gap-1 p-1" id="pillsTabFilterForm" role="tablist" style="">
                        <input type="hidden" id="filterStatus" value="ONGOING">
                        <li class="nav-item m-0" role="presentation">
                            <button class="nav-link filterTab px-3 py-1 w-100 active" id="userFlowTab" data-coreui-toggle="pill" data-coreui-target="#userFlowContent" type="button" role="tab" aria-controls="userFlowContent" aria-selected="false" tabindex="-1">User and Flow</button>
                        </li>
                        <li class="nav-item m-0" role="presentation">
                            <button class="nav-link filterTab px-3 py-1 w-100" id="vendorTab" data-coreui-toggle="pill" data-coreui-target="#vendorContent" type="button" role="tab" aria-controls="vendorContent" aria-selected="true">Vendor</button>
                        </li>
                        <li class="nav-item m-0" role="presentation">
                            <button class="nav-link filterTab px-3 py-1 w-100" id="budgetTab" data-coreui-toggle="pill" data-coreui-target="#budgetContent" type="button" role="tab" aria-controls="budgetContent" aria-selected="true">Budget</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane fade active show" id="userFlowContent" role="tabpanel" aria-labelledby="userFlowTab" tabindex="0">
            <div class="container-content px-3" id="body-container">
                <div class="row row-content">
                    <div class="col-12 col-md-7 h-100">
                        <div class="card full-height-column-wrapper" id="orderFormApplicantContainer">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="fs-7 fw-medium">
                                        <div class="card-title card-title-right-column m-0 d-flex align-items-center gap-1">Order Form Applicant</div>
                                    </div>
                                    <div class="d-flex gap-1 justify-content-between align-items-center actionHeaderContainer">
                                        <button class="btn btn-sm btn-transparent actionBtnHeader" type="button" data-type="ADD_ORDER_FORM_APPLICANT" title="Add Order Form Applicant">
                                            <i class="fa-solid fa-plus-large"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0 pb-0">
                                <div class="table-container table-responsive h-100" style="overflow-y: hidden;">
                                    <form role="form" class="form-horizontal d-sm-block d-md-flex justify-content-between align-items-center mb-2 formSearchTable" enctype="multipart/form-data">
                                        <div class="me-md-2">
                                            <div class="form-group mb-2 mb-md-0 w-sm-100" style="width:250px">
                                                <select class="select2" name="filterOrderFormCompany" id="filterOrderFormCompany">
                                                    <option></option>
                                                    @foreach ($arrCompany as $row)
                                                        @php
                                                            $selected = ($row['selected'] === true) ? 'selected' : '';
                                                        @endphp
                                                        <option value="{{$row['id']}}" {{$selected}}>{{$row['text']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
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
                                        </div>
                                    </form>
                                    <table id="orderFormApplicantTable" class="table table-striped table-hover" style="width:100%">
                                        <thead class="table-default">
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th>Employee ID</th>
                                                <th>Employee Name</th>
                                                <th>Department Order Form</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-7 h-100">
                        <div class="card full-height-column-wrapper" id="poApprovalRuleContainer">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="fs-7 fw-medium">
                                        <div class="card-title card-title-right-column m-0 d-flex align-items-center gap-1">PO Approval Rule Flow</div>
                                    </div>
                                    <div class="d-flex gap-1 justify-content-between align-items-center actionHeaderContainer">
                                        <button class="btn btn-sm btn-transparent actionBtnHeader" type="button" data-type="ADD_PO_APPROVAL_RULE" title="Add PO Approval Rule Flow">
                                            <i class="fa-solid fa-plus-large"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0 pb-0">
                                <div class="table-container table-responsive h-100" style="overflow-y: hidden;">
                                    <form role="form" class="form-horizontal d-sm-block d-md-flex justify-content-between align-items-center mb-2 formSearchTable" enctype="multipart/form-data">
                                        <div class="me-md-2">
                                            <div class="form-group mb-2 mb-md-0 w-sm-100" style="width:250px">
                                                <select class="select2" name="filterPoRuleCompany" id="filterPoRuleCompany">
                                                    <option></option>
                                                    @foreach ($arrCompany as $row)
                                                        @php
                                                            $selected = ($row['selected'] === true) ? 'selected' : '';
                                                        @endphp
                                                        <option value="{{$row['id']}}" {{$selected}}>{{$row['text']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
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
                                        </div>
                                    </form>
                                    <table id="poApprovalRuleTable" class="table table-striped table-hover" style="width:100%">
                                        <thead class="table-default">
                                            <tr>
                                                <th>Priority</th>
                                                <th>PO Applicant</th>
                                                <th>Rule Name</th>
                                                <th>Rule ID</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-7 h-100">
                        <div class="card full-height-column-wrapper" id="inspectorTableContainer">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="fs-7 fw-medium">
                                        <div class="card-title card-title-right-column m-0 d-flex align-items-center gap-1">Inspection</div>
                                    </div>
                                    <div class="d-flex gap-1 justify-content-between align-items-center actionHeaderContainer">
                                        <button class="btn btn-sm btn-transparent actionBtnHeader" type="button" data-type="ADD_INSPECTOR" title="Add Inspector">
                                            <i class="fa-solid fa-plus-large"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0 pb-0">
                                <div class="table-container table-responsive h-100" style="overflow-y: hidden;">
                                    <form role="form" class="form-horizontal d-sm-block d-md-flex justify-content-between align-items-center mb-2 formSearchTable" enctype="multipart/form-data">
                                        <div class="me-md-2">
                                            <div class="form-group mb-2 mb-md-0 w-sm-100" style="width:250px">
                                                <select class="select2" name="filterOrderFormCompany" id="filterInspectorCompany">
                                                    <option></option>
                                                    @foreach ($arrCompany as $row)
                                                        @php
                                                            $selected = ($row['selected'] === true) ? 'selected' : '';
                                                        @endphp
                                                        <option value="{{$row['id']}}" {{$selected}}>{{$row['text']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
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
                                        </div>
                                    </form>
                                    <table id="inspectorTable" class="table table-striped table-hover" style="width:100%">
                                        <thead class="table-default">
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th>Employee ID</th>
                                                <th>Inspection Receiver</th>
                                                <th>Department Order Form</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="tab-pane fade" id="vendorContent" role="tabpanel" aria-labelledby="vendorTab" tabindex="0">
            <div class="container-content px-3">
                <div class="row row-content">
                    <div class="col-12 col-md-9 h-100">
                        <div class="card full-height-column-wrapper" id="vendorContainer">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="fs-7 fw-medium">
                                        <div class="card-title card-title-right-column m-0 d-flex align-items-center gap-1">Purchasing Vendor</div>
                                    </div>
                                    <div class="d-flex gap-1 justify-content-between align-items-center actionHeaderContainer">
                                        <button type="button" class="btn btn-info btn-sm w-100 w-md-auto me-2 masterVendor" data-type="NEW" data-reference="TRUE" data-reference-title="Vendor Reference" data-vendor="${$(this).attr('data-vendor')}"><i class="fa-regular fa-plus fa-fw"></i> New Vendor</button>
                                        {{-- <button type="button" class="btn btn-default w-100 w-md-auto masterVendor" data-type="EDIT" data-id="" data-reference="TRUE" data-reference-title="Vendor Reference" data-vendor="${$(this).attr('data-vendor')}"></i><i class="fa-regular fa-pen-to-square fa-fw"></i> Edit Selected Vendor</button> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0 pb-0">
                                <div class="table-container table-responsive h-100" style="overflow-y: auto;">
                                    <form role="form" class="form-horizontal d-flex justify-content-end align-items-center gap-3 mb-2 formSearchTable" enctype="multipart/form-data">
                                        <div class="d-flex align-items-center position-relative">
                                            <input type="hidden" name="sortBy" value="VENDOR_NAME">
                                            <input type="hidden" name="menu" value="SETTINGS">
                                        </div>
                                        <div class="d-flex align-items-center position-relative">
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
                                                <button class="btn btn-md btn-transparent filterButtonTable" type="button" data-reference="TRUE" data-reference-title="Vendor Reference" title="Filter"><i class="fa-regular fa-filter-list fa-lg"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                    <table id="vendorReferenceTable" class="table table-striped table-hover" data-vendor="${$(this).attr('data-vendor')}" style="width:100%">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>#</th>
                                                <th>Vendor Account</th>
                                                <th>Vendor Name</th>
                                                <th>Group</th>
                                                <th>Currency</th>
                                                <th>Component</th>
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
        </div>
        <div class="tab-pane fade" id="budgetContent" role="tabpanel" aria-labelledby="budgetTab" tabindex="0">
            <div class="container-content px-3">
                <div class="row row-content">
                    <div class="col-12 col-md-9 h-100">
                        <div class="card full-height-column-wrapper" id="budgetContainer">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="fs-7 fw-medium">
                                        <div class="card-title card-title-right-column m-0 d-flex align-items-center gap-1">Purchasing Budget</div>
                                    </div>
                                    <div class="d-flex gap-1 justify-content-between align-items-center actionHeaderContainer">
                                        <button type="button" class="btn btn-info btn-sm w-100 w-md-auto me-2 masterBudget" data-type="NEW" data-reference="TRUE" data-reference-title="Budget" data-vendor="${$(this).attr('data-vendor')}"><i class="fa-regular fa-plus fa-fw"></i> New Budget</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0 pb-0">
                                {{-- BUDGET SECTION --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNewDocument" data-coreui-backdrop="static" data-coreui-keyboard="false" data-coreui-focus="false" aria-labelledby="modalNewDocument" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
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
                <div class="modal-header">
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
<script src="{{ asset('js/vendor/dataTables.rowReorder.min.js') }}"></script>
<script src="{{ asset('js/vendor/rowReorder.dataTables.min.js') }}"></script>
<script type="text/javascript">
    $('.select2').select2({
        allowClear: false,
        placeholder: '-- Select --',
    });
    dayjs.locale('id');
    dayjs.extend(window.dayjs_plugin_customParseFormat);
</script>
<script src="{{ asset('js/app/scrollbar-custom.js?v=11.5') }}"></script>
<script src="{{ asset('js/app/procpur/settings.js?v=14.9') }}"></script>
@endsection
