<ul class="nav nav-pills nav-pills-fixed pt-2 mb-2" id="pillsTabViewForm" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link pillTabDetailForm px-3 py-1 viewFormButton active" id="comparisonFormTab" data-form="FORM" data-scroll-top="" data-coreui-toggle="pill" data-coreui-target="#comparisonFormContent" type="button" role="tab" aria-controls="comparisonFormContent" aria-selected="false" tabindex="-1">Comparison Form</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link pillTabDetailForm px-3 py-1 viewFormButton" id="orderFormTab" data-form="ORDER_FORM_TAB" data-token="{{ $dataForm['tokenForm'] }}" data-scroll-top="" data-coreui-toggle="pill" data-coreui-target="#orderFormContent" type="button" role="tab" aria-controls="orderFormContent" aria-selected="true">Order Form</button>
    </li>
</ul>
<div class="tab-content h-100" id="pillsTabContentForm">
    <div class="tab-pane fade active show" id="comparisonFormContent" role="tabpanel" aria-labelledby="comparisonFormTab" tabindex="0">
        <form role="form" class="form-horizontal" enctype="multipart/form-data" id="formDocumentForm">
            @csrf
            @php
                $currency = 'IDR';
                foreach ($dataForm['itemForm'] as $row) {
                    $currency = $row['currency'];
                    break;
                }
            @endphp
            <input type="hidden" name="tokenForm" value="{{$dataForm['tokenForm']}}">
            <input type="hidden" name="documentType" id="documentTypeForm" value="{{$dataForm['documentType']}}">
            <div class="row mb-2 pt-2 pb-1">
                <div class="col-sm-12 col-md-7">
                    <div class="row row-multi-col bg-white mb-2 py-2">
                        <input type="hidden" name="yearForm" value="{{$dataForm['yearForm']}}">
                        <input type="hidden" id="currency" name="currency" value="{{$currency}}">
                        <input type="hidden" name="requestType" id="requestTypeForm" value="{{$dataForm['type']}}">
                        <div class="row form-group m-0 mb-md-1 px-0 py-1">
                            <label for="" class="col-sm-12 col-md-3 col-form-label label-inline-input"><div class="d-inline">Comparison for<span class="required"></span></div></label>
                            <div class="col-sm-12 col-md-9">
                                <input type="text" class="form-control mb-1" name="comparisonTitle" value="{{$dataForm['comparisonTitle']}}" placeholder="Comparison title...">
                            </div>
                        </div>

                        <div class="row form-group m-0 mb-md-1 px-0 py-1">
                            <label for="" class="col-sm-12 col-md-3 col-form-label label-inline-input"><div class="d-inline">For The Month of<span class="required"></span></div></label>
                            <div class="col-sm-12 col-md-9">
                                <input type="text" class="form-control mb-1" name="comparisonDescription" value="{{$dataForm['comparisonDescription']}}" placeholder="Comparison description...">
                            </div>
                        </div>

                        <div class="row form-group m-0 mb-md-1 px-0 py-1">
                            <label for="" class="col-sm-12 col-md-3 col-form-label label-inline-input"><div class="d-inline">Comparison Date<span class="required"></span></div></label>
                            <div class="col-sm-12 col-md-9">
                                <div class="form-group mb-1 date-picker" id="comparisonDate" data-coreui-date="{{$dataForm['comparisonDate']}}" data-coreui-name="comparisonDate"></div>
                            </div>
                        </div>

                        {{-- <div class="row form-group m-0 mb-md-1 px-0 py-1">
                            <label for="" class="col-sm-12 col-md-3 col-form-label label-inline-input">Validity</label>
                            <div class="col-sm-12 col-md-9">
                                <input type="text" class="form-control" name="comparisonValidity" value="{{$dataForm['validity']}}" placeholder="Validity...">
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
            <div class="col-12 mb-2">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <div class="dropdown">
                        @php
                            echo '<button type="button" class="btn btn-default btn-sm fs-8 fw-medium dropdown-toggle" data-bs-boundary="viewport" data-coreui-toggle="dropdown" data-coreui-auto-close="outside" aria-expanded="false" title="Modify Order Item"><i class="fa-solid fa-bars-staggered fa-fw"></i> Modify Order Item</button>

                            <ul class="dropdown-menu" aria-labelledby="">
                                <li><label class="dropdown-item modifyItem" data-form="COMPARISON" data-type="ADD" data-currency="'.$currency.'">Add Item</label></li>
                                <li><label class="dropdown-item modifyItem" data-form="COMPARISON" data-type="EDIT">Edit Item</label></li>
                                <div class="dropdown-divider"></div>
                                <li><label class="dropdown-item modifyItem" data-form="COMPARISON" data-type="COMBINE">Combine Order Form</label></li>
                            </ul>';
                        @endphp
                    </div>
                    {{-- <button type="button" class="btn btn-default btn-sm fs-8 fw-medium masterVendor" data-type="NEW"><i class="fa-regular fa-plus fa-fw"></i> New Master Vendor</button> --}}
                </div>
            </div>
            <div class="table-responsive mb-2">
                <table class="table table-hover table-form" id="tableComparison">
                    <thead>
                        <tr>
                            <th rowspan="2"><div style="width: 15px;">#</div></th>
                            <th rowspan="2"><div style="width: 15px;">No.</div></th>
                            <th rowspan="2"><div style="width: 250px">Criteria</div></th>
                            <th rowspan="2" colspan="2"><div style="width: 150px">Qty</div></th>
                            <th colspan="2" style="background-color: #f3fff1; vertical-align: top;">
                                <div class="form-group d-flex gap-1" style="width: 300px">
                                    <div style="width: 265px">
                                        <select class="select2 selectVendor" id="selectVendor1" name="selectVendor[]" data-vendor="1"></select>
                                        <div class="form-group date-picker mt-1" id="validity1" name="validity[]" data-coreui-date="" data-coreui-name="validity[]"></div>
                                    </div>
                                    <button class="btn btn-sm btn-transparent masterVendor" type="button" data-type="REFERENCE" data-vendor="1" data-sortby="VENDOR_NAME" title="Vendor Reference">
                                        <i class="fa-solid fa-search fa-lg"></i>
                                    </button>
                                </div>
                            </th>
                            <th colspan="2" style="background-color: #fffbe3; vertical-align: top;">
                                <div class="form-group d-flex gap-1" style="width: 300px">
                                    <div style="width: 265px">
                                        <select class="select2 selectVendor" id="selectVendor2" name="selectVendor[]" data-vendor="2"></select>
                                        <div class="form-group date-picker mt-1" id="validity2" name="validity[]" data-coreui-date="" data-coreui-name="validity[]"></div>
                                    </div>
                                    <button class="btn btn-sm btn-transparent masterVendor" type="button" data-type="REFERENCE" data-vendor="2" data-sortby="VENDOR_NAME" title="Vendor Reference">
                                        <i class="fa-solid fa-search fa-lg"></i>
                                    </button>
                                </div>
                            </th>
                            <th colspan="2" style="background-color: #fff0f0; vertical-align: top;">
                                <div class="form-group d-flex gap-1" style="width: 300px">
                                    <div style="width: 265px">
                                        <select class="select2 selectVendor" id="selectVendor3" name="selectVendor[]" data-vendor="3"></select>
                                        <div class="form-group date-picker mt-1" id="validity3" name="validity[]" data-coreui-date="" data-coreui-name="validity[]"></div>
                                    </div>
                                    <button class="btn btn-sm btn-transparent masterVendor" type="button" data-type="REFERENCE" data-vendor="3" data-sortby="VENDOR_NAME" title="Vendor Reference">
                                        <i class="fa-solid fa-search fa-lg"></i>
                                    </button>
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th style="width: 150px; background-color: #f3fff1">Unit Price</th>
                            <th style="width: 150px; background-color: #f3fff1">Total</th>
                            <th style="width: 150px; background-color: #fffbe3">Unit Price</th>
                            <th style="width: 150px; background-color: #fffbe3">Total</th>
                            <th style="width: 150px; background-color: #fff0f0">Unit Price</th>
                            <th style="width: 150px; background-color: #fff0f0">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $comparisonIdVendor1 = $comparisonIdVendor2 = $comparisonIdVendor3 = null;
                            if($dataForm['resetComparisonItem'] == false) {
                                foreach ($dataForm['arrVendor'] as $index => $rowVendor) {
                                    ${"comparisonIdVendor".($index + 1)} = $rowVendor['comparisonVendorId'];
                                }
                            }

                            $x = 0;
                            foreach ($dataForm['itemForm'] as $row) {
                                if($row['orderDetailId'] != '') {
                                    $x++;
                                    $unitPriceVendor1 = $subtotalPriceVendor1 = $unitPriceVendor2 = $subtotalPriceVendor2 = $unitPriceVendor3 = $subtotalPriceVendor3 = '';
                                    if($row['comparisonItemId'] != null) {
                                        $comparisonItemId = $row['comparisonItemId'];
                                        $unitPriceVendor1 = ($comparisonIdVendor1 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor1][$comparisonItemId]['unitPrice'] : '';
                                        $subtotalPriceVendor1 = ($comparisonIdVendor1 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor1][$comparisonItemId]['totalPrice'] : '';
                                        $unitPriceVendor2 = ($comparisonIdVendor2 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor2][$comparisonItemId]['unitPrice'] : '';
                                        $subtotalPriceVendor2 = ($comparisonIdVendor2 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor2][$comparisonItemId]['totalPrice'] : '';
                                        $unitPriceVendor3 = ($comparisonIdVendor3 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor3][$comparisonItemId]['unitPrice'] : '';
                                        $subtotalPriceVendor3 = ($comparisonIdVendor3 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor3][$comparisonItemId]['totalPrice'] : '';
                                    }

                                    echo '<tr id="rowItem'.$x.'" class="rowItem rowItemNumber" data-id="'.$x.'" data-label="'.$row['applianceItem'].'">
                                            <td class="text-center td-form">
                                                <input type="hidden" class="colNo" name="colNo[]" value="'.$x.'">
                                                <div class="d-flex justify-content-between">
                                                    <button class="btn btn-sm btn-transparent modifyRowBtn" type="button" title="Remove Item" data-type="REMOVE" data-form="COMPARISON" data-row="'.$x.'">
                                                        <i class="fa-regular fa-trash-can-xmark fa-lg"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-center td-form">
                                                <div class="d-flex justify-content-between">
                                                    <span class="colNoLabel mx-1">'.$x.'</span>
                                                </div>
                                            </td>
                                            <td class="td-form">
                                                <input type="hidden" class="reviseOrderDetailId" id="reviseOrderDetailId'.$x.'" name="reviseOrderDetailId[]" value="'.$row['reviseOrderDetailId'].'">
                                                <input type="hidden" class="colCriteriaId" id="colCriteriaId'.$x.'" name="colCriteriaId[]" value="'.$row['orderDetailId'].'">
                                                <input type="hidden" class="colCriteria" id="colCriteria'.$x.'" name="colCriteria[]" value="'.$row['applianceItem'].'"><span class="colCriteria">'.$row['applianceItem'].'</span>
                                            </td class="td-form">
                                            <td class="text-center td-form" data-item="'.$x.'">
                                                <input type="hidden" class="colQty" id="colQty'.$x.'" name="colQty[]" value="'.$row['unitQuantity'].'"><span class="colQty">'.$row['unitQuantity'].'</span>
                                            </td>
                                            <td class="text-center td-form">
                                                <input type="hidden" class="colUnit" id="colUnit'.$x.'" name="colUnit[]" value="'.$row['unitName'].'"><span class="colUnit">'.$row['unitName'].'</span>
                                            </td>
                                            <td class="td-form" style="background-color: #f3fff1;">
                                                <div class="form-group d-flex align-items-center position-relative">
                                                    <input type="hidden" class="unitCurrency" name="currency1[]" value="'.$row['currency'].'">
                                                    <input type="text" class="form-control text-end d-flex currencyValue countAmount compareVendor1" name="unitPrice1[]" data-vendor="1" data-item="'.$x.'" data-qty="true" spellcheck="false" autocomplete="off" value="'.$unitPriceVendor1.'" style="padding-right: 33px;">
                                                    <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$row['currency'].'</span>
                                                </div>
                                            </td>
                                            <td class="td-form" style="background-color: #f3fff1;">
                                                <div class="form-group d-flex align-items-center position-relative">
                                                    <input type="text" class="form-control text-end d-flex no-input currencyValue subTotalPrice subTotalPrice1 compareVendor1 subTotalVendor1" id="subTotalVendor1_'.$x.'" data-vendor="1" name="subTotalPrice[]" data-item="'.$x.'" spellcheck="false" autocomplete="off" style="padding-right: 33px;" value="'.$subtotalPriceVendor1.'" readonly="" tabindex="-1">
                                                    <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$row['currency'].'</span>
                                                </div>
                                            </td>
                                            <td class="td-form" style="background-color: #fffbe3;">
                                                <div class="form-group d-flex align-items-center position-relative">
                                                    <input type="hidden" class="unitCurrency" name="currency2[]" value="'.$row['currency'].'">
                                                    <input type="text" class="form-control text-end d-flex currencyValue countAmount compareVendor2" name="unitPrice2[]" data-vendor="2" data-item="'.$x.'" data-qty="true" spellcheck="false" autocomplete="off" value="'.$unitPriceVendor2.'" style="padding-right: 33px;">
                                                    <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$row['currency'].'</span>
                                                </div>
                                            </td>
                                            <td class="td-form" style="background-color: #fffbe3;">
                                                <div class="form-group d-flex align-items-center position-relative">
                                                    <input type="text" class="form-control text-end d-flex no-input currencyValue subTotalPrice subTotalPrice2 compareVendor2 subTotalVendor2" id="subTotalVendor2_'.$x.'" data-vendor="2" name="subTotalPrice[]" data-item="'.$x.'" spellcheck="false" autocomplete="off" style="padding-right: 33px;" value="'.$subtotalPriceVendor2.'" readonly="" tabindex="-1">
                                                    <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$row['currency'].'</span>
                                                </div>
                                            </td>
                                            <td class="td-form" style="background-color: #fff0f0">
                                                <div class="form-group d-flex align-items-center position-relative">
                                                    <input type="hidden" class="unitCurrency" name="currency3[]" value="'.$row['currency'].'">
                                                    <input type="text" class="form-control text-end d-flex currencyValue countAmount compareVendor3" name="unitPrice3[]" data-vendor="3" data-item="'.$x.'" data-qty="true" spellcheck="false" autocomplete="off" value="'.$unitPriceVendor3.'" style="padding-right: 33px;">
                                                    <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$row['currency'].'</span>
                                                </div>
                                            </td>
                                            <td class="td-form" style="background-color: #fff0f0">
                                                <div class="form-group d-flex align-items-center position-relative">
                                                    <input type="text" class="form-control text-end d-flex no-input currencyValue subTotalPrice subTotalPrice3 compareVendor3 subTotalVendor3" id="subTotalVendor3_'.$x.'" data-vendor="3" name="subTotalPrice[]" data-item="'.$x.'" spellcheck="false" autocomplete="off" style="padding-right: 33px;" value="'.$subtotalPriceVendor3.'" readonly="" tabindex="-1">
                                                    <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$row['currency'].'</span>
                                                </div>
                                            </td>
                                        </tr>';
                                }
                            }

                            $y = $x - 1;
                            // if($comparisonIdVendor1 != null) {
                                $comparisonItemId = $dataForm['itemForm'][($y + 1)]['comparisonItemId'];
                                $deliveryVendor1 = ($comparisonIdVendor1 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor1][$comparisonItemId]['unitPrice'] : '';
                                $subtotalDeliveryVendor1 = ($comparisonIdVendor1 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor1][$comparisonItemId]['totalPrice'] : '';
                                $deliveryVendor2 = ($comparisonIdVendor2 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor2][$comparisonItemId]['unitPrice'] : '';
                                $subtotalDeliveryVendor2 = ($comparisonIdVendor2 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor2][$comparisonItemId]['totalPrice'] : '';
                                $deliveryVendor3 = ($comparisonIdVendor3 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor3][$comparisonItemId]['unitPrice'] : '';
                                $subtotalDeliveryVendor3 = ($comparisonIdVendor3 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor3][$comparisonItemId]['totalPrice'] : '';

                                $comparisonItemId = $dataForm['itemForm'][($y + 2)]['comparisonItemId'];
                                $discountVendor1 = ($comparisonIdVendor1 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor1][$comparisonItemId]['unitPrice'] : '';
                                $subtotalDiscountVendor1 = ($comparisonIdVendor1 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor1][$comparisonItemId]['totalPrice'] : '';
                                $discountVendor2 = ($comparisonIdVendor2 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor2][$comparisonItemId]['unitPrice'] : '';
                                $subtotalDiscountVendor2 = ($comparisonIdVendor2 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor2][$comparisonItemId]['totalPrice'] : '';
                                $discountVendor3 = ($comparisonIdVendor3 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor3][$comparisonItemId]['unitPrice'] : '';
                                $subtotalDiscountVendor3 = ($comparisonIdVendor3 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor3][$comparisonItemId]['totalPrice'] : '';

                                $comparisonItemId = $dataForm['itemForm'][($y + 5)]['comparisonItemId'];
                                if(!empty($comparisonItemId)) {
                                    $pphVendor1 = ($comparisonIdVendor1 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor1][$comparisonItemId]['unitPrice'] : '';
                                    $subtotalPphVendor1 = ($comparisonIdVendor1 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor1][$comparisonItemId]['totalPrice'] : '';
                                    $pphVendor2 = ($comparisonIdVendor2 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor2][$comparisonItemId]['unitPrice'] : '';
                                    $subtotalPphVendor2 = ($comparisonIdVendor2 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor2][$comparisonItemId]['totalPrice'] : '';
                                    $pphVendor3 = ($comparisonIdVendor3 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor3][$comparisonItemId]['unitPrice'] : '';
                                    $subtotalPphVendor3 = ($comparisonIdVendor3 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor3][$comparisonItemId]['totalPrice'] : '';
                                }
                                else {
                                    $pphVendor1 = $subtotalPphVendor1 = $pphVendor2 = $subtotalPphVendor2 = $pphVendor3 = $subtotalPphVendor3 = '';
                                }

                                $comparisonItemId = $dataForm['itemForm'][($y + 4)]['comparisonItemId'];

                                // $vatVendor1 = ($comparisonIdVendor1 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor1][$comparisonItemId]['unitPrice'] : '';

                                $arrVatRate = [
                                                '1.1' => '1.1%',
                                                '1.2' => '1.2%',
                                                '11' => '11%',
                                                '12x11/12' => '12% x 11/12',
                                                '12' => '12%',
                                            ];

                                $optionVatRate1 = '<option></option>';
                                $vatVendor1 = $dppOtherValue1 = null;
                                if (!empty($comparisonIdVendor1) && !empty($comparisonItemId)) {
                                    $vatVendor1 = ($comparisonIdVendor1 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor1][$comparisonItemId]['unitPrice'] : '';
                                    $dppOtherValue1 = $dataForm['arrItemVendor'][$comparisonIdVendor1][$comparisonItemId]['dppOtherValue'];
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

                                $optionVatRate2 = '<option></option>';
                                $vatVendor2 = $dppOtherValue2 = null;
                                if (!empty($comparisonIdVendor2) && !empty($comparisonItemId)) {
                                    $vatVendor2 = ($comparisonIdVendor2 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor2][$comparisonItemId]['unitPrice'] : '';
                                    $dppOtherValue2 = $dataForm['arrItemVendor'][$comparisonIdVendor2][$comparisonItemId]['dppOtherValue'];
                                }

                                foreach ($arrVatRate as $vatRateKey => $vatRateValue) {
                                    $selected = '';
                                    if($dppOtherValue2 == null && $vatVendor2 != null && $vatVendor2 == $vatRateKey) {
                                        $selected = ' selected=""';
                                    }
                                    else if ($vatVendor2.'x'.$dppOtherValue2 == $vatRateKey){
                                        $selected = ' selected=""';
                                    }

                                    $optionVatRate2 .= '<option value="'.$vatRateKey.'"'.$selected.'>'.$vatRateValue.'</option>';
                                }

                                $optionVatRate3 = '<option></option>';
                                $vatVendor3 = $dppOtherValue3 = null;
                                if (!empty($comparisonIdVendor3) && !empty($comparisonItemId)) {
                                    $vatVendor3 = ($comparisonIdVendor3 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor3][$comparisonItemId]['unitPrice'] : '';
                                    $dppOtherValue3 = $dataForm['arrItemVendor'][$comparisonIdVendor3][$comparisonItemId]['dppOtherValue'];
                                }

                                foreach ($arrVatRate as $vatRateKey => $vatRateValue) {
                                    $selected = '';
                                    if($dppOtherValue3 == null && $vatVendor3 != null && $vatVendor3 == $vatRateKey) {
                                        $selected = ' selected=""';
                                    }
                                    else if ($vatVendor3.'x'.$dppOtherValue3 == $vatRateKey){
                                        $selected = ' selected=""';
                                    }

                                    $optionVatRate3 .= '<option value="'.$vatRateKey.'"'.$selected.'>'.$vatRateValue.'</option>';
                                }

                                $subtotalVatVendor1 = (!empty($comparisonIdVendor1) && !empty($comparisonItemId)) ? $dataForm['arrItemVendor'][$comparisonIdVendor1][$comparisonItemId]['totalPrice'] : '';
                                // $vatVendor2 = ($comparisonIdVendor2 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor2][$comparisonItemId]['unitPrice'] : '';
                                $subtotalVatVendor2 = (!empty($comparisonIdVendor2) && !empty($comparisonItemId)) ? $dataForm['arrItemVendor'][$comparisonIdVendor2][$comparisonItemId]['totalPrice'] : '';
                                // $vatVendor3 = ($comparisonIdVendor3 != null) ? $dataForm['arrItemVendor'][$comparisonIdVendor3][$comparisonItemId]['unitPrice'] : '';
                                $subtotalVatVendor3 = (!empty($comparisonIdVendor3) && !empty($comparisonItemId)) ? $dataForm['arrItemVendor'][$comparisonIdVendor3][$comparisonItemId]['totalPrice'] : '';
                                // dd($deliveryVendor1);
                            // }
                            echo '<tr id="rowItema" class="rowItemString" data-id="a" data-label="Delivery Fee">
                                        <td class="text-center align-middle td-form"></td>
                                        <td class="text-center align-middle td-form">
                                            <input type="hidden" class="colNo" name="colNo[]" value="a"><span class="colNoLabel">a</span>
                                        </td>
                                        <td class="align-middle td-form">
                                            <input type="hidden" class="colCriteriaId" name="colCriteriaId[]" value="'.$dataForm['itemForm'][($y + 1)]['orderDetailId'].'">
                                            <input type="hidden" class="colCriteria" name="colCriteria[]" value="Delivery Fee"><span class="colCriteria">Delivery Fee</span>
                                        </td>
                                        <td class="td-form">
                                            <input type="hidden" class="colQty" name="colQty[]" value="'.$dataForm['itemForm'][($y + 1)]['unitQuantity'].'"><span class="colQty"></span>
                                        </td>
                                        <td class="td-form">
                                            <input type="hidden" class="colUnit" name="colUnit[]" value="'.$dataForm['itemForm'][($y + 1)]['unitName'].'"><span class="colUnit"></span>
                                        </td>
                                        <td class="td-form" style="background-color: #f3fff1;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex currencyValue countAmount compareVendor1" id="deliveryFeeVendor1" name="deliveryFee[]" data-vendor="1" data-item="a" data-qty="false" spellcheck="false" autocomplete="off" value="'.$deliveryVendor1.'" style="padding-right: 33px;">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                        <td class="td-form" style="background-color: #f3fff1;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input fw-semibold currencyValue compareVendor1 subTotalVendor1" id="subTotalVendor1_a" name="deliveryFeeTotal[]" data-vendor="1" data-item="a" data-type="DELIVERY_FEE" spellcheck="false" autocomplete="off" style="padding-right: 33px;" value="'.$subtotalDeliveryVendor1.'" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                        <td class="td-form" style="background-color: #fffbe3;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex currencyValue countAmount compareVendor2" id="deliveryFeeVendor2" name="deliveryFee[]" data-vendor="2" data-item="a" data-qty="false" spellcheck="false" autocomplete="off" value="'.$deliveryVendor2.'" style="padding-right: 33px;">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                        <td class="td-form" style="background-color: #fffbe3;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input fw-semibold currencyValue compareVendor2 subTotalVendor2" id="subTotalVendor2_a" name="deliveryFeeTotal[]" data-vendor="2" data-item="a" data-type="DELIVERY_FEE" value="'.$subtotalDeliveryVendor2.'" spellcheck="false" autocomplete="off" style="padding-right: 33px;" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                        <td class="td-form" style="background-color: #fff0f0;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex currencyValue countAmount compareVendor3" id="deliveryFeeVendor3" name="deliveryFee[]" data-vendor="3" data-item="a" data-qty="false" spellcheck="false" autocomplete="off" value="'.$deliveryVendor3.'" style="padding-right: 33px;">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                        <td class="td-form" style="background-color: #fff0f0;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input fw-semibold currencyValue compareVendor3 subTotalVendor3" id="subTotalVendor3_a" name="deliveryFeeTotal[]" data-vendor="3" data-item="a" data-type="DELIVERY_FEE" spellcheck="false" autocomplete="off" value="'.$subtotalDeliveryVendor3.'" style="padding-right: 33px;" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr id="rowItemb" class="rowItemString" data-id="b" data-label="Discount">
                                        <td class="text-center align-middle td-form"></td>
                                        <td class="text-center align-middle td-form">
                                            <input type="hidden" class="colNo" name="colNo[]" value="b"><span class="colNoLabel">b</span>
                                        </td>
                                        <td class="align-middle td-form">
                                            <input type="hidden" class="colCriteriaId" name="colCriteriaId[]" value="'.$dataForm['itemForm'][($y + 2)]['orderDetailId'].'">
                                            <input type="hidden" class="colCriteria" name="colCriteria[]" value="Discount">
                                            <span class="colCriteria">Discount</span><span class="fst-italic fw-normal fs-9"> (can be changed Nominal or %)</span>
                                        </td>
                                        <td class="td-form">
                                            <input type="hidden" class="colQty" name="colQty[]" value="'.$dataForm['itemForm'][($y + 2)]['unitQuantity'].'"><span class="colQty"></span>
                                        </td>
                                        <td class="td-form">
                                            <input type="hidden" class="colUnit" name="colUnit[]" value="'.$dataForm['itemForm'][($y + 2)]['unitName'].'"><span class="colUnit"></span>
                                        </td>
                                        <td class="td-form" style="background-color: #f3fff1;">
                                            <div class="input-group">
                                                <input type="text" class="form-control text-end pe-0 compareVendor1 discountVendor nominalPercentageInput" id="discountVendor1" name="discount[]" spellcheck="false" autocomplete="off" data-vendor="1" data-item="b" data-qty="false" value="'.$discountVendor1.'">
                                                <span class="input-group-text p-0">
                                                    <select class="form-select text-end nominalPercentageSelect" id="discountType1" name="discountType[]">';
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
                                        <td class="td-form" style="background-color: #f3fff1;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input fw-semibold currencyValue compareVendor1 subTotalVendor1" id="subTotalVendor1_b" name="discountTotal[]" data-vendor="1" data-item="b" data-type="DISCOUNT" spellcheck="false" autocomplete="off" style="padding-right: 33px;" value="'.$subtotalDiscountVendor1.'" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                        <td class="td-form" style="background-color: #fffbe3;">
                                            <div class="input-group">
                                                <input type="text" class="form-control text-end pe-0 compareVendor2 discountVendor nominalPercentageInput" id="discountVendor2" name="discount[]" spellcheck="false" autocomplete="off" data-vendor="2" data-item="b" data-qty="false" value="'.$discountVendor2.'">
                                                <span class="input-group-text p-0">
                                                    <select class="form-select text-end nominalPercentageSelect" id="discountType2" name="discountType[]">';
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
                                        <td class="td-form" style="background-color: #fffbe3;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input fw-semibold currencyValue compareVendor2 subTotalVendor2" id="subTotalVendor2_b" name="discountTotal[]" data-vendor="2" data-item="b" data-type="DISCOUNT" spellcheck="false" autocomplete="off" style="padding-right: 33px;" value="'.$subtotalDiscountVendor2.'" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                        <td class="td-form" style="background-color: #fff0f0;">
                                            <div class="input-group">
                                                <input type="text" class="form-control text-end pe-0 compareVendor3 discountVendor nominalPercentageInput" id="discountVendor3" name="discount[]" spellcheck="false" autocomplete="off" data-vendor="3" data-item="b" data-qty="false" value="'.$discountVendor3.'">
                                                <span class="input-group-text p-0">
                                                    <select class="form-select text-end nominalPercentageSelect" id="discountType3" name="discountType[]">';
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
                                        <td class="td-form" style="background-color: #fff0f0;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input fw-semibold currencyValue compareVendor3 subTotalVendor3" id="subTotalVendor3_b" name="discountTotal[]" data-vendor="3" data-item="b" data-type="DISCOUNT" spellcheck="false" autocomplete="off" style="padding-right: 33px;" value="'.$subtotalDiscountVendor3.'" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr id="rowItemc" class="rowItemString" data-id="c" data-label="Total Price">
                                        <td class="text-center align-middle td-form"></td>
                                        <td class="text-center align-middle td-form">
                                            <input type="hidden" class="colNo" name="colNo[]" value="c"><span class="colNoLabel">c</span>
                                        </td>
                                        <td class="align-middle td-form">
                                            <input type="hidden" class="colCriteriaId" name="colCriteriaId[]" value="'.$dataForm['itemForm'][($y + 3)]['orderDetailId'].'">
                                            <input type="hidden" class="colCriteria" name="colCriteria[]" value="Total Price"><span class="colCriteria">Total Price</span>
                                        </td>
                                        <td class="td-form">
                                            <input type="hidden" class="colQty" name="colQty[]" value="'.$dataForm['itemForm'][($y + 3)]['unitQuantity'].'"><span class="colQty"></span>
                                        </td>
                                        <td class="td-form">
                                            <input type="hidden" class="colUnit" name="colUnit[]" value="'.$dataForm['itemForm'][($y + 3)]['unitName'].'"><span class="colUnit"></span>
                                        </td>
                                        <td class="td-form" style="background-color: #f3fff1;"></td>
                                        <td class="td-form" style="background-color: #f3fff1;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input fw-semibold currencyValue compareVendor1" id="totalVendor1" name="totalVendor[]" data-vendor="1" data-item="c" spellcheck="false" autocomplete="off" style="padding-right: 33px;" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                        <td class="td-form" style="background-color: #fffbe3;"></td>
                                        <td class="td-form" style="background-color: #fffbe3;">
                                             <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input fw-semibold currencyValue compareVendor2" id="totalVendor2" name="totalVendor[]" data-vendor="2" data-item="c" spellcheck="false" autocomplete="off" style="padding-right: 33px;" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                        <td class="td-form" style="background-color: #fff0f0"></td>
                                        <td class="td-form" style="background-color: #fff0f0">
                                             <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input fw-semibold currencyValue compareVendor3" id="totalVendor3" name="totalVendor[]" data-vendor="3" data-item="c" spellcheck="false" autocomplete="off" style="padding-right: 33px;" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr id="rowItemd" class="rowItemString" data-id="d" data-label="VAT">
                                        <td class="text-center align-middle td-form"></td>
                                        <td class="text-center align-middle td-form">
                                            <input type="hidden" class="colNo" name="colNo[]" value="d"><span class="colNoLabel">d</span>
                                        </td>
                                        <td class="align-middle td-form">
                                            <input type="hidden" class="colCriteriaId" name="colCriteriaId[]" value="'.$dataForm['itemForm'][($y + 4)]['orderDetailId'].'">
                                            <input type="hidden" class="colCriteria" name="colCriteria[]" value="VAT"><span class="colCriteria">VAT (PPn)</span></td>
                                        </td>
                                        <td class="td-form">
                                            <input type="hidden" class="colQty" name="colQty[]" value="'.$dataForm['itemForm'][($y + 4)]['unitQuantity'].'"><span class="colQty"></span>
                                        </td>
                                        <td class="td-form">
                                            <input type="hidden" class="colUnit" name="colUnit[]" value="'.$dataForm['itemForm'][($y + 4)]['unitName'].'"><span class="colUnit"></span>
                                        </td>
                                        <td class="td-form" style="background-color: #f3fff1;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <select class="select2 countVat compareVendor1 selectVat" id="vatRateVendor1" name="vatRate[]" data-vendor="1" data-item="d">
                                                    '.$optionVatRate1.'
                                                </select>
                                            </div>
                                        </td>
                                        <td class="td-form" style="background-color: #f3fff1;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="hidden" class="unitCurrency" name="currencyVatRate[]" value="'.$currency.'">
                                                <input type="text" class="form-control text-end d-flex no-input fw-semibold currencyValue countAmount compareVendor1" id="vatVendor1" name="vat[]" data-vendor="1" spellcheck="false" autocomplete="off" value="'.$subtotalVatVendor1.'" style="padding-right: 33px;" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                        <td class="td-form" style="background-color: #fffbe3;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <select class="select2 countVat compareVendor2 selectVat" id="vatRateVendor2" name="vatRate[]" data-vendor="2" data-item="d">
                                                    '.$optionVatRate2.'
                                                </select>
                                            </div>
                                        </td>
                                        <td class="td-form" style="background-color: #fffbe3;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="hidden" class="unitCurrency" name="currencyVatRate[]" value="'.$currency.'">
                                                <input type="text" class="form-control text-end d-flex no-input fw-semibold currencyValue countAmount compareVendor2" id="vatVendor2" name="vat[]" data-vendor="2" spellcheck="false" autocomplete="off" value="'.$subtotalVatVendor2.'" style="padding-right: 33px;" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                        <td class="td-form" style="background-color: #fff0f0">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <select class="select2 countVat compareVendor3 selectVat" id="vatRateVendor3" name="vatRate[]" data-vendor="3" data-item="d">
                                                    '.$optionVatRate3.'
                                                </select>
                                            </div>
                                        </td>
                                        <td class="td-form" style="background-color: #fff0f0">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="hidden" class="unitCurrency" name="currencyVatRate[]" value="'.$currency.'">
                                                <input type="text" class="form-control text-end d-flex no-input fw-semibold currencyValue countAmount compareVendor3" id="vatVendor3" name="vat[]" data-vendor="3" spellcheck="false" autocomplete="off" value="'.$subtotalVatVendor3.'" style="padding-right: 33px;" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr id="rowIteme" class="rowItemString" data-id="e" data-label="PPh">
                                        <td class="text-center align-middle td-form"></td>
                                        <td class="text-center align-middle td-form">
                                            <input type="hidden" class="colNo" name="colNo[]" value="e"><span class="colNoLabel">e</span>
                                        </td>
                                        <td class="align-middle td-form">
                                            <input type="hidden" class="colCriteriaId" name="colCriteriaId[]" value="'.$dataForm['itemForm'][($y + 5)]['orderDetailId'].'">
                                            <input type="hidden" class="colCriteria" name="colCriteria[]" value="PPh">
                                            <span class="colCriteria">PPh</span><span class="fst-italic fw-normal fs-9"> (can be changed Nominal or %)</span>
                                        </td>
                                        <td class="td-form">
                                            <input type="hidden" class="colQty" name="colQty[]" value="'.$dataForm['itemForm'][($y + 5)]['unitQuantity'].'"><span class="colQty"></span>
                                        </td>
                                        <td class="td-form">
                                            <input type="hidden" class="colUnit" name="colUnit[]" value="'.$dataForm['itemForm'][($y + 5)]['unitName'].'"><span class="colUnit"></span>
                                        </td>
                                        <td class="td-form" style="background-color: #f3fff1;">
                                            <div class="input-group">
                                                <input type="text" class="form-control text-end pe-0 compareVendor1 pphVendor nominalPercentageInput" id="pphVendor1" name="pph[]" spellcheck="false" autocomplete="off" data-vendor="1" data-item="e" data-qty="false" value="'.$pphVendor1.'">
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
                                        <td class="td-form" style="background-color: #f3fff1;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input fw-semibold currencyValue compareVendor1" id="subTotalVendor1_e" name="pphTotal[]" data-vendor="1" data-item="e" data-type="PPh" spellcheck="false" autocomplete="off" style="padding-right: 33px;" value="'.$subtotalPphVendor1.'" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                        <td class="td-form" style="background-color: #fffbe3;">
                                            <div class="input-group">
                                                <input type="text" class="form-control text-end pe-0 compareVendor1 pphVendor nominalPercentageInput" id="pphVendor2" name="pph[]" spellcheck="false" autocomplete="off" data-vendor="2" data-item="e" data-qty="false" value="'.$pphVendor2.'">
                                                <span class="input-group-text p-0">
                                                    <select class="form-select text-end nominalPercentageSelect" id="pphType2" name="pphType[]">';
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
                                        <td class="td-form" style="background-color: #fffbe3;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input fw-semibold currencyValue compareVendor2" id="subTotalVendor2_e" name="pphTotal[]" data-vendor="2" data-item="e" data-type="PPh" spellcheck="false" autocomplete="off" style="padding-right: 33px;" value="'.$subtotalPphVendor2.'" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                        <td class="td-form" style="background-color: #fff0f0;">
                                            <div class="input-group">
                                                <input type="text" class="form-control text-end pe-0 compareVendor3 pphVendor nominalPercentageInput" id="pphVendor3" name="pph[]" spellcheck="false" autocomplete="off" data-vendor="3" data-item="e" data-qty="false" value="'.$pphVendor3.'">
                                                <span class="input-group-text p-0">
                                                    <select class="form-select text-end nominalPercentageSelect" id="pphType3" name="pphType[]">';
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
                                        <td class="td-form" style="background-color: #fff0f0;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input fw-semibold currencyValue compareVendor3" id="subTotalVendor3_e" name="pphTotal[]" data-vendor="3" data-item="e" data-type="PPh" spellcheck="false" autocomplete="off" style="padding-right: 33px;" value="'.$subtotalPphVendor3.'" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr id="rowItemf" class="rowItemString" data-id="f" data-label="Total Price + VAT - PPh">
                                        <td class="text-center align-middle td-form"></td>
                                        <td class="text-center align-middle td-form">
                                            <input type="hidden" class="colNo" name="colNo[]" value="f"><span class="colNoLabel">f</span>
                                        </td>
                                        <td class="align-middle td-form">
                                            <input type="hidden" class="colCriteriaId" name="colCriteriaId[]" value="'.$dataForm['itemForm'][($y + 6)]['orderDetailId'].'">
                                            <input type="hidden" class="colCriteria" name="colCriteria[]" value="Total Price + VAT - PPh"><span class="colCriteria">Total Price + VAT - PPh</span></td>
                                        </td>
                                        <td class="td-form">
                                            <input type="hidden" class="colQty" name="colQty[]" value="'.$dataForm['itemForm'][($y + 6)]['unitQuantity'].'"><span class="colQty"></span>
                                        </td>
                                        <td class="td-form">
                                            <input type="hidden" class="colUnit" name="colUnit[]" value="'.$dataForm['itemForm'][($y + 6)]['unitName'].'"><span class="colUnit"></span>
                                        </td>
                                        <td class="td-form" style="background-color: #f3fff1;"></td>
                                        <td class="td-form" style="background-color: #f3fff1;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input fw-bold currencyValue countAmount compareVendor1" id="grandTotalVendor1" name="grandTotal[]" data-vendor="1" spellcheck="false" autocomplete="off" style="padding-right: 33px;" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                        <td class="td-form" style="background-color: #fffbe3;"></td>
                                        <td class="td-form" style="background-color: #fffbe3;">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input fw-bold currencyValue countAmount compareVendor2" id="grandTotalVendor2" name="grandTotal[]" data-vendor="2" spellcheck="false" autocomplete="off" style="padding-right: 33px;" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                        <td class="td-form" style="background-color: #fff0f0"></td>
                                        <td class="td-form" style="background-color: #fff0f0">
                                            <div class="form-group d-flex align-items-center position-relative">
                                                <input type="text" class="form-control text-end d-flex no-input fw-bold currencyValue countAmount compareVendor3" id="grandTotalVendor3" name="grandTotal[]" data-vendor="3" spellcheck="false" autocomplete="off" style="padding-right: 33px;" readonly="" tabindex="-1">
                                                <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">'.$currency.'</span>
                                            </div>
                                        </td>
                                    </tr>';
                        @endphp
                    </tbody>
                </table>
            </div>
            <div class="row pt-2 pb-5 mb-5">
                <div class="col-sm-12 col-md-7">
                    <div class="row row-multi-col bg-white mb-3 py-2">
                        <div class="row form-group m-0 mb-md-1 px-0 py-1">
                            <label for="" class="col-sm-12 col-md-3 col-form-label label-inline-input"><div class="d-inline">Selected Vendor<span class="required"></span></div></label>
                            <div class="col-sm-12 col-md-9">
                                <select class="select2" id="selectedVendor" name="selectedVendor">
                                    <option></option>
                                    @php
                                        foreach ($dataForm['arrVendor'] as $rowVendor) {
                                            $selected = ($rowVendor['vendorId'] == $dataForm['vendorIdSelected']) ? ' selected=""' : '';
                                            echo '<option value="'.$rowVendor['vendorId'].'" data-description1="'.$rowVendor['vendorPic'].'" data-description2="'.$rowVendor['vendorAddress'].'"'.$selected.'>'.$rowVendor['vendorName'].'</option>';
                                        }
                                    @endphp
                                </select>
                            </div>
                        </div>

                        <div class="row form-group m-0 mb-md-1 px-0 py-2">
                            <label for="" class="col-sm-12 col-md-3 col-form-label label-inline-input"><div class="d-inline">Vendor Address<span class="required"></span></div></label>
                            <div class="col-sm-12 col-md-9">
                                <div class="card">
                                    <div class="card-body p-0" id="vendorAdrressSelectedContainer">
                                        <textarea class="form-control autosize" data-limit-rows="true" rows="2" spellcheck="false" maxlength="255" id="vendorAddressSelected" name="vendorAddressSelected" style="height: 53px;" readonly></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row form-group m-0 mb-md-1 px-0 py-2">
                            <label for="" class="col-sm-12 col-md-3 col-form-label label-inline-input"><div class="d-inline">Vendor PIC<span class="required"></span></div></label>
                            <div class="col-sm-12 col-md-9">
                                <input type="text" class="form-control" name="vendorPicSelected" id="vendorPicSelected" value="" readonly>
                            </div>
                        </div>

                        <div class="row form-group m-0 mb-md-1 px-0 py-2">
                            <label for="" class="col-sm-12 col-md-3 col-form-label label-inline-input"><div class="d-inline">Note<span class="required"></span></div></label>
                            <div class="col-sm-12 col-md-9">
                                <textarea class="form-control autosize" data-limit-rows="true" rows="2" spellcheck="false" maxlength="255" id="comparisonNote" name="comparisonNote">{{$dataForm['comparisonNotes']}}</textarea>
                            </div>
                        </div>

                        <div class="row form-group m-0 mb-md-1 px-0 py-1">
                            <label for="" class="col-sm-12 col-md-3 col-form-label label-inline-input"><div class="d-inline">Quotation<span class="required"></span></div></label>
                            <div class="col-sm-12 col-md-9">
                                <div class="">
                                    <div id="dropZone" class="form-group border p-4 text-center bg-light">
                                        <p>Drag and drop files here or Select files</p>
                                        <button type="button" class="btn btn-secondary btn-md" id="selectFileBtn">Select files...</button>
                                        <input type="file" class="form-control d-none" id="fileUpload" name="attachmentFile[]" multiple>
                                    </div>
                                </div>
                                <div id="fileList" class="list-group mt-2">
                                    @foreach ($dataForm['attachmentList'] as $index => $row)
                                        <div class="form-group existAttachment" data-index="{{ $index }}">
                                            <div class="file-item list-group-item list-group-item-action" data-index="{{ $index }}">
                                                <div class="file-details view-file-fullscreen" data-token="{{ $row['tokenAttachment'] }}">
                                                    <input type="hidden" name="existAttachment[]" value="{{ $row['tokenAttachment'] }}">
                                                    <i class="{{ $row['icon'] }} file-icon"></i>
                                                    <div>
                                                        <span class="file-name">{{ $row['filename'] }}</span>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn-close-black deleteFileItem" title="Delete" aria-label="Delete"></button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
    <div class="tab-pane fade w-100 h-100" id="orderFormContent" role="tabpanel" aria-labelledby="orderFormTab" tabindex="0">
    </div>
</div>
@once
<script>
    selectedFiles = [];
    selectedFilesProperties = [];
    $('.autosize').autosize().trigger('change');
    dayjs.locale('id');
    dayjs.extend(window.dayjs_plugin_customParseFormat);
    var optionsComparisonDatePicker = {
        locale: 'en-US',
        placeholder: 'Comparison date',
        inputDateFormat: date => dayjs(date).locale('en').format('DD-MM-YYYY'),
        inputDateParse: date => dayjs(date, 'DD-MM-YYYY', 'id').toDate(),
        maxDate: dayjs(new Date()),
        showAdjacementDays: false,
    }
    new coreui.DatePicker(document.getElementById(`comparisonDate`), optionsComparisonDatePicker);

    @json($dataForm['attachmentList']).forEach((element, index) => {
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

    // $('.nominalPercentageSelect').select2({
    //     dropdownParent: $('#modalDocumentForm'),
    //     minimumResultsForSearch: Infinity,
    //     allowClear: false,
    //     placeholder: '-- Select --',
    // });

    var $nominalPercentageSelect = $('.nominalPercentageSelect').parent().find('span.select2-container--default').find('span.select2-selection--single');
    $('.nominalPercentageSelect').parent().find('span.select2-container--default').addClass('text-end');
    $nominalPercentageSelect.find('span.select2-selection__arrow').addClass('d-none');

    $nominalPercentageSelect.find('span.select2-selection__rendered').css('cssText',
        'padding-left: 11px !important; ' +
        'padding-right: 11px !important; ' +
        'width: 42px !important'
    );
    $nominalPercentageSelect.css('cssText',
        'border-left: 0 !important; ' +
        'border-top-left-radius: 0 !important; ' +
        'border-bottom-left-radius: 0 !important'
    );

    // $('#discountType').select2({
    //     dropdownParent: $('#modalDocumentForm'),
    //     minimumResultsForSearch: Infinity,
    //     allowClear: false,
    //     placeholder: '-- Select --',
    // });

    // $('#pphType').select2({
    //     dropdownParent: $('#modalDocumentForm'),
    //     minimumResultsForSearch: Infinity,
    //     allowClear: false,
    //     placeholder: '-- Select --',
    // });

    // $('.discountTypeText').html($('#discountType').val());

    $('.selectVat').select2({
        dropdownParent: $('#modalDocumentForm'),
        minimumResultsForSearch: Infinity,
        allowClear: true,
        placeholder: '-- Select --',
    });

    var preselectedVendor = null;
    var preselectedVendor1 = null, preselectedVendor2 = null, preselectedVendor3 = null;
    var preselectedVendorName1 = null, preselectedVendorName2 = null, preselectedVendorName3 = null;
    var preselectVendor1description1 = null, preselectVendor2description1 = null, preselectVendor3description1 = null;
    var preselectVendor1description2 = null, preselectVendor2description2 = null, preselectVendor3description2 = null;
    var preselectedValidity1 = null, preselectedValidity2 = null, preselectedValidity3 = null;

    @json($dataForm['arrVendor']).forEach((element, index) => {
        if(index === 0) {
            preselectedVendor1 = element.vendorId;
            preselectedVendorName1 = element.vendorName;
            preselectVendor1description1 = element.vendorPic;
            preselectVendor1description2 = element.vendorAddress;
            preselectedValidity1 = element.quotationValidity;
        }
        else if(index === 1) {
            preselectedVendor2 = element.vendorId;
            preselectedVendorName2 = element.vendorName;
            preselectVendor2description1 = element.vendorPic;
            preselectVendor2description2 = element.vendorAddress;
            preselectedValidity2 = element.quotationValidity;
        }
        else if(index === 2) {
            preselectedVendor3 = element.vendorId;
            preselectedVendorName3 = element.vendorName;
            preselectVendor3description1 = element.vendorPic;
            preselectVendor3description2 = element.vendorAddress;
            preselectedValidity3 = element.quotationValidity;
        }
    });

    $('#selectVendor1').select2({
        dropdownParent: $('#modalDocumentForm'),
        minimumInputLength: 2,
        allowClear: true,
        placeholder: '-- Select Vendor --',
        data: preselectedVendor1 ? [{
            id: preselectedVendor1,
            text: preselectedVendorName1,
            description1: preselectVendor1description1,
            description2: preselectVendor1description2,
        }] : [],
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
                        description1: row.vendorPicName,
                        description2: row.vendorAddress,
                    })),
                    pagination: {
                        more: (params.page * 10) < data.count_filtered
                    }
                }
            },
        },
        templateResult: formatResultRemote,
        templateSelection: formatSelection
    });

    $('#selectVendor2').select2({
        dropdownParent: $('#modalDocumentForm'),
        minimumInputLength: 2,
        allowClear: true,
        placeholder: '-- Select Vendor --',
        data: preselectedVendor2 ? [{
            id: preselectedVendor2,
            text: preselectedVendorName2,
            description1: preselectVendor2description1,
            description2: preselectVendor2description2,
        }] : [],
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
                        description1: row.vendorPicName,
                        description2: row.vendorAddress,
                    })),
                    pagination: {
                        more: (params.page * 10) < data.count_filtered
                    }
                }
            },
        },
        templateResult: formatResultRemote,
        templateSelection: formatSelection
    });

    $('#selectVendor3').select2({
        dropdownParent: $('#modalDocumentForm'),
        minimumInputLength: 2,
        allowClear: true,
        placeholder: '-- Select Vendor --',
        data: preselectedVendor3 ? [{
            id: preselectedVendor3,
            text: preselectedVendorName3,
            description1: preselectVendor3description1,
            description2: preselectVendor3description2,
        }] : [],
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
                        description1: row.vendorPicName,
                        description2: row.vendorAddress,
                    })),
                    pagination: {
                        more: (params.page * 10) < data.count_filtered
                    }
                }
            },
        },
        templateResult: formatResultRemote,
        templateSelection: formatSelection
    });

    preselectedVendor = @json($dataForm['vendorIdSelected']);

    $('#selectedVendor').select2({
        dropdownParent: $('#modalDocumentForm'),
        minimumResultsForSearch: Infinity,
        allowClear: false,
        placeholder: '-- Select --',
    });

    if($('#selectedVendor').val()) {
        setTimeout(function () {
            $('#selectedVendor').val(preselectedVendor).trigger('change');
        }, 0);
    }

    var optionsComparisonDatePicker = {
        locale: 'en-US',
        placeholder: 'Quotation validity date',
        inputDateFormat: date => dayjs(date).locale('en').format('DD-MM-YYYY'),
        inputDateParse: date => dayjs(date, 'DD-MM-YYYY', 'id').toDate(),
        minDate: dayjs().startOf('day'),
        showAdjacementDays: false,
    }
    new coreui.DatePicker(document.getElementById(`validity1`), optionsComparisonDatePicker);

    var optionsComparisonDatePicker = {
        locale: 'en-US',
        placeholder: 'Quotation validity date',
        inputDateFormat: date => dayjs(date).locale('en').format('DD-MM-YYYY'),
        inputDateParse: date => dayjs(date, 'DD-MM-YYYY', 'id').toDate(),
        minDate: dayjs().startOf('day'),
        showAdjacementDays: false,
    }
    new coreui.DatePicker(document.getElementById(`validity2`), optionsComparisonDatePicker);

    var optionsComparisonDatePicker = {
        locale: 'en-US',
        placeholder: 'Quotation validity date',
        inputDateFormat: date => dayjs(date).locale('en').format('DD-MM-YYYY'),
        inputDateParse: date => dayjs(date, 'DD-MM-YYYY', 'id').toDate(),
        minDate: dayjs().startOf('day'),
        showAdjacementDays: false,
    }
    new coreui.DatePicker(document.getElementById(`validity3`), optionsComparisonDatePicker);

    $(document).off('change', '.selectVendor').on('change', '.selectVendor', function (e) {
        let $this = $(this);
        $('#selectedVendor').html('<option></option>').val(null).trigger('change');
        $('.selectVendor').each(function() {
            let selectedData = $(this).select2('data')[0];

            if(selectedData) {
                let newOption = new Option(selectedData.text, selectedData.id, false, false);
                $(newOption).data('description1', selectedData.description1);
                $(newOption).data('description2', selectedData.description2);
                $('#selectedVendor').append(newOption).trigger('change');
            }
        });
    });

    $(document).off('change', '#selectedVendor').on('change', '#selectedVendor', async function (e) {
        let $this = $(this);
        if ($('#vendorPicSelected').hasClass('select2-hidden-accessible')) {
            $('#vendorPicSelected').select2('destroy');
        }

        const vendorAdrressSelectedContainer = $('#vendorAdrressSelectedContainer');
        vendorAdrressSelectedContainer.removeClass('p-2 px-2-2 p-0').addClass('p-0');
        vendorAdrressSelectedContainer.html('<div class="skeleton textarea mb-0" id="vendorAddressSelected"></div>');
        $('#vendorPicSelected').replaceWith('<div class="skeleton mb-0" id="vendorPicSelected"></div>');

        if(!$this.val()) {
            $('#vendorAddressSelected').replaceWith('<textarea class="form-control autosize" data-limit-rows="true" rows="2" spellcheck="false" maxlength="255" id="vendorAddressSelected" name="vendorAddressSelected" style="height: 53px;" readonly></textarea>');
            $('#vendorPicSelected').replaceWith('<input type="text" class="form-control" name="vendorPicSelected" id="vendorPicSelected" value="" readonly>');

            return;
        }

        const params = {
            'id': $this.val(),
            'type': 'ROW_DETAILS',
        };

        const getDetails = await getVendorDetails(params);
        const vendorAddress = getDetails.vendorAddress;
        const vendorPic = getDetails.vendorPic;
        if (vendorAddress.length > 0) {
            vendorAdrressSelectedContainer.html('');
            vendorAdrressSelectedContainer.removeClass('p-2 px-2-2 p-0').addClass('p-2 px-2-2');
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
                                            <div class="white-space-pre"><input type="checkbox" class="form-check-input me-2 vendorAddressSelected" name="vendorAddressSelected" value="${item.addressId}" ${checked}>${item.addressName}</div>
                                        </label>
                                    </div>
                                </div>`;
                vendorAdrressSelectedContainer.append(div);
            });
        }
        else {
            vendorAdrressSelectedContainer.html('<textarea class="form-control autosize" data-limit-rows="true" rows="2" spellcheck="false" maxlength="255" id="vendorAddressSelected" name="vendorAddressSelected" style="height: 53px;" readonly>No Address data</textarea>');
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

            $('#vendorPicSelected').replaceWith(`<select class="select2" name="vendorPicSelected" id="vendorPicSelected"><option></option></select>`);
            $('#vendorPicSelected').select2({
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
            $('#vendorPicSelected').replaceWith('<input type="text" class="form-control" name="vendorPicSelected" id="vendorPicSelected" value="" readonly>');
        }

    });

</script>
@endonce

{{-- $('#filterDepartment').replaceWith('<div class="skeleton" id="filterDepartment"></div>');
        if($this.val() == 'ALL') {
            $('#filterDepartment').replaceWith('<select class="select2" name="filterDepartment" id="filterDepartment"><option value="ALL" selected="">-- ALL DEPARTMENT --</option></select>')
            $('#filterDepartment').select2({
                dropdownParent: $('#filterAside'),
                allowClear: false,
                placeholder: '-- Select --',
            }).trigger('change');
        }
        else {
            let optionDepartment = await getDepartment({'companyId': $this.val(),'departmentId': 'ALL'});
            $('#filterDepartment').replaceWith(`<select class="select2" name="filterDepartment" id="filterDepartment"><option></option></select>`);
            $('#filterDepartment').select2({
                dropdownParent: $('#filterAside'),
                allowClear: false,
                placeholder: '-- Select --',
                data: optionDepartment,
                templateResult: formatResultRemote,
                templateSelection: function (data) {
                    return data.text || data.id;
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            }).val(dept).trigger('change');
        } --}}