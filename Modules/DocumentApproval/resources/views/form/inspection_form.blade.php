<form role="form" class="form-horizontal" enctype="multipart/form-data" id="formDocumentForm">
    @csrf
    @php
        $currency = '';
    @endphp
    <input type="hidden" name="tokenForm" value="{{$dataForm['tokenForm']}}">
    <input type="hidden" name="requestType" id="requestTypeForm" value="{{$dataForm['type']}}">
    <input type="hidden" name="documentType" id="documentTypeForm" value="{{$dataForm['documentType']}}">
    <div id="headerContainer">
        <div class="row row-multi-col mb-3 pt-2 pb-1 bg-white">
            <div class="col-sm-4 mb-sm-0">
                <div class="form-group mb-3">
                    <label for="companyPoInspection" class="form-label">Company<span class="required"></span> :</label>
                    <select class="select2 selectNonClear" name="companyPoInspection" id="companyPoInspection">
                        <option></option>
                        @php
                            // if($dataForm['type'] == 'REVISE') {
                            //     echo '<option value="'.$dataForm['company']['id'].'" selected="">'.$dataForm['company']['text'].'</option>';
                            // }
                            // else {
                                foreach ($dataForm['company'] as $row) {
                                    $selected = ($row['selected'] == true) ? ' selected' : '';
                                    echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['text'].'</option>';
                                }
                            // }
                        @endphp
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="inspectionPoNo" class="form-label">PO Number<span class="required"></span> :</label>
                    <div class="row">
                        @php
                            if($dataForm['type'] == 'REVISE') {
                                echo '<div class="col-12">
                                        <input type="hidden" name="inspectionPoNo" value="'.$dataForm['latestHeader']['poNumber'].'">
                                        <select class="select2 selectPo" name="inspectionId" id="inspectionId">
                                            <option></option>
                                            <option value="'.$dataForm['latestHeader']['tokenInspection'].'" selected="">'.$dataForm['latestHeader']['poNumber'].'</option>
                                        </select>
                                    </div>
                                ';
                            }
                            else {
                                echo '<div class="col-10 pe-0">
                                        <select class="select2 selectPo" name="inspectionPoNo" id="inspectionPoNo">
                                            <option></option>
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <button type="button" class="btn btn-secondary float-end" id="poListBtn" data-type="" data-form="" data-token=""><i class="fa-solid fa-search fa-lg"></i></button>
                                    </div>';
                            }
                        @endphp
                    </div>

                </div>
                <div class="form-group mb-3">
                    <label for="inspectionPoDate" class="form-label">PO Date<span class="required"></span> :</label>
                    <input type="text" class="form-control" name="inspectionPoDate" id="inspectionPoDate" value="{{$dataForm['latestHeader']['poDate']}}" readonly>
                </div>
                <div class="form-group mb-3">
                    <label for="inspectionVendor" class="form-label">Vendor<span class="required"></span> :</label>
                    <textarea class="form-control autosize d-block" spellcheck="false" maxlength="255" id="inspectionVendor" name="inspectionVendor" readonly>{{$dataForm['latestHeader']['vendor']}}</textarea>
                </div>

                <div class="form-group mb-3">
                    <label for="inspectionDelivery" class="form-label">Delivery To<span class="required"></span> :</label>
                    <textarea class="form-control autosize d-block" spellcheck="false" maxlength="255" id="inspectionDelivery" name="inspectionDelivery" readonly>{{$dataForm['latestHeader']['inspectionDelivery']}}</textarea>
                </div>
            </div>
            <div class="col-sm-4 mb-sm-0">
                <div class="form-group mb-3">
                    <label for="inspectionIncomingDate" class="form-label">Incoming Date<span class="required"></span> :</label>
                    {{-- @php
                        if($dataForm['type'] == 'REVISE') {
                            $poIncoming = $dataForm['latestHeader']['poIncoming'];
                        }
                        else {
                            $poIncoming =  $dataForm['poIncoming'];
                        }
                    @endphp --}}
                    <div class="form-group date-picker" id="inspectionIncomingDate" data-coreui-name="inspectionIncomingDate" data-coreui-date="{{$dataForm['latestHeader']['incomingDate']}}"></div>
                </div>

                <div class="card mb-3">
                    <label class="form-label px-2-2 pt-2">Checklist :</label>
                    <div class="card-body py-2 px-2-2">
                        <div class="pb-2">
                            <div class="data-details">
                                <div class="">
                                    <label class="text-wrap lh-base d-block" style="padding-left: 1.5rem; text-indent: -1.6rem;cursor:pointer">
                                        <div class="white-space-pre"><input type="checkbox" class="form-check-input me-2" name="inspectionChecklist[]" value="1" {{$dataForm['latestHeader']['latestChecklist'][0]}}>Received quantity same as ordered</div>
                                    </label>
                                    <label class="text-wrap lh-base d-block mt-2" style="padding-left: 1.5rem; text-indent: -1.6rem;cursor:pointer">
                                        <div class="white-space-pre"><input type="checkbox" class="form-check-input me-2" name="inspectionChecklist[]" value="2" {{$dataForm['latestHeader']['latestChecklist'][1]}}>Received specification same as ordered</div>
                                    </label>
                                    <label class="text-wrap lh-base d-block mt-2" style="padding-left: 1.5rem; text-indent: -1.6rem;cursor:pointer">
                                        <div class="white-space-pre"><input type="checkbox" class="form-check-input me-2" name="inspectionChecklist[]" value="3" {{$dataForm['latestHeader']['latestChecklist'][2]}}>On-time delivery</div>
                                    </label>
                                    <label class="text-wrap lh-base d-block mt-2" style="padding-left: 1.5rem; text-indent: -1.6rem;cursor:pointer">
                                        <div class="white-space-pre"><input type="checkbox" class="form-check-input me-2" name="inspectionChecklist[]" value="4" {{$dataForm['latestHeader']['latestChecklist'][3]}}>No defect found on items</div>
                                    </label>
                                    <label class="text-wrap lh-base d-block mt-2" style="padding-left: 1.5rem; text-indent: -1.6rem;cursor:pointer">
                                        <div class="white-space-pre"><input type="checkbox" class="form-check-input me-2" name="inspectionChecklist[]" value="5" {{$dataForm['latestHeader']['latestChecklist'][4]}}>Vendor documents same as delivered item</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="inspectionRemarks" class="form-label">Additional Remarks :</label>
                    <textarea class="form-control autosize" spellcheck="false" maxlength="200" id="inspectionRemarks" name="inspectionRemarks" data-limit-rows="true" rows="4">{{$dataForm['latestHeader']['inspectionRemarks']}}</textarea>
                </div>
            </div>
            <div class="col-sm-4 mb-sm-0">
                @php
                    $latestChecker = $latestConfirmer = '';
                    if($dataForm['type'] == 'REVISE') {
                        $latestChecker = $dataForm['latestHeader']['latestChecker'];
                        $latestConfirmer = $dataForm['latestHeader']['latestConfirmer'];
                    }
                @endphp
                <div class="form-group mb-3">
                    <label for="receiver" class="form-label">Receiver<span class="required"></span> :</label>
                    <select class="select2 selectNonClear" name="receiver" id="receiver">
                        <option></option>
                        @php
                            if($dataForm['type'] == 'REVISE') {
                                echo '<option value="'.$dataForm['latestHeader']['employeeId'].'" selected="">'.$dataForm['latestHeader']['employeeName'].'</option>';
                            }
                            else {
                                foreach ($dataForm['receiver'] as $row) {
                                    echo '<option value="'.$row['id'].'" '.$row['selected'].'>'.$row['text'].'</option>';
                                }
                            }
                        @endphp
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="checker" class="form-label">Checker<span class="required"></span> :</label>
                    <select class="select2 selectNonClear" name="checker" id="checker" data-type="CHECKER">
                        <option></option>
                        @php
                            if($dataForm['type'] == 'REVISE') {
                                echo '<option value="'.$dataForm['latestHeader']['latestChecker']['id'].'" selected="">'.$dataForm['latestHeader']['latestChecker']['text'].'</option>';
                            }
                        @endphp
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="approverSelect" class="form-label">Confirmer<span class="required"></span> :</label>
                    <select class="select2 selectNonClear" name="confirmer" id="confirmer" data-type="CONFIRMER">
                        <option></option>
                        @php
                            if($dataForm['type'] == 'REVISE') {
                                echo '<option value="'.$dataForm['latestHeader']['latestConfirmer']['id'].'" selected="">'.$dataForm['latestHeader']['latestConfirmer']['text'].'</option>';
                            }
                        @endphp
                    </select>
                </div>


            </div>
        </div>
    </div>
    <div class="pt-2" id="itemContainer">
        <div class="table-responsive mb-2">
            <table class="table table-form" id="tableInspectionItem">
                <thead>
                    <tr>
                        <th style="width: 5%">No.</th>
                        <th style="width: 50%">Description</th>
                        <th style="width: 15%">Qty</th>
                        <th style="width: 15%">Incoming Qty</th>
                        <th style="width: 15%">Remaining Qty</th>
                    </tr>
                </thead>
                <tbody id="tbodyInspectionItem">
                    @php
                        if(count($dataForm['latestHeader']['latestItem']) > 0) {
                            $x = 1;
                            foreach ($dataForm['latestHeader']['latestItem'] as $rowItem) {
                                echo '<tr>
                                        <td class="text-center">
                                            <input type="hidden" name="itemId[]" value="'.$rowItem['itemId'].'">
                                            '.$x.'
                                        </td>
                                        <td>'.$rowItem['applianceItem'].'</td>
                                        <td class="text-center">
                                            <input type="hidden" name="qty[]" value="'.$rowItem['unitQuantity'].'">
                                            '.$rowItem['unitQuantity'].'
                                        </td>
                                        <td><input type="text" class="form-control text-center numberValue countRemaining" name="incomingQty[]" value="'.$rowItem['incomingQty'].'"></td>
                                        <td><input type="text" class="form-control text-center" name="remainingQty[]" value="'.$rowItem['remainingQty'].'" readonly></td>
                                    </tr>';
                            }
                        }
                        else{
                            echo '<tr>
                                    <td colspan="5" class="no_result_table py-3 text-center">
                                        <input type="hidden" name="itemId[]">
                                        No Item data
                                    </td>
                                </tr>';
                        }
                    @endphp
                </tbody>
            </table>
    </div>
    <div class="row">
        <div id="formContainerFooter" class="col-12 col-md-6">
            <div class="row row-multi-col mb-3 pb-1 bg-white">
                <div class="row-multi-col-header">
                    <span style="display: inline">Documents<span class="required"></span> :</span>
                </div>
                <div id="footerAttachment" class="pb-3">
                    <div class="">
                        <div id="dropZone" class="form-group border p-4 text-center bg-light">
                            <p>Drag and drop files here or Select files</p>
                            <button type="button" class="btn btn-secondary btn-md" id="selectFileBtn">Select files...</button>
                            <input type="file" class="form-control d-none" id="fileUpload" name="attachmentFile[]" multiple>
                        </div>
                    </div>
                    <div id="fileList" class="list-group mt-2">
                        @php
                            $attachmentList = '';
                            foreach ($dataForm['latestHeader']['attachmentList'] as $index => $row) {
                                $attachmentList .= '<div class="form-group existAttachment" data-index="'.$index.'">
                                                        <div class="file-item list-group-item list-group-item-action" data-index="'.$index.'">
                                                            <div class="file-details view-file-fullscreen" data-token="'.$row['tokenAttachment'].'">
                                                                <input type="hidden" name="existAttachment[]" value="'.$row['tokenAttachment'].'">
                                                                <i class="'.$row['icon'].' file-icon"></i>
                                                                <div>
                                                                    <span class="file-name">'.$row['filename'].'</span>
                                                                </div>
                                                            </div>
                                                            <button type="button" class="btn-close-black deleteFileItem" title="Delete" aria-label="Delete"></button>
                                                        </div>
                                                    </div>';
                            }
                            echo $attachmentList;
                        @endphp
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@once
<script>
    var poInspectionScrollbarInstance;
    var currentPagePoInspection = 1;
    var isLoadingPo = false;
    var hasMoreDataPo = true;

    selectedFiles = [];
    selectedFilesProperties = [];
    $('.autosize').autosize().trigger('change');
    dayjs.locale('id');
    dayjs.extend(window.dayjs_plugin_customParseFormat);

    var inspectionIncomingDate = {
        locale: 'en-US',
        inputDateFormat: date => dayjs(date).locale('en').format('DD-MMM-YYYY'),
        inputDateParse: date => dayjs(date, 'DD-MMM-YYYY', 'id').toDate(),
        maxDate: dayjs(new Date()),
        showAdjacementDays: false,
    }

    new coreui.DatePicker(document.getElementById(`inspectionIncomingDate`), inspectionIncomingDate);

    if($('#requestTypeForm').val() == 'REVISE') {
        @json($dataForm['latestHeader']['attachmentList']).forEach((element, index) => {
            const properties = {
                'name': element.filename,
                'type': element.mimeType,
                'size': null,
                'exist': true,
                'content': element.tokenAttachment,  // Menyimpan konten file jika diperlukan
            };
            selectedFiles.push(properties);
            selectedFilesProperties.push(properties);
        });
    }

    $('.selectPo').select2({
        minimumResultsForSearch: Infinity,
        allowClear: false,
        placeholder: '-- Select --',
        matcher: function(params, data) {
            if ($.trim(params.term) === '') {
            return data;
            }

            if (typeof data.text === 'undefined') {
            return null;
            }

            const term = params.term.toLowerCase();
            const text = data.text.toLowerCase();
            const description = $(data.element).data('description1')?.toString().toLowerCase();

            if (text.indexOf(term) > -1 || (description && description.indexOf(term) > -1)) {
            return data;
            }

            return null;
        },
        templateResult: formatResult,
        templateSelection: function (data) {
            return data.text || data.id;
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    $('.selectNonClear').select2({
        allowClear: false,
        placeholder: '-- Select --',
        matcher: function(params, data) {
            if ($.trim(params.term) === '') {
            return data;
            }

            if (typeof data.text === 'undefined') {
            return null;
            }

            const term = params.term.toLowerCase();
            const text = data.text.toLowerCase();
            const description = $(data.element).data('description1')?.toString().toLowerCase();

            if (text.indexOf(term) > -1 || (description && description.indexOf(term) > -1)) {
            return data;
            }

            return null;
        },
        templateResult: formatResult,
        templateSelection: function (data) {
            return data.text || data.id;
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    $(document).off('click', '#poListBtn').on('click', '#poListBtn', async function (e) {
        Snackbar.close();
        clearValidation();
        if (!$('#companyPoInspection').val()) {
            $('#companyPoInspection').addClass('is-invalid');
            $('#companyPoInspection').closest('.form-group').find('span.selection').find('span.select2-selection').addClass('is-invalid');
            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Please select Company first` });
            return;
        }

        asideHide();
        $('.aside-title').html('Purchase Order <span class="start-100 ms-1 badge rounded-pill bg-company py-015 px-3 fs-8" id="badgeCountPOInspection"><i class="fa-regular fa-circle-notch fa-spin"></i></span>');
        $('.aside-content').html(`
            <div class="row row-content">
                <div class="col-12 card-container full-height-column-wrapper px-0">
                    <div class="card right-column-card" style="border:none !important; left:-5px !important;">
                        <div class="card-body full-height-column-wrapper py-0">
                            <form role="form" class="form-horizontal" enctype="multipart/form-data" id="filterPoInspection">
                                <div id="searchPoInspectionContainer" class="search-form-filter pt-0">
                                    <div class="d-flex align-items-center position-relative">
                                        <div class="form-group d-flex align-items-center position-relative w-100">
                                            <input type="text" class="form-control d-flex" id="searchInputPoInspection" name="search" data-iscleared="true" data-form="PO_INSPECTION_RECEIVER" data-type="" spellcheck="false" autocomplete="off" placeholder="Search..." style="padding-right: 66px;">
                                            <input type="hidden" name="type" value="PO_INSPECTION_RECEIVER">
                                            <div class="position-absolute end-0 top-0 h-100 d-flex align-items-center px-1">
                                                <button class="btn btn-sm btn-transparent d-none" id="searchButtonClear" type="button" data-form="PO_INSPECTION" title="Clear PO Search">
                                                    <i class="fa-solid fa-times fa-lg"></i>
                                                </button>
                                                <button class="btn btn-sm btn-transparent" id="searchButton" type="button" data-form="PO_INSPECTION" title="Search PO">
                                                    <i class="fa-solid fa-search fa-lg"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="px-0 mt-3" id="poInspectionContainer" style="height:100% !important" data-scroll-top="" data-type="PO_INSPECTION">
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
            </div>
        `);

        $('.asideFooterBtn').html(`<div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                    <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
                </div>
                <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                    <button type="button" class="btn btn-secondary w-100 w-md-auto" id="selectPoInspection" data-type="" data-item="" disabled></i>Select PO</button>
                </div>`);
        $('.overlay-aside').addClass('show').trigger('shown');
        $('#globalAside').addClass('show').trigger('shown');
        // $('#asideDetailForm').css({
        //     'overflow': 'hidden',
        //     'padding-left': '0',
        //     'padding-right': '0',
        // }).scrollTop(0);
        $('body').addClass('overflow-hidden');

        currentPagePoInspection = 1;
        isLoadingPo = false;
        hasMoreDataPo = true;
        poInspection();
    });

    $(document).off('click', '#selectPoInspection').on('click', '#selectPoInspection', async function(event) {
        $('#inspectionPoNo').val(null).empty().trigger('change');
        clearValidation();
        const container = $('#poInspectionContainer').find('div.card-link-content.active');
        var data = {
            id: container.data('token'),
            text: container.find('div.card-title').text()
        };

        var newOption = new Option(data.text, data.id, false, false);
        $('#inspectionPoNo').append(newOption).trigger('change');

        asideHide();
        $('#inspectionPoDate').replaceWith(`<div class="skeleton w-100" id="inspectionPoDate"></div>`);
        $('#inspectionVendor').replaceWith(`<div class="skeleton textarea w-100" id="inspectionVendor"></div>`);
        $('#inspectionDelivery').replaceWith(`<div class="skeleton textarea w-100" id="inspectionDelivery"></div>`);

        if ($('#checker').hasClass('select2-hidden-accessible')) {
            $('#checker').val(null).empty().trigger('change');
            $('#checker').select2('destroy');
        }

        if ($('#confirmer').hasClass('select2-hidden-accessible')) {
            $('#confirmer').val(null).empty().trigger('change');
            $('#confirmer').select2('destroy');
        }

        $('#checker').replaceWith(`<div class="skeleton w-100" id="checker"></div>`);
        $('#confirmer').replaceWith(`<div class="skeleton w-100" id="confirmer"></div>`);

        $('#tbodyInspectionItem').html(`<tr>
                                            <td class="py-3 text-center">
                                                <input type="hidden" name="itemId[]">
                                                <div class="skeleton w-100"></div>
                                            </td>
                                            <td class="py-3 text-center">
                                                <div class="skeleton w-100"></div>
                                            </td>
                                            <td class="py-3 text-center">
                                                <div class="skeleton w-100"></div>
                                            </td>
                                            <td class="py-3 text-center">
                                                <div class="skeleton w-100"></div>
                                            </td>
                                            <td class="py-3 text-center">
                                                <div class="skeleton w-100"></div>
                                            </td>
                                        </tr>`);

        try {
            let formData;
            formData = new FormData();
            formData.append('form', 'PO_INSPECTION');
            formData.append('token', $('#inspectionPoNo').val());
            const queryString = new URLSearchParams(formData).toString();
            const response = await fetch(`/proc_pur/newForm?${queryString}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                    'Referer': window.location.href
                }
            });

            const result = await response.json();

            $('#inspectionPoDate').replaceWith(`<input type="text" class="form-control" name="inspectionPoDate" id="inspectionPoDate" readonly>`);
            $('#inspectionVendor').replaceWith(`<textarea class="form-control autosize d-block" spellcheck="false" maxlength="255" id="inspectionVendor" name="inspectionVendor" readonly></textarea>`);
            $('#inspectionDelivery').replaceWith(`<textarea class="form-control autosize d-block" spellcheck="false" maxlength="255" id="inspectionDelivery" name="inspectionDelivery" readonly></textarea>`);

            $('#checker').replaceWith(`<select class="select2 selectNonClear" name="checker" id="checker" data-type="CHECKER"><option></option></select>`);
            $('#confirmer').replaceWith(`<select class="select2 selectNonClear" name="confirmer" id="confirmer" data-type="CONFIRMER"><option></option></select>`);

            $('#tbodyInspectionItem').html(`<tr>
                                                <td colspan="5" class="no_result_table py-3 text-center">
                                                    <input type="hidden" name="itemId[]">
                                                    No Item data
                                                </td>
                                            </tr>`);

            if (result.status === 200) {
                $('#inspectionPoDate').val(result.data.data.poDate);
                $('#inspectionVendor').text(result.data.data.vendor);
                $('#inspectionDelivery').val(result.data.data.deliveryTo);

                if(result.data.data.checker.length > 0) {
                    result.data.data.checker.forEach(row => {
                        var option = {
                            id: row.id,
                            text: row.text,
                            description1: row.description1,
                        };

                        var newOption = new Option(option.text, option.id, false, false);
                        $(newOption).attr('data-description1', option.description1);
                        $('#checker').append(newOption);
                    });
                    $('#checker').trigger('change');
                }

                if(result.data.data.confirmer.length > 0) {
                    result.data.data.confirmer.forEach(row => {
                        var option = {
                            id: row.id,
                            text: row.text,
                            description1: row.description1,
                        };

                        var newOption = new Option(option.text, option.id, false, false);
                        $(newOption).attr('data-description1', option.description1);
                        $('#confirmer').append(newOption);
                    });
                    $('#confirmer').trigger('change');
                }

                if(result.data.data.item.length > 0) {
                    $('#tbodyInspectionItem').html('');
                    result.data.data.item.forEach((row, index) => {
                        let item = `<tr>
                                        <td class="text-center">
                                            <input type="hidden" name="itemId[]" value="${row.itemId}">
                                            ${index + 1}
                                        </td>
                                        <td>${row.applianceItem}</td>
                                        <td class="text-center">
                                            <input type="hidden" name="qty[]" value="${row.unitQuantity}">
                                            ${row.unitQuantity}
                                        </td>
                                        <td><input type="text" class="form-control text-center numberValue countRemaining" name="incomingQty[]"></td>
                                        <td><input type="text" class="form-control text-center" name="remainingQty[]" readonly></td>
                                    </tr>`;
                        $('#tbodyInspectionItem').append(item);
                    });
                }

                $('.autosize').autosize().trigger('change');
            }
            else {
                Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> ${result.message}` });
            }
        }
        catch (error) {
            $('#inspectionPoDate').replaceWith(`<input type="text" class="form-control" name="inspectionPoDate" id="inspectionPoDate" readonly>`);
            $('#inspectionVendor').replaceWith(`<textarea class="form-control autosize d-block" spellcheck="false" maxlength="255" id="inspectionVendor" name="inspectionVendor" readonly></textarea>`);
            $('#inspectionDelivery').replaceWith(`<textarea class="form-control autosize d-block" spellcheck="false" maxlength="255" id="inspectionDelivery" name="inspectionDelivery" readonly></textarea>`);

            $('#checker').replaceWith(`<select class="select2 selectNonClear" name="checker" id="checker" data-type="CHECKER"><option></option></select>`);
            $('#confirmer').replaceWith(`<select class="select2 selectNonClear" name="confirmer" id="confirmer" data-type="CONFIRMER"><option></option></select>`);

            $('#tbodyInspectionItem').html(`<tr>
                                                <td colspan="5" class="no_result_table py-3 text-center">
                                                    <input type="hidden" name="itemId[]">
                                                    No Item data
                                                </td>
                                            </tr>`);

            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> 'Error fetching data:', ${error}` });
        }
        finally {
            $('.selectNonClear').select2({
                allowClear: false,
                placeholder: '-- Select --',
                matcher: function(params, data) {
                    if ($.trim(params.term) === '') {
                    return data;
                    }

                    if (typeof data.text === 'undefined') {
                    return null;
                    }

                    const term = params.term.toLowerCase();
                    const text = data.text.toLowerCase();
                    const description = $(data.element).data('description1')?.toString().toLowerCase();

                    if (text.indexOf(term) > -1 || (description && description.indexOf(term) > -1)) {
                    return data;
                    }

                    return null;
                },
                templateResult: formatResult,
                templateSelection: function (data) {
                    return data.text || data.id;
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
        }
    });

    $(document).off('click', '#searchButtonClear').on('click', '#searchButtonClear', function(event) {
        let isCleared = $(this).closest('.form-group').find('input#searchInputPoInspection').attr('data-iscleared');
        $(this).closest('.form-group').find('input#searchInputPoInspection').val('');
        $(this).closest('.form-group').find('input#searchInputPoInspection').focus();
        $(this).addClass('d-none');

        if (isCleared == 'false') {
            event.stopImmediatePropagation();
            currentPagePoInspection = 1;
            isLoadingPo = false;
            hasMoreDataPo = true;
            poInspection();
        }

        $(this).closest('.form-group').find('input#searchInputPoInspection').attr('data-iscleared', 'true');
    });

    $(document).off('click', '#searchButton').on('click', '#searchButton', function(event) {
        let e = jQuery.Event("keydown");
        e.keyCode = 13;
        $("#searchInputPoInspection").trigger(e);
    });

    $(document).off('input', '#searchInputPoInspection').on('input', '#searchInputPoInspection', function(event) {
        clearTimeout(debounceTimeout);
        const currentValue = $(this).val().trim();
        let isCleared = $(this).attr('data-iscleared');
        if(currentValue.length === 0) {
            $(this).closest('.form-group').find('button#searchButtonClear').addClass('d-none');
            debounceTimeout = setTimeout(function () {
                if (isCleared == 'false') {
                    event.stopImmediatePropagation();
                    currentPagePoInspection = 1;
                    isLoadingPo = false;
                    hasMoreDataPo = true;
                    poInspection();
                }
            }, 1000);

            $(this).attr('data-iscleared', 'true');
        }
        else {
            $(this).closest('.form-group').find('button#searchButtonClear').removeClass('d-none');
            // $(this).attr('data-iscleared', 'false');
        }
    });

    $(document).off('keydown', '#searchInputPoInspection').on('keydown', '#searchInputPoInspection', function(event) {
        const currentValue = $(this).val().trim();
        let isCleared = $(this).attr('data-iscleared');
        if (event.key === 'Enter' || event.keyCode === 13) {
            if (currentValue.length === 0 && isCleared == 'false') {
                event.stopImmediatePropagation();
                currentPagePoInspection = 1;
                isLoadingPo = false;
                hasMoreDataPo = true;
                poInspection();
                $(this).attr('data-iscleared', 'true');
            }
            else if (currentValue.length > 0) {
                event.stopImmediatePropagation();
                currentPagePoInspection = 1;
                isLoadingPo = false;
                hasMoreDataPo = true;
                poInspection();
                $(this).attr('data-iscleared', 'false');
            }

            event.preventDefault();
        }
    });

    // $(document).off('change', '#inspectionId').on('change', '#inspectionId', async function(event) {
    //     console.log('abcd');
    //     $('#inspectionPoDate').replaceWith(`<div class="skeleton w-100" id="inspectionPoDate"></div>`);
    //     $('#inspectionVendor').replaceWith(`<div class="skeleton textarea w-100" id="inspectionVendor"></div>`);
    //     $('#inspectionDelivery').replaceWith(`<div class="skeleton textarea w-100" id="inspectionDelivery"></div>`);

    //     if ($('#checker').hasClass('select2-hidden-accessible')) {
    //         $('#checker').val(null).empty().trigger('change');
    //         $('#checker').select2('destroy');
    //     }

    //     if ($('#confirmer').hasClass('select2-hidden-accessible')) {
    //         $('#confirmer').val(null).empty().trigger('change');
    //         $('#confirmer').select2('destroy');
    //     }

    //     $('#checker').replaceWith(`<div class="skeleton w-100" id="checker"></div>`);
    //     $('#confirmer').replaceWith(`<div class="skeleton w-100" id="confirmer"></div>`);

    //     $('#tbodyInspectionItem').html(`<tr>
    //                                         <td class="py-3 text-center">
    //                                             <input type="hidden" name="itemId[]">
    //                                             <div class="skeleton w-100"></div>
    //                                         </td>
    //                                         <td class="py-3 text-center">
    //                                             <div class="skeleton w-100"></div>
    //                                         </td>
    //                                         <td class="py-3 text-center">
    //                                             <div class="skeleton w-100"></div>
    //                                         </td>
    //                                         <td class="py-3 text-center">
    //                                             <div class="skeleton w-100"></div>
    //                                         </td>
    //                                         <td class="py-3 text-center">
    //                                             <div class="skeleton w-100"></div>
    //                                         </td>
    //                                     </tr>`);

    //     try {
    //         let formData;
    //         formData = new FormData();
    //         formData.append('form', 'PO_INSPECTION_REVISE');
    //         formData.append('token', $('#inspectionId').val());
    //         const queryString = new URLSearchParams(formData).toString();
    //         const response = await fetch(`/proc_pur/newForm?${queryString}`, {
    //             method: 'GET',
    //             headers: {
    //                 'Content-Type': 'application/json',
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    //                 'Accept': 'application/json',
    //                 'Referer': window.location.href
    //             }
    //         });

    //         const result = await response.json();

    //         $('#inspectionPoDate').replaceWith(`<input type="text" class="form-control" name="inspectionPoDate" id="inspectionPoDate" readonly>`);
    //         $('#inspectionVendor').replaceWith(`<textarea class="form-control autosize d-block" spellcheck="false" maxlength="255" id="inspectionVendor" name="inspectionVendor" readonly></textarea>`);
    //         $('#inspectionDelivery').replaceWith(`<textarea class="form-control autosize d-block" spellcheck="false" maxlength="255" id="inspectionDelivery" name="inspectionDelivery" readonly></textarea>`);

    //         $('#checker').replaceWith(`<select class="select2 selectNonClear" name="checker" id="checker" data-type="CHECKER"><option></option></select>`);
    //         $('#confirmer').replaceWith(`<select class="select2 selectNonClear" name="confirmer" id="confirmer" data-type="CONFIRMER"><option></option></select>`);

    //         $('#tbodyInspectionItem').html(`<tr>
    //                                             <td colspan="5" class="no_result_table py-3 text-center">
    //                                                 <input type="hidden" name="itemId[]">
    //                                                 No Item data
    //                                             </td>
    //                                         </tr>`);

    //         if (result.status === 200) {
    //             $('#inspectionPoDate').val(result.data.data.poDate);
    //             $('#inspectionVendor').text(result.data.data.vendor);
    //             $('#inspectionDelivery').val(result.data.data.deliveryTo);

    //             if(result.data.data.checker.length > 0) {
    //                 result.data.data.checker.forEach(row => {
    //                     var option = {
    //                         id: row.id,
    //                         text: row.text,
    //                         description1: row.description1,
    //                     };

    //                     var newOption = new Option(option.text, option.id, false, false);
    //                     $(newOption).attr('data-description1', option.description1);
    //                     $('#checker').append(newOption);
    //                 });
    //                 $('#checker').trigger('change');
    //             }

    //             if(result.data.data.confirmer.length > 0) {
    //                 result.data.data.confirmer.forEach(row => {
    //                     var option = {
    //                         id: row.id,
    //                         text: row.text,
    //                         description1: row.description1,
    //                     };

    //                     var newOption = new Option(option.text, option.id, false, false);
    //                     $(newOption).attr('data-description1', option.description1);
    //                     $('#confirmer').append(newOption);
    //                 });
    //                 $('#confirmer').trigger('change');
    //             }

    //             if(result.data.data.item.length > 0) {
    //                 $('#tbodyInspectionItem').html('');
    //                 result.data.data.item.forEach((row, index) => {
    //                     let item = `<tr>
    //                                     <td class="text-center">
    //                                         <input type="hidden" name="itemId[]" value="${row.itemId}">
    //                                         ${index + 1}
    //                                     </td>
    //                                     <td>${row.applianceItem}</td>
    //                                     <td class="text-center">
    //                                         <input type="hidden" name="qty[]" value="${row.unitQuantity}">
    //                                         ${row.unitQuantity}
    //                                     </td>
    //                                     <td><input type="text" class="form-control text-center numberValue countRemaining" name="incomingQty[]"></td>
    //                                     <td><input type="text" class="form-control text-center" name="remainingQty[]" readonly></td>
    //                                 </tr>`;
    //                     $('#tbodyInspectionItem').append(item);
    //                 });
    //             }

    //             $('.autosize').autosize().trigger('change');
    //         }
    //         else {
    //             Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> ${result.message}` });
    //         }
    //     }
    //     catch (error) {
    //         $('#inspectionPoDate').replaceWith(`<input type="text" class="form-control" name="inspectionPoDate" id="inspectionPoDate" readonly>`);
    //         $('#inspectionVendor').replaceWith(`<textarea class="form-control autosize d-block" spellcheck="false" maxlength="255" id="inspectionVendor" name="inspectionVendor" readonly></textarea>`);
    //         $('#inspectionDelivery').replaceWith(`<textarea class="form-control autosize d-block" spellcheck="false" maxlength="255" id="inspectionDelivery" name="inspectionDelivery" readonly></textarea>`);

    //         $('#checker').replaceWith(`<select class="select2 selectNonClear" name="checker" id="checker" data-type="CHECKER"><option></option></select>`);
    //         $('#confirmer').replaceWith(`<select class="select2 selectNonClear" name="confirmer" id="confirmer" data-type="CONFIRMER"><option></option></select>`);

    //         $('#tbodyInspectionItem').html(`<tr>
    //                                             <td colspan="5" class="no_result_table py-3 text-center">
    //                                                 <input type="hidden" name="itemId[]">
    //                                                 No Item data
    //                                             </td>
    //                                         </tr>`);

    //         Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> 'Error fetching data:', ${error}` });
    //     }
    //     finally {
    //         $('.selectNonClear').select2({
    //             allowClear: false,
    //             placeholder: '-- Select --',
    //             matcher: function(params, data) {
    //                 if ($.trim(params.term) === '') {
    //                 return data;
    //                 }

    //                 if (typeof data.text === 'undefined') {
    //                 return null;
    //                 }

    //                 const term = params.term.toLowerCase();
    //                 const text = data.text.toLowerCase();
    //                 const description = $(data.element).data('description1')?.toString().toLowerCase();

    //                 if (text.indexOf(term) > -1 || (description && description.indexOf(term) > -1)) {
    //                 return data;
    //                 }

    //                 return null;
    //             },
    //             templateResult: formatResult,
    //             templateSelection: function (data) {
    //                 return data.text || data.id;
    //             },
    //             escapeMarkup: function (markup) {
    //                 return markup;
    //             }
    //         });
    //     }
    // });

    async function poInspection() {
        if (isLoadingPo || !hasMoreDataPo) return;
        isLoadingPo = true;
        let skeletonCard = `<div class="text-center skeletonCardOrder">
                                <div class="stripes-red-blue stripes-red-blue-md"></div>
                                <div class="d-block fs-7">Loading...</div>
                            </div>`;

        if(currentPagePoInspection == 1){
            document.querySelector('#poInspectionContainer').innerHTML = `<div class="full-height-column-wrapper">
                                                                    <div class="card-body d-flex justify-content-center align-items-center">
                                                                        <div class="center-container">
                                                                            <div class="stripes-red-blue stripes-red-blue-md"></div>
                                                                            <div class="d-block fs-7 mt-2">Loading...</div>
                                                                        </div>
                                                                    </div>
                                                                </div>`;
        }
        else{
            document.querySelector('#poInspectionContainer').insertAdjacentHTML('beforeend', skeletonCard);
        }

        try {
            let formData;
            formData = new FormData($('#filterPoInspection')[0]);
            formData.append('company', $('#companyPoInspection').val());
            formData.append('page', currentPagePoInspection);
            const queryString = new URLSearchParams(formData).toString();
            const response = await fetch(`/proc_pur/poInspection?${queryString}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                    'Referer': window.location.href
                }
            });

            const data = await response.json();
            if (data.status === 200) {
                if(currentPagePoInspection == 1) {
                    document.querySelector(`#poInspectionContainer`).innerHTML = '';
                    document.querySelector(`#badgeCountPOInspection`).innerHTML = data.countPo;
                    if (data.data.length > 0) {
                        document.querySelector(`#badgeCountPOInspection`).classList.remove('d-none');
                    }
                    else {
                        document.querySelector(`#badgeCountPOInspection`).classList.add('d-none');
                    }
                }
                else{
                    $('.skeletonCardOrder').remove();
                }

                if (data.data.length > 0) {
                    data.data.forEach((item, index) => {
                        setTimeout(() => {
                            const newElement = document.createElement('div');
                            newElement.classList.add('fade-in-up');
                            newElement.innerHTML = item.html;
                            document.querySelector(`#poInspectionContainer`).appendChild(newElement);
                        }, index * 100);

                        if (index === data.data.length - 1) {
                            requestAnimationFrame(() => {
                                if (currentPagePoInspection === 2) {
                                    poInspectionScrollbarInstance = new ScrollbarCustom('#poInspectionContainer', {
                                        top: null,
                                        right: 0,
                                        overflowX: 'none',
                                        overflowY: 'scroll'
                                    });
                                    poInspectionScrollbarInstance.forceUpdate();
                                } else {
                                    // untuk page 2, 3, dst
                                    if (poInspectionScrollbarInstance) {
                                        poInspectionScrollbarInstance.refresh();
                                    }
                                }
                            });
                        }
                    });

                    currentPagePoInspection++;
                    hasMoreDataPo = data.hasMorePages;
                }
                else {
                    hasMoreDataPo = false; // No more data to load
                    if(currentPagePoInspection == 1){
                        document.querySelector(`#poInspectionContainer`).innerHTML = `<div class="card full-height-column-wrapper">
                                                                                <div class="card-body d-flex justify-content-center align-items-center">
                                                                                    <div class="center-container">
                                                                                        <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                                                        <div class="d-block fs-7 mt-2">No available PO</div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>`;
                    }
                }
            }
            else if (data.status === 404) {
                $('.skeletonCardOrder').remove();
                document.querySelector(`#badgeCountPOInspection`).classList.add('d-none');
                document.querySelector(`.openOrderContainer`).innerHTML = `<div class="card full-height-column-wrapper">
                                                                        <div class="card-body d-flex justify-content-center align-items-center">
                                                                            <div class="center-container">
                                                                                <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                                                <div class="d-block fs-7 mt-2">${data.message}</div>
                                                                            </div>
                                                                        </div>
                                                                    </div>`;
            }
            else {
                $('.skeletonCardOrder').remove();
                console.error('HTTP Error:', response.status);
            }
        } catch (error) {
            console.error('Error fetching data:', error);
        } finally {
            isLoadingPo = false;
        }
    }

    $(document).off('input', '.countRemaining').on('input', '.countRemaining', function (e) {
        let $this = $(this);
        let qty = $(this).closest('tr').find('input[name="qty[]"]').val();

        if($this.val().trim() != '') {
            let remaining = qty - $this.val();
            $(this).closest('tr').find('input[name="remainingQty[]"]').val(remaining);
        }
    });
</script>
@endonce