let selectedFiles = [], selectedFilesProperties = [], costCenterOption = [];
let selectedFilesDocument = [], selectedFilesPropertiesDocument = [];
let virtualSelectCodeOrder = {};
let suggestionController = null, suggestionTimeout = null;
const maxFileSize = 5 * 1024 * 1024;

var leftColScrollbarInstance;
// var leftColHistoryScrollbarInstance;
var rightColScrollbarInstance;
let currentPage = 1;
let currentPageHistory = 1;
let isLoading = false;
let isLoadingHistory = false;
let hasMoreData = true;
let hasMoreDataHistory = true;
let popperInstance;

let searchTimeoutSuggestion;
let currentHighlightIndexSuggestion = -1;
let suggestions = [];
let currentPageSuggestion = 1;
let isLoadingSuggestion = false;
let hasMoreDataSuggestion = true;
let currentQuerySuggestion = '';

window.addEventListener('load', function() {
    $('#filterCheckAllRequestOngoing').prop('checked', true).trigger('change');
});

$(document).on('click', '.filterTab', function (event) {
    if ($('#filterStatus').val() !== $(this).attr('data-status')) {
        event.stopImmediatePropagation();
        if($(this).attr('data-status') === 'ONGOING') {
            $('#filterStatus').val($(this).attr('data-status'));
            $('#historyContainer').toggleClass('d-none d-block');
            $('#ongoingContainer').toggleClass('d-none d-block');
            currentPage = 1;
            isLoading = false;
            hasMoreData = true;
            loadLeftColumn();
        }
        else if($(this).attr('data-status') === 'HISTORY') {
            $('#filterStatus').val($(this).attr('data-status'));
            $('#ongoingContainer').toggleClass('d-none d-block');
            $('#historyContainer').toggleClass('d-none d-block');
            if ($.fn.DataTable.isDataTable('#historyTable')) {
                let table = $('#historyTable').DataTable();
                let scrollBody = $(table.table().node()).parent();
                let currentScrollTop = scrollBody.scrollTop();
                table.ajax.reload(function () {
                    scrollBody.scrollTop(currentScrollTop);
                }, false);
            }
            else {
                historyTable();
            }
        }
    }
});

$(document).on('change', '.filterCheckAllRequest', function (event) {
    if($(this).prop('checked') == true) {
        $(this).val('TRUE');
        if($(this).attr('data-status') == 'ONGOING') {
            $('.filterCheckRequestOngoing').prop('checked', true);
            $('.filterCheckRequestOngoing').closest('.dropdown-item').addClass('active');
        }
        else {
            $('.filterCheckRequestHistory').prop('checked', true);
            $('.filterCheckRequestHistory').closest('.dropdown-item').addClass('active');
        }
    }
    else {
        $(this).val('FALSE');
        if($(this).attr('data-status') == 'ONGOING') {
            $('.filterCheckRequestOngoing').prop('checked', false);
            $('.filterCheckRequestOngoing').closest('.dropdown-item').removeClass('active');
        }
        else {
            $('.filterCheckRequestHistory').prop('checked', false);
            $('.filterCheckRequestHistory').closest('.dropdown-item').removeClass('active');
        }
    }

    if($('#filterStatus').val() == $(this).attr('data-status')) {
        event.stopImmediatePropagation();
        currentPage = 1;
        isLoading = false;
        hasMoreData = true;
        loadLeftColumn();
    }
});

$(document).on('click', '.filterCheckRequest', function (event) {
    event.stopImmediatePropagation();
    if($(this).prop('checked') == true){
        $(this).closest('.dropdown-item').addClass('active');
    }
    else if($(this).prop('checked') == false){
        if($(this).attr('data-status') == 'ONGOING') {
            $('#filterCheckAllRequestOngoing').val('FALSE');
            $('#filterCheckAllRequestOngoing').prop('checked', false);
        }
        else {
            $('#filterCheckAllRequestHistory').val('FALSE');
            $('#filterCheckAllRequestHistory').prop('checked', false);
        }
        $(this).closest('.dropdown-item').removeClass('active');
    }

    currentPage = 1;
    isLoading = false;
    hasMoreData = true;
    loadLeftColumn();
});

$(document).on('change', '.filterCheckRequest', async function (event) {
    event.stopImmediatePropagation();
    loadLeftColumn();
});

$(document).on('click', '.card-link-content', async function (event) {
    const $this = $(this);
    const container = $this.parent('div').parent('div');
    if ($this.hasClass('active')) {
        $this.removeClass('active');
        if($this.attr('data-form') == 'PO_INSPECTION') {
            $('#selectPoInspection').prop('disabled', true)
        }
        else {
            clearRightContent();
        }
    }
    else {
        container.find('.card-link-content').removeClass('active');
        $this.addClass('active');
        if($this.attr('data-form') == 'ORDER_FORM' && $this.attr('data-type') == 'MY_REQUEST') {
            if($this.attr('data-type') == 'VIEW_ONGOING') {
                event.stopImmediatePropagation();
                const params = {
                    'token': result.data.tokenForm,
                    'rowId': $this.attr('data-row'),
                    'type': 'ONGOING',
                };

                viewForm(params);
            }
            else {
                Snackbar.show({
                    pos: 'bottom-center',
                    duration: '6000',
                    text: `<i class="fa-regular fa-circle-notch fa-spin fs-6 fa-fw text-info"></i> Processing...`
                });

                const token = $this.attr('data-token');
                const form = $this.attr('data-form');

                try {
                    const response = await fetch(`/proc_pur/getComparison?type=${$this.attr('data-type')}&form=${form}&token=${token}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json',
                            'Referer': window.location.href
                        }
                    });

                    const result = await response.json();
                    if (response.status === 401) {
                        $('.card-footer-fixed').addClass('border-top-0');
                        Snackbar.close();
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
                                window.location.href = result.redirect_uri;
                            }
                        }, 1000);
                    }
                    else {
                        Snackbar.close();
                        if (result.status === 200) {
                            if(result.data.processedStatus === true && result.data.multiVendor === true) {
                                $('#modalMessageTitle').html(result.data.title);
                                $('#modalMessageBody').html(result.data.listItem);
                                $('#modalMessageFooter').html(`<div class="container-fluid p-0">
                                    <div class="row justify-content-center w-100 mx-0">
                                        <div class="col-sm-12 col-md-8 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                <i class="fas fa-xmark"></i> Close
                                            </button>
                                        </div>
                                    </div>
                                </div>`);
                                $('#modalMessageDialog').removeClass('top-20');
                                $('#modalMessageDialog').removeClass('modal-dialog-scrollable').addClass('modal-dialog-scrollable');
                                $('#modalMessage').modal('show');
                            }
                            else {
                                event.stopImmediatePropagation();
                                const params = {
                                    'token': result.data.tokenForm,
                                    'rowId': $this.attr('data-row'),
                                    'type': 'ONGOING',
                                };

                                viewForm(params);
                            }
                        }
                        else if (result.status === 404) {
                            Snackbar.show({
                                pos: 'bottom-center',
                                duration: '6000',
                                text: `<i class="fa-solid fa-circle-exclamation fa-lg fa-fw text-error"></i> 404 Not found`,
                            });
                        }
                        else {
                            Snackbar.show({
                                pos: 'bottom-center',
                                duration: '6000',
                                text: `<i class="fa-solid fa-circle-exclamation fa-lg fa-fw text-error"></i> Failed to get data`,
                            });
                        }
                    }
                }
                catch (error) {
                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}` });
                }
            }
        }
        else if($this.attr('data-form') == 'PO_INSPECTION') {
            $('#selectPoInspection').prop('disabled', false)
        }
        else {
            const params = {
                'token': $this.attr('data-token'),
                'rowId': $this.attr('data-row'),
                'type': 'ONGOING',
            };
            viewForm(params);
        }
    }
});

$(document).off('click', '.multiplePoList').on('click', '.multiplePoList', async function (event) {
    const $this = $(this);
    Snackbar.close();
    const params = {
        'token': $this.attr('data-token'),
        'type': $this.attr('data-type'),
    };
    $('.modal').modal('hide');
    viewForm(params);
});

$(document).off('click', '.progressList').on('click', '.progressList', async function (event) {
    let $this = $(this);
    $('#modalMessage').modal('hide');
    event.stopImmediatePropagation();
    const params = {
        'title': `Progress ${$this.attr('data-selectedversion')}`,
        'dataType': $this.attr('data-type'),
        'url': `/doc_approval/getProgress?token=${$this.attr('data-token')}`,
    };
    viewProgress(params);
});

$(document).on('click', '.docVersionItem', function (event) {
    const $this = $(this);
    if (!$this.hasClass('active')) {
        $('.docVersionItem').removeClass('active');
        $this.addClass('active');
        const params = {
            'token': $this.attr('data-token'),
            'rowId': null,
            'type': 'ONGOING',
        };
        viewForm(params);
    }
});

async function loadLeftColumn() {
    if (isLoading || !hasMoreData) return;
    isLoading = true;
    let skeletonCard = `<div class="center-container skeletonCard">
                            <div class="stripes-red-blue stripes-red-blue-md"></div>
                            <div class="d-block fs-7 mt-2">Loading...</div>
                        </div>`;

    if(currentPage == 1){
        $('#leftColumnOngoing').html(`<div class="card full-height-column-wrapper">
                                    <div class="card-body d-flex justify-content-center align-items-center">
                                        <div class="center-container">
                                            <div class="stripes-red-blue stripes-red-blue-md"></div>
                                            <div class="d-block fs-7 mt-2">Loading...</div>
                                        </div>
                                    </div>
                                </div>`);
        rightContentSkeleton('FETCH');
    }
    else{
        document.querySelector('#leftColumnOngoing').insertAdjacentHTML('beforeend', skeletonCard);
    }

    try {
        let formData;
        // $('#badgeCountOngoing').html('<i class="fa-regular fa-circle-notch fa-spin"></i>');
        formData = new FormData($('#filterOngoing')[0]);
        if($('#filterCheckAllRequestOngoing').val() == 'TRUE'){
            formData.delete('requestType[]');
        }

        formData.append('type', 'MY_REQUEST');
        formData.append('page', currentPage);

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

        const response = await fetch(`/doc_approval/ongoing`, {
            method: 'POST',
            body: escapedData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'Referer': window.location.href
            }
        });

        const data = await response.json();
        if (data.status === 200) {
            if(currentPage == 1) {
                $('#leftColumnOngoing').html('');
            }
            else{
                $('.skeletonCard').remove();
            }

            if(data.ongoing >= 1) {
                let countOngoing = data.ongoing;
                if(data.ongoing >= 100) {
                    countOngoing = '99+';
                }
                $('#badgeCountOngoing').html(countOngoing);
                $('#badgeCountOngoing').removeClass('d-none');
            }
            else {
                $('#badgeCountOngoing').addClass('d-none');
            }

            if (data.data.length > 0) {
                data.data.forEach((item, index) => {
                    setTimeout(() => {
                        const newElement = document.createElement('div');
                        newElement.classList.add('fade-in-up');
                        newElement.innerHTML = item.html;
                        document.querySelector('#leftColumnOngoing').appendChild(newElement);

                        if (index === data.data.length - 1) {
                            requestAnimationFrame(() => {
                                if (!leftColScrollbarInstance) {
                                    leftColScrollbarInstance = new ScrollbarCustom('#leftColumnOngoing', {
                                        top: null,
                                        right: 10,
                                        overflowX: 'none',
                                        overflowY: 'scroll'
                                    });
                                    leftColScrollbarInstance.forceUpdate();
                                }
                                else {
                                    leftColScrollbarInstance.refresh();
                                }
                            });
                        }
                    }, index * 100);
                });

                if(currentPage == 1) rightContentSkeleton('PRE_SELECT');

                currentPage++;
                hasMoreData = data.hasMorePages;
            }
            else {
                hasMoreData = false; // No more data to load
                if(currentPage == 1){
                    document.querySelector('#leftColumnOngoing').innerHTML = `<div class="card full-height-column-wrapper">
                                                                            <div class="card-body d-flex justify-content-center align-items-center">
                                                                                <div class="text-center">
                                                                                    <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                                                    <div class="d-block fs-7 mt-2">No data</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>`;
                    const rightContent = `<div class="full-height-column-wrapper">
                                            <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                <div class="text-center">
                                                    <i class="fa-light fa-file fs-1"></i>
                                                    <div class="d-block fs-7 mt-2">Select an item to view</div>
                                                </div>
                                            </div>
                                        </div>`;
                    $('.card-title-right-column').html('Detail Form');
                    $('.actionHeaderContainer').html('');
                    $('.card-footer-fixed').addClass('border-top-0');
                    $('.card-footer-fixed').html('');
                    $('#asideDetailForm').html(rightContent);
                    $('.right-column').html(rightContent);
                }

                requestAnimationFrame(() => {
                    if (!leftColScrollbarInstance) {
                        leftColScrollbarInstance = new ScrollbarCustom('#leftColumnOngoing', {
                            top: null,
                            right: 10,
                            overflowX: 'none',
                            overflowY: 'scroll'
                        });
                        leftColScrollbarInstance.forceUpdate();
                    }
                    else {
                        leftColScrollbarInstance.refresh();
                    }
                });
            }
        }
        else if (response.status === 419) {
            Snackbar.show({ pos: 'bottom-center', text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : CSRF token mismatch, please refresh this page' });
        }
        else if (data.status === 404) {
            $('.skeletonCard').remove();
            document.querySelector('#leftColumnOngoing').innerHTML = `<div class="card full-height-column-wrapper">
                                                                    <div class="card-body d-flex justify-content-center align-items-center">
                                                                        <div class="text-center">
                                                                            <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                                            <div class="d-block fs-7 mt-2">${data.message}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>`;
            const rightContent = `<div class="full-height-column-wrapper">
                                            <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                <div class="text-center">
                                                    <i class="fa-light fa-file fs-1"></i>
                                                    <div class="d-block fs-7 mt-2">Select an item to view</div>
                                                </div>
                                            </div>
                                        </div>`;
            $('.card-title-right-column').html('Detail Form');
            $('.actionHeaderContainer').html('');
            $('.card-footer-fixed').addClass('border-top-0');
            $('.card-footer-fixed').html('');
            $('#asideDetailForm').html(rightContent);
            $('.right-column').html(rightContent);
        }
        else {
            $('.skeletonCard').remove();
            console.error('HTTP Error:', response.status);
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        isLoading = false;
    }
}

function rightContentSkeleton(type){
    if(type == 'FETCH' || type == 'FETCH_ASIDE'){
        const rightContent = `<div class="full-height-column-wrapper">
                                    <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                        <div class="center-container">
                                            <div class="stripes-red-blue stripes-red-blue-md"></div>
                                            <div class="d-block fs-7 mt-2">Loading...</div>
                                        </div>
                                    </div>
                                </div>`;

        if ($(window).width() < 768) {
            $('.aside-title').html(`<div class="skeleton mb-0" style="width: 200px; height: 24px"></div>`);
            $('#asideDetailForm').html(rightContent);
            $('.aside-footer').html(`<div class="container-fluid p-0">
                                        <div class="row justify-content-center w-100 mx-0 asideFooterBtn">
                                            <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <div class="skeleton w-100"></div>
                                            </div>
                                            <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <div class="skeleton w-100"></div>
                                            </div>
                                        </div>
                                    </div>`);

            if(type == 'FETCH_ASIDE'){
                asideHide();
                $('.overlay-aside').addClass('show').trigger('shown');
                $('#right-column-aside').addClass('show').trigger('shown');
                $('body').addClass('overflow-hidden');
                $('#asideDetailForm').scrollTop(0);
            }
        }
        else {
            $('.card-title-right-column').html(`<div class="skeleton mb-0" style="width: 250px; height: 24px"></div>`);
            $('.actionHeaderContainer').html(`<div class="skeleton mb-0" style="width: 100px; height: 24px"></div>`);
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
    }
    else if(type == 'PRE_SELECT') {
        clearRightContent();
    }
}

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
        $('.aside-footer').html(`<div class="container-fluid p-0">
                                    <div class="row justify-content-center w-100 mx-0 asideFooterBtn">
                                        <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <div class="skeleton w-100"></div>
                                        </div>
                                        <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <div class="skeleton w-100"></div>
                                        </div>
                                    </div>
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
    const form = params['form'];
    const source = params['source'];
    const type = params['type'];
    Snackbar.close();
    if(type == 'HISTORY') {
        $('#modalDocumentFormBodyHistory').removeClass('pt-0 pb-5 p-0 overflow-x-hidden overflow-y-hidden').addClass('p-0 overflow-y-hidden overflow-x-hidden');
        $('#modalDocumentFormTitleHistory').html(`<div class="skeleton mb-0" style="width: 250px; height: 24px"></div>`);
        $('.actionHeaderContainerHistory').html(`<div class="skeleton mb-0" style="width: 100px; height: 24px"></div>`);
        $('#modalDocumentFormBodyHistory').html(`<div class="row min-vh-75">
                                    <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                        <div class="center-container">
                                            <div class="stripes-red-blue stripes-red-blue-md"></div>
                                            <div class="d-block fs-7 mt-2">Loading...</div>
                                        </div>
                                    </div>
                                </div>`);
        $('#modalDocumentFormFooterHistory').html(`<div class="container-fluid p-0">
                                                <div class="row justify-content-center w-100 mx-0">
                                                    <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                        <div class="skeleton mb-0 w-100 w-md-auto me-md-2"></div>
                                                    </div>
                                                    <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-start mb-2 px-0">
                                                            <div class="skeleton mb-0 w-100 w-md-auto me-md-2"></div>
                                                    </div>
                                                </div>
                                            </div>`);

        if(!$('#modalDocumentFormHistory').hasClass('show')) {
            $('.modal').modal('hide');
            $('#modalDocumentFormHistory').modal('show');
        }
    }
    else {
        // $('#modalDocumentFormBodyHistory').removeClass('pt-0 pb-5 p-0 overflow-x-hidden overflow-y-hidden').addClass('pt-0 pb-5 overflow-x-hidden overflow-y-auto');
        // return false;
        $('#asideDetailForm').html('');
        $('.popper-area').html('');
        if ($(window).width() < 768) {
            // rightContentSkeleton('FETCH_ASIDE');
            // $('#modalDocumentForm').find('div.modal-dialog').removeClass('modal-dialog-scrollable');
            $('#modalDocumentForm').find('div.modal-content').removeClass('h-100').addClass('h-100');
            $('.modal').modal('hide');
        }
        else{
            rightContentSkeleton('FETCH');
        }
    }

    try {
        const response = await fetch(`/doc_approval/viewForm?type=${type}&source=${source}&form=${form}&token=${token}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'Referer': window.location.href
            }
        });

        const data = await response.json();
        if (response.status === 401) {
            $('.card-footer-fixed').addClass('border-top-0');
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
        else if(type == 'HISTORY') {
            if (data.status === 200) {
                const footerButton = data.data.footerButton;
                $('#modalDocumentFormTitleHistory').html(data.data.title);
                $('.actionHeaderContainerHistory').html(`
                    <button class="btn btn-default btn-sm d-none d-md-inline actionBtnHeader" data-token="${data.data.tokenAttachment}" data-type="PRINT" data-selectedversion="${data.data.selectedVersionLabel}" title="Print Documents"><i class="fa-solid fa-print fa-fw"></i></button>
                    <button class="btn btn-default btn-sm actionBtnHeader" data-token="${data.data.tokenAttachment}" data-type="DOWNLOAD" data-selectedversion="${data.data.selectedVersionLabel}" title="Downloads"><i class="fa-solid fa-arrow-down-to-line fa-fw"></i></button>
                    <button class="btn btn-default btn-sm actionBtnHeader" data-token="${data.data.tokenAttachment}" data-type="ATTACHMENTS" data-type-flow="PURCHASING" data-selectedversion="${data.data.selectedVersionLabel}"><i class="fa-solid fa-bars-progress"></i> View Attachments</button>`);
                $('#modalDocumentFormBodyHistory').html(`<object class="w-100 h-100" id="subfile_frame" data="/framePdf?token=${data.data.tokenForm}" type="text/html"><param name="allowfullscreen" value="true"></object>`);
                // {$('#modalDocumentFormBody').html(data.data.form);}
                let modalFooterButton = footerButton;
                modalFooterButton = modalFooterButton.replaceAll('-outline', '');
                modalFooterButton = modalFooterButton.replaceAll('col-sm-3', '');
                modalFooterButton = modalFooterButton.replaceAll('actionBtn', 'actionBtn w-100 w-md-auto me-md-2');

                const buttonRegex = /<button[^>]*>[\s\S]*?<\/button>/g;
                const buttonsArray = modalFooterButton.match(buttonRegex);

                // Proses setiap button untuk disesuaikan dengan layout modal
                const processedButtons = buttonsArray.map(button => {
                    return `
                        <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                        ${button}
                        </div>
                    `;
                }).join('');

                modalFooterButton = `
                                        <div class="container-fluid p-0">
                                            <div class="row justify-content-center w-100 mx-0">
                                            <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                <i class="fas fa-xmark"></i> Close
                                                </button>
                                            </div>
                                            ${processedButtons}
                                            </div>
                                        </div>
                                        `;

                $('#modalDocumentFormFooterHistory').html(`<div class="popper-area-modal"></div>${modalFooterButton}`);
                $('#modalDocumentFormBodyHistory').scrollTop(0);
                $('.table-responsive').scrollLeft(0);
                $('.autosize').autosize().trigger('change');
            }
            else if (data.status === 404) {
                $('#modalDocumentFormTitle').html('Detail Form');
                $('.actionHeaderContainerHistory').html('');
                $('#modalDocumentFormBodyHistory').html(data.data.form);
                $('#modalDocumentFormFooterHistory').html(data.data.footerButton);
            }
            else {
                $('#modalDocumentFormTitleHistory').html('Detail Form');
                $('.actionHeaderContainerHistory').html('');
                $('#modalDocumentFormBodyHistory').html(`<div class="row min-vh-75">
                                                    <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                        <div class="center-container">
                                                            <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                            <div class="d-block fs-7 mt-2">Failed to get data</div>
                                                        </div>
                                                    </div>
                                                </div>`);
                $('#modalDocumentFormFooterHistory').html(`<div class="container-fluid p-0">
                                                        <div class="row justify-content-center w-100 mx-0">
                                                            <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                                <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                    <i class="fas fa-xmark"></i> Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>`);
            }
        }
        else {
            if (data.status === 200) {
                const rightTitle = data.data.title;
                const rightContent = `/framePdf?token=${data.data.tokenForm}`;
                const footerButton = data.data.footerButton;
                if ($(window).width() < 768) {
                    $('#modalDocumentFormBody').removeClass('pt-0 pb-5 p-0').addClass('p-0');
                    $('#modalDocumentFormTitle').html(rightTitle);
                    $('#modalDocumentFormBody').html(`<object class="w-100 h-100" id="subfile_frame" data="${rightContent}" type="text/html"><param name="allowfullscreen" value="true"></object>`);

                    let modalFooterButton = footerButton;
                    modalFooterButton = modalFooterButton.replaceAll('-outline', '');
                    modalFooterButton = modalFooterButton.replaceAll('col-sm-3', '');
                    modalFooterButton = modalFooterButton.replaceAll('actionBtn', 'actionBtn w-100 w-md-auto me-md-2');

                    const buttonRegex = /<button[^>]*>[\s\S]*?<\/button>/g;
                    const buttonsArray = modalFooterButton.match(buttonRegex);

                    // Proses setiap button untuk disesuaikan dengan layout modal
                    const processedButtons = buttonsArray.map(button => {
                    return `
                        <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                        ${button}
                        </div>
                    `;
                    }).join('');

                    modalFooterButton = `
                                            <div class="container-fluid p-0">
                                                <div class="row justify-content-center w-100 mx-0">
                                                <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                    <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                    <i class="fas fa-xmark"></i> Close
                                                    </button>
                                                </div>
                                                ${processedButtons}
                                                </div>
                                            </div>
                                            `;

                    $('#modalDocumentFormFooter').html(`<div class="popper-area-modal"></div>${modalFooterButton}`);
                    // $('.popper-area-modal').html(popperForm);
                    $('#modalDocumentForm').modal('show');
                }
                else{
                    $('.card-title-right-column').html(rightTitle);
                    $('.actionHeaderContainer').html(`
                        <button class="btn btn-default btn-sm d-none d-md-inline actionBtnHeader" data-token="${data.data.tokenAttachment}" data-type="PRINT" data-selectedversion="${data.data.selectedVersionLabel}" title="Print Documents"><i class="fa-solid fa-print fa-fw"></i></button>
                        <button class="btn btn-default btn-sm actionBtnHeader" data-token="${data.data.tokenAttachment}" data-type="DOWNLOAD" data-selectedversion="${data.data.selectedVersionLabel}" title="Downloads"><i class="fa-solid fa-arrow-down-to-line fa-fw"></i></button>
                        <button class="btn btn-default btn-sm actionBtnHeader" data-token="${data.data.tokenAttachment}" data-type="ATTACHMENTS" data-type-flow="REQUEST" data-selectedversion="${data.data.selectedVersionLabel}"><i class="fa-solid fa-bars-progress"></i> View Attachments</button>`);
                    // $('.right-column').html(rightContent);
                    $('.right-column').html(`<object class="w-100 h-100" id="subfile_frame" data="${rightContent}" type="text/html"><param name="allowfullscreen" value="true"></object>`)
                    $('.card-footer-fixed').html(footerButton);
                }

                if(footerButton != '') {
                    $('.card-footer-fixed').removeClass('border-top-0');
                }
            }
            else if (data.status === 404) {
                $('.card-footer-fixed').addClass('border-top-0');
                const rightContent = data.data.form;
                if ($(window).width() < 768) {
                    $('.aside-title').html('Detail Form');
                    $('#asideDetailForm').html(rightContent);
                    $('.aside-footer').html('');
                }
                else{
                    $('.card-title-right-column').html('Detail Form');
                    $('.actionHeaderContainer').html('');
                    $('.right-column').html(rightContent);
                    $('.card-footer-fixed').html('');
                }
            }
            else if (response.status === 401) {
                $('.card-footer-fixed').addClass('border-top-0');
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
                $('.card-footer-fixed').addClass('border-top-0');
                $('.card-title-right-column').html('Form');
                $('.actionHeaderContainer').html('');
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
        }
    }
    catch (error) {
        if(form == 'ORDER_FORM') {
            $('#modalDocumentFormTitleHistory').html('Detail Form');
            $('.actionHeaderContainerHistory').html('');
            $('#modalDocumentFormBodyHistory').html(`<div class="row min-vh-75">
                                                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                    <div class="center-container">
                                                        <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                        <div class="d-block fs-7 mt-2">Failed to get data</div>
                                                    </div>
                                                </div>
                                            </div>`);
            $('#modalDocumentFormFooterHistory').html(`<div class="container-fluid p-0">
                                                    <div class="row justify-content-center w-100 mx-0">
                                                        <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                            <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                <i class="fas fa-xmark"></i> Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>`);
        }
    } finally {

    }
}

var historyTable = (function() {
    let $container = $('#historyTableContainer');
    let containerHeight = $container.height();
    let headerHeight = 190;
    let footerFixedHeight = 60;
    let scrollHeight = containerHeight - headerHeight;
    // let tableContainerHeight = $container.find('.table-container').height();
    var tableId = 'historyTable';
    var table = $(`#${tableId}`).DataTable({
        scrollY: scrollHeight + 'px',
        scrollCollapse: true,
        paging: true,
        processing: true,
        serverSide: true,
        responsive: false,
        ordering: false,
        dom: '<"row"<"col-sm-12"tr>><"dt-footer-fixed"<"d-flex justify-content-between align-items-center mt-2"ilp>>',
        pageLength: 25,

        ajax: {
            url: 'documentHistory',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function (d) {
                let $form = $(`#${tableId}`).closest('.table-container').find('.formSearchTable');
                let formData = $form.serializeArray();
                formData.forEach(function (item) {
                    d[item.name] = item.value;
                });
            },
        },
        drawCallback: function(settings) {
            $(this).parent().scrollTop(0);
            let tableContainerHeight = $(this).closest('.table-container').height();
            let tableFormHeight = $(this).closest('.table-container').find('form.form-horizontal').outerHeight(true);
            let theadHeight = $(this).closest('.dt-scroll').find('.dt-scroll-head').outerHeight(true);
            let footerFixedHeight = $(this).closest('.dt-container').find('.dt-footer-fixed').height();
            let scrollHeight = tableContainerHeight - tableFormHeight - theadHeight - footerFixedHeight;
            $(this).parent().css({'height': scrollHeight + 'px', 'max-height': scrollHeight + 'px'});
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false, className: 'text-center', width: '4%'},
            {data: 'docName', type: 'string', searchable: false, orderable: false, width: '10%'},
            {data: 'docNo', type: 'string', searchable: false, orderable: false, width: '13%'},
            {data: 'companyName', type: 'string', searchable: false, orderable: false, width: '17%'},
            {data: 'deptName', type: 'string', searchable: false, orderable: false, width: '16%'},
            {data: 'priority', type: 'string', searchable: false, orderable: false, width: '6%'},
            {data: 'items', type: 'string', searchable: false, orderable: false, width: '24%', renderHtml: true, render: (data) => data.replace(/\n/g, '<br>')},
            {data: 'status', type: 'string', searchable: false, orderable: false, className: 'text-center', width: '5%'},
            {data: 'action', name: 'action', searchable: false, orderable: false, className: 'text-center', width: '5%'}
        ]
    }).on('preXhr.dt', function(e, settings, data) {
        $(`#${settings.sTableId} tbody`).html(generateSkeletonRows(settings));
    })
    .on('xhr.dt', function(e, settings, data) {
        $(`#${settings.sTableId} tbody`).find('.skeleton-row').remove();
    }).on('error.dt', function(e, settings, data) {
        $(`#${settings.sTableId} tbody`).find('.skeleton-row').remove();
    });

    let customSearch = $(`#${tableId}_wrapper`).prev('.formSearchTable');
    let wrapper = $(`#${tableId}_wrapper .dt-search`);
    wrapper.empty();
    wrapper.append(customSearch);
    return table;
});

document.querySelector('#leftColumnOngoing').addEventListener('scroll', () => {
    const leftColumn = document.querySelector('#leftColumnOngoing');
    const scrollThreshold = 100; // Threshold in pixels before the bottom to trigger loading

    if (leftColumn.scrollTop + leftColumn.clientHeight >= leftColumn.scrollHeight - scrollThreshold) {
        loadLeftColumn(); // Load more data when near the bottom
    }
});

$(document).on('click', '.sortBySelectOngoing', function (event) {
    const $this = $(this);
    if ($this.hasClass('active')) {
        $this.find('input').prop('checked', true);
        return;
    }
    else{
        event.stopImmediatePropagation();
        $this.closest('ul').find('.sortBySelectOngoing').removeClass('active');
        $this.closest('ul').find('input').prop('checked', false);
        $this.addClass('active');
        $this.find('input').prop('checked', true);
        currentPage = 1;
        isLoading = false;
        hasMoreData = true;
        loadLeftColumn();
    }
});

$(document).on('change', '.sortBy', async function (event) {
    event.stopImmediatePropagation();
    currentPage = 1;
    isLoading = false;
    hasMoreData = true;
    loadLeftColumn();
});

$(document).on('click', '.searchFilterClear', function(event) {
    let isCleared = $(this).closest('.form-group').find('input.search').attr('data-iscleared');
    $(this).closest('.form-group').find('input.search').val('');
    $(this).closest('.form-group').find('input.search').focus();
    $(this).addClass('d-none');

    if (isCleared == 'false') {
        event.stopImmediatePropagation();
        currentPage = 1;
        isLoading = false;
        hasMoreData = true;
        loadLeftColumn();
    }

    $(this).closest('.form-group').find('input.search').attr('data-iscleared', 'true');
});

$(document).on('click', '.searchFilter', function(event) {
    let isCleared = $(this).closest('.form-group').find('input.search').attr('data-iscleared');
    if (isCleared == 'false') {
        event.stopImmediatePropagation();
        currentPage = 1;
        isLoading = false;
        hasMoreData = true;
        loadLeftColumn();
        $(this).attr('data-iscleared', 'false');
    }
});

$(document).on('keydown', '.search', function(event) {
    const currentValue = $(this).val().trim();
    let isCleared = $(this).attr('data-iscleared');
    if (event.key === 'Enter' || event.keyCode === 13) {
        if (currentValue.length === 0 && isCleared == 'false') {
            event.stopImmediatePropagation();
            currentPage = 1;
            isLoading = false;
            hasMoreData = true;
            loadLeftColumn();
            $(this).attr('data-iscleared', 'true');
        }
        else if (currentValue.length > 0) {
            event.stopImmediatePropagation();
            currentPage = 1;
            isLoading = false;
            hasMoreData = true;
            loadLeftColumn();
            $(this).attr('data-iscleared', 'false');
        }

        event.preventDefault();
    }
});

$(document).on('input', '.search', function(event) {
    clearTimeout(debounceTimeout);
    const currentValue = $(this).val().trim();
    let isCleared = $(this).attr('data-iscleared');
    if(currentValue.length === 0) {
        $(this).closest('.form-group').find('button.searchFilterClear').addClass('d-none');
        debounceTimeout = setTimeout(function () {
            if (isCleared == 'false') {
                event.stopImmediatePropagation();
                currentPage = 1;
                isLoading = false;
                hasMoreData = true;
                loadLeftColumn();
            }
        }, 1000);

        $(this).attr('data-iscleared', 'true');
    }
    else {
        $(this).closest('.form-group').find('button.searchFilterClear').removeClass('d-none');
        $(this).attr('data-iscleared', 'false');
    }
});

$(document).on('click', '.sortByOption', function (event) {
    const $this = $(this);
    if ($this.hasClass('active')) {
        return;
    }
    else{
        $this.closest('ul').find('.sortByOption').removeClass('active');
        $this.addClass('active');
        $this.closest('ul').find('input.sortBy').val($this.attr('data-value')).trigger('change');
    }
});

$(document).on('mousedown', '.view-file', function (e) {
    if (e.detail > 1) {
        e.preventDefault();
    }
});

$(document).on('click', '.view-file', async function (event) {
    let $this = $(this);
    event.stopImmediatePropagation();
    const selectedText = window.getSelection().toString().trim();
    if (selectedText || $this.hasClass('active')) {
        event.preventDefault();
        return false;
    }

    $('.view-file').removeClass('active');
    $this.addClass('active');
    $('.fullscreen-title').html($this.find('span.file-name').html());
    $('#fullscreen-content-aside').html(`<div class="loading-content">
                                            <div class="center-container text-white">
                                                <div class="stripes-red-blue stripes-red-blue-md"></div>
                                                <div class="d-block fs-7 mt-2">Loading...</div>
                                            </div>
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
            Snackbar.show({ pos: 'bottom-center', duration: '6000', text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Unauthorized access, plase login or refresh this page' });
            $('.loading-content').html(`<p class="mt-2 text-company fs-7"><i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Unauthorized access, plase login or refresh this page</p>`);
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

$(document).off('click', '.view-file-fullscreen').on('click', '.view-file-fullscreen', async function (event) {
    let $this = $(this);
    event.stopImmediatePropagation();
    $('.fullscreen-title').html($this.find('span.file-name').html());
    $('#fullscreen-content-container').html(`<div class="loading-content">
                                                <div class="center-container text-white">
                                                    <div class="stripes-red-blue stripes-red-blue-md"></div>
                                                    <div class="d-block fs-7 mt-2">Loading...</div>
                                                </div>
                                            </div>`);
    $('body').addClass('overflow-hidden');
    $('.fullscreen-container').removeClass('hide').addClass('show').trigger('shown');
    try {
        const response = await fetch(`/render?token=${$this.data('token')}`, {
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
                $('#fullscreen-content-container').html(`<object id="subfile_frame" data="${data.data.frameSrc}" type="text/html"><param name="allowfullscreen" value="true"></object>`);
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

$('#modalDocumentForm').on('hidden.coreui.modal', function (){
    selectedFiles = [];
    selectedFilesProperties = [];
    selectedFilesDocument = [];
    selectedFilesPropertiesDocument = [];
});

$(document).on('click', '.createNewRequest', async function (event) {
    const $this = $(this);
    clearValidation();
    $('#modalNewDocumentTitle').html('New Approval Request');
    $('#newRequestNext').attr('data-request-type', 'NEW_APPROVAL_REQUEST');
    $(`#documentTypeContainer`).html('');
    const skeleton = `
            <div class="row">
                <div class="col-sm-12 mb-3">
                    <label for="newDocumentType" class="form-label">Document Type<span class="required"></span> :</label>
                    <div class="skeleton" id="newDocumentType"></div>
                </div>
            </div>`;
    $('#documentTypeContainer').html(skeleton);
    $('#modalNewDocument').modal('show');
    const params = {
        'viewType': 'REQUEST',
    };
    let optionDocumentType = await getDocumentTypeRequest(params);
    $('#newDocumentType').replaceWith(`<select class="select2" name="newDocumentType" id="newDocumentType"><option></option></select>`);
    $('#newDocumentType').select2({
        dropdownParent: $('#modalNewDocument'),
        allowClear: false,
        placeholder: '-- Select --',
        // data: optionDocumentType,
        data: optionDocumentType.data.map(group => ({
            text: group.groupName,
            children: group.items.map(item => ({
                id: item.id,
                text: item.text,
            })),
        })),
    }).trigger('change');
});

$(document).on('click', '#createNewReport', async function (event) {
    const $this = $(this);
    clearValidation();
    $('#modalNewDocumentTitle').html('Report Type');
    $('#newRequestNext').attr('data-request-type', 'NEW_REPORT');
    $(`#documentTypeContainer`).html('');
    const skeleton = `
            <div class="row">
                <div class="col-sm-12 mb-3">
                    <label for="newDocumentType" class="form-label">Document Type<span class="required"></span> :</label>
                    <div class="skeleton" id="newDocumentType"></div>
                </div>
            </div>`;
    $('#documentTypeContainer').html(skeleton);
    $('#modalNewDocument').modal('show');
    const params = {
        'viewType': 'REQUEST',
    };
    let optionDocumentType = await getDocumentTypeRequest(params);
    $('#newDocumentType').replaceWith(`<select class="select2" name="newDocumentType" id="newDocumentType"><option></option></select>`);
    $('#newDocumentType').select2({
        dropdownParent: $('#modalNewDocument'),
        allowClear: false,
        placeholder: '-- Select --',
        // data: optionDocumentType,
        data: optionDocumentType.data.map(group => ({
            text: group.groupName,
            children: group.items.map(item => ({
                id: item.id,
                text: item.text,
            })),
        })),
    }).trigger('change');
});

$(document).on('click', '#newRequestNext', async function (event) {
    const $this = $(this);
    clearValidation();
    selectedFiles = [];
    selectedFilesProperties = [];
    selectedFilesDocument = [];
    selectedFilesPropertiesDocument = [];
    costCenterOption = [];
    virtualSelectCodeOrder = {};
    $('#fileList').empty();
    $('#modalDocumentFormTitle').html(`<div class="skeleton mb-0" style="width: 250px; height: 24px"></div>`);
    if($('#newDocumentType').val() != ''){
        const params = {
            'documentType': $('#newDocumentType').val(),
            'requestType': $this.attr('data-request-type'),
            'loadType': 'NEW',
            'tokenForm': $this.attr('data-token'),
        };

        newForm(params);
    }
    else{
        $('#newDocumentType').addClass('is-invalid');
    }
});

async function newForm(params) {
    const requestType = params['requestType']
    const loadType = params['loadType'];
    const type = params['documentType'];
    const tokenForm = params['tokenForm'];
    Snackbar.close();
    clearValidation();
    selectedFiles = [];
    selectedFilesProperties = [];
    selectedFilesDocument = [];
    selectedFilesPropertiesDocument = [];

    $('#modalDocumentFormTitle').html(`<div class="skeleton mb-0" style="width: 250px; height: 24px"></div>`);
    $('#modalDocumentFormBody').html(`<div class="row min-vh-75">
                                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                    <div class="center-container">
                                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                                        <div class="d-block fs-7 mt-2">Loading...</div>
                                    </div>
                                </div>
                            </div>`);
    $('#modalDocumentFormFooter').html(`<div class="container-fluid p-0">
                                            <div class="row justify-content-center w-100 mx-0">
                                                <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                    <div class="skeleton mb-0 w-100 w-md-auto me-md-2"></div>
                                                </div>
                                                <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-start mb-2 px-0">
                                                        <div class="skeleton mb-0 w-100 w-md-auto me-md-2"></div>
                                                </div>
                                            </div>
                                        </div>`);

    if(!$('#modalDocumentForm').hasClass('show')) {
        $('.modal').modal('hide');
        $('#modalDocumentForm').modal('show');
    }

    try {
        const response = await fetch(`/doc_approval/newRequest?type=${type}&requestType=${requestType}&loadType=${loadType}&tokenForm=${tokenForm}`, {
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
            // let selectedFiles = [];
            // let selectedFilesProperties = [];
            // const maxFileSize = 5 * 1024 * 1024;
            $('#modalDocumentFormTitle').html(data.data.title);
            if (Array.isArray(data.data?.dataForm)) {
                let poList = '';
                if (data.data.dataForm.length > 0) {
                    data.data.dataForm.forEach((item, index) => {
                        poList += item.html;
                    });
                }
                $('#modalDocumentFormBody').html(poList);
            }
            else {
                $('#modalDocumentFormBody').html(data.data.form);
            }

            $('#modalDocumentFormFooter').html(data.data.footerButton);
            $('.autosize').autosize().trigger('change');
        }
        else if (data.status === 404) {
            $('#modalDocumentFormTitle').html('Detail Form');
            $('#modalDocumentFormBody').html( data.data.form);
            $('#modalDocumentFormFooter').html(data.data.footerButton);
        }
        else if (response.status === 401) {
            $('.card-footer-fixed').addClass('border-top-0');
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
            $('#modalDocumentFormTitle').html('Form');
            $('#modalDocumentFormBody').html(`<div class="row min-vh-75">
                                                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                    <div class="text-center">
                                                        <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                        <div class="d-block fs-7 mt-2">Failed to get data</div>
                                                    </div>
                                                </div>
                                            </div>`);
            $('#modalDocumentFormFooter').html(`<div class="container-fluid p-0">
                                                    <div class="row justify-content-center w-100 mx-0">
                                                        <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                            <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                <i class="fas fa-xmark"></i> Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>`);
        }
    } catch (error) {
        $('#modalDocumentFormTitle').html('Form');
        $('#modalDocumentFormBody').html(`<div class="row min-vh-75">
                                            <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                <div class="text-center">
                                                    <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                    <div class="d-block fs-7 mt-2">Failed to get data</div>
                                                </div>
                                            </div>
                                        </div>`);
        $('#modalDocumentFormFooter').html(`<div class="container-fluid p-0">
                                                <div class="row justify-content-center w-100 mx-0">
                                                    <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                        <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                            <i class="fas fa-xmark"></i> Close
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>`);
    } finally {

    }
}

async function getDocumentTypeRequest(params) {
    try {
        const response = await fetch(`/doc_approval/getDocumentTypeRequest?documentType=ALL&viewType=${params['viewType']}`, {
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
            // const options = data.map(row => ({
            //     id: row.doc_type_id,
            //     text: row.doc_name
            // }));

            return data;
        }
        else if (response.status === 401) {
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
    }
    catch (error) {
        return;
    }
}

// OTHER DOCUMENT
$(document).on('change', '#fileUploadDocument', function (event) {
    handleFilesDocument(event.target.files);
});

$(document).on('click', '#selectFileBtnDocument', function (event) {
    if(!$('#fileUploadDocument').is('[multiple]') && $('.fileListItemDocument').length == 1) {
        Snackbar.show({ pos:'bottom-center', duration: '6000', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Only allow one document, delete the document first.` });
        return false;
    }
    else {
        $('#fileUploadDocument').trigger('click');
    }
});

$(document).on('dragover', '#dropZoneDocument', function (event) {
    event.preventDefault();
    $(this).addClass('dragover');
});

$(document).on('dragleave', '#dropZoneDocument', function (event) {
    event.preventDefault();
    $(this).removeClass('dragover');
});

$(document).on('drop', '#dropZoneDocument', function (event) {
    event.preventDefault();
    Snackbar.close();
    $(this).removeClass('dragover');
    if(!$('#fileUploadDocument').is('[multiple]') && $('.fileListItemDocument').length == 1) {
        Snackbar.show({ pos:'bottom-center', duration: '6000', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Only allow one document, delete the document first.` });
        return false;
    }
    else {
        const files = event.originalEvent.dataTransfer.files;
        handleFilesDocument(files);
    }
});

$(document).on('click', '.deleteFileItemDocument', async function (event) {
    const fileItem = $(this).closest('.form-group');
    const index = fileItem.data('index');
    selectedFilesDocument.splice(index, 1);
    selectedFilesPropertiesDocument.splice(index, 1);
    renderFileListDocument();
});

$(document).on('click', '#clearFilesDocument', function (event) {
    selectedFilesDocument = [];
    selectedFilesPropertiesDocument = [];
    renderFileListDocument();
});

async function handleFilesDocument(files) {
    Snackbar.close();

    if(!$('#fileUploadDocument').is('[multiple]') && $('.fileListItemDocument').length == 1) {
        Snackbar.show({ pos:'bottom-center', duration: '6000', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Only allow one document, delete the document first.` });
        return false;
    }

    $('#fileListDocument').append(`
        <div class="file-item list-group-item list-group-item-action" id="fileItemSkeletonDocument">
            <div class="file-details">
                <i class="fa-solid fa-spinner fa-spin file-icon"></i>
                <div>
                    <span class="file-name skeletonText">Please wait...</span>
                </div>
            </div>
        </div>`)

    let renderFileDocument = false;
    const allowedMimeTypes = [
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ''
    ];

    return Promise.resolve()
    .then(() => {
        document.querySelector(`#fileItemSkeletonDocument`).scrollIntoView({ behavior: 'smooth', block: 'start' }, true);
    })
    .then(() => {
        return Promise.all(Array.from(files).map(file => readFile(file)));
    })
    .then(processedFiles => {
        processedFiles.forEach(({file, content}) => {
            let isAllowed = allowedMimeTypes.includes(file.type);
            if(file.type == '') {
                isAllowed = false;
            }

            const isFileNameExists = selectedFilesPropertiesDocument.some(function(property) {
                return property.name === file.name;
            });

            if (!isAllowed) {
                Snackbar.show({ pos:'bottom-center', duration: '6000', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> File "${file.name}" is not a valid type.` });
                return;
            }
            else if (file.size > maxFileSize) {
                Snackbar.show({ pos:'bottom-center', duration: '6000', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> File "${file.name}" exceeds the 5MB size limit.` });
            }
            else if (isFileNameExists) {
                Snackbar.show({ pos:'bottom-center', duration: '6000', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> File "${file.name}" is already selected.` });
            }
            else {
                renderFileDocument = true;
                selectedFilesDocument.push(file);
                const properties = {
                    'name': file.name,
                    'type': file.type,
                    'size': file.size,
                    'content': content  // Menyimpan konten file jika diperlukan
                };
                selectedFilesPropertiesDocument.push(properties);
            }
        });
    })
    .finally(() => {
        if(renderFileDocument){
            renderFileListDocument();
        }
        else{
            $('#fileItemSkeletonDocument').remove();
        }
    });
}

function renderFileListDocument() {
    const fileList = $('#fileListDocument');
    fileList.empty();

    selectedFilesPropertiesDocument.forEach((file, index) => {
        const fileName = file.name;
        const fileType = fileName.split('.').pop().toLowerCase();
        let iconClass = getFileIcon(file.type, file.name);

        const fileItem = `
            <div class="form-group" data-index="${index}">
                <div class="file-item list-group-item list-group-item-action fileListItemDocument" data-index="${index}">
                    <div class="file-details">
                        <i class="${iconClass} file-icon"></i>
                        <div>
                            <span class="file-name">${fileName}</span>
                            <div class="file-size">${(file.size / 1024).toFixed(2)} KB</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close-black deleteFileItemDocument" title="Delete" aria-label="Delete"></button>
                </div>
            </div>`;

        fileList.append(fileItem);
    });
}

// ORDER FORM
$(document).on('change', '#fileUpload', function (event) {
    handleFiles(event.target.files);
});

$(document).on('click', '#selectFileBtn', function (event) {
    $('#fileUpload').trigger('click');
});

$(document).on('dragover', '#dropZone', function (event) {
    event.preventDefault();
    $(this).addClass('dragover');
});

$(document).on('dragleave', '#dropZone', function (event) {
    event.preventDefault();
    $(this).removeClass('dragover');
});

$(document).on('drop', '#dropZone', function (event) {
    event.preventDefault();
    Snackbar.close();
    $(this).removeClass('dragover');
    const files = event.originalEvent.dataTransfer.files;
    handleFiles(files);
});

$(document).on('click', '#clearFiles', function (event) {
    selectedFiles = [];
    selectedFilesProperties = [];
    renderFileList();
});

$(document).on('click', '.deleteFileItem', async function (event) {
    const fileItem = $(this).closest('.form-group');
    const index = fileItem.data('index');
    selectedFiles.splice(index, 1);
    selectedFilesProperties.splice(index, 1);
    renderFileList();
});

async function handleFiles(files) {
    Snackbar.close();
    $('#fileList').append(`
        <div class="file-item list-group-item list-group-item-action" id="fileItemSkeleton">
            <div class="file-details">
                <i class="fa-solid fa-spinner fa-spin file-icon"></i>
                <div>
                    <span class="file-name skeletonText">Please wait...</span>
                </div>
            </div>
        </div>`)

    let renderFile = false;
    const allowedMimeTypes = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-outlook',
        'application/ms-tnef',
        'message/rfc822',
        ''
    ];

    return Promise.resolve()
    .then(() => {
        document.querySelector(`#fileItemSkeleton`).scrollIntoView({ behavior: 'smooth', block: 'start' }, true);
    })
    .then(() => {
        return Promise.all(Array.from(files).map(file => readFile(file)));
    })
    .then(processedFiles => {
        processedFiles.forEach(({file, content}) => {
            let isAllowed = allowedMimeTypes.includes(file.type);
            if (isAllowed && (file.name.endsWith('.msg') || file.name.endsWith('.eml'))) {
                isAllowed = true;
            }
            else if(file.type == '') {
                isAllowed = false;
            }

            const isFileNameExists = selectedFilesProperties.some(function(property) {
                return property.name === file.name;
            });

            if (!isAllowed) {
                Snackbar.show({ pos:'bottom-center', duration: '6000', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> File "${file.name}" is not a valid type.` });
                return;
            }
            else if (file.size > maxFileSize) {
                Snackbar.show({ pos:'bottom-center', duration: '6000', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> File "${file.name}" exceeds the 5MB size limit.` });
            }
            else if (isFileNameExists) {
                Snackbar.show({ pos:'bottom-center', duration: '6000', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> File "${file.name}" is already selected.` });
            }
            else {
                renderFile = true;
                selectedFiles.push(file);
                const properties = {
                    'name': file.name,
                    'type': file.type,
                    'size': file.size,
                    'exist': false,
                    'content': content  // Menyimpan konten file jika diperlukan
                };
                selectedFilesProperties.push(properties);
            }
        });
    })
    .finally(() => {
        if(renderFile){
            renderFileList();
        }
        else{
            $('#fileItemSkeleton').remove();
        }
    });
}

function readFile(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (e) => resolve({file: file, content: e.target.result});
        reader.onerror = (e) => reject(e);
        reader.onprogress = (e) => {
            if (e.lengthComputable) {
                const percentLoaded = Math.round((e.loaded / e.total) * 100);
                $('.skeletonText').text(`Uploading progress : ${percentLoaded}%`);
            }
        };
        reader.readAsArrayBuffer(file);  // Atau gunakan readAsText() untuk file teks
    });
}

function renderFileList() {
    const fileList = $('#fileList');
    fileList.empty();

    selectedFilesProperties.forEach((file, index) => {
        const fileName = file.name;
        const fileType = fileName.split('.').pop().toLowerCase();
        let iconClass = getFileIcon(file.type, file.name);
        let fileSize = (file.size) ? `${(file.size / 1024).toFixed(2)} KB` : '';

        let fileItem = '';
        if(file.exist === true) {
            fileItem = `
                <div class="form-group existAttachment" data-index="${index}">
                    <div class="file-item list-group-item list-group-item-action" data-index="${index}">
                        <div class="file-details view-file-fullscreen" data-token="${file.content}">
                            <input type="hidden" name="existAttachment[]" value="${file.content}">
                            <i class="${iconClass} file-icon"></i>
                            <div>
                                <span class="file-name">${fileName}</span>
                                <div class="file-size">${fileSize}</div>
                            </div>
                        </div>
                        <button type="button" class="btn-close-black deleteFileItem" title="Delete" aria-label="Delete"></button>
                    </div>
                </div>`;
        }
        else {
            fileItem = `
                <div class="form-group" data-index="${index}">
                    <div class="file-item list-group-item list-group-item-action fileListItem" data-index="${index}">
                        <div class="file-details">
                            <i class="${iconClass} file-icon"></i>
                            <div>
                                <span class="file-name">${fileName}</span>
                                <div class="file-size">${fileSize}</div>
                            </div>
                        </div>
                        <button type="button" class="btn-close-black deleteFileItem" title="Delete" aria-label="Delete"></button>
                    </div>
                </div>`;
        }

        fileList.append(fileItem);
    });
}

$(document).on('click', '.saveForm', async function (event) {
    let $this = $(this);
    Snackbar.close();
    clearValidation();

    let formData;
    if($('#documentTypeForm').val() == '7') {
        formData = new FormData($('#formDocumentForm')[0]);
        formData.append('actionType', $this.attr('data-action'));
        formData.delete('attachmentFileDocument[]');
        formData.delete('attachmentFile[]');
        const fileUpload = document.getElementById('fileUpload');
        const fileUploadDocument = document.getElementById('fileUploadDocument');

        if (selectedFilesDocument && selectedFilesDocument.length > 0) {
            selectedFilesDocument.forEach((file, index) => {
                formData.append('attachmentFileDocument[]', file);
            });
        }
        else {
            formData.append('attachmentFileDocument[]', '');
        }

        if (selectedFiles && selectedFiles.length > 0) {
            selectedFiles.forEach((file, index) => {
                formData.append('attachmentFile[]', file);
            });
        }
        else {
            formData.append('attachmentFile[]', '');
        }
    }
    else {
        formData = new FormData($('#formDocumentForm')[0]);
        formData.delete('attachmentFile[]');
        const fileUpload = document.getElementById('fileUpload');

        if (selectedFiles && selectedFiles.length > 0) {
            selectedFiles.forEach((file, index) => {
                if(file.size != null) {
                    const newFileName = escapeFilename(file.name);
                    const newFile = new File([file], newFileName, {
                        type: file.type,
                        lastModified: file.lastModified,
                    });

                    formData.append('attachmentFile[]', newFile);
                }
            });
        }
        else {
            formData.append('attachmentFile[]', '');
        }
        formData.append('actionType', $this.attr('data-action'));
    }

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

    $('#loadingSpinner').removeClass('d-none');

    try {
        const response = await fetch('/doc_approval/saveForm', {
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
            selectedFiles = [];
            selectedFilesProperties = [];
            $('.modal').modal('hide');
            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result['message']}` });
            currentPage = 1;
            isLoading = false;
            hasMoreData = true;
            loadLeftColumn();
            // Swal.fire({
            //     icon: 'success',
            //     text: result['message'],
            //     showConfirmButton: false,
            //     showCancelButton: true,
            //     // timer: 20500,
            //     cancelButtonText: `<i class="fas fa-xmark"></i> Close`,
            // });
        }
        else if (response.status === 401) {
            Snackbar.show({ pos: 'bottom-center', duration: '6000', text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Unauthorized access, plase login or refresh this page' });
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
            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${response.status} ${response.statusText}` });
        }
    }
    catch (error) {
        Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}` });
    }
    finally {
        $('#loadingSpinner').addClass('d-none');
    }
});

$(document).on('click', '.asideButton', async function () {
    let $this = $(this);
    if($this.attr('data-type') == 'PRINT' || $this.attr('data-type') == 'DOWNLOAD') {
        const printBackdrop = $(`
            <div class="modal-backdrop" style="position: fixed; top: 0; left: 0; z-index: 9999; background: rgba(255,255,255,0.9)">
                <div class="modal-content" style="width: 100%; height: 100%;display: flex; justify-content: center; align-items: center;">
                    <div class="center-container">
                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                    </div>
                    <p class="mt-3 fw-700 fs-5 text-company-emphasis">Preparing document...</p>
                </div>
            </div>
        `);
        $('body').append(printBackdrop);

        const formData = new FormData($('#asideForm')[0]);
        formData.append('actionType', $this.attr('data-type'));
        formData.append('module', 'DOCUMENT_APPROVAL');

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
                $this.prop('disabled', true);
                $this.html('<i class="fas fa-spinner fa-spin"></i> Please wait');
                const response = await fetch('/generatePdf', {
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
                    if(Object.keys(result.data).length > 0) {
                        if($this.attr('data-type') == 'PRINT') {
                            let pdfUrl = `/printPdf?token=${result.data.token}`;
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
                        else {
                            const url = document.createElement('a');
                            url.href = `/downloadPdf?token=${result.data.token}`;
                            url.setAttribute('download', '');
                            document.body.appendChild(url);
                            url.click();
                            document.body.removeChild(url);
                            printBackdrop.remove();
                        }
                    }
                    else {
                        printBackdrop.remove();
                        Snackbar.show({ pos: 'bottom-center', duration: '5000', text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed to preparing documents' });
                    }
                }
                else if (response.status === 401) {
                    printBackdrop.remove();
                    Snackbar.show({ pos: 'bottom-center', duration: '6000', text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Unauthorized access, please login or refresh this page' });
                }
                else if (response.status === 422) {
                    printBackdrop.remove();
                    handleValidationErrors(result.errors);
                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : ${result['message']}` });
                }
                else if (response.status === 419) {
                    printBackdrop.remove();
                    handleValidationErrors(result.errors);
                    Snackbar.show({ pos: 'bottom-center', text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : CSRF token mismatch, please refresh this page' });
                }
                else {
                    printBackdrop.remove();
                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed ${response.status} : ${response.statusText}` });
                }
            }
            catch (error) {
                printBackdrop.remove();
                Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}` });
            }
            finally {
                $this.prop('disabled', false);
            }
        });
    }
    else if($this.attr('data-type') == 'CHANGE_SIGNER') {
        Snackbar.close();
        clearValidation();
        const formData = new FormData($('#asideForm')[0]);
        formData.append('actionType', $this.attr('data-type'));

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
                const response = await fetch('/doc_approval/updateSigner', {
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
                    asideHide();
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
            finally {
                $('.confirmAction').prop('disabled', false);
            }
        });
    }
});

$(document).on('click', '.actionBtn', async function (event) {
    event.stopImmediatePropagation();
    let $this = $(this);
    if($this.attr('data-type') == 'PROGRESS' || $this.attr('data-type') == 'CHANGE_SIGNER' || $this.attr('data-type') == 'INSPECTION') {
        asideHide();
        if($this.attr('data-type') == 'PROGRESS') {
            Snackbar.show({
                pos: 'bottom-center',
                duration: '6000',
                text: `<i class="fa-regular fa-circle-notch fa-spin fs-6 fa-fw text-info"></i> Processing...`
            });

            try {
                const response = await fetch(`/proc_pur/getComparison?type=${$this.attr('data-type')}&form=ORDER_FORM&token=${$this.attr('data-token')}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'Referer': window.location.href
                    }
                });

                const result = await response.json();
                if (response.status === 401) {
                    Snackbar.close();
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
                            window.location.href = result.redirect_uri;
                        }
                    }, 1000);
                }
                else {
                    Snackbar.close();
                    if (result.status === 200) {
                        if(result.data.processedStatus === true && result.data.multiVendor === true) {
                            $('#modalMessageTitle').html(result.data.title);
                            $('#modalMessageBody').html(result.data.listItem);
                            $('#modalMessageFooter').html(`<div class="container-fluid p-0">
                                <div class="row justify-content-center w-100 mx-0">
                                    <div class="col-sm-12 col-md-8 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                            <i class="fas fa-xmark"></i> Close
                                        </button>
                                    </div>
                                </div>
                            </div>`);
                            $('#modalMessageDialog').removeClass('top-20');
                            $('#modalMessageDialog').removeClass('modal-dialog-scrollable').addClass('modal-dialog-scrollable');
                            $('#modalMessage').modal('show');
                        }
                        else {
                            event.stopImmediatePropagation();
                            const params = {
                                'title': `Progress ${$this.attr('data-selectedversion')}`,
                                'dataType': $this.attr('data-type'),
                                'url': `/doc_approval/getProgress?token=${result.data.tokenForm}`,
                            };
                            viewProgress(params);
                        }
                    }
                    else if (result.status === 404) {
                        Snackbar.show({
                            pos: 'bottom-center',
                            duration: '6000',
                            text: `<i class="fa-solid fa-circle-exclamation fa-lg fa-fw text-error"></i> 404 Not found`,
                        });
                    }
                    else {
                        Snackbar.show({
                            pos: 'bottom-center',
                            duration: '6000',
                            text: `<i class="fa-solid fa-circle-exclamation fa-lg fa-fw text-error"></i> Failed to get data`,
                        });
                    }
                }
            }
            catch (error) {
                Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}` });
            }
        }
        else if($this.attr('data-type') == 'INSPECTION') {
            const params = {
                'title': 'Inspection Form',
                'dataType': $this.attr('data-type'),
                'url': `/doc_approval/getInspection?token=${$this.attr('data-token')}`,
            };
            viewProgress(params);
        }
        else if($this.attr('data-type') == 'CHANGE_SIGNER') {
            const params = {
                'title': 'Change Checker or Approver',
                'dataType': $this.attr('data-type'),
                'url': `/doc_approval/getApproval?documentType=${$this.attr('data-doc')}&token=${$this.attr('data-token')}`,
            };
            viewProgress(params);
        }
    }
    else if($this.attr('data-type') == 'CANCEL' ) {
        $('.modal').modal('hide');
        $('#modalMessageTitle').html('Cancel Form');
        $('#modalMessageBody').html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="formAction">
                                        <input type="hidden" id="tokenFormAction" name="tokenForm" autocomplete="false" value="${$(this).attr('data-token')}">
                                        <div class="form-group mb-3">
                                            <label for="commentAction" class="form-label required" id="commentActionLabel">Reason (required) :</label>
                                            <textarea class="form-control autosize singleLine" spellcheck="false" placeholder="Reason to cancel form" maxlength="255" id="commentAction" name="commentAction" style="height: 53px;"></textarea>
                                        </div>
                                    </form>`);

        $('#modalMessageFooter').html(`<div class="container-fluid p-0">
                                            <div class="row justify-content-center w-100 mx-0">
                                                <div class="col-6 d-flex justify-content-center mb-2 px-0">
                                                    <button type="button" class="btn btn-default w-100 me-2" data-coreui-dismiss="modal" title="Close">
                                                        <i class="fas fa-xmark"></i> Close
                                                    </button>
                                                </div>
                                                <div class="col-6 d-flex justify-content-center mb-2 px-0">
                                                    <button type="button" class="btn btn-secondary w-100 confirmAction" data-type="${$(this).attr('data-type')}" data-form="" data-token="" title="">
                                                        Cancel Form <i class="fa-solid fa-arrow-right"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>`);
        $('#modalMessageDialog').removeClass('top-20').addClass('top-20');
        $('#modalMessageDialog').removeClass('modal-dialog-scrollable');
        $('#modalMessage').modal('show');
        $('.autosize').autosize().trigger('change');
    }
    else if($this.attr('data-type') == 'REVISE' ) {
        clearValidation();
        selectedFiles = [];
        selectedFilesProperties = [];
        selectedFilesDocument = [];
        selectedFilesPropertiesDocument = [];
        costCenterOption = [];
        virtualSelectCodeOrder = {};
        $('#fileList').empty();
        $('.modal').modal('hide');
        $('#modalDocumentFormTitle').html(`<div class="skeleton mb-0" style="width: 250px; height: 24px"></div>`);
        const params = {
            'documentType': $this.attr('data-doc'),
            'requestType': 'NEW_APPROVAL_REQUEST',
            'loadType': 'REVISE',
            'tokenForm': $this.attr('data-token'),
        };

        newForm(params);
    }
    else if($this.attr('data-type') == 'REORDER' ) {
        clearValidation();
        selectedFiles = [];
        selectedFilesProperties = [];
        selectedFilesDocument = [];
        selectedFilesPropertiesDocument = [];
        costCenterOption = [];
        virtualSelectCodeOrder = {};
        $('#fileList').empty();
        $('.modal').modal('hide');
        $('#modalDocumentFormTitle').html(`<div class="skeleton mb-0" style="width: 250px; height: 24px"></div>`);
        const params = {
            'documentType': $this.attr('data-doc'),
            'requestType': 'NEW_APPROVAL_REQUEST',
            'loadType': 'REORDER',
            'tokenForm': $this.attr('data-token'),
        };

        newForm(params);
    }
});

async function viewProgress(params) {
    title = params['title'];
    dataType = params['dataType'];
    url = params['url'];

    $('.aside-title').html(title);
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
    if(dataType == 'PROGRESS'){
        $('#globalAside').addClass('show aside-lg').trigger('shown');
    }
    else {
        $('#globalAside').addClass('show').trigger('shown');
    }

    $('body').addClass('overflow-hidden');
    $('#asideDetailForm').scrollTop(0);
    if(dataType == 'CHANGE_SIGNER') {
        $('.asideFooterBtn').html(`<div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
                                    </div>
                                    <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-info w-100 w-md-auto me-2 asideButton" data-type="${dataType}" data title="Update">Update</button>
                                    </div>`);
    }
    else {
        $('.asideFooterBtn').html(`<div class="col-12 d-flex justify-content-center justify-content-md-end mb-2 px-0">
            <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
        </div>`);
    }

    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'Referer': window.location.href
            },
        });

        const result = await response.json();
        if(dataType == 'CHANGE_SIGNER'){
            if (response.status === 200) {
                if(Object.keys(result.data).length > 0) {
                    let secondCheckerInfo = result.data.locationId == '22' ? '<span id="secondCheckerInfo" class="required"></span>': '<span id="secondCheckerInfo" class=""> (optional)</span>';

                    let disabledChecker = '', disabledChecker2 = '', disabledApprover = '';
                    let optionChecker = '<option></option>', optionChecker2 = '<option></option>', optionApprover = '<option></option>';
                    result.data.approval.checker_1.forEach((row, index) => {
                        disabledChecker = (row.readonly == true && disabledChecker == '') ? ' readonly=""' : '';
                        let selected = row.selected == true ? ' selected=""' : '';
                        optionChecker += `<option value="${row.employeeId}"${selected}>${row.employeeName}</option>`;
                    });

                    result.data.approval.checker_2.forEach((row, index) => {
                        disabledChecker2 = (row.readonly == true && disabledChecker2 == '') ? ' readonly=""' : '';
                        let selected = row.selected == true ? ' selected=""' : '';
                        optionChecker2 += `<option value="${row.employeeId}"${selected}>${row.employeeName}</option>`;
                    });

                    result.data.approval.APPROVER.forEach((row, index) => {
                        disabledApprover = (row.readonly == true && disabledApprover == '') ? ' readonly=""' : '';
                        let selected = row.selected == true ? ' selected=""' : '';
                        optionApprover += `<option value="${row.employeeId}"${selected}>${row.employeeName}</option>`;
                    });

                    $('.aside-content').html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
                                            <input type="hidden" name="tokenForm" value="${$this.attr('data-token')}">
                                            <input type="hidden" name="documentType" value="${$this.attr('data-doc')}">
                                            <input type="hidden" name="locationOrder" value="${result.data.locationId}">
                                            <div class="form-group mb-3">
                                                <label for="checkerSelectChange" class="form-label">First Checked By<span class="required"></span> :</label>
                                                <select class="select2" name="checker_1" id="checkerSelectChange"${disabledChecker}>${optionChecker}</select>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="checkerSelect2Change" class="form-label">Second Checked By${secondCheckerInfo} :</label>
                                                <select class="select2" name="checker_2" id="checkerSelect2Change"${disabledChecker2}>${optionChecker2}</select>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="approverSelect" class="form-label">Approved By<span class="required"></span> :</label>
                                                <select class="select2" name="approver[]" id="approverSelectChange"${disabledApprover}>${optionApprover}</select>
                                            </div>
                                        </form>`);

                    $('#checkerSelectChange').select2({
                        dropdownParent: $('#globalAside'),
                        allowClear: true,
                        placeholder: '-- Select --',
                    });
                    $('#checkerSelect2Change').select2({
                        dropdownParent: $('#globalAside'),
                        allowClear: true,
                        placeholder: '-- Select --',
                    });
                    $('#approverSelectChange').select2({
                        dropdownParent: $('#globalAside'),
                        allowClear: true,
                        placeholder: '-- Select --',
                    });
                    // $('#ccSelect').select2({
                    //     dropdownParent: $('#modalDocumentForm'),
                    //     allowClear: true,
                    //     placeholder: '-- Select --',
                    // });
                }
                else {
                    asideHide();
                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : ${result['message']}` });
                }
            }
            else {
                asideHide();
                Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : ${result['message']}` });
            }
        }
        else {
            if (response.status === 200) {
                $('#asideForm').html(result.data.form);
            }
            else {
                console.error('HTTP Error:', response.status);
            }
        }
    } catch (error) {
        return;
    }
}

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
                currentPage = 1;
                isLoading = false;
                hasMoreData = true;
                loadLeftColumn();
                $('.modal').modal('hide');
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
        finally {
            $('.confirmAction').prop('disabled', false);
        }
    });
});

$(document).on('click', '.actionBtnHeader', async function (event) {
    let $this = $(this);
    if($this.attr('data-type') == 'ATTACHMENTS') {
        asideHide();
        $('.aside-title').html(`Attachment List ${$this.attr('data-selectedversion')}`);
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
    else if($this.attr('data-type') == 'PROGRESS' || $this.attr('data-type') == 'INSPECTION') {
        let url = '', title = '';
        if($this.attr('data-type') == 'PROGRESS') {
            title = 'Progress';
            url = `/doc_approval/getProgress?token=${$this.attr('data-token')}`;
        }
        else if($this.attr('data-type') == 'INSPECTION') {
            title = 'Inspection Form';
            url = `/doc_approval/getInspection?token=${$this.attr('data-token')}`;
        }

        asideHide();
        $('.aside-title').html(title);
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
        if($this.attr('data-type') == 'PROGRESS'){
            $('#globalAside').addClass('show aside-lg').trigger('shown');
        }
        else {
            $('#globalAside').addClass('show').trigger('shown');
        }

        $('body').addClass('overflow-hidden');
        $('#asideDetailForm').scrollTop(0);
        $('.asideFooterBtn').html(`<div class="col-12 d-flex justify-content-center justify-content-md-end mb-2 px-0">
            <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
        </div>`);

        try {
            const response = await fetch(url, {
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
                $('#asideForm').html(result.data.form);
            }
            else {
                console.error('HTTP Error:', response.status);
            }
        } catch (error) {
            return;
        }
    }
    else if($this.attr('data-type') == 'PRINT' || $this.attr('data-type') == 'DOWNLOAD') {
        asideHide();
        let asideTitle = '', asideButton = '';
        if($this.attr('data-type') == 'PRINT') {
            asideTitle = 'Print Documents';
            asideButton = `<button type="button" class="btn btn-info w-100 w-md-auto me-2 asideButton" data-type="${$this.attr('data-type')}" data-token="" title="Print Selected Documents">Print</button>`;
        }
        else {
            asideTitle = 'Download';
            asideButton = `<button type="button" class="btn btn-danger w-100 w-md-auto me-2 asideButton" data-type="${$this.attr('data-type')}" data-token="" title="Download Selected">Download</button>`;
        }

        $('.aside-title').html(`${asideTitle}`);
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

        $('.asideFooterBtn').html(`<div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
                                    </div>
                                    <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        ${asideButton}
                                    </div>`);

        const params = {
            'dataType': $this.attr('data-type'),
            'token': $this.attr('data-token'),
        };
        const documentList = await getDocumentList(params);
        $('#asideForm').html(documentList.data.form);
    }
});

$(document).on('click', '.suggestions', async function (event) {
    var $this = $(this);
    const query = $(this).val().trim();
    let type = $(this).attr('data-type');
    let $suggestionsGroup = $(this).closest('div.suggestions-group');
    let $suggestionsContainer = $suggestionsGroup.find('div.suggestions-container');
    currentQuerySuggestion = query;
    hasMoreDataSuggestion = true;
    currentPageSuggestion = 1;

    if (query.length > 2 && !$suggestionsGroup.hasClass('focused')) {
        loadSuggestions($this, type);
        $suggestionsContainer.scrollTop(0);
    }
    else {
        hideSuggestions($this);
    }
});

$(document).on('input', '.suggestions', async function (event) {
    var $this = $(this);
    const query = $(this).val().trim();
    let type = $(this).attr('data-type');
    let $suggestionsGroup = $(this).closest('div.suggestions-group');
    let $suggestionsContainer = $suggestionsGroup.find('div.suggestions-container');
    currentQuerySuggestion = query;
    $suggestionsGroup.find('input.id').val('');
    hasMoreDataSuggestion = true;
    currentPageSuggestion = 1;
    $suggestionsContainer.scrollTop(0);

    if (query.length > 2) {
        loadSuggestions($this, type);
        $suggestionsContainer.scrollTop(0);
    }
    else {
        hideSuggestions($this);
    }
});

$(document).on('keydown', '.suggestions', function (e) {
    var $this = $(this);
    let type = $(this).attr('data-type');
    let $suggestionsGroup = $(this).closest('div.suggestions-group');
    let $suggestionsContainer = $suggestionsGroup.find('div.suggestions-container');

    if (!$suggestionsContainer.is(':visible') || suggestions.length === 0) {
        return;
    }

    switch(e.keyCode) {
        case 38: // Arrow Up
            e.preventDefault();
            currentHighlightIndexSuggestion = currentHighlightIndexSuggestion <= 0
                ? suggestions.length - 1
                : currentHighlightIndexSuggestion - 1;
            highlightSuggestion($this, currentHighlightIndexSuggestion);
            break;

        case 40: // Arrow Down
            e.preventDefault();
            currentHighlightIndexSuggestion = currentHighlightIndexSuggestion >= suggestions.length - 1
                ? 0
                : currentHighlightIndexSuggestion + 1;
            highlightSuggestion($this, currentHighlightIndexSuggestion);
            break;

        case 13: // Enter
            e.preventDefault();
            if (currentHighlightIndexSuggestion >= 0) {
                $this.closest('.suggestions-group').find('input.id').val(suggestions[currentHighlightIndexSuggestion]['id']);
                $this.val(suggestions[currentHighlightIndexSuggestion]['text']);
                let goodsTypeId = suggestions[currentHighlightIndexSuggestion]['goodsTypeId'];
                let goodsType = suggestions[currentHighlightIndexSuggestion]['goodsTypeName'];
                let unit = suggestions[currentHighlightIndexSuggestion]['unit'];
                hideSuggestions($this);
                if(type == 'ITEM') {
                    $('#typeOrderAside').replaceWith('<div class="skeleton" id="typeOrderAside"></div>');
                    setTimeout(function() {
                        $('#typeOrderAsideId').val(goodsTypeId);
                        $('#typeOrderAside').replaceWith(`<input type="text" class="form-control suggestions" data-type="TYPE" spellcheck="false" id="typeOrderAside" name="typeOrderAside" value="${goodsType}">`);
                        $('#unitOrderAside').val(unit).trigger('change');
                    }, 500);
                }
            }
            break;

        case 27: // Esc
            hideSuggestions($this);
            break;
    }
});

$(document).on('click', '.suggestion-item', function() {
    const value = $(this).data('value');
    inputElement = $(this).closest('.suggestions-group').find('input.suggestions');
    inputElement.closest('.suggestions-group').find('input.id').val($(this).data('id'));
    inputElement.val(value);
    let goodsTypeId = $(this).data('secondvalueid');
    let goodsType = $(this).data('secondvalue');
    let unit = $(this).data('thirdvalue');
    hideSuggestions(inputElement);
    inputElement.focus();
    if(inputElement.data('type') == 'ITEM') {
        $('#typeOrderAside').replaceWith('<div class="skeleton" id="typeOrderAside"></div>');
        setTimeout(function() {
            $('#typeOrderAsideId').val(goodsTypeId);
            $('#typeOrderAside').replaceWith(`<input type="text" class="form-control suggestions" data-type="TYPE" spellcheck="false" id="typeOrderAside" name="typeOrderAside" value="${goodsType}">`);
            $('#unitOrderAside').val(unit).trigger('change');
        }, 500);
    }
});

$(document).on('mouseenter', '.suggestion-item', function() {
    currentHighlightIndexSuggestion = parseInt($(this).data('index'));
    inputElement = $(this).closest('.suggestions-group').find('input.suggestions');
    highlightSuggestion(inputElement, currentHighlightIndexSuggestion);
});

$(document).on('click', function(e) {
    if (!$(e.target).closest('.suggestions-group .focused').length) {
        $('.suggestions-container').hide();
        $('.suggestions-group').removeClass('focused');
        suggestions = [];
        currentHighlightIndexSuggestion = -1;
        currentPageSuggestion = 1;
        hasMoreDataSuggestion = true;
    }
});

// document.querySelector('#leftColumnOngoing').addEventListener('scroll', () => {
//     const leftColumn = document.querySelector('#leftColumnOngoing');
//     const scrollThreshold = 100; // Threshold in pixels before the bottom to trigger loading

//     if (leftColumn.scrollTop + leftColumn.clientHeight >= leftColumn.scrollHeight - scrollThreshold) {
//         loadLeftColumn(); // Load more data when near the bottom
//     }
// });

document.addEventListener('scroll', function(e) {
    // Pastikan target adalah elemen dengan class suggestions-container
    const target = e.target;
    if (target.classList.contains('suggestions-container')) {
        const $container = $(target);
        if(currentPageSuggestion == 1) {
            $container.scrollTop(0);
        }

        if (isLoadingSuggestion || !hasMoreDataSuggestion) return;

        // Dapatkan properti scroll
        const scrollTop = $container.scrollTop();
        const scrollHeight = target.scrollHeight; // Gunakan DOM property langsung
        const containerHeight = $container.outerHeight();

        // Temukan input element terkait
        const $inputElement = $container.closest('.suggestions-group.focused').find('input.suggestions');
        const type = $inputElement.data('type');

        if (scrollTop + containerHeight >= scrollHeight - 5 && currentPageSuggestion > 1) {
            const loadingHtml = `
                    <div class="suggestion-item-loading">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        <span>Loading...</span>
                    </div>
                `;
            $container.append(loadingHtml);
            loadSuggestions($inputElement, type);
        }
    }
}, true);

async function loadSuggestions(element, type) {
    let $suggestionsGroup = element.closest('div.suggestions-group');
    let $suggestionsContainer = $suggestionsGroup.find('div.suggestions-container');

    try {
        isLoadingSuggestion = true;
        const params = {
            'search': currentQuerySuggestion,
            'type': type,
            'page': currentPageSuggestion,
        };
        const result = await suggestionControl(params);
        if(!result) {
            hideSuggestions(element);
            return;
        }

        if(currentPageSuggestion == 1) {
            $suggestionsContainer.html('<div class="suggestion-item-title">Suggestions : <span class="suggestion-close">Close Suggestions</span></div>');
            if(!result.data || result.data.length === 0) {
                hideSuggestions(element);
                return;
            }
        }

        $('.suggestion-item-loading').remove();
        result.data.forEach((suggestion, index) => {
            let secondValueId = '', secondValue = '', thirdValue = '';
            if(type === 'ITEM') {
                secondValueId = suggestion.goodsTypeId;
                secondValue = suggestion.goodsTypeName;
                thirdValue = suggestion.unit;
            }

            const html = `
                            <div class="suggestion-item" data-index="${index}" data-id="${suggestion.id}" data-value="${suggestion.text}" data-secondvalueid="${secondValueId}" data-secondvalue="${secondValue}" data-thirdvalue="${thirdValue}">
                                <span class="suggestion-text">${suggestion.text}</span>
                            </div>
                        `;
            $suggestionsContainer.append(html);
        });

        if(currentPageSuggestion == 1) {
            $suggestionsContainer.show();
            $suggestionsGroup.addClass('focused');
            currentHighlightIndexSuggestion = -1;
            positionSuggestions(element);
            setTimeout(function () {
                $suggestionsContainer.scrollTop(0);
            }, 0);
        }

        hasMoreDataSuggestion = result.hasMorePages;
        currentPageSuggestion++;
    }
    catch (error) {
        console.error('Error loading more suggestions:', error);
        $('.suggestion-item-loading').remove();
    }

    isLoadingSuggestion = false;
}

function hideSuggestions(inputElement) {
    const $suggestionsGroup = inputElement.closest('.suggestions-group');
    const $suggestionsContainer = $suggestionsGroup.find('div.suggestions-container');

    $suggestionsContainer.hide();
    $suggestionsContainer.empty();
    $suggestionsGroup.removeClass('focused');
    suggestions = [];
    currentHighlightIndexSuggestion = -1;
    currentPageSuggestion = 1;
    hasMoreDataSuggestion = true;
}

function highlightSuggestion(inputElement, index) {
    const $suggestionsGroup = inputElement.closest('.suggestions-group');
    const $suggestionsContainer = $suggestionsGroup.find('div.suggestions-container');

    $('.suggestion-item').removeClass('highlighted');
    if (index >= 0 && index < suggestions.length) {
        const $item = $(`.suggestion-item[data-index="${index}"]`);
        $item.addClass('highlighted');
    }
    currentHighlightIndexSuggestion = index;
}

function positionSuggestions(inputElement) {
    let $suggestionsGroup = inputElement.closest('.suggestions-group');
    let $suggestionsContainer = $suggestionsGroup.find('div.suggestions-container');
    let $inputElement = $(inputElement);

    if (!$suggestionsContainer.length) return;
    $suggestionsContainer.css({
        'position': 'absolute',
        'left': 0,
        'right': 0
    });

    const inputRect = $inputElement[0].getBoundingClientRect(); // [0] untuk akses DOM element
    const viewportHeight = $(window).height();

    // Ruang tersedia
    const spaceBelow = viewportHeight - inputRect.bottom;
    const spaceAbove = inputRect.top;

    // Tinggi container
    const wasHidden = $suggestionsContainer.css('display') === 'none';
    if (wasHidden) {
        $suggestionsContainer.css({
            'visibility': 'hidden',
            'display': 'block'
        });
    }

    const containerHeight = $suggestionsContainer.outerHeight();
    if (wasHidden) {
        $suggestionsContainer.css({
            'visibility': '',
            'display': wasHidden ? 'none' : ''
        });
    }

    // posisi
    const minSpaceNeeded = Math.min(containerHeight, 200);
    if (spaceBelow >= minSpaceNeeded || spaceBelow >= spaceAbove) {
        $suggestionsContainer
            .removeClass('position-above')
            .css({
                'top': '100%',
                'bottom': 'auto'
            });
    }
    else {
    $suggestionsContainer
        .addClass('position-above')
        .css({
        'top': 'auto',
        'bottom': '100%'
        });
    }
}

async function suggestionControl(params) {
    // Batalkan request sebelumnya jika ada
    if (suggestionController) {
        suggestionController.abort();
        suggestionController = null;
    }

    // Batalkan timeout sebelumnya jika ada
    if (suggestionTimeout) {
        clearTimeout(suggestionTimeout);
        suggestionTimeout = null;
    }

    return new Promise((resolve, reject) => {
        suggestionTimeout = setTimeout(async () => {
            suggestionController = new AbortController();
            try {
                const data = await getSuggestion(params, suggestionController.signal);
                resolve(data);
            }
            catch (error) {
                reject(error);
            }
            finally {
                suggestionController = null;
                suggestionTimeout = null;
            }
        }, 300);
    });
}

async function getSuggestion(params, signal = null) {
    const queryString = new URLSearchParams(params).toString();
    try {
        const response = await fetch(`/doc_approval/suggestion?${queryString}`, {
            method: 'GET',
            signal: signal,
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
    }
    catch (error) {
        if (error.name === 'AbortError') {
            // console.log('Request canceled');
        }
        else {
            console.error('Fetch error:', error);
        }
    }
}

async function getDocumentList(params) {
    const dataType = params['dataType'];
    const tokenForm = params['token']
    try {
        const response = await fetch(`/doc_approval/getDocumentList?token=${tokenForm}`, {
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
};

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

function formatResultCc(data) {
    if (!data.id) return data.text;
    let description = $(data.element).data('description') || '';

    return $(`
        <div class="select2-result-item">
            <div class="select2-result-item__title">${data.text} - ${description}</div>
        </div>
    `);
}

function formatSelectionCc(data) {
    // return data.text || data.id;
    return $(data.element).data('description') || data.id;
}

async function getDocumentNumber(params) {
    try {
        const response = await fetch(`/doc_approval/getDocumentNumber?documentType=${params['documentType']}&type=${params['type']}&companyId=${params['companyId']}`, {
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
            return data;
        }
        else if (response.status === 401) {
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
    }
    catch (error) {
        Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}` });
        return;
    }
}

async function getLocation(params) {
    if(params == null) {
        return;
    }

    try {
        const response = await fetch(`/doc_approval/getLocation?&token=${params['token']}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'Referer': window.location.href
            }
        });

        const result = await response.json();
        if (response.status === 200) {
            return result;
        }
        else if (response.status === 401) {
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
    }
    catch (error) {
        return;
    }
}

async function getEmployee(params) {
    try {
        const response = await fetch(`/doc_approval/getEmployee?dataForm=${params['dataForm']}&dataType=${params['dataType']}&dataMatrix=${params['dataMatrix']}&tokenForm=${params['tokenForm']}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'Referer': window.location.href
            }
        });

        const result = await response.json();
        if (response.status === 200) {
            return result;
        }
        else if (response.status === 401) {
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
    }
    catch (error) {
        Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}` });
        return
    }
}

async function getCostCenter(params) {
    try {
        const response = await fetch(`/doc_approval/getCostCenter?token=${params['token']}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'Referer': window.location.href
            }
        });

        const result = await response.json();
        if (response.status === 200) {
            return result;
        }
        else if (response.status === 401) {
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
    }
    catch (error) {
        Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}` });
        return
    }
}

async function actionTable(token, action) {
    if(action == 'VIEW') {
        const params = {
            'token': token,
            'source': 'WEB',
            'form': 'ORDER_FORM',
            'type': 'HISTORY',
        };
        viewForm(params);
    }
}
