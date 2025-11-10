@extends('layouts.app')

@section('extra_css')
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

        .container-content {
            overflow: hidden !important;
        }
    }

    body {
        --height-content-full: calc(100vh - (var(--cui-header-height) + 1rem));
    }

    .container-content {
        height: calc((var(--vh, 1vh) * 100) - (var(--cui-header-height) + 1rem));
        padding-bottom: 1rem;
        display: flex;
        overflow: hidden:
    }

    .row-content {
        height: 100%;
        /* overflow: hidden; */
        display: flex;
        flex-wrap: nowrap;
        width: 100%;
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
    <div class="container-lg container-content">
        <div class="row-content">
            <div class="col-md-4">
                <div class="card mb-4" data-scroll-top="">
                    <div class="card-header pb-0">
                        <div class="card-title">Purchasing Archive</div>
                    </div>
                    <div class="card-body">

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
    <script src="{{ asset('js/app/scrollbar-custom.js?v=9.1') }}"></script>
    {{-- <script src="{{ asset('js/app/procpur/purchasing_request.js?v=9.1') }}"></script> --}}
    <script>
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
