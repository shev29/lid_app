let selectedFiles = [], selectedFilesProperties = [], costCenterOption = [];
let selectedFilesDocument = [], selectedFilesPropertiesDocument = [];
let virtualSelectCodeOrder = {};
const maxFileSize = 5 * 1024 * 1024;

var leftColScrollbarInstance;
var leftColHistoryScrollbarInstance;
var rightColScrollbarInstance;
let currentPage = 1;
let currentPageHistory = 1;
let isLoading = false;
let isLoadingHistory = false;
let hasMoreData = true;
let hasMoreDataHistory = true;
let popperInstance;

window.addEventListener('load', function() {
    leftColScrollbarInstance = new ScrollbarCustom('#leftColumnOngoing', {top:null, left: null, right: null, overflowX: 'none', overflowY: 'scroll' });
    leftColHistoryScrollbarInstance = new ScrollbarCustom('#leftColumnHistory', {top:null, left: null, right: null, overflowX: 'none', overflowY: 'scroll' });
    // rightColScrollbarInstance = new ScrollbarCustom('.right-column', { top: 0, left: null, right: 0, overflowX: 'none', overflowY: 'scroll' });
    // GET LEFT COLUMN DATA
    $('#filterCheckAllRequestOngoing').prop('checked', true).trigger('change');
    $('#filterCheckAllRequestHistory').prop('checked', true).trigger('change');
});

$(document).on('click', '.filterTab', function (event) {
    if ($('#filterStatus').val() !== $(this).attr('data-status')) {
        $('#filterStatus').val($(this).attr('data-status'));
        currentPage = 1;
        isLoading = false;
        hasMoreData = true;
        event.stopImmediatePropagation();
        loadLeftColumn();
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
        currentPage = 1;
        isLoading = false;
        hasMoreData = true;
        loadLeftColumn();
    }
});

$(document).on('click', '.filterCheckRequest', function (event) {
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
    loadLeftColumn();
});

$(document).on('click', '.card-link-content', async function (event) {
    const $this = $(this);
    if ($this.hasClass('active')) {
        $this.removeClass('active');
        clearRightContent();
    }
    else {
        $('.card-link-content').removeClass('active');
        $this.addClass('active');
        if($this.attr('data-form') == 'ORDER_FORM' && $this.attr('data-type') == 'MY_REQUEST') {
            if($this.attr('data-type') == 'VIEW_ONGOING') {
                event.stopImmediatePropagation();
                const params = {
                    'token': result.data.tokenForm,
                    'rowId': $this.attr('data-row'),
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
        else {
            const params = {
                'token': $this.attr('data-token'),
                'rowId': $this.attr('data-row'),
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
        if($('#filterStatus').val() == 'ONGOING') {
            formData = new FormData($('#filterLeftColumnFormOngoing')[0]);
            if($('#filterCheckAllRequestOngoing').val() == 'TRUE'){
                formData.delete('requestType[]');
            }
        }
        else {
            formData = new FormData($('#filterLeftColumnFormHistory')[0]);
            if($('#filterCheckAllRequestHistory').val() == 'TRUE'){
                formData.delete('requestType[]');
            }
        }

        formData.append('typeAs', 'MY_REQUEST');
        formData.append('status', $('#filterStatus').val());
        formData.append('page', currentPage);
        // const queryString = new URLSearchParams(formData).toString();
        // const response = await fetch(`/doc_approval/headerForm?${queryString}`, {
        const response = await fetch(`/doc_approval/headerForm`, {
            method: 'POST',
            body: formData,
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
                    }, index * 100);
                });

                // data.data.forEach(item => {
                //     document.querySelector('#leftColumnOngoing').insertAdjacentHTML('beforeend', item.html);
                // });

                if(currentPage == 1){
                    leftColScrollbarInstance.updateScrollbarVisibility();
                    leftColScrollbarInstance.scrollTo(0);
                    leftColScrollbarInstance.updateScrollbarThumb();
                    rightContentSkeleton('PRE_SELECT');
                }
                currentPage++;
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
            }

            if (!data.hasMorePages) {
                hasMoreData = false; // If no more pages, stop further loading
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
            $('.aside-footer').html(`<div class="d-flex position-relative gap-2">
                                        <div class="skeleton mb-0" style="width:130px"></div>
                                        <div class="skeleton mb-0" style="width:130px"></div>
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
    const rowId = params['rowId'];
    Snackbar.close();
    $('#asideDetailForm').html('');
    $('.popper-area').html('');
    if ($(window).width() < 768) {
        rightContentSkeleton('FETCH_ASIDE');
    }
    else{
        rightContentSkeleton('FETCH');
    }

    try {
        const response = await fetch(`/doc_approval/viewForm?source=WEB&row_id=${rowId}&token=${token}`, {
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
            const rightTitle = data.data.title;
            const rightContent = `/framePdf?token=${data.data.tokenForm}`;
            const footerButton = data.data.footerButton;
            if ($(window).width() < 768) {
                $('#pillsTabViewForm').css('z-index', '1062 !important');
                $('.aside-title').html(rightTitle);
                // $('#asideDetailForm').html(rightContent+popperForm);
                $('.aside-content').removeClass('px-0').addClass('px-0');
                $('.aside-content').html(`<object class="w-100 h-100" id="subfile_frame" data="/framePdf?token=${data.data.tokenForm}" type="text/html"><param name="allowfullscreen" value="true"></object>`);
                $('.aside-footer').html(footerButton);
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
    } catch (error) {
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
    } finally {

    }
}

document.querySelector('#leftColumnOngoing').addEventListener('scroll', () => {
    const leftColumn = document.querySelector('#leftColumnOngoing');
    const scrollThreshold = 100; // Threshold in pixels before the bottom to trigger loading

    if (leftColumn.scrollTop + leftColumn.clientHeight >= leftColumn.scrollHeight - scrollThreshold) {
        loadLeftColumn(); // Load more data when near the bottom
    }
});

$(document).on('click', '.pillTabDetailForm', function (event) {
    // rightColScrollbarInstance.updateScrollbarVisibility();
    // rightColScrollbarInstance.updateScrollbarThumb();
});

$(document).on('change', '.sortBy', async function (event) {
    currentPage = 1;
    isLoading = false;
    hasMoreData = true;
    loadLeftColumn();
});

$(document).on('click', '.searchFilterClear', function(event) {
    let isCleared = $(this).closest('.form-group').find('input.searchKeywordsLeftColumn').attr('data-iscleared');
    $(this).closest('.form-group').find('input.searchKeywordsLeftColumn').val('');
    $(this).closest('.form-group').find('input.searchKeywordsLeftColumn').focus();
    $(this).addClass('d-none');

    if (isCleared == 'false') {
        currentPage = 1;
        isLoading = false;
        hasMoreData = true;
        loadLeftColumn();
    }

    $(this).closest('.form-group').find('input.searchKeywordsLeftColumn').attr('data-iscleared', 'true');
});

$(document).on('click', '.searchFilter', function(event) {
    let isCleared = $(this).closest('.form-group').find('input.searchKeywordsLeftColumn').attr('data-iscleared');
    if (isCleared == 'false') {
        currentPage = 1;
        isLoading = false;
        hasMoreData = true;
        loadLeftColumn();
        $(this).attr('data-iscleared', 'false');
    }
});

$(document).on('keydown', '.searchKeywordsLeftColumn', function(event) {
    const currentValue = $(this).val().trim();
    let isCleared = $(this).attr('data-iscleared');
    if (event.key === 'Enter' || event.keyCode === 13) {
        if (currentValue.length === 0 && isCleared == 'false') {
            currentPage = 1;
            isLoading = false;
            hasMoreData = true;
            loadLeftColumn();
            $(this).attr('data-iscleared', 'true');
        }
        else if (currentValue.length > 0) {
            currentPage = 1;
            isLoading = false;
            hasMoreData = true;
            loadLeftColumn();
            $(this).attr('data-iscleared', 'false');
        }

        event.preventDefault();
    }
});

$(document).on('input', '.searchKeywordsLeftColumn', function(event) {
    clearTimeout(debounceTimeout);
    const currentValue = $(this).val().trim();
    let isCleared = $(this).attr('data-iscleared');
    if(currentValue.length === 0) {
        $(this).closest('.form-group').find('button.searchFilterClear').addClass('d-none');
        debounceTimeout = setTimeout(function () {
            if (isCleared == 'false') {
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
        // $(this).attr('data-iscleared', 'false');
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

$('#modalDocumentForm').on('hidden.coreui.modal', function (){
    selectedFiles = [];
    selectedFilesProperties = [];
    selectedFilesDocument = [];
    selectedFilesPropertiesDocument = [];
});

$(document).on('click', '#createNewRequest', async function (event) {
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
        // $('.modal').modal('hide');
        // $('#modalDocumentForm').modal('show');
        // $('#modalDocumentFormTitle').html(`New ${toTitleCase(document.querySelector('#newDocumentType').getSelectedOptions()['label'])}`);
        // $('#documentTypeForm').val($('#newDocumentType').val());
        // $(`#headerContainer`).html('');
        // $('#itemContainer').html('');
        // $('#itemContainerFooter').html('');
        // $('#formContainerFooter').html('');
        const params = {
            'documentType': $('#newDocumentType').val(),
            'requestType': $this.attr('data-request-type'),
            'loadType': 'NEW',
            'tokenForm': $this.attr('data-token'),
        };

        newForm(params);

        // headerForms(params);
        // itemForms(params);
        // itemContainerFooter(params);
        // containerFooter(params);
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
            $('#modalDocumentFormBody').html( data.data.form);
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

$(document).off('change', '#departmentOrder').on('change', '#departmentOrder', async function (event) {
    let $this = $(this);
    const params = {
        'documentType': $('#documentTypeForm').val(),
        'departmentId': $this.val(),
    };

    if($('#documentTypeForm').val() == '1') {
        if ($('#locationOrder').hasClass('select2-hidden-accessible')) {
            $('#locationOrder').select2('destroy');
        }

        if ($('#reviewerSelect').hasClass('select2-hidden-accessible')) {
            $('#reviewerSelect').select2('destroy');
        }

        if ($('#reviewerSelect2').hasClass('select2-hidden-accessible')) {
            $('#reviewerSelect2').select2('destroy');
        }

        if ($('#approverSelect').hasClass('select2-hidden-accessible')) {
            $('#approverSelect').select2('destroy');
        }

        if ($('#ccSelect').hasClass('select2-hidden-accessible')) {
            $('#ccSelect').select2('destroy');
        }

        $('#documentNumber').replaceWith(`<div class="skeleton" id="documentNumber"></div>`);
        $('#locationOrder').replaceWith(`<div class="skeleton" id="locationOrder"></div>`);
        $('#reviewerSelect').replaceWith(`<div class="skeleton" id="reviewerSelect" data-select=""></div>`);
        $('#reviewerSelect2').replaceWith(`<div class="skeleton" id="reviewerSelect2" data-select=""></div>`);
        $('#approverSelect').replaceWith(`<div class="skeleton" id="approverSelect" data-select=""></div>`);
        $('#ccSelect').replaceWith(`<div class="skeleton" id="ccSelect" data-select=""></div>`);

        if($this.val() == '') {
            $('#locationOrder').replaceWith(`<select class="select2" name="locationOrder" id="locationOrder"></select>`);
            $('#documentNumber').replaceWith(`<input type="text" class="form-control" id="documentNumber" name="seqNumber" value="" readonly="">`);
            $('#reviewerSelect').replaceWith(`<select class="select2 getEmployee" name="reviewer[]" id="reviewerSelect" data-type="REVIEWER"><option></option></select>`);
            $('#reviewerSelect2').replaceWith(`<select class="select2 getEmployee" name="reviewer[]" id="reviewerSelect2" data-type="REVIEWER"><option></option></select>`);
            $('#approverSelect').replaceWith(`<select class="select2 getEmployee" name="approver[]" id="approverSelect" data-type="APPROVER"><option></option></select>`);
            $('#ccSelect').replaceWith(`<select class="select2 getEmployee" name="cc[]" id="ccSelect" data-type="CC"><option></option></select>`);

            $('#locationOrder').select2({
                dropdownParent: $('#modalDocumentForm'),
                allowClear: true,
                placeholder: '-- Select --',
            });

            $('#reviewerSelect').select2({
                dropdownParent: $('#modalDocumentForm'),
                allowClear: true,
                placeholder: '-- Select --',
            });
            $('#reviewerSelect2').select2({
                dropdownParent: $('#modalDocumentForm'),
                allowClear: true,
                placeholder: '-- Select --',
            });
            $('#approverSelect').select2({
                dropdownParent: $('#modalDocumentForm'),
                allowClear: true,
                placeholder: '-- Select --',
            });
            $('#ccSelect').select2({
                dropdownParent: $('#modalDocumentForm'),
                allowClear: true,
                placeholder: '-- Select --',
            });
            return;
        }

        if($('#requestTypeForm') != 'REVISE') {
            const documentNumber = await getDocumentNumber(params);
            const seqNumber = documentNumber['seqNumber'];
            const formatNumber = documentNumber['formatNumber'];
            $('#documentNumber').replaceWith(`<input type="text" class="form-control" id="documentNumber" name="seqNumber" value="${seqNumber}${formatNumber}">`);
        }

        let optionLocation = await getLocation(params);
        $('#locationOrder').replaceWith(`<select class="select2" name="locationOrder" id="locationOrder"></select>`);
        $('#locationOrder').select2({
            dropdownParent: $('#modalDocumentForm'),
            minimumResultsForSearch: Infinity,
            allowClear: false,
            placeholder: '-- Select --',
            data: optionLocation,
        }).trigger('change');

        params.dataType = 'REVIEWER_1';
        let optionReviewer = await getApproval(params);
        let preselectReviewer = $('#reviewerSelect').attr('data-select');
        $('#reviewerSelect').replaceWith(`<select class="select2 getEmployee" name="reviewer[]" id="reviewerSelect" data-type="REVIEWER_1"><option></option></select>`);
        $('#reviewerSelect').select2({
            dropdownParent: $('#modalDocumentForm'),
            allowClear: true,
            placeholder: '-- Select --',
            data: optionReviewer,
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }

                if(data.description1 == '' || data.description1 == null || data.description1 == 'null') {
                    return $(`<div class="select2-result-item">
                        <div class="select2-result-item__title fw-semibold">${data.text}</div>
                    </div>`);
                }
                else{
                    return $(`<div class="select2-result-item">
                        <div class="select2-result-item__title fw-semibold">${data.text}</div>
                        <div class="select2-result__description">
                            <div class="description-line">${data.description1}</div>
                        </div>
                    </div>`);
                }
            },
            templateSelection: function(data) {
                return data.text;
            }
        });

        if(preselectReviewer != '') {
            $('#reviewerSelect').val(preselectReviewer).trigger('change');
        }

        params.dataType = 'REVIEWER_2';
        let optionReviewer2 = await getApproval(params);
        let preselectReviewer2 = $('#reviewerSelect2').attr('data-select');
        $('#reviewerSelect2').replaceWith(`<select class="select2 getEmployee" name="reviewer[]" id="reviewerSelect2" data-type="REVIEWER_2"><option></option></select>`);
        $('#reviewerSelect2').select2({
            dropdownParent: $('#modalDocumentForm'),
            allowClear: true,
            placeholder: '-- Select --',
            data: optionReviewer2,
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }

                if(data.description1 == '' || data.description1 == null || data.description1 == 'null') {
                    return $(`<div class="select2-result-item">
                        <div class="select2-result-item__title fw-semibold">${data.text}</div>
                    </div>`);
                }
                else{
                    return $(`<div class="select2-result-item">
                        <div class="select2-result-item__title fw-semibold">${data.text}</div>
                        <div class="select2-result__description">
                            <div class="description-line">${data.description1}</div>
                        </div>
                    </div>`);
                }
            },
            templateSelection: function(data) {
                return data.text;
            }
        });

        if(preselectReviewer2 != '') {
            $('#reviewerSelect2').val(preselectReviewer2).trigger('change');
        }

        params.dataType = 'APPROVER';
        let optionApprover = await getApproval(params);
        let preselectApprover = $('#approverSelect').attr('data-select');
        $('#approverSelect').replaceWith(`<select class="select2 getEmployee" name="approver[]" id="approverSelect" data-type="APPROVER"><option></option></select>`);
        $('#approverSelect').select2({
            dropdownParent: $('#modalDocumentForm'),
            allowClear: true,
            placeholder: '-- Select --',
            data: optionApprover,
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }

                if(data.description1 == '' || data.description1 == null || data.description1 == 'null') {
                    return $(`<div class="select2-result-item">
                        <div class="select2-result-item__title fw-semibold">${data.text}</div>
                    </div>`);
                }
                else{
                    return $(`<div class="select2-result-item">
                        <div class="select2-result-item__title fw-semibold">${data.text}</div>
                        <div class="select2-result__description">
                            <div class="description-line">${data.description1}</div>
                        </div>
                    </div>`);
                }
            },
            templateSelection: function(data) {
                return data.text;
            }
        });

        if(preselectApprover != '') {
            $('#approverSelect').val(preselectApprover).trigger('change');
        }

        params.dataType = 'CC';
        let optionCc = await getApproval(params);
        let preselectCc = $('#ccSelect').attr('data-select');
        $('#ccSelect').replaceWith(`<select class="select2 getEmployee" name="cc[]" id="ccSelect" data-type="CC"><option></option></select>`);
        $('#ccSelect').select2({
            dropdownParent: $('#modalDocumentForm'),
            allowClear: true,
            placeholder: '-- Select --',
            data: optionCc,
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }

                if(data.description1 == '' || data.description1 == null || data.description1 == 'null') {
                    return $(`<div class="select2-result-item">
                        <div class="select2-result-item__title fw-semibold">${data.text}</div>
                    </div>`);
                }
                else{
                    return $(`<div class="select2-result-item">
                        <div class="select2-result-item__title fw-semibold">${data.text}</div>
                        <div class="select2-result__description">
                            <div class="description-line">${data.description1}</div>
                        </div>
                    </div>`);
                }
            },
            templateSelection: function(data) {
                return data.text;
            }
        });

        if(preselectCc != '') {
            $('#ccSelect').val(preselectCc).trigger('change');
        }
    }
    else if($('#newDocumentType').val() == '7' || $('#newDocumentType').val() == '8' || $('#newDocumentType').val() == '9') {
        params.dataType = 'REVIEWER';
        let optionReviewer = await getApproval(params);
        $('#reviewerSelect').replaceWith(`<select class="select2 getEmployee" name="reviewer[]" id="reviewerSelect" data-type="REVIEWER"><option></option></select>`);
        $('#reviewerSelect').select2({
            dropdownParent: $('#modalDocumentForm'),
            allowClear: false,
            placeholder: '-- Select --',
            data: optionReviewer,
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }
                return $(`<div class="select2-result-item">
                            <div class="select2-result-item__title fw-semibold">${data.text}</div>
                            <div class="select2-result__description">
                                <div class="description-line">${data.description1}</div>
                            </div>
                        </div>`);
            },
            templateSelection: function(data) {
                return data.text;
            }
        });

        params.dataType = 'APPROVER';
        let optionApprover = await getApproval(params);
        $('#approverSelect').replaceWith(`<select class="select2 getEmployee" name="approver[]" id="approverSelect" data-type="APPROVER"><option></option></select>`);
        $('#approverSelect').select2({
            dropdownParent: $('#modalDocumentForm'),
            allowClear: false,
            placeholder: '-- Select --',
            data: optionApprover,
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }

                if(data.description1 == '' || data.description1 == null || data.description1 == 'null') {
                    return $(`<div class="select2-result-item">
                        <div class="select2-result-item__title fw-semibold">${data.text}</div>
                    </div>`);
                }
                else{
                    return $(`<div class="select2-result-item">
                        <div class="select2-result-item__title fw-semibold">${data.text}</div>
                        <div class="select2-result__description">
                            <div class="description-line">${data.description1}</div>
                        </div>
                    </div>`);
                }
            },
            templateSelection: function(data) {
                return data.text;
            }
        });
    }

});

$(document).off('change', '#locationOrder').on('change', '#locationOrder', async function (event) {
    let $this = $(this);
    $('#secondCheckerInfo').removeClass('required');
    if($this.val() == '22') {
        $('#secondCheckerInfo').html('');
        $('#secondCheckerInfo').addClass('required');
    }
    else {
        $('#secondCheckerInfo').html(' (optional)');
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

async function getDocumentNumber(params) {
    try {
        const response = await fetch(`/doc_approval/getDocumentNumber?documentType=${params['documentType']}&departmentId=${params['departmentId']}&type=DRAFT`, {
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
        return;
    }
}

async function getLocation(params) {
    try {
        const response = await fetch(`/doc_approval/getLocation?documentType=${params['documentType']}&departmentId=${params['departmentId']}`, {
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
            const options = data.map(row => ({
                id: row.location_id,
                text: row.location_name
            }));
            return options;
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

async function getApproval(params) {
    try {
        const response = await fetch(`/doc_approval/getApproval?documentType=${params['documentType']}&departmentId=${params['departmentId']}&dataType=${params['dataType']}`, {
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
            const options = data.map(row => ({
                id: row.employee_id,
                text: row.employee_name,
                description1: row.position_name,
            }));
            return options;
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

function updateCostCenterVirtualSelects() {
    Object.entries(virtualSelectCodeOrder).forEach(([elementId, vsInstance]) => {
        if (vsInstance && typeof vsInstance.setOptions === 'function') {
            if (costCenterOption.length == 1) {
                if (typeof vsInstance.destroy === 'function' && typeof VirtualSelect.init === 'function') {
                    vsInstance.destroy();
                    virtualSelectCodeOrder[elementId] = VirtualSelect.init({
                        ele: `#codeOrder${elementId}`,
                        hideClearButton: true,
                        search: true,
                        options: costCenterOption,
                        name: 'codeOrder[]',
                        selectedValue: costCenterOption[0].value,
                    });
                }
            }
            else{
                vsInstance.setOptions(costCenterOption);
                vsInstance.afterSetOptions();
            }
        }
    });
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
                                            <input type="text" class="form-control d-flex fw-bold" id="grandTotal" name="grandTotal" spellcheck="false" autocomplete="off" placeholder="0.00" disabled="" style="padding-right: 50px;">
                                            <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-3 fw-semibold">---</span>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
        $('#itemContainerFooter').html(itemFooter);
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
                    <button type="button" class="btn-close-black deleteFileItemDocument" title="Close" aria-label="Close"></button>
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

        const fileItem = `
            <div class="form-group" data-index="${index}">
                <div class="file-item list-group-item list-group-item-action fileListItem" data-index="${index}">
                    <div class="file-details">
                        <i class="${iconClass} file-icon"></i>
                        <div>
                            <span class="file-name">${fileName}</span>
                            <div class="file-size">${(file.size / 1024).toFixed(2)} KB</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close-black deleteFileItem" title="Close" aria-label="Close"></button>
                </div>
            </div>`;

        fileList.append(fileItem);
    });
}

$(document).on('click', '.saveForm', async function (event) {
    let $this = $(this);
    Snackbar.close();
    clearValidation();
    const formData = new FormData($('#formDocumentForm')[0]);
    formData.append('actionType', $this.attr('data-action'));
    if($('#documentTypeForm').val() == '7') {
        formData.delete('attachmentFileDocument[]');
        formData.delete('attachmentFile[]');
        const fileUpload = document.getElementById('fileUpload');
        const fileUploadDocument = document.getElementById('fileUploadDocument');

        if (selectedFilesDocument && selectedFilesDocument.length > 0) {
            selectedFilesDocument.forEach((file, index) => {
                const newFileName = escapeFilename(file.name);
                const newFile = new File([file], newFileName, {
                    type: file.type,
                    lastModified: file.lastModified,
                });

                formData.append('attachmentFileDocument[]', newFile);
            });
        }
        else {
            formData.append('attachmentFileDocument[]', '');
        }

        if (selectedFiles && selectedFiles.length > 0) {
            selectedFiles.forEach((file, index) => {
                const newFileName = escapeFilename(file.name);
                const newFile = new File([file], newFileName, {
                    type: file.type,
                    lastModified: file.lastModified,
                });

                formData.append('attachmentFile[]', newFile);
            });
        }
        else {
            formData.append('attachmentFile[]', '');
        }
    }
    else {
        formData.delete('attachmentFile[]');
        const fileUpload = document.getElementById('fileUpload');

        if (selectedFiles && selectedFiles.length > 0) {
            selectedFiles.forEach((file, index) => {
                const newFileName = escapeFilename(file.name);
                const newFile = new File([file], newFileName, {
                    type: file.type,
                    lastModified: file.lastModified,
                });

                formData.append('attachmentFile[]', newFile);
            });
        }
        else {
            formData.append('attachmentFile[]', '');
        }
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

        $(this).btnLoading(async function() {
            try {
                $this.prop('disabled', true);
                $this.html('<i class="fas fa-spinner fa-spin"></i> Please wait');
                const response = await fetch('/generatePdf', {
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

        $(this).btnLoading(async function() {
            try {
                $('.confirmAction').prop('disabled', true);
                $this.html('<i class="fas fa-spinner fa-spin"></i> Please wait');
                const response = await fetch('/doc_approval/updateSigner', {
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
                                'url': `/doc_approval/getProgress?token=${$this.attr('data-token')}`,
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
                    result.data.approval.REVIEWER.forEach((row, index) => {
                        disabledChecker = (row.readonly == true && disabledChecker == '') ? ' readonly=""' : '';
                        let selected = row.selected == true ? ' selected=""' : '';
                        optionChecker += `<option value="${row.employeeId}"${selected}>${row.employeeName}</option>`;
                    });

                    result.data.approval.REVIEWER2.forEach((row, index) => {
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
                                                <label for="reviewerSelectChange" class="form-label">First Checked By<span class="required"></span> :</label>
                                                <select class="select2" name="reviewer[]" id="reviewerSelectChange"${disabledChecker}>${optionChecker}</select>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="reviewerSelect2Change" class="form-label">Second Checked By${secondCheckerInfo} :</label>
                                                <select class="select2" name="reviewer[]" id="reviewerSelect2Change"${disabledChecker2}>${optionChecker2}</select>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="approverSelect" class="form-label">Approved By<span class="required"></span> :</label>
                                                <select class="select2" name="approver[]" id="approverSelectChange"${disabledApprover}>${optionApprover}</select>
                                            </div>
                                        </form>`);

                    $('#reviewerSelectChange').select2({
                        dropdownParent: $('#globalAside'),
                        allowClear: true,
                        placeholder: '-- Select --',
                    });
                    $('#reviewerSelect2Change').select2({
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

    $(this).btnLoading(async function() {
        try {
            $('.confirmAction').prop('disabled', true);
            $this.html('<i class="fas fa-spinner fa-spin"></i> Please wait');
            const response = await fetch('/doc_approval/updateAction', {
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