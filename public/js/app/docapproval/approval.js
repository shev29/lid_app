var leftColScrollbarInstance;
var rightColScrollbarInstance;
let currentPage = 1;
let isLoading = false;
let hasMoreData = true;
let popperInstance;
const commentBuffer = [];

window.addEventListener('load', function() {
    $('#filterCheckAllRequestOngoing').prop('checked', true).trigger('change');
//     // leftColHistoryScrollbarInstance = new ScrollbarCustom('#leftColumnHistory', {top:null, right: 11.2, overflowX: 'none', overflowY: 'scroll' });
//     // rightColScrollbarInstance = new ScrollbarCustom('.right-column', { top: 0, left: null, right: 0, overflowX: 'none', overflowY: 'scroll' });
//     // GET LEFT COLUMN DATA
//     // $('#filterCheckAllRequestOngoing').prop('checked', true).trigger('change');
//     // $('#filterCheckAllRequestHistory').prop('checked', true).trigger('change');
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

$(document).on('click', '.filterButtonAside', function (event) {
    asideHide();
    $('#filterStatus').val($(this).attr('data-status'));
    currentPage = 1;
    isLoading = false;
    hasMoreData = true;
    loadLeftColumn();
});

$(document).on('change', '.filterCheckRequestCompany', function(event) {
    const $allCheckbox = $('.filterCheckRequestCompany[value="ALL"]');
    const $otherCheckboxes = $('.filterCheckRequestCompany').not($allCheckbox);
    const $checkedOthers = $otherCheckboxes.filter(':checked');

    if($(this).val() === 'ALL') {
        if($(this).is(':checked')) {
            $(this).prop('checked', false);
            $('.filterCheckRequestCompany').prop('checked', true);
            $('.filterCheckRequestCompany').closest('.dropdown-item').addClass('active');
        }
        else {
            $('.filterCheckRequestCompany').prop('checked', false);
            $('.filterCheckRequestCompany').closest('.dropdown-item').removeClass('active');
            document.querySelector('#leftColumnOngoing').innerHTML = `<div class="card full-height-column-wrapper">
                                                                <div class="card-body d-flex justify-content-center align-items-center">
                                                                    <div class="text-center">
                                                                        <i class="fa-light fa-file fs-1"></i>
                                                                        <div class="d-block fs-7 mt-2">Select at least one approval type</div>
                                                                    </div>
                                                                </div>
                                                            </div>`;
            const rightContent = `<div class="full-height-column-wrapper">
                                            <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                <div class="text-center">
                                                    <i class="fa-light fa-file fs-1"></i>
                                                    <div class="d-block fs-7 mt-2">Select at least one approval type</div>
                                                </div>
                                            </div>
                                        </div>`;
            $('.card-title-right-column').html('Detail Form');
            $('.actionHeaderContainer').html('');
            $('.card-footer-fixed').addClass('border-top-0');
            $('.card-footer-fixed').html('');
            $('#asideDetailForm').html(rightContent);
            $('.right-column').html(rightContent);
            return;
        }
    }
    else {
        if($checkedOthers.length === $otherCheckboxes.length) {
            $allCheckbox.prop('checked', true)
                       .closest('.dropdown-item').addClass('active').trigger('change');
        }
        else if($allCheckbox.is(':checked')) {
            $allCheckbox.prop('checked', false)
                       .closest('.dropdown-item').removeClass('active');
        }

        $(this).closest('.dropdown-item').toggleClass('active', $(this).is(':checked'));

        if($('.filterCheckRequestCompany:checked').length === 0) {
            $allCheckbox.prop('checked', false)
                       .closest('.dropdown-item').removeClass('active');

            document.querySelector('#leftColumnOngoing').innerHTML = `<div class="card full-height-column-wrapper">
                                                                <div class="card-body d-flex justify-content-center align-items-center">
                                                                    <div class="text-center">
                                                                        <i class="fa-light fa-file fs-1"></i>
                                                                        <div class="d-block fs-7 mt-2">Select at least one approval type</div>
                                                                    </div>
                                                                </div>
                                                            </div>`;
            const rightContent = `<div class="full-height-column-wrapper">
                                            <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                <div class="text-center">
                                                    <i class="fa-light fa-file fs-1"></i>
                                                    <div class="d-block fs-7 mt-2">Select at least one approval type</div>
                                                </div>
                                            </div>
                                        </div>`;
            $('.card-title-right-column').html('Detail Form');
            $('.actionHeaderContainer').html('');
            $('.card-footer-fixed').addClass('border-top-0');
            $('.card-footer-fixed').html('');
            $('#asideDetailForm').html(rightContent);
            $('.right-column').html(rightContent);
            return;
        }
    }

    currentPage = 1;
    isLoading = false;
    hasMoreData = true;
    loadLeftColumn();
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

$(document).on('click', '.card-link-content', function (event) {
    const $this = $(this);
    if ($this.hasClass('active')) {
        $this.removeClass('active');
        clearRightContent();
    }
    else {
        $('.card-link-content').removeClass('active');
        $this.addClass('active');
        const params = {
            'token': $this.attr('data-token'),
            'rowId': $this.attr('data-row'),
        };
        viewForm(params);
    }
});

$(document).on('click', '.docVersionItem', function (event) {
    const $this = $(this);
    if (!$this.hasClass('active')) {
        $('.docVersionItem').removeClass('active');
        $this.addClass('active');
        const rowId = null;
        const params = {
            'token': $this.attr('data-token'),
            'rowId': rowId,
        };
        viewForm(params);
    }
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

$(document).on('click', '.sortBySelectOngoing', function (event) {
    const $this = $(this);
    if ($this.hasClass('active')) {
        $this.find('input').prop('checked', true);
        return;
    }
    else{
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
    currentPage = 1;
    isLoading = false;
    hasMoreData = true;
    loadLeftColumn();
});

async function loadLeftColumn() {
    if (isLoading || !hasMoreData) return;
    isLoading = true;
    let skeletonCard = `<div class="center-container skeletonCard">
                            <div class="stripes-red-blue stripes-red-blue-md"></div>
                            <div class="d-block fs-7 mt-2">Loading...</div>
                        </div>`;

    if(currentPage == 1){
        document.querySelector('.left-column').innerHTML = `<div class="card full-height-column-wrapper">
                                                                <div class="card-body d-flex justify-content-center align-items-center">
                                                                    <div class="center-container">
                                                                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                                                                        <div class="d-block fs-7 mt-2">Loading...</div>
                                                                    </div>
                                                                </div>
                                                            </div>`;
        rightContentSkeleton('FETCH');
    }
    else{
        document.querySelector('.left-column').insertAdjacentHTML('beforeend', skeletonCard);
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

        formData.append('type', 'MY_APPROVAL');
        formData.append('status', $('#filterStatus').val());
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
                document.querySelector('.left-column').innerHTML = '';
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

                if(currentPage == 1){
                    rightContentSkeleton('PRE_SELECT');
                }

                currentPage++;
                hasMoreData = data.hasMorePages;
            }
            else {
                hasMoreData = false; // No more data to load
                if(currentPage == 1){
                    document.querySelector('.left-column').innerHTML = `<div class="card full-height-column-wrapper">
                                                                            <div class="card-body d-flex justify-content-center align-items-center">
                                                                                <div class="text-center">
                                                                                    <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                                                    <div class="d-block fs-7 mt-2">No approvals</div>
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
        else if (data.status === 404) {
            $('.skeletonCard').remove();
            document.querySelector('.left-column').innerHTML = `<div class="card full-height-column-wrapper">
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
        else if (response.status === 419) {
            Snackbar.show({ pos: 'bottom-center', text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : CSRF token mismatch, please refresh this page' });
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
                // $('#modalDocumentFormFooterHistory').html(data.data.footerButton);
                let modalFooterButton = footerButton;
                modalFooterButton = modalFooterButton.replaceAll('-outline', '');
                modalFooterButton = modalFooterButton.replaceAll('col-sm-3', '');
                modalFooterButton = modalFooterButton.replaceAll('actionBtn', 'actionBtn w-100 w-md-auto me-md-2');

                const buttonRegex = /<button[^>]*>[\s\S]*?<\/button>/g;
                const buttonsArray = modalFooterButton.match(buttonRegex);

                const regex = /<div class="alert[^>]*>.*?<\/div>/s;
                const alertDiv = modalFooterButton.match(regex)[0];
                let alert = '';
                if(alertDiv != '' && alertDiv != undefined) {
                    alert = `<div class="col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">${alertDiv}</div>`
                }

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
                                            ${alert}
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
                commentBuffer.length = 0;
                const rightTitle = data.data.title;
                // const rightContent = data.data.form;
                const rightContent = `/framePdf?token=${data.data.tokenForm}`;
                const footerButton = data.data.footerButton;
                const popperForm = `<div class="popper-container col-md-6 p-3 shadow-medium d-none" id="popperAction" data-active="" style="z-index: 1061;">
                                        <div class="popper-arrow" data-popper-arrow></div>
                                        <form role="form" class="form-horizontal" enctype="multipart/form-data" id="formAction">
                                            <input type="hidden" id="tokenFormAction" name="tokenForm" autocomplete="false" value="${data.data.tokenForm}">
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

                if ($(window).width() < 768) {
                    $('#modalDocumentFormBody').removeClass('pt-0 pb-5 p-0').addClass('p-0');
                    $('#modalDocumentFormTitle').html(rightTitle);
                    $('#modalDocumentFormBody').html(`<object class="w-100 h-100" id="subfile_frame" data="${rightContent}" type="text/html"><param name="allowfullscreen" value="true"></object>`);

                    let modalFooterButton = footerButton;
                    modalFooterButton = modalFooterButton.replaceAll('-outline', '');
                    modalFooterButton = modalFooterButton.replaceAll('col-sm-3', 'w-100 w-md-auto me-md-2');

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
                        <button class="btn btn-default btn-sm actionBtnHeader" data-token="${data.data.tokenAttachment}" data-type="ATTACHMENTS" data-type-flow="APPROVAL" data-selectedversion="${data.data.selectedVersionLabel}"><i class="fa-solid fa-bars-progress"></i> View Attachments</button>`);
                    // $('.right-column').html(rightContent);
                    $('.right-column').html(`<object class="w-100 h-100" id="subfile_frame" data="${rightContent}" type="text/html"><param name="allowfullscreen" value="true"></object>`);
                    $('.card-footer-fixed').html(footerButton);
                    $('.popper-area').html(popperForm);
                    // rightColScrollbarInstance.updateScrollbarVisibility();
                    // rightColScrollbarInstance.scrollTo(0);
                    // rightColScrollbarInstance.updateScrollbarThumb();
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
            {data: 'employeeName', type: 'string', searchable: false, orderable: false, width: '13%'},
            {data: 'docNo', type: 'string', searchable: false, orderable: false, width: '13%'},
            {data: 'companyName', type: 'string', searchable: false, orderable: false, width: '13%'},
            {data: 'deptName', type: 'string', searchable: false, orderable: false, width: '13%'},
            {data: 'priority', type: 'string', searchable: false, orderable: false, width: '5%'},
            {data: 'items', type: 'string', searchable: false, orderable: false, width: '20%', renderHtml: true, render: (data) => data.replace(/\n/g, '<br>')},
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

document.querySelector('.left-column').addEventListener('scroll', () => {
    const leftColumn = document.querySelector('.left-column');
    const scrollThreshold = 100;
    if (leftColumn.scrollTop + leftColumn.clientHeight >= leftColumn.scrollHeight - scrollThreshold) {
        loadLeftColumn();
    }
});

$(document).on('click', '.pillTabDetailForm', function (event) {
    // rightColScrollbarInstance.updateScrollbarVisibility();
    // rightColScrollbarInstance.updateScrollbarThumb();
});

async function showPopupAction(params) {
    // $('#popupTitle').text(title);
    const button = params['button'];
    const action = params['action'];
    const rowId = params['rowId'];
    $('#popperAction').attr('data-type', action);
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

function hidePopup() {
    if (popperInstance) {
        let action = $('#popperAction').attr('data-type');
        if (action !== '') {
            let existingIndex = commentBuffer.findIndex(item => item.action === action);
            if (existingIndex !== -1) {
                // Update existing item
                commentBuffer[existingIndex].comment = $('#commentAction').val().trim();
            }
            else if($('#commentAction').val() != '') {
                // Push new item
                commentBuffer.push({
                    action: action,
                    comment: $('#commentAction').val(),
                });
            }
        }

        $('#popperAction').attr('data-type', '');
        $('.popper-container').addClass('d-none');

        popperInstance.destroy();
    }
}

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
                            url.setAttribute('download', ''); // Force download
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
});

$(document).on('click', '.actionBtn', async function (event) {
    let $this = $(this);
    if($this.attr('data-type') == 'PROGRESS' || $this.attr('data-type') == 'PROGRESS_SIGN' || $this.attr('data-type') == 'INSPECTION') {
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
        else if($this.attr('data-type') == 'PROGRESS_SIGN') {
            const params = {
                'title': 'Progress',
                'dataType': $this.attr('data-type'),
                'url': `/doc_approval/getProgress?type=${$this.attr('data-type')}&token=${$this.attr('data-token')}`,
            };
            viewProgress(params);
        }
        else if($this.attr('data-type') == 'INSPECTION') {
            const params = {
                'title': 'Inspection Form',
                'dataType': $this.attr('data-type'),
                'url': `/doc_approval/getInspection?token=${$this.attr('data-token')}`,
            };
            viewProgress(params);
        }
    }
    else if($('#popperAction').attr('data-type') != $(this).attr('data-type')){
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
    if(dataType == 'PROGRESS' || dataType == 'PROGRESS_SIGN'){
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
                currentPage = 1;
                isLoading = false;
                hasMoreData = true;
                loadLeftColumn();

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

async function getSendBackOptions(params) {
    let option = `<option></option>`;
    try {
        const response = await fetch(`/doc_approval/getSendBackOptions?type=APPROVAL&tokenForm=${params['tokenForm']}`, {
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
                value: row.sign_flow_id,
                label: row.employee_name+' - '+row.flow_as,
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
    } catch (error) {
        return;
    }
}

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

    if ($(window).width() < 768) {
        asideHide();
        $('.aside-title').html(`${$this.find('span.file-name').html()}`);
        $('.aside-content').html(`<div class="loading-content">
                                    <div class="center-container text-white">
                                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                                        <div class="d-block fs-7 mt-2">Loading...</div>
                                    </div>
                                </div>`);

        $('.overlay-aside').addClass('show').trigger('shown');
        $('#globalAside').addClass('show').trigger('shown');
        $('body').addClass('overflow-hidden');
        $('#asideDetailForm').removeClass('py-3 p-0').addClass('p-0');
        $('#asideDetailForm').scrollTop(0);

        $('.asideFooterBtn').html(`<div class="col-12 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
                                    </div>`);
    }
    else {
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
    }

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
                if ($(window).width() < 768) {
                    $('.aside-content').html(`<object id="subfile_frame" class="w-100 h-100" data="${data.data.frameSrc}" type="text/html"><param name="allowfullscreen" value="true"></object>`);
                }
                else {
                    $('#fullscreen-content-aside').html(`<object id="subfile_frame" data="${data.data.frameSrc}" type="text/html"><param name="allowfullscreen" value="true"></object>`);
                }
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
        $('#asideDetailForm').removeClass('py-3 p-0').addClass('py-3');
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

$(document).on('click', '#filterHistory', async function (event) {
    let $this = $(this);
    asideHide();
    $('.overlay-aside').addClass('show').trigger('shown');
    $('#historyFilterAside').addClass('show').trigger('shown');
    $('body').addClass('overflow-hidden');
    $('#historyFilterAsideForm').scrollTop(0);
    $('.autosize').autosize({ append: "\n" });
    $('#historySortBy').select2({
        dropdownParent: $('#historyFilterAside'),
        minimumResultsForSearch: Infinity,
        allowClear: false,
        placeholder: '-- Select --',
    });
    $('#historyYear').select2({
        dropdownParent: $('#historyFilterAside'),
        minimumResultsForSearch: Infinity,
        allowClear: false,
        placeholder: '-- Select --',
    });
    $('#historyPeriodFilter').select2({
        dropdownParent: $('#historyFilterAside'),
        minimumResultsForSearch: Infinity,
        allowClear: false,
        placeholder: '-- Select --',
    });
});

$(document).on('change', '#historyPeriodFilter', async function (event) {
    $this = $(this);
    $('#historyYearContainer').removeClass('d-none').addClass('d-none');
    $('#historyRangePeriodContainer').removeClass('d-none').addClass('d-none');
    if($this.val() == 'YEARLY') {
        $('#historyYearContainer').removeClass('d-none');
    }
    else if($this.val() == 'DATE_RAGE') {
        $('#historyRangePeriodContainer').removeClass('d-none');
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