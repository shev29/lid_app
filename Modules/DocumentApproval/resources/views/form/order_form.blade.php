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
                    <label for="preparedBy" class="form-label">Prepared By<span class="required"></span> :</label>
                    <select class="select2 selectNonClear" name="preparedBy" id="preparedBy">
                        <option></option>
                        @php
                            if($dataForm['type'] == 'REVISE') {
                                echo '<option value="'.$dataForm['latestHeader']['employeeId'].'" selected="">'.$dataForm['latestHeader']['employeeName'].'</option>';
                            }
                            else {
                                foreach ($dataForm['preparedBy'] as $row) {
                                    echo '<option value="'.$row['id'].'" '.$row['selected'].'>'.$row['text'].'</option>';
                                }
                            }
                        @endphp
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="companyOrder" class="form-label">Company<span class="required"></span> :</label>
                    <select class="select2" name="companyOrder" id="companyOrder">
                        <option></option>
                        @php
                            if($dataForm['type'] == 'REVISE') {
                                echo '<option value="'.$dataForm['company']['id'].'" selected="">'.$dataForm['company']['text'].'</option>';
                            }
                            else {
                                foreach ($dataForm['company'] as $row) {
                                    $selected = ($row['selected'] == true) ? ' selected' : '';
                                    echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['text'].'</option>';
                                }
                            }
                        @endphp
                    </select>
                </div>
                <div class="form-group mb-3">
                    <input type="hidden" id="costCenterOrder" value="" data-text="">
                    {{-- <select class="select2 d-none" id="arrCostCenter">
                        <option></option>
                        @foreach ($dataForm['arrCostCenter'] as $rowCostCenter)
                            <option value="{{ $rowCostCenter['id'] }}" data-description="{{ $rowCostCenter['description'] }}">{{ $rowCostCenter['text'] }}</option>
                        @endforeach
                    </select> --}}
                    <select class="select2 d-none" id="arrUnit">
                        <option></option>
                        @foreach ($dataForm['arrUnit'] as $row)
                            <option value="{{ $row['id'] }}">{{ $row['text'] }}</option>
                        @endforeach
                    </select>

                    <label for="departmentOrder" class="form-label">Department<span class="required"></span> :</label>
                    @if ($dataForm['type'] === 'REVISE')
                        <select class="select2" name="departmentOrder" id="departmentOrder">
                            <option value="{{ $dataForm['department']['id'] }}" selected>{{ $dataForm['department']['text'] }}</option>
                        </select>
                    @else
                        <div class="skeleton" id="departmentOrder"></div>
                    @endif

                </div>

                <div class="form-group mb-3">
                    <label for="locationOrder" class="form-label">Location<span class="required"></span> :</label>
                    @if ($dataForm['type'] === 'REVISE')
                        <select class="select2" name="locationOrder" id="locationOrder">
                            <option value="{{ $dataForm['location']['id'] }}" selected>{{ $dataForm['location']['text'] }}</option>
                        </select>
                    @else
                        <div class="skeleton" id="locationOrder"></div>
                    @endif
                </div>
            </div>
            <div class="col-sm-4 mb-sm-0">
                <div class="form-group mb-3">
                    @if ($dataForm['type'] === 'REVISE')
                        <label for="seqNumber" class="form-label">Order Form No.<span class="required"></span> :</label>
                        <input type="text" class="form-control" name="seqNumber" value="{{ $dataForm['latestHeader']['documentNumber'] }}" readonly="">
                    @else
                        <label for="seqNumber" class="form-label">Order Form No.<span class="required"></span> :<span class="fst-italic fw-normal fs-8"> (Auto-generated after submitting)</span></label>
                        <div class="skeleton" id="documentNumber"></div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="seqNumber" class="form-label">Request Date<span class="required"></span> :</label>
                    @php
                        if($dataForm['type'] == 'REVISE') {
                            $requestDate = $dataForm['latestHeader']['requestDate'];
                        }
                        else {
                            $requestDate =  $dataForm['requestDate'];
                        }
                    @endphp
                    <div class="form-group date-picker" id="requestDate" data-coreui-name="requestDate" data-coreui-date="{{$requestDate}}"></div>
                </div>
                <div class="form-group mb-3">
                    <label for="currencyOrder" class="form-label">Currency<span class="required"></span> :</label>
                    <select class="select2 selectNonClear" name="currencyOrder" id="currencyOrder">
                        <option></option>
                        @foreach ($dataForm['currency'] as $row)
                            <option value="{{ $row['id'] }}" {{ $row['description1'] }} {{ $row['selected'] }}>{{ $row['text'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="priorityLevelOrder" class="form-label">Priority Level<span class="required"></span> :</label>
                    <select class="select2 selectNonClear" name="priorityLevelOrder" id="priorityLevelOrder">
                        <option></option>
                        @foreach ($dataForm['priorityLevel'] as $row)
                            <option value="{{ $row['id'] }}" {{ $row['description1'] }} {{ $row['selected'] }}>{{ $row['text'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-4 mb-sm-0">
                @php
                    $latestChecker1 = $latestChecker2 = $latestApprover = $latestCc = '';
                    if($dataForm['type'] == 'REVISE') {
                        $currency = $dataForm['latestHeader']['currency'];
                        $latestChecker1 = $dataForm['latestHeader']['latestChecker1'];
                        $latestChecker2 = $dataForm['latestHeader']['latestChecker2'];
                        $latestApprover = $dataForm['latestHeader']['latestApprover'];
                        $latestCc = $dataForm['latestHeader']['latestCc'];
                    }
                @endphp

                <div class="form-group mb-3">
                    <label for="checkerSelect" class="form-label">First Checked By<span class="required"></span> :</label>
                    <div class="skeleton" id="checkerSelect_1" data-select="{{$latestChecker1}}"></div>
                </div>
                <div class="form-group mb-3">
                    <label for="checkerSelect2" class="form-label">Second Checked By<span id="secondCheckerInfo" class="fst-italic fw-normal fs-8"></span> :</label>
                    <div class="skeleton" id="checkerSelect_2" data-select="{{$latestChecker2}}"></div>
                </div>
                <div class="form-group mb-3">
                    <label for="approverSelect" class="form-label">Approved By<span class="required"></span> :</label>
                    <div class="skeleton" id="approverSelect" data-select="{{$latestApprover}}"></div>
                </div>
                <div class="form-group mb-3">
                    <label for="approverSelect" class="form-label">Cc <span class="fst-italic fw-normal fs-8">(optional)</span> :</label>
                    <div class="skeleton" id="ccSelect" data-select="{{$latestCc}}"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="pt-2" id="itemContainer">
        <div class="col-12 mb-2">
            <div class="d-flex justify-content-end align-items-center gap-2">
                <button type="button" class="btn btn-secondary btn-sm fs-8 fw-medium itemBtn" data-type="ADD" data-currency=""><i class="fa-regular fa-plus fa-fw"></i> Add Item</button>
                <button type="button" class="btn btn-default btn-sm fs-8 fw-medium itemBtn" data-type="EDIT" data-currency=""><i class="fa-solid fa-pen-to-square fa-fw"></i> Edit Item</button>
                <button type="button" class="btn btn-default btn-sm fs-8 fw-medium itemBtn" data-type="REMOVE" data-currency=""><i class="fa-solid fa-trash-can-list fa-fw"></i> Remove Item</button>
            </div>
        </div>
        <div class="table-responsive mb-2">
            <table class="table table-form" id="tableItemOrder">
                <thead>
                    <tr>
                        <th style="width: 3%">No.</th>
                        <th style="width: 7%">Code</th>
                        <th style="width: 19%">Appliance / Item</th>
                        <th style="width: 19%">Brand / Type</th>
                        <th style="width: 8%">Qty</th>
                        <th style="width: 10%">Unit</th>
                        <th style="width: 12%">Unit Price (Est)</th>
                        <th style="width: 13%">Total (Est)</th>
                        <th style="width: 10%">Required Date</th>
                    </tr>
                </thead>
                <tbody id="tbodyItemOrder">
                    @php
                        if($dataForm['type'] != 'NEW') {
                            if(count($dataForm['latestHeader']['latestItem']) > 0) {
                                $x = 1;
                                foreach ($dataForm['latestHeader']['latestItem'] as $rowItem) {
                                    echo '<tr class="itemBody" id="itemBody'.$x.'" data-item="'.$x.'">
                                            <td class="text-center">
                                                <span class="itemNo">'.$x.'</span>
                                            </td>
                                            <td class="text-center">
                                                <input type="hidden" id="codeOrder'.$x.'" name="codeOrder[]" value="'.$rowItem['costCenter'].'">
                                                <span class="">'.$rowItem['costCenter'].'</span>
                                            </td>
                                            <td>
                                                <input type="hidden" class="allowRevision" id="allowRevision'.$x.'" name="allowRevision[]" value="'.$rowItem['allowRevision'].'">
                                                <input type="hidden" name="itemId[]" value="'.$rowItem['itemId'].'">
                                                <input type="hidden" id="itemOrder'.$x.'" name="itemOrder[]" value="'.$rowItem['applianceItem'].'">
                                                <span class="itemOrderBody">'.$rowItem['applianceItem'].'</span>
                                            </td>
                                            <td>
                                                <input type="hidden" id="typeOrder'.$x.'" name="typeOrder[]" value="'.$rowItem['brandType'].'">
                                                <span class="">'.$rowItem['brandType'].'</span>
                                            </td>
                                            <td class="text-center">
                                                <input type="hidden" id="qtyOrder'.$x.'" name="qtyOrder[]" value="'.$rowItem['unitQuantity'].'">
                                                <span class="">'.$rowItem['unitQuantity'].'</span>
                                            </td>
                                            <td class="text-center">
                                                <input type="hidden" id="unitOrder'.$x.'" name="unitOrder[]" value="'.$rowItem['unitId'].'">
                                                <span class="">'.$rowItem['unitName'].'</span>
                                            </td>
                                            <td class="text-end">
                                                <input type="hidden" id="unitPriceOrder'.$x.'" name="unitPriceOrder[]" value="'.$rowItem['unitPriceEst'].'">
                                                <span class="">'.$rowItem['unitPriceEst'].'</span><span class="currencyText ps-2">'.$currency.'</span>
                                            </td>
                                            <td class="text-end">
                                                <input type="hidden" id="subTotalPriceOrder'.$x.'" class="subTotalPriceOrder" name="subTotalPriceOrder[]" value="'.$rowItem['totalPriceEst'].'">
                                                <span class="">'.$rowItem['totalPriceEst'].'</span><span class="currencyText ps-2">'.$currency.'</span>
                                            </td>
                                            <td class="text-center">
                                                <input type="hidden" id="requiredDateOrder'.$x.'" name="requiredDateOrder[]" value="'.$rowItem['requiredDate'].'">
                                                <span class="">'.$rowItem['requiredDate'].'</span>
                                            </td>
                                        </tr>';
                                    $x++;
                                }
                            }
                            else {
                                echo '<tr>
                                    <td colspan="9" class="no_result_table py-3 text-center">
                                        <input type="hidden" name="codeOrder[]" value="">
                                        <input type="hidden" name="itemOrder[]" value="">
                                        <input type="hidden" name="typeOrder[]" value="">
                                        <input type="hidden" name="qtyOrder[]" value="">
                                        <input type="hidden" name="unitOrder[]" value="">
                                        <input type="hidden" name="unitPriceOrder[]" value="">
                                        <input type="hidden" name="subTotalPriceOrder[]" value="">
                                        <input type="hidden" name="requiredDateOrder[]" value="">
                                        No Item data
                                    </td>
                                </tr>';
                            }
                        }
                        else{
                            echo '<tr>
                                    <td colspan="9" class="no_result_table py-3 text-center">
                                        <input type="hidden" name="codeOrder[]" value="">
                                        <input type="hidden" name="itemOrder[]" value="">
                                        <input type="hidden" name="typeOrder[]" value="">
                                        <input type="hidden" name="qtyOrder[]" value="">
                                        <input type="hidden" name="unitOrder[]" value="">
                                        <input type="hidden" name="unitPriceOrder[]" value="">
                                        <input type="hidden" name="subTotalPriceOrder[]" value="">
                                        <input type="hidden" name="requiredDateOrder[]" value="">
                                        No Item data
                                    </td>
                                </tr>';
                        }
                    @endphp
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" class="text-end align-middle">Grand Total (Est)</td>
                        <td class="text-end">
                            @php
                                $grandTotal = '0.00';
                                if($dataForm['type'] != 'NEW') {
                                    $grandTotal = $dataForm['latestHeader']['grandTotal'];
                                }
                            @endphp
                            <span class="" id="grandTotal">{{$grandTotal}}</span><span class="currencyText ps-2">{{$currency}}</span>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
    </div>
    <div id="itemContainerFooter">
        <div class="row row-multi-col mb-3 pt-2 pb-1 bg-white">
            <div class="row-multi-col-header">
                <span style="display: inline">Purpose</span>
            </div>
            <div class="col-sm-4 mb-sm-0">
                <div class="form-group mb-3">
                    <label for="preparedBy" class="form-label">Description<span class="required"></span> :</label>
                    <select class="select2 purposeId" name="purposeId[]" id="purposeId">
                        <option></option>
                        @php
                            foreach ($dataForm['purpose'] as $row) {
                                echo '<option value="'.$row['id'].'" '.$row['selected'].'>'.$row['text'].'</option>';
                            }
                        @endphp
                    </select>
                </div>
            </div>
            <div class="col-sm-4 mb-sm-0">
                <div class="form-group mb-3">
                    <label for="reasonOrder" class="form-label">Reason<span class="required"></span> :</label>
                    @php
                        if($dataForm['type'] != 'NEW') {
                            echo '<div class="skeleton textarea reasonOrder" data-selectid="'.$dataForm['latestHeader']['reasonId'].'" data-select="'.$dataForm['latestHeader']['reason'].'"></div>';
                        }
                        else {
                            echo '<textarea class="form-control autosize singleLine reasonOrder" spellcheck="false" maxlength="200" id="reasonOrder" name="reasonOrder[]"></textarea>';
                        }
                    @endphp
                </div>
            </div>
            <div class="col-sm-4 mb-sm-0">
                <div class="form-group mb-3">
                    <label for="remarksOrder" class="form-label">Remarks :</label>
                    @php
                        if($dataForm['type'] != 'NEW') {
                            echo '<div class="skeleton textarea remarksOrder" data-select="'.$dataForm['latestHeader']['remarks'].'"></div>';
                        }
                        else {
                            echo '<textarea class="form-control autosize singleLine remarksOrder" spellcheck="false" maxlength="200" id="remarksOrder" name="remarksOrder[]"></textarea>';
                        }
                    @endphp
                </div>
            </div>
        </div>
    </div>
    <div id="formContainerFooter">
        <div class="row row-multi-col mb-3 bg-white" id="" data-item="">
            <div class="row-multi-col-header">
                <span style="display: inline">Supporting Documents</span>
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
                        if($dataForm['type'] != 'NEW') {
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
                        }
                    @endphp
                </div>
            </div>
        </div>
    </div>
</form>

@once
<script>
    selectedFiles = [];
    selectedFilesProperties = [];
    $('.autosize').autosize().trigger('change');
    dayjs.locale('id');
    dayjs.extend(window.dayjs_plugin_customParseFormat);
    var optionsDatePicker = {
        locale: 'en-US',
        inputDateFormat: date => dayjs(date).locale('en').format('DD-MMM-YYYY'),
        inputDateParse: date => dayjs(date, 'DD-MMM-YYYY', 'id').toDate(),
        // minDate: dayjs(new Date()),
        showAdjacementDays: false,
    }

    var optionsRequestDate = {
        locale: 'en-US',
        inputDateFormat: date => dayjs(date).locale('en').format('DD-MMM-YYYY'),
        inputDateParse: date => dayjs(date, 'DD-MMM-YYYY', 'id').toDate(),
        maxDate: dayjs(new Date()),
        showAdjacementDays: false,
    }

    new coreui.DatePicker(document.getElementById(`requestDate`), optionsRequestDate);

    if($('#requestTypeForm').val() != 'NEW') {
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
        templateResult: formatResultRemote,
        templateSelection: function (data) {
            return data.text || data.id;
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    }).trigger('change');

    $('#companyOrder').select2({allowClear: false, placeholder: '-- Select --'});
    setTimeout(function () {
        $('#companyOrder').trigger('change');
    }, 0);

    $('#purposeId').select2({allowClear: false, placeholder: '-- Select --'});
    setTimeout(function () {
        $('#purposeId').trigger('change');
    }, 0);


    $(document).off('input', '.countAmountAside').on('input', '.countAmountAside', function (e) {
        let unitPrice = $('#unitPriceOrderAside').val() ?? '0';
        unitPrice = currencyToNumber(unitPrice);
        if (isNaN(unitPrice)) {
            unitPrice = 0;
        }

        let qty = $('#qtyOrderAside').val() ?? '0';
        qty =  parseFloat(qty);
        if (isNaN(qty)) {
            qty = 0;
        }

        let subTotalPriceOrder = qty * unitPrice;
        let formattedSubTotal = formatCurrency(subTotalPriceOrder, 2);
        $('#subTotalPriceOrderAside').val(formattedSubTotal);
    });

    $(document).off('change', '#currencyOrder').on('change', '#currencyOrder', function (e) {
        $('.currencyText').html($(this).val());
        $('.itemBtn').attr('data-currency', $(this).val());
    });

    $(document).off('change', '#companyOrder').on('change', '#companyOrder', async function (event) {
        if($('#requestTypeForm').val() != 'REVISE') {
            if ($('#departmentOrder').hasClass('select2-hidden-accessible')) {
                $('#departmentOrder').val(null).empty().trigger('change');
                $('#departmentOrder').select2('destroy');
            }

            if(!$(this).val()) {
                $('#documentNumber').replaceWith(`<input type="text" class="form-control" id="documentNumber" name="seqNumber" value="" readonly="">`);
                $('#departmentOrder').replaceWith(`<select class="select2" name="departmentOrder" id="departmentOrder"><option></option></select>`);
                $('#departmentOrder').select2({allowClear: false, placeholder: '-- Select --'});
                setTimeout(function () {
                    $('#departmentOrder').trigger('change');
                }, 0);

                return;
            }

            $('#departmentOrder').replaceWith(`<div class="skeleton" id="departmentOrder"></div>`);
            $('#documentNumber').replaceWith(`<div class="skeleton" id="documentNumber"></div>`);

            let params = {
                'dataForm': 'ORDER_FORM',
                'dataType': 'APPLICANT_MATRIX',
                'dataMatrix': 'DEPARTMENT',
                'tokenForm': $(this).val(),
            };
            const optionDepartment = []
            const getDepartment = await getEmployee(params);
            if(getDepartment.data.length > 0) {
                let selected = getDepartment.data.length == 1 ?? false;
                getDepartment.data.forEach((item, index) => {
                    optionDepartment.push({
                        id: item.departmentId,
                        text: item.departmentName,
                        description1: `Cost center : ${(item.costCenter) ? item.costCenter : '--'}`,
                        selected: selected
                    });
                });
            }

            $('#departmentOrder').replaceWith(`<select class="select2" name="departmentOrder" id="departmentOrder"><option></option></select>`);
            $('#departmentOrder').select2({
                dropdownParent: $('#modalDocumentForm'),
                minimumResultsForSearch: Infinity,
                allowClear: false,
                placeholder: '-- Select --',
                data: optionDepartment,
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
                templateResult: formatResultRemote,
                templateSelection: function (data) {
                    return data.text || data.id;
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });

            params = {
                'documentType': $('#documentTypeForm').val(),
                'companyId': $('#companyOrder').val(),
                'type': 'DRAFT_SEQ',
            };
            const documentNumber = await getDocumentNumber(params);
            const seqNumber = documentNumber['seqNumber'];
            const formatNumber = documentNumber['formatNumber'];
            $('#documentNumber').replaceWith(`<input type="text" class="form-control" id="documentNumber" name="seqNumber" value="${documentNumber['documentNumber']}" readonly="">`);

            setTimeout(function () {
                $('#departmentOrder').trigger('change');
            }, 0);
        }
        else {
            $('#departmentOrder').select2({
                dropdownParent: $('#modalDocumentForm'),
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
                templateResult: formatResultRemote,
                templateSelection: function (data) {
                    return data.text || data.id;
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            }).trigger('change');
        }
    });

    $(document).off('change', '#departmentOrder').on('change', '#departmentOrder', async function (event) {
        let $this = $(this);
        if($('#requestTypeForm').val() != 'REVISE') {
            if ($('#locationOrder').hasClass('select2-hidden-accessible')) {
                $('#locationOrder').select2('destroy');
                $('#locationOrder').replaceWith(`<div class="skeleton" id="locationOrder"></div>`);
            }
        }

        if ($('#checkerSelect_1').hasClass('select2-hidden-accessible')) {
            $('#checkerSelect_1').select2('destroy');
        }

        if ($('#checkerSelect_2').hasClass('select2-hidden-accessible')) {
            $('#checkerSelect_2').select2('destroy');
        }

        if ($('#approverSelect').hasClass('select2-hidden-accessible')) {
            $('#approverSelect').select2('destroy');
        }

        if ($('#ccSelect').hasClass('select2-hidden-accessible')) {
            $('#ccSelect').select2('destroy');
        }

        $('#checkerSelect_1').replaceWith(`<div class="skeleton" id="checkerSelect_1" data-select=""></div>`);
        $('#checkerSelect_2').replaceWith(`<div class="skeleton" id="checkerSelect_2" data-select=""></div>`);
        $('#approverSelect').replaceWith(`<div class="skeleton" id="approverSelect" data-select=""></div>`);
        $('#ccSelect').replaceWith(`<div class="skeleton" id="ccSelect" data-select=""></div>`);

        if(!$(this).val()) {
            if($('#requestTypeForm').val() != 'REVISE') {
                $('#locationOrder').replaceWith(`<select class="select2 selectChange" name="locationOrder" id="locationOrder"></select>`);
            }

            $('#checkerSelect_1').replaceWith(`<select class="select2 selectChange" name="checker_1" id="checkerSelect_1" data-type="CHECKER_1"><option></option></select>`);
            $('#checkerSelect_2').replaceWith(`<select class="select2 selectChange" name="checker_2" id="checkerSelect_2" data-type="CHECKER_2"><option></option></select>`);
            $('#approverSelect').replaceWith(`<select class="select2 selectChange" name="approver" id="approverSelect" data-type="APPROVER"><option></option></select>`);
            $('#ccSelect').replaceWith(`<select class="select2 selectChange" name="cc" id="ccSelect" data-type="CC"><option></option></select>`);

            $('.selectChange').select2({
                dropdownParent: $('#modalDocumentForm'),
                allowClear: true,
                placeholder: '-- Select --',
            });

            return;
        }

        if($('#requestTypeForm').val() != 'REVISE') {
            let optionLocation = [];
            let getLocationDept = await getLocation({'token': $(this).val()});
            if (getLocationDept.data.length > 0) {
                let selected = getLocationDept.data.length == 1 ?? false;
                getLocationDept.data.forEach((item) => {
                    optionLocation.push({
                            id: item.locationId,
                            text: item.locationName,
                            selected : selected,
                    });
                });
            }
            $('#locationOrder').replaceWith(`<select class="select2" name="locationOrder" id="locationOrder"><option></option></select>`);
            $('#locationOrder').select2({
                dropdownParent: $('#modalDocumentForm'),
                minimumResultsForSearch: Infinity,
                allowClear: false,
                placeholder: '-- Select --',
                data: optionLocation,
            }).trigger('change');
        }
        else {
            $('#locationOrder').select2({
                dropdownParent: $('#modalDocumentForm'),
                minimumResultsForSearch: Infinity,
                allowClear: false,
                placeholder: '-- Select --',
            }).trigger('change');
        }

        const params = {
            'dataForm': 'ORDER_FORM',
            'dataType': 'APPLICANT_MATRIX',
            'dataMatrix': 'APPROVAL_FLOW',
            'tokenForm': $(this).val(),
        };

        $('#secondCheckerInfo').removeClass('required');
        $('#secondCheckerInfo').html(' (optional)');
        let optionChecker1 = [], optionChecker2 = [], optionApprover = [], optionCc = [];
        const approvalArr = {'CHECKER_1': optionChecker1, 'CHECKER_2': optionChecker2, 'APPROVER': optionApprover, 'CC': optionCc};
        let getMatrix = await getEmployee(params);
        if (getMatrix.data.length > 0) {
            getMatrix.data.forEach((item) => {
                if (approvalArr.hasOwnProperty(item.flow)) {
                    approvalArr[item.flow].push({
                        id: item.employeeId,
                        text: item.employeeName,
                        description1: item.positionName,
                    });
                }
            });

            if(optionChecker2.length > 0) {
                $('#secondCheckerInfo').html('');
                $('#secondCheckerInfo').addClass('required');
            }
        }

        let preselectChecker = $('#checkerSelect_1').attr('data-select');
        $('#checkerSelect_1').replaceWith(`<select class="select2" name="checker_1" id="checkerSelect_1" data-type="CHECKER_1"><option></option></select>`);
        $('#checkerSelect_1').select2({
            dropdownParent: $('#modalDocumentForm'),
            allowClear: true,
            placeholder: '-- Select --',
            data: optionChecker1,
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
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });
        if(preselectChecker != '') {
            $('#checkerSelect_1').val(preselectChecker).trigger('change');
        }

        let preselectChecker2 = $('#checkerSelect_2').attr('data-select');
        $('#checkerSelect_2').replaceWith(`<select class="select2" name="checker_2" id="checkerSelect_2" data-type="CHECKER_2"><option></option></select>`);
        $('#checkerSelect_2').select2({
            dropdownParent: $('#modalDocumentForm'),
            allowClear: true,
            placeholder: '-- Select --',
            data: optionChecker2,
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
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });
        if(preselectChecker2 != '') {
            $('#checkerSelect_2').val(preselectChecker2).trigger('change');
        }

        let preselectApprover = $('#approverSelect').attr('data-select');
        $('#approverSelect').replaceWith(`<select class="select2" name="approver" id="approverSelect" data-type="APPROVER"><option></option></select>`);
        $('#approverSelect').select2({
            dropdownParent: $('#modalDocumentForm'),
            allowClear: true,
            placeholder: '-- Select --',
            data: optionApprover,
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
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });
        if(preselectApprover != '') {
            $('#approverSelect').val(preselectApprover).trigger('change');
        }

        let preselectCc = $('#ccSelect').attr('data-select');
        $('#ccSelect').replaceWith(`<select class="select2" name="cc" id="ccSelect" data-type="CC"><option></option></select>`);
        $('#ccSelect').select2({
            dropdownParent: $('#modalDocumentForm'),
            allowClear: true,
            placeholder: '-- Select --',
            data: optionCc,
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
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });
        if(preselectCc != '') {
            $('#ccSelect').val(preselectCc).trigger('change');
        }
    });

    $(document).off('change', '#purposeId').on('change', '#purposeId', async function (event) {
        const $this = $(this);
        const parentContainer = $this.parent().parent().parent();
        if($this.val() != ''){
            let preselectReasonId = '', preselectReason = '', preselectRemarks = '';
            if($('#requestTypeForm').val() == 'NEW') {
                parentContainer.find('.reasonOrder').next('span.select2').remove();
                parentContainer.find('.reasonOrder').replaceWith(`<div class="skeleton textarea reasonOrder"></div>`);
                parentContainer.find('.remarksOrder').replaceWith(`<div class="skeleton textarea remarksOrder"></div>`);
            }
            else {
                preselectReasonId = parentContainer.find('.reasonOrder').attr('data-selectid');
                preselectReason = parentContainer.find('.reasonOrder').attr('data-select');
                preselectRemarks = parentContainer.find('.remarksOrder').attr('data-select');

                parentContainer.find('.reasonOrder').next('span.select2').remove();
                parentContainer.find('.reasonOrder').replaceWith(`<div class="skeleton textarea reasonOrder" data-selectid="${preselectReasonId}" data-select="${preselectReason}"></div>`);
                parentContainer.find('.remarksOrder').replaceWith(`<div class="skeleton textarea remarksOrder" data-select="${preselectRemarks}"></div>`);
            }

            try {
                const response = await fetch(`/doc_approval/getReason?documentType=${$('#requestTypeForm').val()}&purposeId=${$this.val()}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'Referer': window.location.href
                    }
                });

                const data = await response.json();
                if (response.status === 200) {
                    if(data.length > 0){
                        const options = data.map(row => ({
                            id: row.reason_id,
                            text: row.reason_name
                        }));

                        parentContainer.find('.reasonOrder').replaceWith(`<select class="select2 reasonOrder" name="reasonOrder[]" id="reasonOrder" data-type=""></select>`);
                        $('#reasonOrder').select2({
                            minimumResultsForSearch: Infinity,
                            allowClear: false,
                            placeholder: '-- Select --',
                            data: options
                        });

                        if($('#requestTypeForm') != 'NEW') {
                            $('#reasonOrder').val(preselectReasonId).trigger('change');
                        }
                    }
                    else{
                        parentContainer.find('.reasonOrder').replaceWith(`<textarea class="form-control autosize singleLine reasonOrder" spellcheck="false" maxlength="255" id="reasonOrder" name="reasonOrder[]"></textarea>`);

                        if($('#requestTypeForm') != 'NEW') {
                            $('#reasonOrder').val(preselectReason).trigger('change');
                        }
                    }
                }
                else if (response.status === 401) {
                    let countdown = 5;
                    Snackbar.show({
                        pos: 'bottom-center',
                        duration: '6000',
                        text: `<i class="fa-solid fa-circle-exclamation fa-lg fa-fw text-warning"></i> Session expired, please reload your browser or it will automatically reload in <span id="snackbar-countdown">${countdown}</span> seconds.`
                    });

                    let countdownInterval = setInterval(() => {
                        countdown--;
                        document.getElementById('snackbar-countdown').textContent = countdown;
                        if (countdown < 0) {
                            clearInterval(countdownInterval);
                            window.location.href = data.redirect_uri;
                        }
                    }, 1000);
                }
                else {
                    console.error('HTTP Error:', response.status);
                }
            }
            catch (error) {
                return;
            }
            finally {
                parentContainer.find('.remarksOrder').replaceWith(`<textarea class="form-control autosize singleLine remarksOrder" spellcheck="false" maxlength="200" id="remarksOrder" name="remarksOrder[]"></textarea>`);

                if($('#requestTypeForm') != 'NEW') {
                    $('#remarksOrder').val(preselectRemarks).trigger('change');
                }

                $('.autosize').autosize({ append: "\n" }).trigger('change');
            }
        }
        else{
            return;
        }
    });

    $(document).off('click', '.itemBtn').on('click', '.itemBtn', async function (e) {
        let type = $(this).attr('data-type');
        let isValid = true;
        let btnText;
        if(type == 'ADD' || type == 'EDIT') {
            btnText = type == 'ADD' ? 'Add' : 'Edit';
            if ($('#currencyOrder').val().trim() === '') {
                $('#currencyOrder').addClass('is-invalid');
                $('#currencyOrder').closest('.form-group').find('span.selection').find('span.select2-selection').addClass('is-invalid');
                isValid = false;
            }

            if ($('#departmentOrder').val().trim() === '') {
                $('#departmentOrder').addClass('is-invalid');
                $('#departmentOrder').closest('.form-group').find('span.selection').find('span.select2-selection').addClass('is-invalid');
                isValid = false;
            }
        }
        else {
            btnText = 'Delete';
        }

        if(isValid === true) {
            let ccy = $('#currencyOrder').val();
            let costCenterId = $('#costCenterOrder').val();
            let costCenterText = $('#costCenterOrder').attr('data-text');
            Snackbar.close();
            asideHide();
            if(type == 'ADD' || type == 'EDIT') {
                let selectItem = '';
                if(type == 'ADD') {
                    $('.aside-title').html('Add Order Item');
                }
                else {
                    $('.aside-title').html('Edit Item Order');
                    let itemOrderOption = `<option></option>`;
                    let x = 1;
                    $('.itemOrderBody').each(function() {
                        let itemBody = $(this).closest('.itemBody').attr('data-item');
                        let allowRevision = $(this).closest('td').find('input.allowRevision').val();
                        let disabledItem = allowRevision == 'DISALLOWED' ? ' disabled=""' : '';
                        let disabledItemText = allowRevision == 'DISALLOWED' ? ' (Not Allowed to Edit)' : '';
                        itemOrderOption += `<option value="${itemBody}"${disabledItem}>${x} - ${$(this).html()}${disabledItemText}</option>`;
                        x++;
                    });

                    selectItem = `<div class="form-group mb-3">
                                    <label for="itemRowNumber" class="form-label" data-type="${type}">Item to Edit<span class="required"></span> :</label>
                                    <select class="select2" name="itemRowNumber" id="itemRowNumber" data-type=${type}>
                                        ${itemOrderOption}
                                    </select>
                                </div>`;
                }

                $('.aside-content').html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
                                            ${selectItem}
                                            <div class="form-group mb-3">
                                                <label for="codeOrderAside" class="form-label">Code<span class="required"></span> :</label>
                                                <div class="skeleton" id="codeOrderAside"></div>
                                                </select>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="itemOrderAside" class="form-label">Appliance / Item<span class="required"></span> :</label>
                                                <div class="suggestions-group">
                                                    <input type="hidden" class="id" id="itemOrderAsideId" name="itemOrderAsideId">
                                                    <input type="text" class="form-control suggestions" data-type="ITEM" spellcheck="false" id="itemOrderAside" name="itemOrderAside">
                                                    <div class="suggestions-container" style="display: none;"></div>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="typeOrderAside" class="form-label">Brand / Type<span class="required"></span> :</label>
                                                <div class="suggestions-group">
                                                    <input type="hidden" class="id" id="typeOrderAsideId" name="typeOrderAsideId">
                                                    <input type="text" class="form-control suggestions" data-type="TYPE" spellcheck="false" id="typeOrderAside" name="typeOrderAside">
                                                    <div class="suggestions-container" style="display: none;"></div>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="qtyOrderAside" class="form-label">Quantity<span class="required"></span> :</label>
                                                <input type="text" class="form-control numberValue countAmountAside" id="qtyOrderAside" name="qtyOrderAside" maxlength="10" spellcheck="false" autocomplete="off">
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="unitOrderAside" class="form-label">Unit<span class="required"></span> :</label>
                                                <select class="select2" name="unitOrderAside" id="unitOrderAside" data-type=""><option></option></select>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="unitPriceOrderAside" class="form-label">Unit Price Estimate<span class="required"></span> :</label>
                                                <div class="form-group d-flex align-items-center position-relative">
                                                    <input type="text" class="form-control d-flex currencyValue countAmountAside" id="unitPriceOrderAside" name="unitPriceOrderAside" data-item="" spellcheck="false" autocomplete="off" style="padding-right: 50px;">
                                                    <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-3">${ccy}</span>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="subTotalPriceOrderAside" class="form-label">Total Price Estimate<span class="required"></span> :</label>
                                                <div class="form-group d-flex align-items-center position-relative">
                                                    <input type="text" class="form-control d-flex currencyValue" id="subTotalPriceOrderAside" name="subTotalPriceOrderAside" data-item="" spellcheck="false" autocomplete="off" style="padding-right: 50px;" placeholder="0" disabled="">
                                                    <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-3">${ccy}</span>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="requiredDateOrderAside" class="form-label">Required Date<span class="required"></span> :</label>
                                                <div class="form-group date-picker" id="requiredDateOrderAside" data-coreui-name="requiredDateOrderAside" data-coreui-date=""></div>
                                            </div>
                                        </form>`);
            }
            else if(type == 'REMOVE') {
                let itemOrderOption = `<option></option>`;
                let x = 1;
                $('.itemOrderBody').each(function() {
                    let itemBody = $(this).closest('.itemBody').attr('data-item');
                    let allowRevision = $(this).closest('td').find('input.allowRevision').val();
                    let disabledItem = allowRevision == 'DISALLOWED' ? ' disabled=""' : '';
                    let disabledItemText = allowRevision == 'DISALLOWED' ? ' (Not Allowed to Remove)' : '';
                    itemOrderOption += `<option value="${itemBody}"${disabledItem}>${x} - ${$(this).html()}${disabledItemText}</option>`;
                    x++;
                });

                $('.aside-title').html('Remove Item Order');
                $('.aside-content').html(`<form role="form" class="form-horizontal pt-3 mb-5" enctype="multipart/form-data" id="asideForm">
                                            <div class="form-group mb-3">
                                                <label for="itemRowNumber" class="form-label">Item to Remove<span class="required"></span> :</label>
                                                <select class="select2" name="itemRowNumber" id="itemRowNumber" data-type="${type}">
                                                    ${itemOrderOption}
                                                </select>
                                            </div>
                                        </from>`);
            }

            $('.overlay-aside').addClass('show').trigger('shown');
            $('#globalAside').addClass('show aside-lg').trigger('shown');
            $('#asideDetailForm').scrollTop(0);
            $('body').addClass('overflow-hidden');

            if(type == 'REMOVE' || type == 'EDIT') {
                $('#itemRowNumber').select2({dropdownParent: $('#globalAside'), allowClear: false, placeholder: '-- Select --'});
            }

            if(type == 'ADD' || type == 'EDIT') {
                if (document.getElementById('requiredDateOrderAside')) {
                    new coreui.DatePicker(document.getElementById(`requiredDateOrderAside`), optionsDatePicker);
                }

                $('#unitOrderAside').select2({dropdownParent: $('#globalAside'), allowClear: false, placeholder: '-- Select --'});
                $('.autosize').autosize({ append: "\n" });
                $('#unitOrderAside').html($('#arrUnit').html());

                const optionCostCenter = []
                const getCostCenters = await getCostCenter({'token': $('#departmentOrder').val()});
                if(getCostCenters.data.length > 0) {
                    let selected = (getCostCenters.data.length == 1) ?? false;
                    getCostCenters.data.forEach((item, index) => {
                        optionCostCenter.push({
                            id: item.id,
                            text: item.text,
                            description1: item.description1,
                            selected: selected
                        });
                    });
                }

                $('#codeOrderAside').replaceWith(`<select class="select2" name="codeOrderAside" id="codeOrderAside"><option></option></select>`);
                $('#codeOrderAside').select2({
                    dropdownParent: $('#globalAside'),
                    allowClear: false,
                    placeholder: '-- Select --',
                    data: optionCostCenter,
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
                    templateResult: formatResultRemote,
                    templateSelection: function (data) {
                        return data.text || data.id;
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    }
                });
            }

            $('.asideFooterBtn').html(`<div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
                                        </div>
                                        <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-secondary w-100 w-md-auto saveItemOrder" data-type="${type}" data-item=""></i> ${btnText} Item</button>
                                        </div>`);
        }
        else {
            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Please select Department or Currency first` });
        }
    });

    $(document).off('change', '#itemRowNumber').on('change', '#itemRowNumber', function (event) {
        if($(this).attr('data-type') == 'EDIT') {
            let itemRow = $(this).val();
            $('.saveItemOrder').attr('data-item', itemRow);
            $('#codeOrderAside').val($(`#codeOrder${itemRow}`).val()).trigger('change');
            $('#itemOrderAside').val($(`#itemOrder${itemRow}`).val()).trigger('change');
            $('#typeOrderAside').val($(`#typeOrder${itemRow}`).val()).trigger('change');
            $('#qtyOrderAside').val($(`#qtyOrder${itemRow}`).val()).trigger('change');
            $('#unitOrderAside').val($(`#unitOrder${itemRow}`).val()).trigger('change');
            $('#unitPriceOrderAside').val($(`#unitPriceOrder${itemRow}`).val()).trigger('change');
            $('#subTotalPriceOrderAside').val($(`#subTotalPriceOrder${itemRow}`).val()).trigger('change');

            if($(`#requiredDateOrder${itemRow}`).val() != '') {
                const datepickerEl = document.getElementById('requiredDateOrderAside');
                const datePicker = coreui.DatePicker.getInstance(datepickerEl);
                if (datePicker) {
                    datePicker.dispose();
                    datepickerEl.innerHTML = '';
                    $('#requiredDateOrderAside').attr('data-coreui-date', $(`#requiredDateOrder${itemRow}`).val());
                }

                let requiredDateInstance =  new coreui.DatePicker(datepickerEl, optionsDatePicker);
                requiredDateInstance._startDate = new Date($(`#requiredDateOrder${itemRow}`).val());
            }
        }
    });

    $(document).off('click', '.saveItemOrder').on('click', '.saveItemOrder', function (event) {
        Snackbar.close();
        clearValidation();
        event.preventDefault();
        let $this = $(this);
        let type = $(this).attr('data-type');
        let isValid = true;
        let requiredDate;

        if(type == 'ADD' || type == 'EDIT') {
            let requiredDateInstance = coreui.DatePicker.getInstance(document.getElementById(`requiredDateOrderAside`));
            requiredDate = requiredDateInstance._startDate;

            if(type == 'EDIT') {
                if ($('#itemRowNumber').val() == '') {
                    $('#itemRowNumber').addClass('is-invalid');
                    $('#itemRowNumber').closest('.form-group').find('span.selection').find('span.select2-selection').addClass('is-invalid');
                    isValid = false;
                }
            }

            if ($('#codeOrderAside').val() == '') {
                $('#codeOrderAside').addClass('is-invalid');
                $('#codeOrderAside').closest('.form-group').find('span.selection').find('span.select2-selection').addClass('is-invalid');
                isValid = false;
            }

            if ($('#itemOrderAside').val().trim() === '') {
                $('#itemOrderAside').addClass('is-invalid');
                isValid = false;
            }

            if ($('#typeOrderAside').val().trim() === '') {
                $('#typeOrderAside').addClass('is-invalid');
                isValid = false;
            }

            if ($('#qtyOrderAside').val().trim() === '') {
                $('#qtyOrderAside').addClass('is-invalid');
                isValid = false;
            }

            if ($('#unitOrderAside').val() == '') {
                $('#unitOrderAside').addClass('is-invalid');
                $('#unitOrderAside').closest('.form-group').find('span.selection').find('span.select2-selection').addClass('is-invalid');
                isValid = false;
            }

            if ($('#unitPriceOrderAside').val().trim() === '') {
                $('#unitPriceOrderAside').addClass('is-invalid');
                isValid = false;
            }

            if (requiredDate == '' || requiredDate == null) {
                $('#requiredDateOrderAside').addClass('is-invalid');
                isValid = false;
            }
        }
        else if(type == 'REMOVE') {
            if ($('#itemRowNumber').val() == '') {
                $('#itemRowNumber').addClass('is-invalid');
                $('#itemRowNumber').closest('.form-group').find('span.selection').find('span.select2-selection').addClass('is-invalid');
                isValid = false;
            }
        }

        if (isValid) {
            if(type == 'ADD' || type == 'EDIT') {
                if(type == 'ADD') {
                    typeMessage = 'Added';
                    $('.no_result_table').remove();
                }
                else {
                    typeMessage = 'Updated';
                }

                let unitPrice = $('#unitPriceOrderAside').val();
                unitPrice = currencyToNumber(unitPrice);
                if (isNaN(unitPrice)) {
                    unitPrice = 0;
                }

                let qty = $('#qtyOrderAside').val();
                qty =  parseFloat(qty);
                if (isNaN(qty)) {
                    qty = 0;
                }

                let subTotalPriceOrder = qty * unitPrice;
                let formattedSubTotal = formatCurrency(subTotalPriceOrder, 2);

                let requiredDateOrderAside = requiredDate.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }).replace(/ /g, '-');

                let ccy = $('#currencyOrder').val();

                let x;
                if(type == 'ADD') {
                    if( $('.itemBody').length == 0) {
                        x = 1;
                    }
                    else {
                        x = parseInt($('.itemBody').last().data('item')) + 1;
                    }
                }
                else{
                    x = $this.attr('data-item');
                }

                unitPrice = (unitPrice == 0) ? '-' : formatCurrency(unitPrice, 2);
                formattedSubTotal = (formattedSubTotal == 0 || formattedSubTotal == 0.00) ? '-' : formattedSubTotal;

                let itemHtml = `<tr class="itemBody" id="itemBody${x}" data-item="${x}">
                                    <td class="text-center">
                                        <span class="itemNo"></span>
                                    </td>
                                    <td class="text-center">
                                        <input type="hidden" id="codeOrder${x}" name="codeOrder[]" value="${$('#codeOrderAside').val()}">
                                        <span class="">${$('#codeOrderAside').val()}</span>
                                    </td>
                                    <td>
                                        <input type="hidden" id="itemOrder${x}" name="itemOrder[]" value="${$('#itemOrderAside').val().trim()}">
                                        <span class="itemOrderBody">${$('#itemOrderAside').val().trim()}</span>
                                    </td>
                                    <td>
                                        <input type="hidden" id="typeOrder${x}" name="typeOrder[]" value="${$('#typeOrderAside').val().trim()}">
                                        <span class="">${$('#typeOrderAside').val().trim()}</span>
                                    </td>
                                    <td class="text-center">
                                        <input type="hidden" id="qtyOrder${x}" name="qtyOrder[]" value="${$('#qtyOrderAside').val().trim()}">
                                        <span class="">${$('#qtyOrderAside').val().trim()}</span>
                                    </td>
                                    <td class="text-center">
                                        <input type="hidden" id="unitOrder${x}" name="unitOrder[]" value="${$('#unitOrderAside').val()}">
                                        <span class="">${$('#unitOrderAside option:selected').text()}</span>
                                    </td>
                                    <td class="text-end">
                                        <input type="hidden" id="unitPriceOrder${x}" name="unitPriceOrder[]" value="${$('#unitPriceOrderAside').val().trim()}">
                                        <span class="">${unitPrice}</span><span class="currencyText ps-2">${ccy}</span>
                                    </td>
                                    <td class="text-end">
                                        <input type="hidden" id="subTotalPriceOrder${x}" class="subTotalPriceOrder" name="subTotalPriceOrder[]" value="${formattedSubTotal}">
                                        <span class="">${formattedSubTotal}</span><span class="currencyText ps-2">${ccy}</span>
                                    </td>
                                    <td class="text-center">
                                        <input type="hidden" id="requiredDateOrder${x}" name="requiredDateOrder[]" value="${requiredDateOrderAside}">
                                        <span class="">${requiredDateOrderAside}</span>
                                    </td>
                                </tr>`;

                if(type == 'ADD') {
                    $('#tbodyItemOrder').append(itemHtml);
                }
                else {
                    $(`#itemBody${x}`).replaceWith(itemHtml);
                }

                let no = 1;
                $('.itemNo').each(function() {
                    $(this).html(no);
                    no++;
                });
            }
            else if($this.attr('data-type') == 'REMOVE') {
                typeMessage = 'Removed';
                let itemNumberRow = $('#itemRowNumber').val();
                $(`#itemBody${itemNumberRow}`).remove();
                if( $('.itemBody').length == 0) {
                    $('#tbodyItemOrder').append(`<tr>
                                                    <td colspan="9" class="no_result_table py-3 text-center">
                                                        <input type="hidden" name="codeOrder[]" value="">
                                                        <input type="hidden" name="itemOrder[]" value="">
                                                        <input type="hidden" name="typeOrder[]" value="">
                                                        <input type="hidden" name="qtyOrder[]" value="">
                                                        <input type="hidden" name="unitOrder[]" value="">
                                                        <input type="hidden" name="unitPriceOrder[]" value="">
                                                        <input type="hidden" name="subTotalPriceOrder[]" value="">
                                                        <input type="hidden" name="requiredDateOrder[]" value="">
                                                        No Item data
                                                    </td>
                                                </tr>`);
                }
                else {
                    let no = 1;
                    $('.itemNo').each(function() {
                        $(this).html(no);
                        no++;
                    });
                }
            }

            countGrandTotal();
            asideHide();
            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> Item ${typeMessage}` });
        }
        else {
            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : Please fill the required form` });
        }
    });

    async function getUnit() {
        try {
            const response = await fetch('/doc_approval/getUnit', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                    'Referer': window.location.href
                },
            });

            const data = await response.json();
            if (response.status === 200) {
                const options = data.map(row => ({
                                                    id: row.unit_id,
                                                    text: row.unit_name
                                                }));
                return options;
            }
            else if (response.status === 401) { // Unauthorized
                let countdown = 5;
                Snackbar.show({
                    pos: 'bottom-center',
                    duration: '6000',
                    text: `<i class="fa-solid fa-circle-exclamation fa-lg fa-fw text-warning"></i> Session expired, please refresh this page or it will automatically refresh in <span id="snackbar-countdown">${countdown}</span> seconds.`
                });

                let countdownInterval = setInterval(() => {
                    countdown--;
                    document.getElementById('snackbar-countdown').textContent = countdown;
                    if (countdown < 0) {
                        clearInterval(countdownInterval);
                        window.location.href = data.redirect_uri;
                    }
                }, 1000);
            }
            else {
                console.error('HTTP Error:', response.status);
            }
        } catch (error) {
            return;
        }
    }

    function itemContainerFooter(params){
        const documentType = params['documentType'];
        const loadType = params['loadType'];
        let itemFooter = '';
        if(documentType == '1'){
            const itemFooter = `
                                <div class="row row-multi-col mb-3 pt-3 pb-2">
                                    <div class="mb-2 row d-none">
                                        <label for="vat" class="col-sm-12 col-md-2 col-form-label text-end">VAT :</label>
                                        <div class="col-sm-12 col-md-2">
                                            <div class="form-group">
                                                <div class="form-group d-flex align-items-center position-relative">
                                                    <input type="text" class="form-control d-flex numberValue" id="vatOrder" name="vat" spellcheck="false" autocomplete="off" value="0" style="padding-right: 50px;">
                                                    <span class="position-absolute end-0 top-0 h-100 d-flex align-items-center fw-bold px-3">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-2 row">
                                        <label for="grandTotal" class="col-sm-12 col-md-2 col-form-label text-end">GRAND TOTAL :</label>
                                        <div class="col-sm-12 col-md-4">
                                            <div class="form-group d-flex align-items-center position-relative" style="font-size:13px">
                                                <input type="text" class="form-control d-flex fw-bold" id="grandTotal" name="grandTotal" spellcheck="false" autocomplete="off" placeholder="-" disabled="" style="padding-right: 50px;">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-3 fw-semibold">---</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
            $('#itemContainerFooter').html(itemFooter);
        }
    }

    function countGrandTotal(){
        let grandTotal = 0.00;
        $('.subTotalPriceOrder').each(function() {
            let subTotal = currencyToNumber($(this).val());
            if (isNaN(subTotal)) {
                subTotal = 0.00;
            }
            grandTotal += subTotal;
        });

        grandTotal = (grandTotal == 0 || grandTotal == 0.00) ? '-' : formatCurrency(grandTotal, 2);
        $('#grandTotal').html(grandTotal);
    }
</script>
@endonce