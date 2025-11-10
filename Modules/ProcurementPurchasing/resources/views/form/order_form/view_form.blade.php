<form role="form" class="form-horizontal" enctype="multipart/form-data" id="">
    <input type="hidden" name="tokenForm" value="{{ $dataForm['tokenForm'] }}">
    <ul class="nav nav-pills nav-pills-fixed pt-2 mb-2 top-0" id="pillsTabViewForm" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link pillTabDetailForm px-3 py-1 active" id="pillsFormTab" data-scroll-top="" data-coreui-toggle="pill" data-coreui-target="#pillsForm" type="button" role="tab" aria-controls="pillsForm" aria-selected="false" tabindex="-1">Order Form</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link pillTabDetailForm px-3 py-1" id="pillsAttachmentsTab" data-scroll-top="" data-coreui-toggle="pill" data-coreui-target="#pillsAttachments" type="button" role="tab" aria-controls="pillsAttachment" aria-selected="true">Attachments</button>
        </li>
    </ul>
    <div class="tab-content" id="pillsTabContentForm">
        <div class="tab-pane fade active show" id="pillsForm" role="tabpanel" aria-labelledby="pillsFormTab" tabindex="0">
            <div id="">
                <div class="row mb-2" style="background-color:#fff">
                    <div class="col-sm-6 mb-sm-0">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" value="{{ $dataForm['docNumber'] }}" readonly="">
                            <label for="">No :</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" value="{{ $dataForm['requestDate'] }}" readonly="">
                            <label for="">Request Date :</label>
                        </div>

                        {{-- <div class="form-group mb-3">
                            <label for="seqNumber" class="form-label">No. :</label>
                            <input type="text" class="form-control" value="{{ $dataForm['docNumber'] }}" readonly="">
                        </div>
                        <div class="form-group mb-3">
                            <label for="currencyOrder" class="form-label">Request Date :</label>
                            <input type="text" class="form-control" value="{{ $dataForm['createdDate'] }}" readonly="">
                        </div> --}}
                    </div>
                    <div class="col-sm-6 mb-sm-0">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" value="{{ $dataForm['departmentName'] }}" readonly="">
                            <label for="">Department :</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" value="{{ $dataForm['locationName'] }}" readonly="">
                            <label for="">Location :</label>
                        </div>
                        {{-- <div class="form-group mb-3">
                            <label for="departmentOrder" class="form-label">Department :</label>
                            <input type="text" class="form-control" value="{{ $dataForm['departmentName'] }}" readonly="">
                        </div>
                        <div class="form-group mb-3">
                            <label for="locationOrder" class="form-label">Location :</label>
                            <input type="text" class="form-control" value="{{ $dataForm['locationName'] }}" readonly="">
                        </div> --}}
                    </div>
                </div>
            </div>
            <div id="">
                <div class="col-sm-12 table-responsive my-2">
                    <table class="table table-hover table-form">
                        <thead>
                            <tr>
                                <th style="width: 2%;">No.</th>
                                <th style="width: 7%">Code</th>
                                <th style="width: 19%">Appliance / Item</th>
                                <th style="width: 18%">Brand / Type</th>
                                <th style="width: 7%">Qty</th>
                                <th style="width: 10%">Unit</th>
                                <th style="width: 12%">Unit Price (Est)<br>({{$dataForm['currency']}})</th>
                                <th style="width: 15%">Total Price (Est)<br>({{$dataForm['currency']}})</th>
                                <th style="width: 10%">Required Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $x = 1;
                            foreach ($dataForm['itemForm'] as $row) {
                                echo '<tr>
                                        <td class="text-center">'.$x.'</td>
                                        <td class="text-center">'.$row['costCenter'].'</td>
                                        <td>'.$row['applianceItem'].'</td>
                                        <td>'.$row['brandType'].'</td>
                                        <td class="text-center">'.$row['unitQuantity'].'</td>
                                        <td class="text-center">'.$row['unitName'].'</td>
                                        <td class="text-end">'.$row['unitPriceEst'].'</td>
                                        <td class="text-end">'.$row['totalPriceEst'].'</td>
                                        <td class="text-center">'.$row['requiredDate'].'</td>
                                    </tr>';
                                $x++;
                            }

                            // echo '<tr>
                            //             <td class="text-center"></td>
                            //             <td class="text-center"></td>
                            //             <td>VAT '.$dataForm['vat']['vatRate'].'%</td>
                            //             <td></td>
                            //             <td class="text-center">1</td>
                            //             <td></td>
                            //             <td class="text-end">'.$dataForm['vat']['vatValue'].'</td>
                            //             <td class="text-end">'.$dataForm['vat']['vatValue'].'</td>
                            //             <td></td>
                            //         </tr>';
                            @endphp
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7" class="text-center">GRAND TOTAL</td>
                                <td class="tfoot-total">
                                    <span class="tfoot-currency">({{$dataForm['currency']}})</span>
                                    <span class="tfoot-amount">{{ $dataForm['grandTotal'] }}</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                {{-- /////////////// --}}
                {{-- @php
                $x = 1;
                foreach ($dataForm['itemForm'] as $row) {
                    echo '<div class="row row-multi-col mb-3 rowItemForm">
                            <div class="row-multi-col-header mb-0">
                                <span class="itemTitle" style="display: inline">Order Item '.$x.'</span>
                            </div>
                            <div class="col-sm-4 mb-sm-0">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" value="'.$row['costCenter'].'" style="padding-right: 70px;" readonly="">
                                    <label for="">Code :</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <textarea class="form-control autosize" spellcheck="false" readonly="">'.$row['applianceItem'].'</textarea>
                                    <label for="">Appliance / Item :</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <textarea class="form-control autosize" spellcheck="false" readonly="">'.$row['brandType'].'</textarea>
                                    <label for="">Brand / Type :</label>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-sm-0">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" value="'.$row['unitQuantity'].'" style="padding-right: 70px;" readonly="">
                                    <label for="">Quantity :</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" value="'.$row['unitName'].'" style="padding-right: 70px;" readonly="">
                                    <label for="">Unit :</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" value="'.$row['unitPriceEst'].'" style="padding-right: 70px;" readonly="">
                                    <span class="span-group-floating position-absolute end-0 top-50 translate-middle-y px-2">'.$row['currency'].'</span>
                                    <label for="">Unit Price (Estimation) :</label>
                                </div>
                            </div>
                            <div class="col-sm-4 mb-sm-0">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" value="'.$row['totalPriceEst'].'" style="padding-right: 70px;" readonly="">
                                    <span class="span-group-floating position-absolute end-0 top-50 translate-middle-y px-2">'.$row['currency'].'</span>
                                    <label for="">Total (Estimation) :</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" value="'.$row['requiredDate'].'" style="padding-right: 70px;" readonly="">
                                    <label for="">Required Date :</label>
                                </div>
                            </div>
                        </div>';
                    $x++;
                }
                @endphp --}}
            </div>
            <div id="">
            </div>
            <div id="">
                <div class="row row-multi-col mb-3 bg-white" id="" data-item="">
                    <div class="row-multi-col-header mb-0">
                        <span style="display: inline">Purpose</span>
                    </div>
                    @php
                    foreach ($dataForm['purpose'] as $row) {
                        echo '<div id="">
                                <div class="row">
                                    <div class="col-sm-4 mb-sm-0">
                                        <div class="form-floating mb-3">
                                            <textarea class="form-control autosize" spellcheck="false" readonly="">'.$row['description'].'</textarea>
                                            <label for="">Description :</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 mb-sm-0">
                                        <div class="form-floating mb-3">
                                            <textarea class="form-control autosize" spellcheck="false" readonly="">'.$row['reason'].'</textarea>
                                            <label for="">Reason :</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 mb-sm-0">
                                        <div class="form-floating mb-3">
                                            <textarea class="form-control autosize" spellcheck="false" readonly="">'.$row['remarks'].'</textarea>
                                            <label for="">Remarks :</label>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                    }
                    @endphp
                </div>

                <div class="row mb-2" style="background-color:#fff">
                    <div class="card-header fw-medium px-2">
                        <span style="display: inline">Requester</span>
                    </div>
                    <div class="col-sm-12 mb-sm-0 px-2">
                        <div class="timeline pt-1 ps-2 pe-1" id="timelineApproval">
                            @php
                            foreach ($dataForm['signatories'] as $row) {
                                $badgeDot = $receivedAt = $badgeStatus = '';
                                if($row['flowAs'] == 'APPLICANT') {
                                    $receivedAt = '<span class="badge bg-default fs-9">'.$row['flowAs'].'</span>';
                                    $receivedAt .= '<span class="d-block">Submited at : '.$row['receivedAt'].'</span>';
                                    if($row['statusName'] == 'SUBMITTED') {
                                        $badgeDot = '<div class="timeline-badge bg-success"><i class="fa-solid fa-check"></i></div>';
                                        $badgeStatus = '<span class="badge bg-success fs-9 fw-semibold">SUBMITTED</span>';
                                    }
                                    else if($row['statusName'] == 'CANCELED') {
                                        $badgeDot = '<div class="timeline-badge bg-warning"><i class="fa-solid fa-arrow-turn-left"></i></div>';
                                        $badgeStatus = '<span class="badge bg-warning fs-9 fw-semibold">CANCELED</span>
                                                        <span class="d-block">'.$row['decisionAt'].'</span>';
                                    }
                                }
                                else{
                                    $receivedAt = '<span class="badge bg-default fs-9">'.$row['flowAs'].'</span>';
                                    if($row['statusName'] == 'RECEIVED') {
                                        $badgeDot = '<div class="timeline-badge pulse-animation bg-info"></div>';
                                        $receivedAt .= '<span class="d-block">Received at : '.$row['receivedAt'].'</span>';
                                        $badgeStatus = '<span class="badge pulse-animation bg-info fs-9 fw-semibold">UNDER REVIEW</span>';
                                    }
                                    else if($row['statusName'] == 'APPROVED') {
                                        $badgeDot = '<div class="timeline-badge bg-success"><i class="fa-solid fa-check" style="font-size:9px"></i></div>';
                                        $receivedAt .= '<span class="d-block">Received at : '.$row['receivedAt'].'</span>';
                                        $badgeStatus = '<span class="badge bg-success fs-9 fw-semibold">APPROVED</span>
                                                        <span class="d-block">'.$row['decisionAt'].'</span>';
                                    }
                                    else if($row['statusName'] == 'REJECTED') {
                                        $badgeDot = '<div class="timeline-badge bg-danger"><i class="fa-solid fa-xmark-large" style="font-size:8px"></i></div>';
                                        $receivedAt .= '<span class="d-block">Received at : '.$row['receivedAt'].'</span>';
                                        $badgeStatus = '<span class="badge bg-danger fs-9 fw-semibold">REJECTED</span>
                                                        <span class="d-block">'.$row['decisionAt'].'</span>';
                                    }
                                    else if($row['statusName'] == 'PENDING') {
                                        $badgeDot = '<div class="timeline-badge bg-light-dark"></div>';
                                        $badgeStatus = '<span class="badge bg-default fs-9 fw-semibold">PENDING</span>';
                                    }
                                    else if($row['statusName'] == 'CANCELED') {
                                        $badgeDot = '<div class="timeline-badge bg-warning"></div>';
                                        $badgeStatus = '<span class="badge bg-warning fs-9 fw-semibold">CANCELED</span>';
                                    }
                                    else if($row['statusName'] == 'SEND BACK') {
                                        $badgeDot = '<div class="timeline-badge bg-secondary"><i class="fa-solid fa-arrow-turn-left" style="font-size:9px"></i></div>';
                                        $receivedAt .= '<span class="d-block">Received at : '.$row['receivedAt'].'</span>';
                                        $badgeStatus = '<span class="badge bg-secondary fs-9 fw-semibold">SEND BACK</span>
                                                        <span class="d-block">'.$row['decisionAt'].'</span>';
                                    }
                                }

                                echo '<div class="timeline-item">
                                        '.$badgeDot.'
                                        <div class="card">
                                            <div class="row pt-2 pb-2">
                                                <div class="col-sm-12 col-md-4 mb-sm-0 pt-0 pb-1">
                                                    <div class="card-header py-0">
                                                        <div class="d-flex align-items-center">
                                                            <span class="avatar-text">'.$row['employeeName'].'</span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body py-0">
                                                        '.$receivedAt.'
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-md-3 mb-sm-0 pt-0 pb-1">
                                                    <div class="card-header py-0">
                                                        <div class="d-flex align-items-center">
                                                            <span class="avatar-text fw-medium">Result</span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body py-0">
                                                        '.$badgeStatus.'
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-md-5 mb-sm-0 pt-0 pb-1">
                                                    <div class="card-header py-0">
                                                        <div class="d-flex align-items-center">
                                                            <span class="avatar-text fw-medium">Comments</span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body py-0">
                                                        '.$row['comment'].'
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                            }
                            @endphp
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="pillsAttachments" role="tabpanel" aria-labelledby="pillsAttachmentsTab" tabindex="1">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 8%;">NO.</th>
                        <th style="width: 92%">ATTACHMENTS FILE</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $x = 1;
                    foreach ($dataForm['attachment'] as $row) {
                        echo '<tr>
                                <td class="text-center align-middle">'.$x.'</td>
                                <td>
                                    <div class="file-details view-file py-1" data-token="'.$row['tokenAttachment'].'">
                                        <i class="'.$row['icon'].' file-icon"></i>
                                        <div>
                                            <span class="file-name fw-medium">'.$row['filename'].'</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>';
                        $x++;
                    }
                    @endphp
                </tbody>
            </table>
        </div>
    </div>
</form>
@once
<script>
    $('.autosize').autosize().trigger('change');
</script>
@endonce