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
                            <button class="nav-link filterTab px-3 py-1 w-100 active" id="userManagementTab" data-coreui-toggle="pill" data-coreui-target="#userManagementContent" type="button" role="tab" aria-controls="userManagementContent" aria-selected="false" tabindex="-1">User Management</button>
                        </li>
                        <li class="nav-item m-0" role="presentation">
                            <button class="nav-link filterTab px-3 py-1 w-100" id="roleNavigationTab" data-coreui-toggle="pill" data-coreui-target="#roleNavigationContent" type="button" role="tab" aria-controls="roleNavigationContent" aria-selected="true">Role Navigation</button>
                        </li>
                        <li class="nav-item m-0" role="presentation">
                            <button class="nav-link filterTab px-3 py-1 w-100" id="applicationAccountsTab" data-coreui-toggle="pill" data-coreui-target="#applicationAccountsContent" type="button" role="tab" aria-controls="applicationAccountsContent" aria-selected="true">Application Accounts</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane fade active show" id="userManagementContent" role="tabpanel" aria-labelledby="userManagementTab" tabindex="0">
            <div class="container-content px-3">
                <div class="row row-content">
                    <div class="col-12 col-md-8 h-100">
                        <div class="card full-height-column-wrapper">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="fs-7 fw-medium">
                                        <div class="card-title card-title-right-column m-0 d-flex align-items-center gap-1">Employee List</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="table-container table-responsive h-100" style="overflow-y: auto;">
                                    <form role="form" class="form-horizontal d-sm-block d-md-flex justify-content-between align-items-center mb-2 formSearchTable" enctype="multipart/form-data">
                                        <div class="me-md-2">
                                            <div class="form-group mb-2 mb-md-0 w-sm-100" style="width:210px">
                                                <select class="select2" name="filterEmployeeCompany" id="filterEmployeeCompany">
                                                    <option value="ALL" selected="">ALL COMPANY</option>
                                                    <option value="OLDEST_DECISION">OLDEST DECISION</option>
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
                                            <div class="d-flex align-items-center position-relative ms-1">
                                                <button class="btn btn-md btn-transparent filterButtonTable" type="button" data-reference="TRUE" data-reference-title="Vendor Reference" title="Filter"><i class="fa-regular fa-filter-list fa-lg"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                    <table id="employeeListTable" class="table table-striped table-hover" style="width:100%">
                                        <thead class="table-default">
                                            <tr>
                                                <th>#</th>
                                                <th>Employee ID</th>
                                                <th>Employee Name</th>
                                                <th>Company Code</th>
                                                <th>Role</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 h-100 d-flex flex-column gap-3">
                        <div class="h-35">
                            <div class="card full-height-column-wrapper">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="fs-7 fw-medium">
                                            <div class="card-title card-title-right-column m-0 d-flex align-items-center gap-1">Company List</div>
                                        </div>
                                        <div class="d-flex gap-1 justify-content-between align-items-center actionHeaderContainer">
                                            <button class="btn btn-sm btn-transparent" type="button" data-form="" title="Add Company">
                                                <i class="fa-solid fa-plus-large"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="table-container table-responsive h-100" style="overflow-y: auto;">
                                        <table id="companyListTable" class="table table-striped table-hover" style="width:100%">
                                            <thead class="table-default">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Code</th>
                                                    <th>Company Name</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                            <tfoot></tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="h-65">
                            <div class="card full-height-column-wrapper">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="fs-7 fw-medium">
                                            <div class="card-title card-title-right-column m-0 d-flex align-items-center gap-1">Employee Multi Company</div>
                                        </div>
                                        <div class="d-flex gap-1 justify-content-between align-items-center actionHeaderContainer">
                                            <button class="btn btn-sm btn-transparent" type="button" data-form="" title="Add Employee">
                                                <i class="fa-solid fa-plus-large"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="table-container table-responsive h-100" style="overflow-y: auto;">
                                        <form role="form" class="form-horizontal d-sm-block d-md-flex justify-content-end align-items-center gap-3 mb-2 formSearchTable" enctype="multipart/form-data">
                                            <div class="d-flex align-items-center position-relative">
                                                <input type="hidden" name="sortBy" value="">
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
                                            </div>
                                        </form>
                                        <table id="employeeMultiCompanyTable" class="table table-striped table-hover" style="width:100%">
                                            <thead class="table-default">
                                                <tr>
                                                    <th>Employee ID</th>
                                                    <th>Employee Name</th>
                                                    <th>Company ID</th>
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
        </div>
        <div class="tab-pane fade active show" id="roleNavigationContent" role="tabpanel" aria-labelledby="roleNavigationTab" tabindex="0">
            <div class="container-content px-3">
                <div class="row row-content">
                    <div class="col-12 col-md-8 h-100">
                        <div class="card full-height-column-wrapper">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="fs-7 fw-medium">
                                        <div class="card-title card-title-right-column m-0 d-flex align-items-center gap-1">Navigation List</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="table-container table-responsive h-100" style="overflow-y: auto;">
                                    <form role="form" class="form-horizontal d-sm-block d-md-flex justify-content-between align-items-center mb-2 formSearchTable" enctype="multipart/form-data">
                                        <div class="me-md-2"></div>
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
                                    <table id="employeeListTable" class="table table-striped table-hover" style="width:100%">
                                        <thead class="table-default">
                                            <tr>
                                                <th>Menu ID</th>
                                                <th>Menu Name</th>
                                                <th>Submenu ID</th>
                                                <th>Submenu Name</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 h-100 d-flex flex-column gap-3">
                        <div class="card full-height-column-wrapper">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="fs-7 fw-medium">
                                        <div class="card-title card-title-right-column m-0 d-flex align-items-center gap-1">Role List</div>
                                    </div>
                                    <div class="d-flex gap-1 justify-content-between align-items-center actionHeaderContainer">
                                        <button class="btn btn-sm btn-transparent" type="button" data-form="" title="Add Company">
                                            <i class="fa-solid fa-plus-large"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="table-container table-responsive h-100" style="overflow-y: auto;">
                                    <table id="companyListTable" class="table table-striped table-hover" style="width:100%">
                                        <thead class="table-default">
                                            <tr>
                                                <th>Role ID</th>
                                                <th>Role Name</th>
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
    <script src="{{ asset('js/app/scrollbar-custom.js?v=9.1') }}"></script>
    {{-- <script src="{{ asset('js/app/docapproval/request.js?v=9.1') }}"></script> --}}
    <script type="text/javascript">
        $('.select2').select2();
        dayjs.locale('id');
        dayjs.extend(window.dayjs_plugin_customParseFormat);
        const optionsRequiredDatePicker = {
            locale: 'en-US',
            inputDateFormat: date => dayjs(date).locale('en').format('DD-MM-YYYY'),
            inputDateParse: date => dayjs(date, 'DD-MM-YYYY', 'id').toDate(),
            // minDate: dayjs(new Date()),
            showAdjacementDays: false,
        }
    </script>
@endsection
