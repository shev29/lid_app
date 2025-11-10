let debounceTimeout;
var leftColScrollbarInstance;
var rightColScrollbarInstance;
let currentPage = 1;
let isLoading = false;
let hasMoreData = true;
let popperInstance;
const commentBuffer = [];

// Import PDF.js dari window object
// const pdfjsLib = window['pdfjsLib'] || window['pdfjs-dist/build/pdf'];

// Set worker source untuk PDF.js
// pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('js/app/pdf/build/pdf.worker.js') }}";



viewForm({'token': $('#token').val()});

function clearRightContent(){
    const rightContent = `<div class="full-height-column-wrapper">
                                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                    <div class="text-center">
                                        <i class="fa-light fa-file fs-1"></i>
                                        <div class="d-block fs-7 mt-2">Select an item to view</div>
                                    </div>
                                </div>
                            </div>`;
    if ($(window).width() < 768) {
        $('.aside-title').html(``);
        $('#asideDetailForm').html(rightContent);
        $('.aside-footer').html(`<div class="d-flex position-relative gap-2">
                                    <div class="skeleton mb-0" style="width:130px"></div>
                                    <div class="skeleton mb-0" style="width:130px"></div>
                                </div>`);
    }
    else {
        $('.card-title-right-column').html(`Detail Form`);
        $('.actionHeaderContainer').html(``);
        $('.right-column').html(rightContent);
        $('.card-footer-fixed').addClass('border-top-0');
        $('.card-footer-fixed').html(``);
        // rightColScrollbarInstance.updateScrollbarVisibility();
        // rightColScrollbarInstance.scrollTo(0);
        // rightColScrollbarInstance.updateScrollbarThumb();
    }
}

async function viewForm(params) {
    const token = params['token'];
    Snackbar.close();
    $('#asideDetailForm').html('');
    $('.popper-area').html('');
    rightContentSkeleton('FETCH');
    try {
        const response = await fetch(`/doc_approval/viewForm?source=EMAIL&token=${token}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'Referer': window.location.href
            }
        });

        const result = await response.json();
        if (result.status === 200) {
            commentBuffer.length = 0;
            const rightTitle = result.data.title;
            // const rightContent = data.data.form;
            const rightContent = `/framePdf?token=${result.data.tokenForm}`;
            const footerButton = result.data.footerButton;
            const popperForm = `<div class="popper-container col-md-6 p-3 shadow-medium d-none" id="popperAction" data-active="" style="z-index: 1061;">
                                    <div class="popper-arrow" data-popper-arrow></div>
                                    <form role="form" class="form-horizontal" enctype="multipart/form-data" id="formAction">
                                        <input type="hidden" id="tokenFormAction" name="tokenForm" autocomplete="false" value="${result.data.tokenForm}">
                                        <input type="hidden" name="type" autocomplete="false" value="APPROVAL">
                                        <div class="alert alert-info mb-3 w-100 p-2" id="alertFormAction" role="alert"></div>
                                        <div class="form-group mb-3 d-none" id="sendBackContainer">
                                            <label for="sendBackTo" class="form-label required">Send Back to :</label>
                                            <div class="skeleton"></div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="commentAction" class="form-label" id="commentActionLabel">Comment :</label>
                                            <textarea class="form-control autosize" spellcheck="false" placeholder="Add your comment" maxlength="255" id="commentAction" name="commentAction" style="height: 73px;"></textarea>
                                        </div>
                                    </form>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn btn-default closePopper">Cancel</button>
                                        <button class="btn btn-info confirmAction" data-type="" data-row="">Approve</button>
                                    </div>
                                </div>`;

            $('.card-title-right-column').html(rightTitle);
            $('.actionHeaderContainer').html(` <button class="btn btn-default btn-sm actionBtnHeader" data-token="${result.data.tokenAttachment}" data-type="ATTACHMENTS" data-type-flow="INSPECTION" data-selectedversion="${result.data.selectedVersionLabel}"><i class="fa-solid fa-bars-progress"></i> View Order Form</button>`);
            $('.card-button-right-column').html('');
            // $('.right-column').html(rightContent);
            $('.right-column').html(`<object class="w-100 h-100" id="subfile_frame" data="${rightContent}" type="text/html"><param name="allowfullscreen" value="true"></object>`);
            $('.card-footer-fixed').html(footerButton);
            $('.popper-area').html(popperForm);

            if(footerButton != '') {
                $('.card-footer-fixed').removeClass('border-top-0');
            }
        }
        else if (result.status === 404) {
            const rightContent = result.data.form;
            $('.card-title-right-column').html('Form');
            $('.card-button-right-column').html('');
            $('.right-column').html(rightContent);
            $('.card-footer-fixed').html('');
            // rightColScrollbarInstance.updateScrollbarVisibility();
            // rightColScrollbarInstance.scrollTo(0);
            // rightColScrollbarInstance.updateScrollbarThumb();
        }
        else {
            $('.card-title-right-column').html('Form');
            $('.card-button-right-column').html('');
            $('.right-column').html(`<div class="full-height-column-wrapper">
                                        <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                            <div class="text-center">
                                                <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                <div class="d-block fs-7 mt-2">Failed to get data</div>
                                            </div>
                                        </div>
                                    </div>`);
            $('.card-footer-fixed').html('');
        }
    } catch (error) {
        console.log(error);
        $('.card-title-right-column').html('Form');
        $('.card-button-right-column').html('');
        $('.right-column').html(`<div class="full-height-column-wrapper">
                                    <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                        <div class="text-center">
                                            <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                            <div class="d-block fs-7 mt-2">Failed to get data</div>
                                        </div>
                                    </div>
                                </div>`);
        $('.card-footer-fixed').html('');
    } finally {

    }
}

function rightContentSkeleton(type){
    const rightContent = `<div class="full-height-column-wrapper">
                                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                    <div class="text-center">
                                        <i class="fa-regular fa-circle-notch fa-spin fs-5"></i>
                                        <div class="d-block fs-7 mt-2">Loading...</div>
                                    </div>
                                </div>
                            </div>`;

    $('.card-title-right-column').html(`<div class="skeleton mb-0" style="width: 300px; height: 24px"></div>`);
    $('.actionHeaderContainer').html(`<div class="skeleton mb-0" style="width: 100px; height: 24px"></div>`);
    $('.card-button-right-column').html(`<div class="d-flex position-relative gap-2">
                                            <div class="skeleton mb-0" style="width: 40px; height: 24px"></div>
                                            <div class="skeleton mb-0" style="width: 40px; height: 24px"></div>
                                            <div class="skeleton mb-0" style="width: 40px; height: 24px"></div>
                                        </div>`);
    $('.right-column').html(rightContent);
    $('.card-footer-fixed').html(`<div class="col-sm-3 mb-sm-0">
                                    <div class="skeleton mb-0"></div>
                                </div>
                                <div class="col-sm-3 mb-sm-0">
                                    <div class="skeleton mb-0"></div>
                                </div>
                                <div class="col-sm-3 mb-sm-0">
                                    <div class="skeleton mb-0"></div>
                                </div>`);
}

async function showPopupAction(params) {
    // $('#popupTitle').text(title);
    const button = params['button'];
    const action = params['action'];
    const rowId = params['rowId'];
    $('#popperAction').attr('data-type', action);
    if(action == 'APPROVE' || action == 'REJECT' || action == 'SEND_BACK') {
        $('#sendBackContainer').addClass('d-none');
        let lastComment = '';
        let commentIndex = commentBuffer.findIndex(item => item.action === action);
        if (commentIndex !== -1) {
            lastComment = commentBuffer[commentIndex].comment;
        }

        $('#commentAction').val(lastComment).trigger('change');
        if(action == 'APPROVE'){
            $('#commentActionLabel').html('Approve Comment (optional) :');
            $('#commentActionLabel').removeClass('required');
            $('.confirmAction').removeClass('btn-info btn-danger btn-secondary');
            $('.confirmAction').addClass('btn-info');
            $('.confirmAction').attr('data-type', action);
            $('.confirmAction').attr('data-row', rowId);
            $('.confirmAction').html('Approve');
        }
        else if(action == 'REJECT'){
            $('#commentActionLabel').html('Reason (required) :');
            $('#commentActionLabel').addClass('required');
            $('.confirmAction').removeClass('btn-info btn-danger btn-secondary');
            $('.confirmAction').addClass('btn-danger');
            $('.confirmAction').attr('data-type', action);
            $('.confirmAction').attr('data-row', rowId);
            $('.confirmAction').html('Reject');
        }
        else if(action == 'SEND_BACK'){
            $('#sendBackContainer').find('div#sendBackTo').replaceWith('<div class="skeleton"></div>');
            $('#sendBackContainer').removeClass('d-none');
            $('#commentActionLabel').html('Reason (required) :');
            $('#commentActionLabel').addClass('required');
            $('.confirmAction').removeClass('btn-info btn-danger btn-secondary');
            $('.confirmAction').addClass('btn-secondary');
            $('.confirmAction').attr('data-type', action);
            $('.confirmAction').attr('data-row', rowId);
            $('.confirmAction').html('Send Back');
        }

        $('#popperAction').removeClass('d-none');
        popperInstance = Popper.createPopper(button, $('#popperAction')[0], {
            placement: 'top',
            modifiers: [
                {
                    name: 'offset',
                    options: {
                        offset: [0, 8],
                    },
                },
            ],
        });
        $('.autosize').autosize().trigger('change');

        if(action == 'SEND_BACK'){
            const formParams = {
                'tokenForm': $('#tokenFormAction').val(),
            };
            const sendBackOption = await getSendBackOptions(formParams);
            $('#sendBackContainer').find('div.skeleton').replaceWith('<div class="virtualSelectSendBackTo" name="sendBackTo"" id="sendBackTo"></div>');
            VirtualSelect.init({
                ele: '#sendBackTo',
                search: false,
                hideClearButton: true,
                options: sendBackOption,
                selectedValue: sendBackOption.length == 1 ? sendBackOption[0].value : null,
            });
        }
    }
    else if(action == 'PRINT') {
        console.log('print');
        $('#popperAction').removeClass('d-none');
        popperInstance = Popper.createPopper(button, $('#popperAction')[0], {
            placement: 'top',
            modifiers: [
                {
                    name: 'offset',
                    options: {
                        offset: [0, 8],
                    },
                },
            ],
        });
    }
}

function hidePopup() {
    if (popperInstance) {
        let action = $('#popperAction').attr('data-type');
        if (action !== '') {
            let existingIndex = commentBuffer.findIndex(item => item.action === action);
            if (existingIndex !== -1) {
                // Update existing item
                commentBuffer[existingIndex].comment = $('#commentAction').val().trim();
            }
            else if($('#commentAction').val().trim() != '') {
                // Push new item
                commentBuffer.push({
                    action: action,
                    comment: $('#commentAction').val().trim(),
                });
            }
        }

        $('#popperAction').attr('data-type', '');
        $('.popper-container').addClass('d-none');

        popperInstance.destroy();
    }
}

$(document).on('click', '.actionBtn', async function () {
    let $this = $(this);
    if($this.attr('data-type') == 'PRINT') {
        const printBackdrop = $(`
            <div class="modal-backdrop" style="position: fixed; top: 0; left: 0; z-index: 9999; background: rgba(255,255,255,0.8)">
                <div class="modal-content" style="width: 100%; height: 100%;display: flex; justify-content: center; align-items: center;">
                    <div class="center-container">
                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                    </div>
                    <p class="mt-3 fw-700 fs-5 text-company-emphasis">Preparing document...</p>
                </div>
            </div>
        `);
        $('body').append(printBackdrop);

        let token = $this.attr('data-token');
        let pdfUrl = `/printPdf?token=${token}`;

        const existingIframe = document.querySelector('body > iframe');
        if (existingIframe) {
            existingIframe.remove();
        }
        const iframe = document.createElement('iframe');
        iframe.src = pdfUrl;
        iframe.style.display = 'none';
        document.body.appendChild(iframe);

        iframe.onload = function() {
            try {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                printBackdrop.remove();
            } catch (e) {
                const newWindow = window.open('', '_blank');
                if (newWindow) {
                    const embed = newWindow.document.createElement('embed');
                    embed.src = pdfUrl;
                    embed.type = 'application/pdf';
                    embed.width = '100%';
                    embed.height = '100%';
                    newWindow.document.body.appendChild(embed);

                    newWindow.onload = function () {
                        newWindow.focus();
                        newWindow.print();
                        printBackdrop.remove();
                    };
                }
                else {
                    printBackdrop.remove();
                    console.error('Failed to open new window. Check pop-up blocker settings.');
                }
            }
        };
    }
    if($('#popperAction').attr('data-type') != $(this).attr('data-type')){
        Snackbar.close();
        clearValidation();

        if (popperInstance) {
            hidePopup();
        }

        $('#alertFormAction').addClass('d-none');
        if($(this).attr('data-type') == 'REJECT') {
            $('#alertFormAction').html('<span class="fw-semibold">“Reject”</span> means the approval form will be CLOSED');
            $('#alertFormAction').removeClass('d-none');
        }
        else if($(this).attr('data-type') == 'SEND_BACK') {
            $('#alertFormAction').html('<span class="fw-semibold">“Send Back”</span> means the form must be REVISE');
            $('#alertFormAction').removeClass('d-none');
        }

        const params = {
            'button': this,
            'action': $(this).attr('data-type'),
            'rowId': $(this).attr('data-row'),
        };
        showPopupAction(params);
    }
});

$(document).on('click', '.closePopper', function () {
    hidePopup();
});

$(document).on('click', '.confirmAction', function () {
    let $this = $(this);
    // let thisHtml = $this.html();
    Snackbar.close();
    clearValidation();
    const formData = new FormData($('#formAction')[0]);
    formData.append('actionType', $this.attr('data-type'));
    formData.append('rowId', $this.attr('data-row'));

    const escapedData = new FormData();
    for (let [key, value] of formData.entries()) {
        if (value instanceof File) {
            const safeFileName = escapeFilename(value.name);
            const safeFile = new File([value], safeFileName, { type: value.type });
            escapedData.append(key, safeFile);
        }
        else if (key.endsWith('[]')) {
            // Handle array fields
            const baseKey = key.replace('[]', '');
            const currentValues = escapedData.getAll(baseKey) || [];
            escapedData.delete(baseKey); // Hapus yang lama
            currentValues.push(escapeInput(value));
            currentValues.forEach(v => escapedData.append(baseKey + '[]', v));
        }
        else {
            // Handle regular fields
            escapedData.append(key, escapeInput(value));
        }
    }

    $(this).btnLoading(async function() {
        try {
            $('.confirmAction').prop('disabled', true);
            $this.html('<i class="fas fa-spinner fa-spin"></i> Please wait');
            const response = await fetch('/doc_approval/updateAction', {
                method: 'POST',
                body: escapedData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Referer': window.location.href,
                }
            });

            const result = await response.json();
            if (response.status === 200) {
                const rightContent = `/framePdf?token=${result.data.tokenForm}`;
                $('.right-column').html(`<object class="w-100 h-100" id="subfile_frame" data="${rightContent}" type="text/html"><param name="allowfullscreen" value="true"></object>`);

                document.querySelector('.card-footer-fixed').innerHTML = result['footerButton'];
                hidePopup();
                Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result['message']}` });
            }
            else if (response.status === 401) {
                Snackbar.show({ pos: 'bottom-center', duration: '6000', text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Unauthorized access, please login or refresh this page' });
            }
            else if (response.status === 422) {
                handleValidationErrors(result.errors);
                Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : ${result['message']}` });
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
    });
});

$(document).on('click', function (e) {
    if (!$(e.target).closest('.popper-container, .approveBtn, .rejectBtn, .sendBackBtn').length) {
        hidePopup();
    }
});

$(document).on('click', '.view-file', async function (event) {
    let $this = $(this);
    event.stopImmediatePropagation();
    $('.fullscreen-title').html($this.find('span.file-name').html());
    $('#fullscreen-content-aside').html(`<div class="loading-content">
                                        <div class="spinner-border text-company" role="status"></div>
                                        <p class="mt-2 text-company fs-7">Loading...</p>
                                    </div>`);
    $('body').addClass('overflow-hidden');
    $('.fullscreen-container-aside').removeClass('hide').addClass('show').trigger('shown');
    try {
        const response = await fetch(`/render?token=${$this.attr('data-token')}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Referer': window.location.href,
            }
        });

        const data = await response.json();
        if (response.status === 200) {
            if (data.status === 200) {
                // const { mimeType, fileExtension, fileContent } = fileInfo;
                // const container = document.getElementById('file-container');
                // container.innerHTML = ''; // Clear previous content
                $('#fullscreen-content-aside').html(`<object id="subfile_frame" data="${data.data.frameSrc}" type="text/html"><param name="allowfullscreen" value="true"></object>`);
            }
            else if (data.status === 404) {
                $('.loading-content').html(`<p class="mt-2 text-company fs-7"><i class="fa-regular fa-file-circle-xmark fa-lg fa-fw"></i> ${data.message}</p>`);
            }
        }
        else if (response.status === 401) {
            Snackbar.show({ pos: 'bottom-center', duration: '6000', text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Unauthorized access, please login or refresh this page' });
            $('.loading-content').html(`<p class="mt-2 text-company fs-7"><i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Unauthorized access, please login or refresh this page</p>`);
        }
        else if (response.status === 422) {
            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : ${data.message}` });
            $('.loading-content').html(`Failed : ${data.message}</p>`);
        }
        else if (response.status === 419) {
            Snackbar.show({ pos: 'bottom-center', text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : CSRF token mismatch, please refresh this page' });
            $('.loading-content').html(`<p class="mt-2 text-company fs-7"><i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : CSRF token mismatch, please refresh this page</p>`);
        }
        else {
            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${response.status} ${response.statusText}` });
            $('.loading-content').html(`<p class="mt-2 text-company fs-7"> ${response.status} ${response.statusText}</p>`);
        }
    }
    catch (error) {
        Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}` });
        $('.loading-content').html(`<p class="mt-2 text-company fs-7"><i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}</p>`);
    }
});

$(document).on('click', '.actionBtnHeader', async function (event) {
    let $this = $(this);
    if($this.attr('data-type') == 'ATTACHMENTS') {
        asideHide();
        $('.aside-title').html(`ORDER FORM`);
        $('.aside-content').html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
            <div class="container-content pb-5">
                <div class="full-height-column-wrapper d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                    <div class="center-container">
                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                        <div class="d-block fs-7 mt-2">Loading...</div>
                    </div>
                </div>
            </div>
        </form>`);

        $('.overlay-aside').addClass('show').trigger('shown');
        $('#globalAside').addClass('show').trigger('shown');
        $('body').addClass('overflow-hidden');
        $('#asideDetailForm').scrollTop(0);
        $('.autosize').autosize({ append: "\n" });

        $('.asideFooterBtn').html(`<div class="col-12 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
                                    </div>`);

        const params = {
            'type': $this.attr('data-type-flow'),
            'token': $this.attr('data-token'),
        };
        const attachmentList = await getAttachment(params);
        $('#asideForm').html(attachmentList.data.form);
    }
});

async function getAttachment(params) {
    try {
        const response = await fetch(`/doc_approval/getAttachment?type=${params['type']}&token=${params['token']}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'Referer': window.location.href
            },
        });

        const result = await response.json();
        if (response.status === 200) {
            return result;
        }
        else {
            console.error('HTTP Error:', response.status);
        }
    } catch (error) {
        return;
    }
}
