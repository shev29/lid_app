@extends('layouts.app')
@section('extra_css')
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/app/scrollbar-custom.css') }}">
<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/vendor/rowReorder.dataTables.min.css') }}">
<style>
    @media (max-width: 991.98px) {
        body {
            --height-content-full: calc(100vh - (var(--cui-header-height) + 1rem));
        }

        .container-content {
            height: var(--height-content-full);
            height: calc((var(--vh, 1vh) * 100) - (var(--cui-header-height) + 1rem));
            padding-bottom: 1rem;
        }
    }
    @media (min-width: 991.99px) {
        body {
            overflow: hidden !important;
            --height-content-full: calc(100vh - (var(--cui-header-height) + 1rem));
        }

        .container-content {
            height: var(--height-content-full);
            height: calc((var(--vh, 1vh) * 100) - (var(--cui-header-height) + 1rem));
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
    <div class="container-content px-3">
        <div class="row row-content">
            <div class="col-12 h-100">
                <div class="card full-height-column-wrapper" id="archivePurchasingTableContainer">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fs-7 fw-medium">
                                <div class="card-title card-title-right-column m-0 d-flex align-items-center gap-1">Purchasing Archive</div>
                            </div>
                            <div class="d-flex gap-1 justify-content-between align-items-center">
                                <button class="btn btn-sm btn-success actionBtnHeader" type="button" data-type="EXPORT_DATA_TABLE" data-table="archivePurchasingTable" title="Export Data"><i class="fa-solid fa-arrow-up-right fa-lg fa-fw"></i> Export Data</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="table-container table-secondary table-responsive h-100" style="overflow-y: hidden;">
                            <form role="form" class="form-horizontal d-sm-block d-md-flex justify-content-between align-items-center mb-2 formSearchTable" enctype="multipart/form-data">
                                <div class="me-md-2 d-flex gap-2">
                                    <div class="form-group mb-2 mb-md-0 w-sm-100" style="width:250px">
                                        <select class="select2" name="company">
                                            @foreach ($arrCompany as $row)
                                                @php
                                                    $selected = ($row['selected'] == true) ? 'selected' : '';
                                                @endphp
                                                <option value="{{ $row['id'] }}" {{$selected}}>{{ $row['text'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-2 mb-md-0 w-sm-100" style="width:250px">
                                        <select class="select2" name="applicant" id="applicant">
                                            @foreach ($arrSelection as $row)
                                                @php
                                                    $selected = ($row['selected'] == true) ? 'selected' : '';
                                                @endphp
                                                <option value="{{ $row['id'] }}" {{$selected}}>{{ $row['text'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <input type="hidden" name="status" value="ALL">
                                <input type="hidden" name="orderStartDate" value="">
                                <input type="hidden" name="orderEndDate" value="">
                                <input type="hidden" name="receivedStartDate" value="">
                                <input type="hidden" name="receivedEndDate" value="">
                                <input type="hidden" name="invStartDate" value="">
                                <input type="hidden" name="invEndDate" value="">
                                <input type="hidden" name="startAmount" value="">
                                <input type="hidden" name="endAmount" value="">
                                <input type="hidden" name="ccy" value="ALL">
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
                                        <button class="btn btn-md btn-transparent filterButtonTable" type="button" title="Filter Table" data-table="archivePurchasingTable"><i class="fa-regular fa-filter-list fa-lg"></i></button>
                                    </div>
                                </div>
                            </form>
                            <table id="archivePurchasingTable" class="table table-striped table-hover" style="width:100%">
                                <thead class="table-secondary">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Application Title</th>
                                        <th>PO Number</th>
                                        <th>Order Date</th>
                                        <th>Vendor</th>
                                        <th>Invoice Number</th>
                                        <th>Total Amount</th>
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
    <script src="{{ asset('js/vendor/dataTables.rowReorder.min.js') }}"></script>
    <script src="{{ asset('js/vendor/rowReorder.dataTables.min.js') }}"></script>
    <script>
        $('.select2').select2({
            allowClear: false,
            placeholder: '-- Select --',
        });
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
    <script src="{{ asset('js/app/scrollbar-custom.js?v=11.5') }}"></script>
    <script src="{{ asset('js/app/procpur/archive.js?v=14.16') }}"></script>
@endsection
