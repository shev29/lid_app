<ul class="nav nav-pills nav-pills-fixed pt-2" id="pillsTabViewForm" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link pillTabDetailForm px-3 py-1 viewFormButton active" id="applicationFormTab" data-form="FORM" data-scroll-top="" data-coreui-toggle="pill" data-coreui-target="#applicationFormContent" type="button" role="tab" aria-controls="applicationFormContent" aria-selected="false" tabindex="-1">Application Form</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link pillTabDetailForm px-3 py-1 viewFormButton" id="comparisonFormTab" data-form="COMPARISON_FORM_TAB" data-token="{{ $dataForm['tokenFormComparison'] }}" data-scroll-top="" data-coreui-toggle="pill" data-coreui-target="#comparisonFormContent" type="button" role="tab" aria-controls="comparisonFormContent" aria-selected="true">Comparison Form</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link pillTabDetailForm px-3 py-1 viewFormButton" id="orderFormTab" data-form="ORDER_FORM_TAB" data-token="{{ $dataForm['tokenFormOrder'] }}" data-scroll-top="" data-coreui-toggle="pill" data-coreui-target="#orderFormContent" type="button" role="tab" aria-controls="orderFormContent" aria-selected="true">Order Form</button>
    </li>
</ul>
<div class="tab-content h-100" id="pillsTabContentForm">
    <div class="tab-pane fade active show" id="applicationFormContent" role="tabpanel" aria-labelledby="applicationFormTab" tabindex="0">
        <form role="form" class="form-horizontal" enctype="multipart/form-data" id="formDocumentForm">
            @csrf
            <input type="hidden" name="tokenForm" value="{{$dataForm['tokenForm']}}">
            <input type="hidden" name="companyId" id="companyId" value="{{$dataForm['companyId']}}">
            <input type="hidden" name="companyIdEncode" id="companyIdEncode" value="{{$dataForm['companyIdEncode']}}">
            <input type="hidden" name="documentType" id="documentTypeForm" value="{{$dataForm['documentType']}}">
            <input type="hidden" name="yearForm" value="{{$dataForm['yearForm']}}">
            <input type="hidden" name="requestType" value="{{$dataForm['requestType']}}">
            <input type="hidden" name="applicationHeader" id="applicationHeader" value="{{$dataForm['applicationHeader']}}">
            <input type="hidden" name="compareItem" value="{{$dataForm['compareItem'] == true ? 'true' : 'false'}}">
            <input type="hidden" id="vat" name="vat" value="{{$dataForm['vat']}}">
            <input type="hidden" id="pph"name="pph" value="{{$dataForm['pph']}}">
            <input type="hidden" id="deliveryFee" name="deliveryFee" value="{{$dataForm['deliveryFee']}}">
            <div id="headerContainer">
                <div class="row mb-3 pt-2 pb-1">
                    <div class="col-sm-12 col-md-6">
                        <div class="row row-multi-col bg-white mb-3 pt-2 pb-1">
                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="" class="col-sm-12 col-md-4 col-form-label label-inline-input">
                                    <div class="d-inline">No<span class="required"></span></div>
                                </label>
                                <div class="col-sm-12 col-md-8">
                                    @php
                                        if($dataForm['applicationNumber'] == '') {
                                            echo '<div class="skeleton mb-0" id="skeletonDocumentNumber"></div>';
                                        }
                                        else {
                                            echo '<input type="text" class="form-control" id="seqNumber" name="seqNumber" value="'.$dataForm['applicationNumber'].'" readonly>';
                                        }
                                    @endphp
                                </div>
                            </div>

                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="titleApplication" class="col-sm-12 col-md-4 col-form-label label-inline-input">
                                    <div class="d-inline">Title<span class="required"></span></div>
                                </label>
                                <div class="col-sm-12 col-md-8">
                                    <input type="text" class="form-control" id="titleApplication" name="titleApplication" spellcheck="false" autocomplete="off" value="{{$dataForm['titleApplication']}}">
                                </div>
                            </div>

                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="" class="col-sm-12 col-md-4 col-form-label label-inline-input">
                                    <div class="d-inline">Submitted Date<span class="required"></span></div>
                                </label>
                                <div class="col-sm-12 col-md-8">
                                    <div class="form-group date-picker" id="submittedDateApplication" data-coreui-date="{{$dataForm['submittedDate']}}" data-coreui-name="submittedDateApplication"></div>
                                </div>
                            </div>

                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="" class="col-sm-12 col-md-4 col-form-label label-inline-input">
                                    <div class="d-inline">Total Amount<span class="required"></span></div>
                                </label>
                                <div class="col-sm-12 col-md-8">
                                    <div class="form-group d-flex align-items-center position-relative">
                                        <input type="text" class="form-control d-flex currencyValue" id="estimateApplication" name="estimateApplication" value="{{$dataForm['totalPriceVat']}}" spellcheck="false" autocomplete="off" style="padding-right: 33px;" readonly="">
                                        <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">{{ $dataForm['currencyCode'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="" class="col-sm-12 col-md-4 col-form-label label-inline-input">
                                    <div class="d-inline">Currency<span class="required"></span></div>
                                </label>
                                <div class="col-sm-12 col-md-8">
                                    <select class="select2 selectNonClear" id="currency" name="currencyApplication">
                                        <option value="{{ $dataForm['currencyCode'] }}" selected>{{ $dataForm['currencyCode'] }}</option>
                                        {{-- @php
                                            foreach ($dataForm['currency'] as $row) {
                                                $selected = ($row['value'] == $dataForm['currencySelected']) ? ' selected=""' : '';
                                                echo '<option value="'.$row['value'].'"'.$selected.'>'.$row['label'].'</option>';
                                            }
                                        @endphp --}}
                                    </select>
                                </div>
                            </div>

                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="" class="col-sm-12 col-md-4 col-form-label label-inline-input">
                                    <div class="d-inline">To be Paid To<span class="required"></span></div>
                                </label>
                                <div class="col-sm-12 col-md-8">
                                    @if ($dataForm['vendorSelected'])
                                        <div class="col-12">
                                            <select class="select2 selectReadonly" id="vendorApplication" name="vendorApplication">
                                                <option value="{{ $dataForm['vendorSelected']['vendorId'] }}" selected>{{ $dataForm['vendorSelected']['vendorName'] }}</option>
                                            </select>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <div class="card">
                                                <div class="card-body py-2 px-2-2" id="vendorAdrressApplicationContainer">
                                                    <div class="py-2">
                                                        <div class="data-details">
                                                            <div class="data-row">
                                                                <label class="text-wrap lh-base d-block" style="padding-left: 1.5rem; text-indent: -1.6rem;cursor:pointer">
                                                                    <div class="white-space-pre"><input type="checkbox" class="form-check-input me-2" name="vendorAddressApplication" value="{{ $dataForm['vendorSelected']['vendorAddressId'] }}" checked readonly>{{ $dataForm['vendorSelected']['vendorAddress'] }}</div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="row">
                                            <div class="col-10 pe-0">
                                                <select class="select2" id="vendorApplication" name="vendorApplication"></select>
                                            </div>
                                            <div class="col-2">
                                                <button class="btn btn-transparent masterVendor float-end" type="button" data-type="REFERENCE_APPLICATION" data-vendor="1" data-sortby="VENDOR_NAME" title="Vendor Reference">
                                                    <i class="fa-solid fa-search fa-lg"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <div class="card">
                                                <div class="card-body py-2 px-2-2" id="vendorAdrressApplicationContainer">
                                                    <textarea class="form-control autosize" data-limit-rows="true" rows="2" spellcheck="false" maxlength="255" id="vendorAddressApplication" name="vendorAddressApplication" style="height: 53px;" readonly></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    {{-- <div class="row mt-2">
                                        <div class="col-12">
                                            <textarea class="form-control autosize vendorInput d-block" data-limit-rows="true" rows="2" spellcheck="false" maxlength="255" id="vendorAddressApplication" name="vendorAddressApplication">{{$dataForm['vendorAddress']}}</textarea>
                                            <div class="skeleton mb-0 textarea vendorSkeleton d-none"></div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>

                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="vendorPicApplication" class="col-sm-12 col-md-4 col-form-label label-inline-input">
                                    <div class="d-inline">Attention To<span class="required"></span></div>
                                </label>
                                <div class="col-sm-12 col-md-8">
                                    @if ($dataForm['vendorSelected'])
                                        <select class="select2 selectReadonly" name="vendorPicApplication" id="vendorPicApplication">
                                            <option value="{{ $dataForm['vendorSelected']['vendorPicId'] }}" selected>{{ $dataForm['vendorSelected']['vendorPic'] }}</option>
                                        </select>
                                    @else
                                        <input type="text" class="form-control" name="vendorPicApplication" id="vendorPicApplication" value="" readonly="">
                                    @endif
                                </div>
                            </div>

                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="deliveryApplication" class="col-sm-12 col-md-4 col-form-label label-inline-input">
                                    <div class="d-inline">Delivery to<span class="required"></span></div>
                                </label>
                                <div class="col-sm-12 col-md-8">
                                    <div class="row">
                                        <div class="col-10 pe-0">
                                            <select class="select2" id="selectDelivery" name="deliveryTo">
                                                <option></option>
                                                @php
                                                foreach ($dataForm['deliveryList'] as $row) {
                                                    $selected = ($row['id'] == $dataForm['deliveryId']) ? ' selected=""' : '';
                                                    echo '<option value="'.$row['id'].'" data-description1="'.$row['description'].'"'.$selected.'>'.$row['label'].'</option>';
                                                }
                                                @endphp
                                            </select>
                                        </div>
                                        <div class="col-2">
                                            <button type="button" class="btn btn-transparent float-end" id="addMasterDelivery" data-type="" data-form="" data-token=""><i class="fa-solid fa-plus fa-lg" title="New Delivery"></i></button>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <textarea class="form-control autosize d-block" data-limit-rows="true" rows="2" spellcheck="false" maxlength="255" id="deliveryApplication" name="deliveryApplication" readonly>{{$dataForm['deliveryAddress']}}</textarea>
                                            <div class="skeleton mb-0 textarea d-none" id="deliveryAddressSkeleton"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="invoiceApplication" class="col-sm-12 col-md-4 col-form-label label-inline-input">
                                    <div class="d-inline">Invoice to<span class="required"></span></div>
                                </label>
                                <div class="col-sm-12 col-md-8">
                                    <div class="row">
                                        <div class="col-10 pe-0">
                                            <select class="select2" id="selectInvoiceTo" name="invoiceTo">
                                                <option></option>
                                                @php
                                                foreach ($dataForm['invoiceToList'] as $row) {
                                                    $selected = ($row['id'] == $dataForm['invoiceToId'] || count($dataForm['invoiceToList']) == 1) ? ' selected' : '';
                                                    echo '<option value="'.$row['id'].'" data-description1="'.$row['description'].'"'.$selected.'>'.$row['label'].'</option>';
                                                }
                                                @endphp
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <textarea class="form-control autosize d-block" data-limit-rows="true" rows="3" spellcheck="false" maxlength="255" id="invoiceApplication" name="invoiceApplication" readonly>{{$dataForm['invoiceToAddress']}}</textarea>
                                            <div class="skeleton mb-0 textarea d-none" id="invoiceToAddressSkeleton"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="col-sm-12 col-md-8">
                                    <div class="row">
                                        <div class="col-12">
                                            <textarea class="form-control autosize" data-limit-rows="true" rows="3" spellcheck="false" maxlength="255" id="invoiceApplication" name="invoiceApplication" readonly>{{$dataForm['company']['companyTitle']."\r\n".$dataForm['company']['companyAddress']}}</textarea>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="row row-multi-col bg-white mb-3 pt-2 pb-1">
                            @php
                                $arrId = $arrBudget = [];
                            @endphp

                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="" class="col-sm-12 col-md-4 col-form-label label-inline-input">
                                    <div class="d-inline">Purchase Type<span class="required"></span></div>
                                </label>
                                <div class="col-sm-12 col-md-8">
                                    <select class="select2" id="purchaseTypeApplication" name="purchaseTypeApplication">
                                        <option></option>
                                        @php
                                            foreach ($dataForm['purchaseType'] as $row) {
                                                $selected = ($row['value'] == $dataForm['purchaseTypeSelected']) ? ' selected=""' : '';
                                                echo '<option value="'.$row['value'].'" data-formtype="'.$row['formType'].'" data-formatnumber="'.$row['formatNumber'].'" data-applicationtype="'.$row['applicationType'].'" data-budget="'.$row['budgetNo'].'"'.$selected.'>'.$row['label'].'</option>';
                                            }
                                        @endphp
                                    </select>
                                </div>
                            </div>

                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="budgetNoApplication" class="col-sm-12 col-md-4 col-form-label label-inline-input">Budget No</label>
                                {{-- <div class="col-sm-12 col-md-8">
                                    <input type="text" class="form-control" id="budgetNoApplication" name="budgetNoApplication" spellcheck="false" autocomplete="off" value="{{$dataForm['budgetNo']}}">
                                </div> --}}
                                <div class="col-sm-12 col-md-8">
                                    @php
                                        if(count($dataForm['budgetSelected']) == 0) {
                                            // $optionBudget = '';
                                            // foreach ($dataForm['budgetList'] as $row) {
                                            //     $optionBudget .= '<option value="'.$row['id'].'" data-description1="'.$row['description1'].'">'.$row['text'].'</option>';
                                            // }

                                            $id = uniqid();
                                            $arrId[] = $id;
                                            $arrBudget[] = [
                                                'budgetAmount' => '',
                                                'withinOver' => '',
                                                'budgetRemaining' => '',
                                            ];
                                            echo '<div class="row">
                                                    <div class="col-10 pe-0 budgetRow d-flex gap-2" id="budgetNo_'.$id.'">
                                                        <div class="col-12 pe-0">
                                                            <input type="text" class="form-control budgetApplication" id="selectBudget1" name="budgetApplication[]">
                                                        </div>
                                                    </div>
                                                    <div class="col-2 d-none">
                                                        <button type="button" class="btn btn-default float-end" id="addBudget" data-type="" data-form="" data-token=""><i class="fa-solid fa-plus"></i></button>
                                                    </div>
                                                </div>';
                                        }
                                        else {
                                            $x = 0;
                                            foreach ($dataForm['budgetSelected'] as $rowBudget) {
                                                $id = uniqid();
                                                $arrId[] = $id;
                                                $arrBudget[] = [
                                                    'budgetAmount' => $rowBudget['budgetAmount'],
                                                    'withinOver' => $rowBudget['withinOver'],
                                                    'budgetRemaining' => $rowBudget['budgetRemaining'],
                                                ];

                                                // $optionBudget = '';
                                                // foreach ($dataForm['budgetList'] as $row) {
                                                //     $selected = ($row['id'] == $rowBudget['budgetId']) ? ' selected' : '';
                                                //     $optionBudget .= '<option value="'.$row['id'].'" data-description1="'.$row['description1'].'"'.$selected.'>'.$row['text'].'</option>';
                                                // }

                                                if($x === 0) {
                                                    $mt = '';
                                                    $budgetButton = '<button type="button" class="btn btn-default float-end" id="addBudget" data-type="" data-form="" data-token=""><i class="fa-solid fa-plus"></i></button>';
                                                }
                                                else {
                                                    $mt = ' mt-2';
                                                    $budgetButton = '<button type="button" class="btn btn-default float-end removeBudget" data-id="'.$id.'"><i class="fa-regular fa-trash-can-xmark"></i></button>';
                                                }

                                                echo '<div class="row'.$mt.'">
                                                    <div class="col-10 pe-0 budgetRow d-flex gap-2" id="budgetNo_'.$id.'">
                                                        <div class="col-12 pe-0">
                                                            <input type="text" class="form-control budgetApplication" id="selectBudget'.($x + 1).'" name="budgetApplication[]" value="'.$rowBudget['budgetNo'].'">
                                                        </div>
                                                    </div>
                                                    <div class="col-2 d-none">
                                                        '.$budgetButton.'
                                                    </div>
                                                </div>';

                                                $x++;
                                            }
                                        }
                                    @endphp
                                </div>
                            </div>

                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="budgetAmountApplication" class="col-sm-12 col-md-4 col-form-label label-inline-input">Budget Amount</label>
                                <div class="col-sm-12 col-md-8" id="budgetAmountContainer">
                                    @php
                                        foreach ($arrId as $index => $id) {
                                            $mt = ($index == 0) ? '' : ' mt-2';
                                            echo '<div class="row'.$mt.' budgetAmountItem" id="budgetAmount_'.$id.'">
                                                    <div class="col-5">
                                                        <input type="text" class="form-control currencyValue budgetAmountApplication" name="budgetAmountApplication[]" spellcheck="false" autocomplete="off" value="'.$arrBudget[$index]['budgetAmount'].'">
                                                    </div>
                                                    <div class="col-2 pe-0">
                                                        <input type="hidden" class="budgetWithinInput" name="budgetWithinInput[]" value="'.$arrBudget[$index]['withinOver'].'" readonly>
                                                        <label for="budgetWithinApplication" class="col-form-label label-inline-input budgetWithinLabel">'.$arrBudget[$index]['withinOver'].'</label>
                                                    </div>
                                                    <div class="col-5">
                                                        <input type="text" class="form-control currencyValue d-flex budgetWithinApplication" name="budgetWithinApplication[]" spellcheck="false" autocomplete="off" value="'.$arrBudget[$index]['budgetRemaining'].'" readonly>
                                                    </div>
                                                </div>';
                                        }
                                    @endphp
                                </div>
                            </div>

                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="" class="col-sm-12 col-md-4 col-form-label label-inline-input">
                                    <div class="d-inline">Cost Center<span class="required"></span></div>
                                </label>
                                <div class="col-sm-12 col-md-8">
                                    @php
                                        if(count($dataForm['costCenterSelected']) == 0) {
                                            $optionCostCenter = '';
                                            foreach ($dataForm['costCenter'] as $row) {
                                                $optionCostCenter .= '<option value="'.$row['id'].'" data-description1="'.$row['description1'].'">'.$row['text'].'</option>';
                                            }

                                            echo '<div class="row">
                                                    <div class="col-10 pe-0 costCenterRow d-flex gap-2" id="costCenter1">
                                                        <div class="col-8 pe-0">
                                                            <select class="select2 selectCostCenter selectDesc" id="selectCostCenter1" name="costCenterApplication[]">
                                                                <option></option>
                                                                '.$optionCostCenter.'
                                                            </select>
                                                        </div>
                                                        <div class="col-4 pe-0">
                                                            <div class="form-group d-flex align-items-center position-relative">
                                                                <input type="text" class="form-control d-flex numberValue" name="costCenterPercentApplication[]" spellcheck="false" autocomplete="off" style="padding-right: 33px;" maxlength="6">
                                                                <span class="position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-2">
                                                        <button type="button" class="btn btn-default float-end" id="addCostCenter" data-type="" data-form="" data-token=""><i class="fa-solid fa-plus"></i></button>
                                                    </div>
                                                </div>';
                                        }
                                        else {
                                            $x = 0;
                                            foreach ($dataForm['costCenterSelected'] as $rowCostCenter) {
                                                $optionCostCenter = '';
                                                foreach ($dataForm['costCenter'] as $row) {
                                                    $selected = ($row['id'] == $rowCostCenter['costCenterId']) ? ' selected' : '';
                                                    $optionCostCenter .= '<option value="'.$row['id'].'" data-description1="'.$row['description1'].'"'.$selected.'>'.$row['text'].'</option>';
                                                }

                                                if($x === 0) {
                                                    $mt = '';
                                                    $costCenterButton = '<button type="button" class="btn btn-default float-end" id="addCostCenter" data-type="" data-form="" data-token=""><i class="fa-solid fa-plus"></i></button>';
                                                }
                                                else {
                                                    $mt = ' mt-2';
                                                    $costCenterButton = '<button type="button" class="btn btn-default float-end removeCostCenter"><i class="fa-regular fa-trash-can-xmark"></i></button>';
                                                }

                                                echo '<div class="row'.$mt.'">
                                                    <div class="col-10 pe-0 costCenterRow d-flex gap-2" id="costCenter'.($x + 1).'">
                                                        <div class="col-8 pe-0">
                                                            <select class="select2 selectCostCenter" id="selectCostCenter'.($x + 1).'" name="costCenterApplication[]">
                                                                <option></option>
                                                                '.$optionCostCenter.'
                                                            </select>
                                                        </div>
                                                        <div class="col-4 pe-0">
                                                            <div class="form-group d-flex align-items-center position-relative">
                                                                <input type="text" class="form-control d-flex numberValue" name="costCenterPercentApplication[]" spellcheck="false" autocomplete="off" style="padding-right: 33px;" maxlength="6" value="'.$rowCostCenter['percentage'].'">
                                                                <span class="position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-2">
                                                        '.$costCenterButton.'
                                                    </div>
                                                </div>';

                                                $x++;
                                            }
                                        }
                                    @endphp

                                </div>
                            </div>

                            {{-- @php
                                dd($dataForm);
                            @endphp --}}

                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="" class="col-sm-12 col-md-4 col-form-label label-inline-input">Reimburse To</label>
                                <div class="col-sm-12 col-md-8">
                                    @php
                                        if(count($dataForm['reimburseToSelected']) == 0) {
                                            echo '<div class="row">
                                                        <div class="col-9 pe-0">
                                                            <input type="text" class="form-control" id="reimburseApplication1" name="reimburseApplication[]" spellcheck="false" autocomplete="off">
                                                        </div>
                                                        <div class="col-3">
                                                            <div class="form-group d-flex align-items-center position-relative">
                                                                <input type="text" class="form-control d-flex numberValue" id="reimburseApplicationPercentage1" name="reimburseApplicationPercentage[]" spellcheck="false" autocomplete="off" style="padding-right: 33px;" maxlength="6">
                                                                <span class="position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">%</span>
                                                            </div>
                                                        </div>
                                                    </div>';
                                        }
                                        else {
                                            foreach ($dataForm['reimburseToSelected'] as $reimburse) {
                                                echo '<div class="row">
                                                        <div class="col-9 pe-0">
                                                            <input type="text" class="form-control" id="reimburseApplication1" name="reimburseApplication[]" spellcheck="false" autocomplete="off" value="'.$reimburse['reimburseTo'].'">
                                                        </div>
                                                        <div class="col-3">
                                                            <div class="form-group d-flex align-items-center position-relative">
                                                                <input type="text" class="form-control d-flex numberValue" id="reimburseApplicationPercentage1" name="reimburseApplicationPercentage[]" spellcheck="false" autocomplete="off" style="padding-right: 33px;" maxlength="6" value="'.$reimburse['percentage'].'">
                                                                <span class="position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">%</span>
                                                            </div>
                                                        </div>
                                                    </div>';
                                            }
                                        }
                                    @endphp
                                </div>
                            </div>

                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="" class="col-sm-12 col-md-4 col-form-label label-inline-input">
                                    <div class="d-inline">Payment Type<span class="required"></span></div>
                                </label>
                                <div class="col-sm-12 col-md-8 d-flex align-items-center">
                                    <div>
                                        @php
                                            $transferChecked = $leasingChecked = '';
                                            if($dataForm['paymentType'] == '1') {
                                                $transferChecked = ' checked=""';
                                            }
                                            else {
                                                $leasingChecked = ' checked=""';
                                            }
                                        @endphp
                                        <div class="form-check form-check-inline me-4">
                                            <input class="form-check-input paymentTypeApplication" type="checkbox" id="inlineCheckbox1" name="paymentTypeApplication" value="1"{{$transferChecked}}>
                                            <label class="form-check-label" for="inlineCheckbox1">Transfer</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input paymentTypeApplication" type="checkbox" id="inlineCheckbox2" name="paymentTypeApplication" value="3"{{$leasingChecked}}>
                                            <label class="form-check-label" for="inlineCheckbox2">Leasing</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($dataForm['companyId'] == '322')
                                <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                    <label for="" class="col-sm-12 col-md-4 col-form-label label-inline-input">
                                        <div class="d-inline">Payment Term<span class="required"></span></div>
                                    </label>
                                    <div class="col-sm-12 col-md-8">
                                        <select class="select2 selectNonClear" id="termPayment" name="termPayment">
                                            <option></option>
                                            @foreach ($dataForm['termPayment'] as $row)
                                                @php
                                                    $selected = ($row['id'] == $dataForm['termPaymentId']) ? ' selected' : '';
                                                @endphp
                                                <option value="{{$row['id']}}" data-description1="{{$row['desc']}}" {{$selected}}>{{ $row['text'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="" class="col-sm-12 col-md-4 col-form-label label-inline-input">
                                    <div class="d-inline">Ship Date<span class="required"></span></div>
                                </label>
                                <div class="col-sm-12 col-md-8">
                                    <div class="row">
                                        <div class="col-12 col-md-5">
                                            <div class="form-group date-picker" id="shipDateApplication" data-coreui-date="{{$dataForm['shipDate']}}" data-coreui-name="shipDateApplication"></div>
                                        </div>
                                        <div class="col-12 col-md-7">
                                            <div class="row">
                                                <label for="" class="col-sm-12 col-md-3 col-form-label pe-0 text-md-end pb-0">
                                                    Type :
                                                </label>
                                                <div class="col-sm-12 col-md-9">
                                                    <select class="select2 selectNonClear" id="formTypeApplication" name="formTypeApplication">
                                                        <option value="{{$dataForm['formTypeSelected']}}" selected="">{{$dataForm['formTypeSelected']}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="" class="col-sm-12 col-md-4 col-form-label label-inline-input">PO Remark</label>
                                <div class="col-sm-12 col-md-8">
                                    <textarea class="form-control autosize" data-limit-rows="true" rows="4" spellcheck="false" maxlength="255" id="remarkApplication" name="remarkApplication">{{$dataForm['purposeRemarks']}}</textarea>
                                </div>
                            </div>

                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="" class="col-sm-12 col-md-4 col-form-label label-inline-input">
                                    <div class="d-inline">Reason<span class="required"></span></div>
                                </label>
                                <div class="col-sm-12 col-md-8">
                                    <textarea class="form-control autosize" data-limit-rows="true" rows="3" spellcheck="false" maxlength="255" id="reasonApplication" name="reasonApplication">{{$dataForm['purposeReason']}}</textarea>
                                </div>
                            </div>

                            <div class="row form-group m-0 mb-md-1 px-0 py-1">
                                <label for="" class="col-sm-12 col-md-4 col-form-label label-inline-input">
                                    <div class="d-inline">Priority Level<span class="required"></span></div>
                                </label>
                                <div class="col-sm-12 col-md-8">
                                    <select class="select2 selectNonClear" id="priority" name="priorityApplication">
                                        <option></option>
                                        @foreach ($dataForm['priorityLevel'] as $row)
                                            @php
                                                $selected = ($row['id'] == $dataForm['priority']) ? ' selected' : '';
                                            @endphp
                                            <option value="{{$row['id']}}" data-description1="{{$row['desc']}}" {{$selected}}>{{ $row['text'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div id="itemContainer">
                @php
                if($dataForm['compareItem'] == false) {
                    echo '<div class="col-12 mb-2">
                            <div class="d-flex justify-content-end align-items-center gap-3">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-default btn-sm fs-8 fw-medium dropdown-toggle" data-bs-boundary="viewport" data-coreui-toggle="dropdown" data-coreui-auto-close="outside" aria-expanded="false" title="Modify Application Item"><i class="fa-solid fa-bars-staggered fa-fw"></i> Modify Application Item</button>
                                    <ul class="dropdown-menu" aria-labelledby="">
                                        <li><label class="dropdown-item modifyItem" data-form="APPLICATION" data-type="ADD" data-currency="'.$dataForm['itemForm'][0]['currency'].'">Add Item</label></li>
                                        <li><label class="dropdown-item modifyItem" data-form="APPLICATION" data-type="EDIT">Edit Item</label></li>
                                        <li><label class="dropdown-item modifyItem" data-form="APPLICATION" data-type="REMOVE">Remove Item</label></li>
                                        <div class="dropdown-divider"></div>
                                        <li><label class="dropdown-item modifyItem" data-form="COMPARISON" data-type="COMBINE">Combine Order Form</label></li>
                                    </ul>
                                </div>
                            </div>
                        </div>';
                }
                @endphp
                <div class="table-responsive mb-2">
                    <table class="table table-hover table-form" id="tableComparison">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 35%">Description</th>
                                <th style="width: 10%">Qty</th>
                                <th style="width: 25%">Price / Unit</th>
                                <th style="width: 25%">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $x = 0;
                            // dd($dataForm['itemForm']);

                            $noInput = $readonly = '';
                            if($dataForm['compareItem'] == true) {
                                $noInput = ' no-input';
                                $readonly = ' readonly=""';
                            }

                            foreach ($dataForm['itemForm'] as $row) {
                                // if(($dataForm['compareItem'] == false && $row['orderDetailId'] == '') || $row['totalPrice'] == '0.00' || $row['totalPrice'] == '0') {
                                if(($dataForm['compareItem'] == false && ($row['applianceItem'] == 'Delivery Fee' || $row['applianceItem'] == 'Discount' || $row['applianceItem'] == 'Total Price' || $row['applianceItem'] == 'VAT' || $row['applianceItem'] == 'PPh' || $row['applianceItem'] == 'Total Price + VAT - PPh')) || $row['totalPrice'] == '0.00' || $row['totalPrice'] == '0'){
                                    continue;
                                }

                                if($row['applianceItem'] != 'Total Price + VAT - PPh' && $row['applianceItem'] != 'Total Price') {
                                    $x++;
                                    $rate = '';
                                    // if($row['orderDetailId'] == '') {
                                    if ($row['applianceItem'] == 'Delivery Fee' || $row['applianceItem'] == 'Discount' || $row['applianceItem'] == 'Total Price' || $row['applianceItem'] == 'PPh' || $row['applianceItem'] == 'Total Price + VAT - PPh') {
                                        $no = '';
                                        $display = 'd-none';

                                        if($row['currencySymbol'] == '%'){
                                            $rate = ' '.(int)$row['unitPrice'].'%';
                                            if($row['applianceItem'] == 'VAT') {
                                                $row['currency'] = 'IDR';
                                            }
                                            else {
                                                $row['currency'] = $dataForm['itemForm'][0]['currency'];
                                            }
                                        }

                                        $pricePerUnit = '<div class="form-group '.$display.' align-items-center position-relative">
                                                            <input type="hidden" class="unitCurrency" name="currency1[]" value="'.$row['currencySymbol'].'">
                                                            <input type="text" class="form-control text-end d-flex currencyValue countAmount'.$noInput.'" name="unitPrice1[]" data-vendor="1" data-item="'.$x.'" data-qty="true" spellcheck="false" autocomplete="off" style="padding-right: 33px;" value="'.$row['unitPrice'].'"'.$readonly.'>
                                                            <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$row['currency'].'</span>
                                                        </div>';
                                    }
                                    else if($row['applianceItem'] == 'VAT' && $row['unitPrice'] != null) {
                                        $no = '';
                                        $dppOtherValue = '';
                                        if($row['dppOtherValue'] != null) {
                                            $dppOtherValue = 'x '.$row['dppOtherValue'];
                                        }

                                        $pricePerUnit = '<div class="form-group '.$display.' align-items-center position-relative">
                                                            <input type="hidden" class="unitCurrency" name="currency1[]" value="'.$row['currencySymbol'].'">
                                                            <input type="text" class="form-control text-end d-flex currencyValue countAmount px-2'.$noInput.'" name="unitPrice1[]" data-vendor="1" data-item="'.$x.'" data-qty="true" spellcheck="false" autocomplete="off" value="'.(int)$row['unitPrice'].'% '.$dppOtherValue.'"'.$readonly.'>
                                                        </div>';
                                    }
                                    else {
                                        $no = $x;
                                        $display = 'd-flex';
                                        $pricePerUnit = '<div class="form-group '.$display.' align-items-center position-relative">
                                                            <input type="hidden" class="unitCurrency" name="currency1[]" value="'.$row['currencySymbol'].'">
                                                            <input type="text" class="form-control text-end d-flex currencyValue countAmount'.$noInput.'" name="unitPrice1[]" data-vendor="1" data-item="'.$x.'" data-qty="true" spellcheck="false" autocomplete="off" style="padding-right: 33px;" value="'.$row['unitPrice'].'"'.$readonly.'>
                                                            <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$row['currency'].'</span>
                                                        </div>';
                                    }

                                    $removeItem = '';
                                    if($dataForm['compareItem'] == false) {
                                        $removeItem = '<div class="position-relative end-0">
                                                            <button class="btn btn-sm btn-transparent modifyRowBtn" type="button" title="Remove Item" data-type="REMOVE" data-form="APPLICATION" data-row="'.$x.'">
                                                                <i class="fa-regular fa-trash-can-xmark fa-lg"></i>
                                                            </button>
                                                        </div>';
                                    }

                                    echo '<tr id="rowItem'.$x.'" class="rowItem rowItemNumber" data-id="'.$x.'" data-label="'.$row['applianceItem'].'">
                                        <td class="text-center align-middle">
                                            <input type="hidden" class="colNo" name="colNo[]" value="'.$x.'">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="colNoLabel mx-1">'.$x.'</span>
                                                '.$removeItem.'
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <input type="hidden" class="reviseOrderDetailId" id="reviseOrderDetailId'.$x.'" name="reviseOrderDetailId[]" value="'.$row['reviseOrderDetailId'].'">

                                            <input type="hidden" class="colCriteriaId" id="colCriteriaId'.$x.'" name="colCriteriaId[]" value="'.$row['orderDetailId'].'">
                                            <input type="hidden" class="colCriteria" id="colCriteria'.$x.'" name="colCriteria[]" value="'.$row['applianceItem'].'"><span class="colCriteria">'.$row['applianceItem'].$rate.'</span>
                                        </td>
                                        <td class="text-center align-middle" data-item="'.$x.'">
                                            <input type="hidden" class="colQty" id="colQty'.$x.'" name="colQty[]" value="'.$row['unitQuantity'].'"><span class="colQty '.$display.' justify-content-center">'.$row['unitQuantity'].'</span>
                                        </td>
                                        <td>
                                            '.$pricePerUnit.'
                                        </td>
                                        <td>
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input currencyValue subTotalPrice1 subTotalVendor1" id="subTotalVendor1_'.$x.'" data-vendor="1" name="subTotalPrice[]" data-item="'.$x.'" spellcheck="false" autocomplete="off" style="padding-right: 33px;" readonly="" tabindex="-1" value="'.$row['totalPrice'].'">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$row['currency'].'</span>
                                            </div>
                                        </td>
                                    </tr>';
                                }
                            }

                            if ($dataForm['compareItem'] == false) {
                                $y = $x - 1;
                                echo '<tr id="rowItema" class="rowItemString" data-id="a" data-label="Delivery Fee">
                                        <td class="text-center align-middle">
                                            <input type="hidden" class="colNo" name="colNo[]" value="a"><span class="colNoLabel"></span>
                                        </td>
                                        <td class="align-middle">
                                            <input type="hidden" class="reviseOrderDetailId" id="reviseOrderDetailId'.$x.'" name="reviseOrderDetailId[]" value="'.$row['reviseOrderDetailId'].'">

                                            <input type="hidden" class="colCriteriaId" name="colCriteriaId[]" value="'.$dataForm['itemForm'][($y + 1)]['orderDetailId'].'">
                                            <input type="hidden" class="colCriteria" name="colCriteria[]" value="Delivery Fee"><span class="colCriteria">Delivery Fee</span>
                                        </td>
                                        <td>
                                            <input type="hidden" class="colQty" name="colQty[]" value="'.$dataForm['itemForm'][($y + 1)]['unitQuantity'].'"><span class="colQty"></span>
                                        </td>
                                        <td>
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="hidden" class="unitCurrency" name="currency1[]" value="'.$row['currencySymbol'].'">
                                                <input type="text" class="form-control text-end d-flex currencyValue countAmount" id="deliveryFeeVendor1" name="unitPrice1[]" data-vendor="1" data-item="a" data-qty="false" spellcheck="false" autocomplete="off" value="'.$dataForm['itemForm'][($y + 1)]['unitPrice'].'" style="padding-right: 33px;">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$dataForm['itemForm'][0]['currency'].'</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input currencyValue subTotalVendor1" id="subTotalVendor1_a" name="subTotalPrice[]" data-vendor="1" data-item="a" data-type="DELIVERY_FEE" spellcheck="false" autocomplete="off" value="'.$dataForm['itemForm'][($y + 1)]['totalPrice'].'" style="padding-right: 33px;" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$dataForm['itemForm'][0]['currency'].'</span>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr id="rowItemb" class="rowItemString" data-id="b" data-label="Discount">
                                        <td class="text-center align-middle">
                                            <input type="hidden" class="colNo" name="colNo[]" value="b"><span class="colNoLabel"></span>
                                        </td>
                                        <td class="align-middle">
                                            <input type="hidden" class="colCriteriaId" name="colCriteriaId[]" value="'.$dataForm['itemForm'][($y + 2)]['orderDetailId'].'">
                                            <input type="hidden" class="colCriteria" name="colCriteria[]" value="Discount">
                                            <span class="colCriteria">Discount</span><span class="fst-italic fw-normal fs-9"> (can be changed Nominal or %)</span>
                                        </td>
                                        <td>
                                            <input type="hidden" class="colQty" name="colQty[]" value="'.$dataForm['itemForm'][($y + 2)]['unitQuantity'].'"><span class="colQty"></span>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="text" class="form-control text-end pe-0 compareVendor1 discountVendor nominalPercentageInput" id="discountVendor1" name="unitPrice1[]" spellcheck="false" autocomplete="off" data-vendor="1" data-item="b" data-qty="false" value="'.$dataForm['itemForm'][($y + 2)]['unitPrice'].'">
                                                <span class="input-group-text p-0">
                                                    <select class="form-select text-end nominalPercentageSelect" id="discountType1" name="discountType1[]">';
                                                        $currencySelected = ' selected';
                                                        $percentageSelected = '';
                                                        if($dataForm['itemForm'][($y + 2)]['currency'] == '%') {
                                                            $currencySelected = '';
                                                            $percentageSelected = ' selected';
                                                        }

                                                    echo '<option value="IDR"'.$currencySelected.'>IDR</option>
                                                        <option value="%"'.$percentageSelected.'>%</option>
                                                    </select>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input currencyValue subTotalVendor1" id="subTotalVendor1_b" name="subTotalPrice[]" data-vendor="1" data-item="b" data-type="DISCOUNT" spellcheck="false" autocomplete="off" value="'.$dataForm['itemForm'][($y + 2)]['totalPrice'].'" style="padding-right: 33px;" readonly="" tabindex="-1">
                                                <span class="currencyPercentageText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$dataForm['itemForm'][0]['currency'].'</span>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr id="rowItemc" class="rowItemString" data-id="c" data-label="Total Price">
                                        <td class="text-center align-middle">
                                            <input type="hidden" class="colNo" name="colNo[]" value="c"><span class="colNoLabel"></span>
                                        </td>
                                        <td class="align-middle">
                                            <input type="hidden" class="colCriteriaId" name="colCriteriaId[]" value="'.$dataForm['itemForm'][($y + 3)]['orderDetailId'].'">
                                            <input type="hidden" class="colCriteria" name="colCriteria[]" value="Total Price"><span class="colCriteria">Total Price</span>
                                        </td>
                                        <td>
                                            <input type="hidden" class="colQty" name="colQty[]" value="'.$dataForm['itemForm'][($y + 3)]['unitQuantity'].'"><span class="colQty"></span>
                                        </td>
                                        <td>
                                            <input type="hidden" class="unitCurrency" name="currency1[]" value="'.$row['currencySymbol'].'">
                                            <input type="hidden" name="unitPrice1[]" data-vendor="1">
                                        </td>
                                        <td>
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input currencyValue" id="totalVendor1" name="subTotalPrice[]" data-vendor="1" data-item="c" spellcheck="false" autocomplete="off" style="padding-right: 33px;" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$dataForm['itemForm'][0]['currency'].'</span>
                                            </div>
                                        </td>
                                    </tr>';

                                    $arrVatRate = [
                                                '1.1' => '1.1%',
                                                '1.2' => '1.2%',
                                                '11' => '11%',
                                                '12x11/12' => '12% x 11/12',
                                                '12' => '12%',
                                            ];

                                    $optionVatRate1 = '<option></option>';
                                    $vatVendor1 = $dppOtherValue1 = null;
                                    if ($dataForm['applicationId'] != null) {
                                        $vatVendor1 = $dataForm['itemForm'][($y + 4)]['unitPrice'];
                                        $dppOtherValue1 = $dataForm['itemForm'][($y + 4)]['dppOtherValue'];
                                    }

                                    foreach ($arrVatRate as $vatRateKey => $vatRateValue) {
                                        $selected = '';
                                        if($dppOtherValue1 == null && $vatVendor1 != null && $vatVendor1 == $vatRateKey) {
                                            $selected = ' selected=""';
                                        }
                                        else if ($vatVendor1.'x'.$dppOtherValue1 == $vatRateKey){
                                            $selected = ' selected=""';
                                        }

                                        $optionVatRate1 .= '<option value="'.$vatRateKey.'"'.$selected.'>'.$vatRateValue.'</option>';
                                    }

                                echo '<tr id="rowItemd" class="rowItemString" data-id="d" data-label="VAT">
                                        <td class="text-center align-middle">
                                            <input type="hidden" class="colNo" name="colNo[]" value="d"><span class="colNoLabel"></span>
                                        </td>
                                        <td class="align-middle">
                                            <input type="hidden" class="colCriteriaId" name="colCriteriaId[]" value="'.$dataForm['itemForm'][($y + 4)]['orderDetailId'].'">
                                            <input type="hidden" class="colCriteria" name="colCriteria[]" value="VAT"><span class="colCriteria">VAT (PPn)</span></td>
                                        </td>
                                        <td>
                                            <input type="hidden" class="colQty" name="colQty[]" value="'.$dataForm['itemForm'][($y + 4)]['unitQuantity'].'"><span class="colQty"></span>
                                        </td>
                                        <td>
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="hidden" class="unitCurrency" name="currency1[]" value="'.$row['currencySymbol'].'">
                                                <select class="select2 countVat compareVendor1 selectVat" id="vatRateVendor1" name="unitPrice1[]" data-vendor="1" data-item="d">
                                                    '.$optionVatRate1.'
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="hidden" class="unitCurrency" name="currencyVatRate[]" value="'.$dataForm['itemForm'][0]['currency'].'">
                                                <input type="text" class="form-control text-end d-flex no-input currencyValue countAmount compareVendor1" id="vatVendor1" name="subTotalPrice[]" data-vendor="1" spellcheck="false" autocomplete="off" value="'.$dataForm['itemForm'][($y + 4)]['totalPrice'].'" style="padding-right: 33px;" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$dataForm['itemForm'][0]['currency'].'</span>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr id="rowItemb" class="rowItemString" data-id="e" data-label="PPh">
                                        <td class="text-center align-middle">
                                            <input type="hidden" class="colNo" name="colNo[]" value="e"><span class="colNoLabel"></span>
                                        </td>
                                        <td class="align-middle">
                                            <input type="hidden" class="colCriteriaId" name="colCriteriaId[]" value="'.$dataForm['itemForm'][($y + 5)]['orderDetailId'].'">
                                            <input type="hidden" class="colCriteria" name="colCriteria[]" value="PPh">
                                            <span class="colCriteria">PPh</span><span class="fst-italic fw-normal fs-9"> (can be changed Nominal or %)</span>
                                        </td>
                                        <td>
                                            <input type="hidden" class="colQty" name="colQty[]" value="'.$dataForm['itemForm'][($y + 5)]['unitQuantity'].'"><span class="colQty"></span>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="text" class="form-control text-end pe-0 compareVendor1 pphVendor nominalPercentageInput" id="pphVendor1" name="unitPrice1[]" spellcheck="false" autocomplete="off" data-vendor="1" data-item="e" data-qty="false" value="'.$dataForm['itemForm'][($y + 5)]['unitPrice'].'">
                                                <span class="input-group-text p-0">
                                                    <select class="form-select text-end nominalPercentageSelect" id="pphType1" name="pphType[]">';
                                                        $currencySelected = ' selected';
                                                        $percentageSelected = '';
                                                        if($dataForm['itemForm'][($y + 5)]['currency'] == '%') {
                                                            $currencySelected = '';
                                                            $percentageSelected = ' selected';
                                                        }

                                                    echo '<option value="IDR"'.$currencySelected.'>IDR</option>
                                                        <option value="%"'.$percentageSelected.'>%</option>
                                                    </select>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input currencyValue" id="subTotalVendor1_e" name="subTotalPrice[]" data-vendor="1" data-item="e" data-type="PPh" spellcheck="false" autocomplete="off" value="'.$dataForm['itemForm'][($y + 5)]['totalPrice'].'" style="padding-right: 33px;" readonly="" tabindex="-1">
                                                <span class="currencyPercentageText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$dataForm['itemForm'][0]['currency'].'</span>
                                            </div>
                                        </td>
                                    </tr>';
                            }
                            @endphp
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end align-middle fw-semibold">Total Price + VAT - PPh</td>
                                <td>
                                    <div class="form-group d-flex align-items-center position-relative">
                                        <input type="text" class="form-control text-end d-flex no-input py-0 fw-bold currencyValue" id="grandTotalVendor1" name="grandTotal" data-vendor="1" spellcheck="false" autocomplete="off" style="padding-right: 33px;" readonly="" tabindex="-1" value="{{$dataForm['totalPriceVat']}}">
                                        <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">{{$dataForm['itemForm'][0]['currency']}}</span>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div id="itemContainerFooter">
                <div class="row row-multi-col mb-4 pt-2 pb-0 bg-white" data-item="">
                    <div class="row-multi-col-header pt-0 mb-0">
                        <div class="d-inline">Quotation<span class="required"></span> :</div>
                    </div>
                    @php
                        if($dataForm['compareItem'] == true) {
                            $quotationList = '';
                            foreach ($dataForm['quotationList'] as $row) {
                                $quotationList .= '<div class="form-group" data-index="">
                                                        <div class="file-item list-group-item list-group-item-action view-file-fullscreen" data-index="">
                                                            <div class="file-details" data-token="'.$row['tokenAttachment'].'">
                                                                <i class="'.$row['icon'].' file-icon"></i>
                                                                <div>
                                                                    <span class="file-name">'.$row['filename'].'</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>';
                            }
                            echo '<div id="fileList" class="list-group px-2 pb-3">'.$quotationList.'</div>';
                        }
                        else {
                            echo '<div id="footerAttachment" class="pb-3">
                                        <div class="">
                                            <div id="dropZone" class="form-group border p-4 text-center bg-light">
                                                <p>Drag and drop files here or Select files</p>
                                                <button type="button" class="btn btn-secondary btn-md" id="selectFileBtn">Select files...</button>
                                                <input type="file" class="form-control d-none" id="fileUpload" name="attachmentFile[]" multiple>
                                            </div>
                                        </div>
                                        <div id="fileList" class="list-group mt-2">
                                        </div>
                                    </div>';
                        }
                    @endphp
                </div>
            </div>
            <div id="formContainerFooter" class="pb-4">
                <div class="row row-multi-col pt-2 pb-1 bg-white mb-4">
                    <div class="row-multi-col-header pt-0">
                        <span class="itemTitle" style="display: inline">Comments (optional) :</span>
                    </div>
                    <div class="col-12 mb-sm-0">
                        <div class="form-group mb-2">
                            <textarea class="form-control autosize" data-limit-rows="false" rows="" spellcheck="false" maxlength="255" id="applicantComment" name="applicantComment"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row row-multi-col pt-2 pb-1 bg-white">
                    <div class="row-multi-col-header pt-0 d-flex">
                        <div class="itemTitle" style="display: inline">Approval Flow<span class="required"></span> : </div>
                        <div class="start-100 ms-1 badge bg-dark bg-opacity-50 py-015 px-3 fs-8 fw-normal" id="ruleIdInfo"></div>
                    </div>
                    <div class="table-responsive mb-2">
                        <input type="hidden" id="ruleId" name="ruleId" value="">
                        <table class="table table-bordered" id="tableApprover">
                            <thead>
                                <tr>
                                    <th style="width: 14.2%;">Approve by</th>
                                    <th style="width: 16.6%">Acknowledge by</th>
                                    <th style="width: 16.6%">Confirm by</th>
                                    <th style="width: 16.6%">Review Admin</th>
                                    <th style="width: 16.6%">Confirm by</th>
                                    <th style="width: 16.6%">Review by</th>
                                    <th style="width: 16.6%">Applied by</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 14.2%;">
                                        <div id="selectApproverContainer">
                                            <select class="select2 selectSigner" id="selectApprover" name="approver"></select>
                                        </div>
                                    </td>
                                    <td style="width: 14.2%;">
                                        <div id="selectAcknowledgeContainer">
                                            <select class="select2 selectSigner" id="selectAcknowledge" name="acknowledger"></select>
                                        </div>
                                    </td>
                                    <td style="width: 14.2%;">
                                        <div id="selectConfirmer2Container">
                                            <select class="select2 selectSigner" id="selectConfirmer2" name="confirmer_2"></select>
                                        </div>
                                    </td>
                                    <td style="width: 14.2%;">
                                        <div id="selectReviewAdminContainer">
                                            <select class="select2 selectSigner" id="selectReviewAdmin" name="reviewAdmin"></select>
                                        </div>
                                    </td>
                                    <td style="width: 14.2%;">
                                        <div id="selectConfirmer1Container">
                                            <select class="select2 selectSigner" id="selectConfirmer1" name="confirmer_1"></select>
                                        </div>
                                    </td>
                                    <td style="width: 14.2%;">
                                        <div id="selectReviewerContainer">
                                            <select class="select2 selectSigner" id="selectReviewer" name="reviewer"></select>
                                        </div>
                                    </td>
                                    <td style="width: 14.2%;">
                                        <select class="select2 selectSigner" id="selectApplicant" name="applicant">
                                            <option></option>
                                            @php
                                                foreach ($dataForm['signer']['APPLICANT'] as $row) {
                                                    $selected = (count($dataForm['signer']['APPLICANT']) == 1) ? ' selected=""' : '';
                                                    echo '<option value="'.$row['employeeId'].'"'.$selected.'>'.$row['employeeName'].'</option>';
                                                }
                                            @endphp
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="tab-pane fade w-100 h-100" id="comparisonFormContent" role="tabpanel" aria-labelledby="comparisonFormTab" tabindex="0">
    </div>
    <div class="tab-pane fade w-100 h-100" id="orderFormContent" role="tabpanel" aria-labelledby="orderFormTab" tabindex="0">
    </div>
</div>

@once
<script>
    loadApprovalFlow();
    $('.autosize').autosize().trigger('change');
    $('.currencyValue').trigger('change');
    dayjs.locale('id');
    dayjs.extend(window.dayjs_plugin_customParseFormat);
    var optionsDatePicker = {
        locale: 'en-US',
        inputDateFormat: date => dayjs(date).locale('en').format('DD-MM-YYYY'),
        inputDateParse: date => dayjs(date, 'DD-MM-YYYY', 'id').toDate(),
        // minDate: dayjs(new Date()),
        showAdjacementDays: false,
    }

    var optionsSubmittedDate = {
        locale: 'en-US',
        inputDateFormat: date => dayjs(date).locale('en').format('DD-MM-YYYY'),
        inputDateParse: date => dayjs(date, 'DD-MM-YYYY', 'id').toDate(),
        maxDate: dayjs(new Date()),
        showAdjacementDays: false,
    }

    new coreui.DatePicker(document.getElementById(`submittedDateApplication`), optionsSubmittedDate);
    new coreui.DatePicker(document.getElementById(`shipDateApplication`), optionsDatePicker);

    $('.selectSigner').select2({
        dropdownParent: $('#modalDocumentForm'),
        minimumResultsForSearch: Infinity,
        allowClear: false,
        placeholder: '-- Select --'
    });

    $('.selectNonClear').select2({
        dropdownParent: $('#modalDocumentForm'),
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

    $(`.selectCostCenter`).select2({
        dropdownParent: $('#modalDocumentForm'),
        allowClear: false,
        placeholder: '-- Select --',
        templateResult: formatResult,
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
        templateSelection: function (data) {
            const description1 = $(data.element).data('description1')?.toString();
            const description = (description1 !== undefined && description1 !== null) ? ` - ${description1}` : ``;
            return data.text+description || data.id;
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    $(`.selectDesc`).select2({
        dropdownParent: $('#modalDocumentForm'),
        allowClear: false,
        placeholder: '-- Select --',
        templateResult: formatResult,
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
        templateSelection: function (data) {
            const description1 = $(data.element).data('description1')?.toString();
            const description = (description1 !== undefined && description1 !== null) ? ` - ${description1}` : ``;
            return data.text+description || data.id;
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    $('.selectVat').select2({
        dropdownParent: $('#modalDocumentForm'),
        minimumResultsForSearch: Infinity,
        allowClear: true,
        placeholder: '-- Select --',
    });

    // $('#selectApprover').select2({dropdownParent: $('#modalDocumentForm'), allowClear: false, placeholder: '-- Select --'});
    $('#purchaseTypeApplication').select2({
        dropdownParent: $('#modalDocumentForm'), allowClear: false, placeholder: '-- Select --'
    }).on('select2:select', async function(e) {
        let selectedValue = e.params.data.id;
        let selectedOption = $('#purchaseTypeApplication option[value="'+selectedValue+'"]');
        let applicationType = selectedOption.data('applicationtype');
        let formatNumber = selectedOption.data('formatnumber');
        let budgetNo = selectedOption.data('budget');
        let formType = selectedOption.data('formtype');

        $('.budgetApplication').removeClass('is-invalid');
        $('.budgetAmountApplication').removeClass('is-invalid');
        $('.budgetWithinApplication').removeClass('is-invalid');
        $('.budgetApplication').parent().find('div.invalid-feedback').remove();
        $('.budgetAmountApplication').parent().find('div.invalid-feedback').remove();
        $('.budgetWithinApplication').parent().find('div.invalid-feedback').remove();

        if(budgetNo != '') {
            $(`#modalDocumentFormTitle`).html(applicationType);
        }
        else if($('#selectBudget1').val().trim() == '' || $('#selectBudget1').val().trim() == '-') {
            $(`#modalDocumentFormTitle`).html(applicationType);
        }
        else {
            $(`#modalDocumentFormTitle`).html(budgetNo);
        }

        $('#applicationHeader').val($(`#modalDocumentFormTitle`).html());
        $(`#formTypeApplication`).html(`<option value="${formType}" selected="selected">${formType}</option>`).trigger('change');
        loadApprovalFlow();
    });

    async function loadApprovalFlow() {
        let paymentType = $('.paymentTypeApplication:checked').val() || '';
        let purchaseType = $('#purchaseTypeApplication').val();
        let totalAmount = currencyToNumber($('#estimateApplication').val());

        if ($('#selectApprover').hasClass('select2-hidden-accessible')) {
            $('#selectApprover').val(null).empty().trigger('change');
        }
        if ($('#selectAcknowledge').hasClass('select2-hidden-accessible')) {
            $('#selectAcknowledge').val(null).empty().trigger('change');
        }
        if ($('#selectConfirmer2').hasClass('select2-hidden-accessible')) {
            $('#selectConfirmer2').val(null).empty().trigger('change');
        }
        if ($('#selectReviewAdmin').hasClass('select2-hidden-accessible')) {
            $('#selectReviewAdmin').val(null).empty().trigger('change');
        }
        if ($('#selectConfirmer1').hasClass('select2-hidden-accessible')) {
            $('#selectConfirmer1').val(null).empty().trigger('change');
        }
        if ($('#selectReviewer').hasClass('select2-hidden-accessible')) {
            $('#selectReviewer').val(null).empty().trigger('change');
        }
        $('#ruleIdInfo').html('');
        $('#ruleId').val('');

        if(paymentType != '' && purchaseType != '' && totalAmount > 0) {
            const params = {
                'dataForm': 'APPLICATION_PO_FORM',
                'dataType': 'APPROVAL_RULE_MATRIX',
                'companyId': $('#companyId').val(),
                'purchaseType': purchaseType,
                'paymentType': paymentType,
                'totalAmount': totalAmount,
            };
            const ruleMatrix = await getPoRuleMatrixControl(params);
            generatePoRuleMatrix(ruleMatrix);
        }
    }

    $(document).off('change', '#currency').on('change', '#currency', function (e) {
        $('.currencyText').html($(this).val());
        $('.currencyPercentageText ').html($(this).val());
        if($('#discountType').val() == 'NOMINAL') {
            $('.discountTypeText').html($(this).val());
        }
    });

    $(document).off('change', '#grandTotalVendor1').on('change', '#grandTotalVendor1', function (e) {
        $('#estimateApplication').val($(this).val()).trigger('change');
    });

    $(document).off('change', '#estimateApplication').on('change', '#estimateApplication', async function (e) {
        loadApprovalFlow();
    });

    $(document).off('input', '.budgetApplication').on('input', '.budgetApplication', function (e) {
        let selectedValue = $('#purchaseTypeApplication').val();
        let selectedOption = $('#purchaseTypeApplication option[value="'+selectedValue+'"]');
        let applicationType = selectedOption.data('applicationtype');
        let budgetNo = selectedOption.data('budget');
        let formType = selectedOption.data('formtype');

        if(budgetNo == '') {
            $(`#modalDocumentFormTitle`).html(applicationType);
        }
        else if($(this).val().trim() == '' || $(this).val().trim() == '-') {
            $(`#modalDocumentFormTitle`).html(applicationType);
        }
        else {
            $(`#modalDocumentFormTitle`).html(budgetNo);
        }

        $('#applicationHeader').val($(`#modalDocumentFormTitle`).html());
        $(`#formTypeApplication`).html(`<option value="${formType}" selected="selected">${formType}</option>`).trigger('change');
    });

    $('#selectDelivery').select2({
        dropdownParent: $('#modalDocumentForm'),
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
    }).on('select2:select', function(e) {
        let selectedValue = $(this).val();
        let selectedOption = $('#selectDelivery option[value="'+selectedValue+'"]');
        let deliveryAddress = selectedOption.data('description1');
        $('#deliveryApplication').toggleClass('d-block d-none');
        $('#deliveryAddressSkeleton').toggleClass('d-block d-none');

        setTimeout(() => {
            $('#deliveryAddressSkeleton').toggleClass('d-block d-none');
            $('#deliveryApplication').toggleClass('d-block d-none');
            $('#deliveryApplication').val(deliveryAddress).trigger('change');
        }, 600);
    });

    $('#selectInvoiceTo').select2({
        dropdownParent: $('#modalDocumentForm'),
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
    }).on('select2:select', function(e) {
        let selectedValue = $(this).val();
        let selectedOption = $('#selectInvoiceTo option[value="'+selectedValue+'"]');
        let invoiceToAddress = selectedOption.data('description1');
        $('#invoiceApplication').toggleClass('d-block d-none');
        $('#invoiceToAddressSkeleton').toggleClass('d-block d-none');

        setTimeout(() => {
            $('#invoiceToAddressSkeleton').toggleClass('d-block d-none');
            $('#invoiceApplication').toggleClass('d-block d-none');
            $('#invoiceApplication').val(invoiceToAddress).trigger('change');
        }, 600);
    });

    // var preselectedVendorId = @json($dataForm['vendorSelected'] ?? null);
    if(@json($dataForm['vendorSelected'])) {
        $('.selectReadonly').select2({
            dropdownParent: $('#modalDocumentForm'),
            minimumResultsForSearch: Infinity,
            allowClear: false,
            placeholder: '-- Select --',
        });
    }
    else {
        $('#vendorApplication').select2({
            dropdownParent: $('#modalDocumentForm'),
            minimumInputLength: 2,
            allowClear: false,
            placeholder: '-- Select --',
            ajax: {
                url : `/proc_pur/getVendor`,
                type: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                    'Referer': window.location.href
                },
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        data: 'SELECT_HEADER',
                        page: params.page || 1
                    }
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.map(row => ({
                            id: row.value,
                            text: row.label,
                            vendorPicId: row.vendorPicId,
                            vendorPicName: row.vendorPicName,
                            vendorAddress: row.vendorAddress,
                        })),
                        pagination: {
                            more: (params.page * 10) < data.count_filtered
                        }
                    }
                },
            }
        });

        // $('.nominalPercentageSelect').select2({
        //     dropdownParent: $('#modalDocumentForm'),
        //     minimumResultsForSearch: Infinity,
        //     allowClear: false,
        //     placeholder: '-- Select --',
        // });

        // var $nominalPercentageSelect = $('.nominalPercentageSelect').parent().find('span.select2-container--default').find('span.select2-selection--single');
        // $('.nominalPercentageSelect').parent().find('span.select2-container--default').addClass('text-end');
        // $nominalPercentageSelect.find('span.select2-selection__arrow').addClass('d-none');

        // $nominalPercentageSelect.find('span.select2-selection__rendered').css('cssText',
        //     'padding-left: 11px !important; ' +
        //     'padding-right: 11px !important; ' +
        //     'width: 42px !important'
        // );
        // $nominalPercentageSelect.css('cssText',
        //     'border-left: 0 !important; ' +
        //     'border-top-left-radius: 0 !important; ' +
        //     'border-bottom-left-radius: 0 !important'
        // );
    }

    $(document).off('change', '#vendorApplication').on('change', '#vendorApplication', async function (e) {
        let $this = $(this);
        if ($('#vendorPicApplication').hasClass('select2-hidden-accessible')) {
            $('#vendorPicApplication').select2('destroy');
        }

        $('#vendorAdrressApplicationContainer').html('<div class="skeleton textarea mb-0" id="vendorAddressApplication"></div>');
        $('#vendorPicApplication').replaceWith('<div class="skeleton mb-0" id="vendorPicApplication"></div>');

        if(!$this.val()) {
            $('#vendorAddressApplication').replaceWith('<textarea class="form-control autosize" data-limit-rows="true" rows="2" spellcheck="false" maxlength="255" id="vendorAddressApplication" name="vendorAddressApplication" style="height: 53px;" readonly></textarea>');
            $('#vendorPicApplication').replaceWith('<input type="text" class="form-control" name="vendorPicApplication" id="vendorPicApplication" value="" readonly>');

            return;
        }

        const params = {
            'id': $this.val(),
            'type': 'ROW_DETAILS',
        };

        const getDetails = await getVendorDetails(params);
        const vendorAddress = getDetails.vendorAddress;
        const vendorPic = getDetails.vendorPic;
        const vendorAdrressApplicationContainer = $('#vendorAdrressApplicationContainer');
        if (vendorAddress.length > 0) {
            vendorAdrressApplicationContainer.html('');
            let checked = (vendorAddress.length == 1) ? 'checked' : '';
            vendorAddress.forEach((item, index) => {
                const div = document.createElement('div');
                div.classList.add('py-2');
                if (index > 0) {
                    div.classList.add('border-top');
                }

                div.innerHTML = `<div class="data-details">
                                    <div class="data-row">
                                        <label class="text-wrap lh-base d-block" style="padding-left: 1.5rem; text-indent: -1.6rem;cursor:pointer">
                                            <div class="white-space-pre"><input type="checkbox" class="form-check-input me-2 vendorAddressApplication" name="vendorAddressApplication" value="${item.addressId}" ${checked}>${item.addressName}</div>
                                        </label>
                                    </div>
                                </div>`;
                vendorAdrressApplicationContainer.append(div);
            });
        }
        else {
            vendorAdrressApplicationContainer.html('<textarea class="form-control autosize" data-limit-rows="true" rows="2" spellcheck="false" maxlength="255" id="vendorAddressApplication" name="vendorAddressApplication" style="height: 53px;" readonly>No Address data</textarea>');
        }

        if (vendorPic.length > 0) {
            let optionPic = [];
            let selected = vendorPic.length == 1 ?? false;
            vendorPic.forEach((item, index) => {
                optionPic.push({
                    id: item.picId,
                    text: item.picName,
                    description1: `Email : ${(item.picEmail) ? item.picEmail : '--'}`,
                    selected: selected
                });
            });

            $('#vendorPicApplication').replaceWith(`<select class="select2" name="vendorPicApplication" id="vendorPicApplication"><option></option></select>`);
            $('#vendorPicApplication').select2({
                dropdownParent: $('#filterAside'),
                allowClear: false,
                placeholder: '-- Select --',
                data: optionPic,
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
        else {
            $('#vendorPicApplication').replaceWith('<input type="text" class="form-control" name="vendorPicApplication" id="vendorPicApplication" value="" readonly>');
        }

    });

    $(document).off('change', '.paymentTypeApplication').on('change', '.paymentTypeApplication', async function (e) {
        if ($(this).is(':checked')) {
            $('.paymentTypeApplication').not(this).prop('checked', false);
        }

        loadApprovalFlow();
    });

    $(document).off('input', '.budgetAmountApplication').on('input', '.budgetAmountApplication', function (e) {
        let $this= $(this);
        const parent = $(this).closest('div.budgetAmountItem');
        const vat = currencyToNumber($('#vat').val());
        const pph = currencyToNumber($('#pph').val());
        const deliveryFee = currencyToNumber($('#deliveryFee').val());
        if($this.val() == '' || $this.val() == '-') {
            parent.find('.budgetWithinInput').val('Within');
            parent.find('.budgetWithinLabel').html('Within');
            parent.find('.budgetWithinApplication').val('').trigger('change');
        }
        else {
            let withinOver;
            let budgetInput;
            if ($this.val().includes('(') || $this.val().includes('-')) {
                budgetInput = $this.val().replace('(', '-');
                budgetInput = budgetInput.replace(')', '');

            }
            else {
                budgetInput = $this.val();
            }

            let budget = currencyToNumber(budgetInput);
            let totalPrice = currencyToNumber($('#grandTotalVendor1').val());
            let remainingBudget = (budget - totalPrice) + vat - pph;
            let formattedBudget = Math.abs(remainingBudget).toLocaleString('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });

            if (remainingBudget < 0) {
                formattedBudget = `(${formattedBudget})`;
                withinOver = 'Over';
            }
            else {
                withinOver = 'Within';
            }

            parent.find('.budgetWithinInput').val(withinOver);
            parent.find('.budgetWithinLabel').html(withinOver);
            parent.find('.budgetWithinApplication').val(formattedBudget);
            // TAMBAHKAN DISINI HEADER JIKA OVER BUDGET
        }
    });

    $(document).off('click', '#addBudget').on('click', '#addBudget', function (e) {
        if($('.budgetRow').length > 3) {
            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : Maximum 4 Budget Number` });
            return;
        }

        let options = '<option></option>';
        @json($dataForm['budgetList']).forEach((element, index) => {
            options += `<option value="${element.id}" data-description1="${element.description1}">${element.text}</option>`;
        });

        let id = Math.random().toString(16).slice(2);
        $(this).closest('.row').parent().append(`<div class="row mt-2">
                                            <div class="col-10 pe-0 budgetRow d-flex gap-2" id="budgetNo_${id}">
                                                <div class="col-12 pe-0 budgetContainer">
                                                    <input type="text" class="form-control budgetApplication" id="selectBudget" name="budgetApplication[]">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <button type="button" class="btn btn-default float-end removeBudget" data-id="${id}"><i class="fa-regular fa-trash-can-xmark"></i></button>
                                            </div>
                                        </div>`);

        $('#budgetAmountContainer').append(`<div class="row mt-2 budgetAmountItem" id="budgetAmount_${id}">
                                                <div class="col-5">
                                                    <input type="text" class="form-control currencyValue budgetAmountApplication" name="budgetAmountApplication[]" spellcheck="false" autocomplete="off">
                                                </div>
                                                <div class="col-2 pe-0">
                                                    <input type="hidden" class="budgetWithinInput" name="budgetWithinInput[]" value="Within" readonly>
                                                    <label for="budgetWithinApplication" class="col-form-label label-inline-input budgetWithinLabel">Within</label>
                                                </div>
                                                <div class="col-5">
                                                    <input type="text" class="form-control currencyValue d-flex budgetWithinApplication" name="budgetWithinApplication[]" spellcheck="false" autocomplete="off" readonly>
                                                </div>
                                            </div>`);

        $(`.selectBudget`).select2({
            dropdownParent: $('#modalDocumentForm'),
            allowClear: false,
            placeholder: '-- Select --',
            templateResult: formatResult,
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
            templateSelection: function (data) {
                const description1 = $(data.element).data('description1')?.toString();
                const description = (description1 !== undefined && description1 !== null) ? ` - ${description1}` : ``;
                return data.text+description || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });
    });

    $(document).off('click', '#addCostCenter').on('click', '#addCostCenter', function (e) {
        if($('.costCenterRow').length > 9) {
            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : Maximum 10 Cost Center` });
            return;
        }

        let options = '<option></option>';
        @json($dataForm['costCenter']).forEach((element, index) => {
            options += `<option value="${element.id}" data-description1="${element.description1}">${element.text}</option>`;
        });

        $(this).closest('.row').parent().append(`<div class="row mt-2">
                                            <div class="col-10 pe-0 costCenterRow d-flex gap-2">
                                                <div class="col-8 pe-0 costCenterContainer">
                                                    <select class="select2 selectCostCenter" name="costCenterApplication[]">
                                                        ${options}
                                                    </select>
                                                </div>
                                                <div class="col-4 pe-0">
                                                    <div class="form-group d-flex align-items-center position-relative">
                                                        <input type="text" class="form-control d-flex numberValue" name="costCenterPercentApplication[]" spellcheck="false" autocomplete="off" style="padding-right: 33px;" maxlength="6">
                                                        <span class="position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <button type="button" class="btn btn-default float-end removeCostCenter"><i class="fa-regular fa-trash-can-xmark"></i></button>
                                            </div>
                                        </div>`);

        $(`.selectCostCenter`).select2({
            dropdownParent: $('#modalDocumentForm'),
            allowClear: false,
            placeholder: '-- Select --',
            templateResult: formatResult,
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
            templateSelection: function (data) {
                const description1 = $(data.element).data('description1')?.toString();
                const description = (description1 !== undefined && description1 !== null) ? ` - ${description1}` : ``;
                return data.text+description || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });
    });

    $(document).off('click', '.removeBudget').on('click', '.removeBudget', function (e) {
        let id = $(this).data('id');
        $(`#budgetNo_${id}`).closest('.row').remove();
        $(`#budgetAmount_${id}`).remove();
    });

    $(document).off('click', '.removeCostCenter').on('click', '.removeCostCenter', function (e) {
        $(this).closest('.row').remove();
    });

    $(document).off('click', '#addMasterDelivery').on('click', '#addMasterDelivery', function (e) {
        $('.aside-title').html('New Delivery');
        $('.aside-content').html(`<div class="alert alert-info mb-3 w-100 p-2" id="alertFormAction" role="alert">
                                    1. Nama <span class="fw-semibold">“PT”</span> tidak perlu dituliskan dengan tanda titik (.)<br>
                                    2. Isi <span class="fw-semibold">“Address”</span> dengan dua baris (tekan Enter untuk baris baru)
                                </div>
                                <form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
                                    <input type="hidden" name="companyId" value="${$('#companyIdEncode').val()}">
                                    <div class="form-group mb-3">
                                        <label for="companyNameDelivery" class="form-label">Delivery Name<span class="required"></span> :</label>
                                        <input type="text" class="form-control" id="companyNameDelivery" name="companyNameDelivery" maxlength="50" spellcheck="false" autocomplete="off">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="companyDeliveryAddress" class="form-label">Delivery Address <span class="fst-italic fw-normal fs-8">(Enter Address in Two Line)</span><span class="required"></span> :</label>
                                        <textarea class="form-control autosize" data-limit-rows="true" rows="2" spellcheck="false" maxlength="255" id="companyDeliveryAddress" name="companyDeliveryAddress"></textarea>
                                    </div>
                                </form>`);

        $('.overlay-aside').addClass('show').trigger('shown');
        $('#globalAside').addClass('show aside-lg').trigger('shown');
        $('.autosize').autosize().trigger('change');
        $('body').addClass('overflow-hidden');

        $('.asideFooterBtn').html(`<div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
            <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside asideBtn" title="Close">Close</button>
        </div>
        <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
            <button type="button" class="btn btn-info w-100 w-md-auto asideBtn" id="saveNewDelivery" data-type="" data-item=""></i> Save</button>
        </div>`);
    });

    $(document).off('click', '#saveNewDelivery').on('click', '#saveNewDelivery', function (e) {
        Snackbar.close();
        clearValidation();
        event.preventDefault();
        let $this = $(this);
        const formData = new FormData($('#asideForm')[0]);
        formData.append('type', $this.attr('data-type'));

        $(this).btnLoading(async function() {
            if ($this.attr('data-type') === 'EDIT') {
                formData.append('_method', 'PUT');
            }

            try {
                $('.asideBtn').prop('disabled', true);
                $this.html('<i class="fas fa-spinner fa-spin"></i> Please wait');
                const response = await fetch('/proc_pur/saveDelivery', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Referer': window.location.href,
                    }
                });

                const result = await response.json();
                if (response.status === 200) {
                    $('#selectDelivery').val(null).empty();

                    try {
                        const response = await fetch('/proc_pur/getDelivery?type=GOODS_DELIVERY&search=&data=ALL', {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json',
                                'Referer': window.location.href,
                            }
                        });

                        const resultDelivery = await response.json();
                        if (response.status === 200) {
                            let option = '<option></option>';
                            resultDelivery.forEach(row => {
                                option += `<option value="${row.deliveryToId}" data-description1="${row.deliveryAddress}">${row.deliveryName}</option>`;
                            });

                            $('#selectDelivery').html(option).trigger('change');
                            $('#selectDelivery').focus();
                            asideHide();
                            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result['message']}`});
                        }
                    }
                    catch (error) {
                        Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}` });
                    }
                }
                else if (response.status === 401) {
                    Snackbar.show({ pos: 'bottom-center', duration: '6000', text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Unauthorized access, plase login or refresh this page' });
                }
                else if (response.status === 422) {
                    handleValidationErrors(result.errors);
                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : ${response.statusText}` });
                }
                else if (response.status === 419) {
                    handleValidationErrors(result.errors);
                    Snackbar.show({ pos: 'bottom-center', text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : CSRF token mismatch, please refresh this page' });
                }
                else {
                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed ${response.status} : ${response.statusText}` });
                }
            }
            catch (error) {
                Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}` });
            }
            finally {
                $('.asideBtn').prop('disabled', false);
            }
        });
    });
</script>
@endonce