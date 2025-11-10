<form role="form" class="form-horizontal" enctype="multipart/form-data" id="formDocumentForm">
    @csrf
    <input type="hidden" name="documentType" id="documentTypeForm" value="{{$dataForm['documentType']}}">
    <div id="headerContainer">
        <div class="row row-multi-col mb-3 pt-2 pb-1 bg-white">
            <div class="col-sm-4 mb-sm-0">
                <div class="form-group mb-3">
                    <label for="preparedBy" class="form-label">Prepared By<span class="required"></span> :</label>
                    <select class="select2 selectNonClear" name="preparedBy" id="preparedBy">
                        <option></option>
                        @php
                            foreach ($dataForm['preparedBy'] as $row) {
                                echo '<option value="'.$row['value'].'" '.$row['selected'].'>'.$row['label'].'</option>';
                            }
                        @endphp
                    </select>
                </div>
                <div class="form-group mb-3 d-none">
                    <label for="departmentOrder" class="form-label">Department<span class="required"></span> :</label>
                    <input type="hidden" id="costCenterOrder" value="" data-text="">
                    <select class="select2" name="departmentOrder" id="departmentOrder">
                        <option></option>
                        @php
                            foreach ($dataForm['department'] as $row) {
                                echo '<option value="'.$row['value'].'" '.$row['selected'].'>'.$row['label'].'</option>';
                            }
                        @endphp
                    </select>
                </div>
            </div>
            <div class="col-sm-4 mb-sm-0">
                <div class="form-group mb-3">
                    <label for="reviewerSelect" class="form-label">Reviewer (left blank if no reviewer) :</label>
                    <div class="skeleton" id="reviewerSelect"></div>
                </div>
                <div class="form-group mb-3">
                    <label for="approverSelect" class="form-label">Approver<span class="required"></span> :</label>
                    <div class="skeleton" id="approverSelect"></div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="row row-multi-col mb-3 bg-white" id="" data-item="">
            <div class="row-multi-col-header">
                <span style="display: inline">General Journal File (xlsx)</span><span class="required"></span>
            </div>
            <div id="documentContainer" class="pb-3">
                <div class="">
                    <div class="form-group border p-4 text-center bg-light dropZone" id="dropZoneDocument">
                        <p>Drag and drop files here or Select files</p>
                        <button type="button" class="btn btn-secondary btn-md" id="selectFileBtnDocument">Select files...</button>
                        <input type="file" class="form-control d-none" id="fileUploadDocument" name="attachmentFileDocument[]">
                    </div>
                </div>
                <div id="fileListDocument" class="list-group mt-2">
                </div>
            </div>
        </div>
    </div>
    <div id="formContainerFooter">
        <div class="row row-multi-col mb-3 bg-white" id="" data-item="">
            <div class="row-multi-col-header">
                <span style="display: inline">Attachment (optional)</span>
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
                </div>
            </div>
        </div>
    </div>
</form>

@once
<script>
    $('.autosize').autosize().trigger('change');
    // dayjs.locale('id');
    // dayjs.extend(window.dayjs_plugin_customParseFormat);
    // var optionsDatePicker = {
    //     locale: 'en-US',
    //     inputDateFormat: date => dayjs(date).locale('en').format('DD-MMM-YYYY'),
    //     inputDateParse: date => dayjs(date, 'DD-MMM-YYYY', 'id').toDate(),
    //     // minDate: dayjs(new Date()),
    //     showAdjacementDays: false,
    // }

    // var optionsRequestDate = {
    //     locale: 'en-US',
    //     inputDateFormat: date => dayjs(date).locale('en').format('DD-MMM-YYYY'),
    //     inputDateParse: date => dayjs(date, 'DD-MMM-YYYY', 'id').toDate(),
    //     maxDate: dayjs(new Date()),
    //     showAdjacementDays: false,
    // }

    // new coreui.DatePicker(document.getElementById(`requestDate`), optionsRequestDate);

    $('#departmentOrder').select2({allowClear: false, placeholder: '-- Select --'}).trigger('change');
    $('.selectNonClear').select2({allowClear: false, placeholder: '-- Select --'});


</script>
@endonce