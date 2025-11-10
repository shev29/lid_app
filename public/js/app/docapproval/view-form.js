// let debounceTimeout;
var leftColScrollbarInstance;
var rightColScrollbarInstance;
let currentPage = 1;
let isLoading = false;
let hasMoreData = true;
let popperInstance;
const commentBuffer = [];

window.addEventListener('load', function() {
    if($('#leftColumnOngoing').length > 0) {
        // leftColScrollbarInstance = new ScrollbarCustom('#leftColumnOngoing', {top:null, left: null, right: null, overflowX: 'none', overflowY: 'scroll' });
        // rightColScrollbarInstance = new ScrollbarCustom('.right-column', { top: 0, left: null, right: 0, overflowX: 'none', overflowY: 'scroll' });
        // GET LEFT COLUMN DATA
        $('.sortBySelectOngoing').removeClass('active');
        $('.sortBySelectOngoing').find('input').prop('checked', false);
        $('.sortByPriority').addClass('active');
        $('.sortByPriority').find('input').prop('checked', true).trigger('change');
        $('#filterCheckAllRequestOngoing').prop('checked', true).trigger('change');
    }
    else {
        let rowId = $('.card-link-content').filter('.active').attr('id');
        viewForm({'token': $('#token').val(), 'rowId': rowId});
    }
});

const element = document.querySelector('#leftColumnOngoing');
if (element) {
    element.addEventListener('scroll', () => {
        const approvalOngoing = document.querySelector('#leftColumnOngoing');
        const scrollThreshold = 100; // Threshold in pixels before the bottom to trigger loading

        if (approvalOngoing.scrollTop + approvalOngoing.clientHeight >= approvalOngoing.scrollHeight - scrollThreshold) {
            loadLeftColumn();
        }
    });
}

function clearRightContent(){
    const rightContent = `<div class="full-height-column-wrapper">
                                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                    <div class="text-center">
                                        <i class="fa-light fa-file fs-1"></i>
                                        <div class="d-block fs-7 mt-2">Select an item on the left to view</div>
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
            'rowId': $('.card-link-content').filter('.active').attr('id'),
        };
        viewForm(params);
    }
});

$(document).off('click', '.docVersionItem').on('click', '.docVersionItem', function (event) {
    const $this = $(this);
    if (!$this.hasClass('active')) {
        $('.docVersionItem').removeClass('active');
        $this.addClass('active');
        const params = {
            'source': 'WEB',
            'form': $this.attr('data-form'),
            'token': $this.attr('data-token'),
            'type': 'VIEW',
            'rowId': $('.card-link-content').filter('.active').attr('id'),
        };
        viewForm(params);
    }
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
        $('.filterCheckRequestOngoing').prop('checked', true);
        $('.filterCheckRequestOngoing').closest('.dropdown-item').addClass('active');
        currentPage = 1;
        isLoading = false;
        hasMoreData = true;
        loadLeftColumn();
    }
    else {
        $(this).val('FALSE');
        $('.filterCheckRequestOngoing').prop('checked', false);
        $('.filterCheckRequestOngoing').closest('.dropdown-item').removeClass('active');
    }
});

$(document).on('click', '.filterCheckAllRequest', function (event) {
    if($(this).prop('checked') == false) {
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

$(document).on('change', '.filterCheckRequest', async function (event) {
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

async function loadLeftColumn() {
    if (isLoading || !hasMoreData) return;
    isLoading = true;
    let skeletonCard = `<div class="text-center skeletonCard">
                            <div class="stripes-red-blue stripes-red-blue-md"></div>
                            <div class="d-block fs-7 mt-2">Loading...</div>
                        </div>`;

    if(currentPage == 1){
        document.querySelector('#leftColumnOngoing').innerHTML = `<div class="card full-height-column-wrapper">
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

        formData.append('type', 'MY_APPROVAL');
        formData.append('status', $('#filterStatus').val());
        formData.append('page', currentPage);
        formData.append('token', $('#token').val());
        // const queryString = new URLSearchParams(formData).toString();

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

        const response = await fetch(`/doc_approval/ongoingApproval`, {
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
                document.querySelector('#leftColumnOngoing').innerHTML = '';
                if(data.viewForm == true) {
                    setTimeout(() => {
                        $('.header-toggler').attr('data-current', 'hide');
                        $('#leftColumnOngoingWrapper').removeClass('d-none').addClass('d-none');

                        let rowId = $('.card-link-content').filter('.active').attr('id');
                        viewForm({'token': $('#token').val(), 'rowId': rowId});
                    }, 100);
                }
                else {
                    clearRightContent();
                    // $('#leftColumnOngoingWrapper').find('div.custom-scrollbar').remove();
                    $('.header-toggler').attr('data-current', 'show');
                    $('#leftColumnOngoingWrapper').removeClass('d-none');

                    // leftColScrollbarInstance = new ScrollbarCustom('#leftColumnOngoing', {top:null, left: null, right: null, overflowX: 'none', overflowY: 'scroll' });
                    // leftColScrollbarInstance.updateScrollbarVisibility();
                    // leftColScrollbarInstance.scrollTo(0);
                    // leftColScrollbarInstance.updateScrollbarThumb();
                }
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
                                                                                    <div class="d-block fs-7 mt-2">No approvals</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>`;
                    const rightContent = `<div class="full-height-column-wrapper">
                                            <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                <div class="text-center">
                                                    <i class="fa-light fa-file fs-1"></i>
                                                    <div class="d-block fs-7 mt-2">Select an item on the left to view</div>
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
            $('#badgeCountOngoing').addClass('d-none');
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
                                                    <div class="d-block fs-7 mt-2">Select an item on the left to view</div>
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

async function viewForm(params) {
    const token = params['token'];
    const rowId = params['rowId'];
    Snackbar.close();
    $('#asideDetailForm').html('');
    $('.modal').modal('hide');
    $('.popper-area').html('');
    rightContentSkeleton('FETCH');
    try {
        const response = await fetch(`/doc_approval/viewForm?source=EMAIL&row_id=${rowId}&token=${token}`, {
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
            let popperForm = '';
            const rightTitle = result.data.title;
            // const rightContent = data.data.form;
            const rightContent = `/framePdf?token=${result.data.tokenForm}`;
            const footerButton = result.data.footerButton;
            // if(rightTitle == 'INSPECTION FORM') {
            //     popperForm = `<div class="popper-container col-md-6 p-3 shadow-medium d-none" id="popperAction" data-active="" style="z-index: 1061;">
            //                 </div>`;
            //     $('.actionHeaderContainer').html(` <button class="btn btn-secondary btn-sm actionBtnHeader" data-token="${result.data.tokenAttachment}" data-type="ATTACHMENTS" data-selectedversion="${result.data.selectedVersionLabel}"><i class="fa-solid fa-bars-progress"></i> View Order Form</button>`);
            // }
            // else {
                popperForm = `<div class="popper-container col-md-6 p-3 shadow-medium d-none" id="popperAction" data-active="" style="z-index: 1061;">
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
                $('.actionHeaderContainer').html(` <button class="btn btn-secondary btn-sm actionBtnHeader" data-token="${result.data.tokenAttachment}" data-type="ATTACHMENTS" data-type-flow="APPROVAL" data-selectedversion="${result.data.selectedVersionLabel}"><i class="fa-solid fa-bars-progress"></i> View Attachments</button>`);
            // }

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
                $('.popper-area-modal').html(popperForm);
                $('#modalDocumentForm').modal('show');
            }
            else{
                $('.card-title-right-column').html(rightTitle);
                $('.card-button-right-column').html('');
                // $('.right-column').html(rightContent);
                $('.right-column').html(`<object class="w-100 h-100" id="subfile_frame" data="${rightContent}" type="text/html"><param name="allowfullscreen" value="true"></object>`);
                $('.card-footer-fixed').html(footerButton);
                $('.popper-area').html(popperForm);
            }

            let badgeStatus = '';
            if(result.data.signStatusName == 'FULLY APPROVED') {
                badgeStatus = `<span class="badge bg-success fs-9 fw-semibold">FULLY APPROVED</span>`;
            }
            if(result.data.signStatusName == 'PARTIAL APPROVED') {
                badgeStatus = `<span class="badge bg-info fs-9 fw-semibold">PARTIAL APPROVED</span>`;
            }
            else if(result.data.signStatusName == 'FULLY REJECTED') {
                badgeStatus = `<span class="badge bg-danger fs-9 fw-semibold">FULLY REJECTED</span>`;
            }
            else if(result.data.signStatusName == 'SEND BACK') {
                badgeStatus = `<span class="badge bg-warning fs-9 fw-semibold">SEND BACK TO REVISE</span>`;
            }

            if(badgeStatus != '') {
                let titleContainer = $(`#${result.rowId}`).find('div.card-header').find('div.card-title');
                if(titleContainer.find('div.badge-status').length > 0) {
                    let badgePriority = '';
                    if(titleContainer.find('div.badge-status').find('.badge-priority').length > 0) {
                        badgePriority = titleContainer.find('div.badge-status span.badge-priority').prop('outerHTML');
                    }
                    titleContainer.find('div.badge-status').html(badgePriority + badgeStatus);
                }
                else {
                    titleContainer.append(`<div class="float-end badge-status">${badgeStatus}</div>`);
                }
                titleContainer.addClass('done');
            }

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
            // rightColScrollbarInstance.updateScrollbarVisibility();
            // rightColScrollbarInstance.scrollTo(0);
            // rightColScrollbarInstance.updateScrollbarThumb();
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
        // rightColScrollbarInstance.updateScrollbarVisibility();
        // rightColScrollbarInstance.scrollTo(0);
        // rightColScrollbarInstance.updateScrollbarThumb();
    } finally {

    }
}

function rightContentSkeleton(type){
    const rightContent = `<div class="full-height-column-wrapper">
                                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                    <div class="center-container">
                                        <div class="stripes-red-blue stripes-red-blue-md"></div>
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

// $(document).on('click', '.header-toggler', function (event) {
//     let dBlock = $('#leftColumnApprovalWrapper').hasClass('d-block');
//     if(dBlock == true) {
//         $('#leftColumnApprovalWrapper').removeClass('d-block').addClass('d-none');
//         $(this).attr('data-current', 'hide');
//     }
//     else {
//         $('#leftColumnApprovalWrapper').removeClass('d-none').addClass('d-block');
//         $(this).attr('data-current', 'show');
//         requestAnimationFrame(() => {
//             if (!leftColScrollbarInstance) {
//                 leftColScrollbarInstance = new ScrollbarCustom('#leftColumnOngoing', {
//                     top: null,
//                     right: 10,
//                     overflowX: 'none',
//                     overflowY: 'scroll'
//                 });
//                 leftColScrollbarInstance.forceUpdate();
//             }
//             else {
//                 leftColScrollbarInstance.refresh();
//             }
//         });
//     }
// });

$(document).on('click', '.pillTabDetailForm', function (event) {
    // rightColScrollbarInstance.updateScrollbarVisibility();
    // rightColScrollbarInstance.updateScrollbarThumb();
});

async function showPopupAction(params) {
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
        $('.autosize').autosize().trigger('change');
    }
    else if(action == 'PRINT' || action == 'DOWNLOAD') {
        let popperTitle = '', popperButton = '';
        if(action == 'PRINT') {
            popperTitle = 'Print Documents';
            popperButton = 'Print';
            popperButtonClass = 'btn-info';
        }
        else  {
            popperTitle = 'Downloads';
            popperButton = 'Download';
            popperButtonClass = 'btn-danger';
        }

        $('#popperAction').html(`<div class="popper-arrow" data-popper-arrow></div>
                                <h6 class="modal-title modal-title-black mb-2">${popperTitle}</h6>
                                <form role="form" class="form-horizontal" enctype="multipart/form-data" id="formAction">
                                    <input type="hidden" id="tokenFormAction" name="tokenForm" autocomplete="false" value="">
                                    <div class="col-sm-12">
                                        <div class="skeleton"></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="skeleton"></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="skeleton"></div>
                                    </div>
                                </form>
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button class="btn btn-default closePopper">Close</button>
                                    <button class="btn ${popperButtonClass} popperButton" data-type="${action}">${popperButton}</button>
                                </div>`);
        $('#popperAction').removeClass('d-none');
    }

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
    else if (action == 'PRINT' || action == 'DOWNLOAD') {
        const documentList = await getDocumentList(params);
    }
}

function hidePopup() {
    if (popperInstance) {
        let action = $('#popperAction').attr('data-type');
        if(action == 'APPROVE' || action == 'REJECT' || action == 'SEND_BACK') {
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

$(document).on('click', '.actionBtn', async function (event) {
    let $this = $(this);
    if($this.attr('data-type') == 'PROGRESS' || $this.attr('data-type') == 'PROGRESS_SIGN' || $this.attr('data-type') == 'INSPECTION') {
        // let url = '', title = '';
        // if($this.attr('data-type') == 'PROGRESS') {
        //     title = 'Progress';
        //     url = `/doc_approval/getProgress?token=${$this.attr('data-token')}`;
        // }
        // else if($this.attr('data-type') == 'INSPECTION') {
        //     title = 'Inspection Form';
        //     url = `/doc_approval/getInspection?token=${$this.attr('data-token')}`;
        // }

        // asideHide();
        // $('.aside-title').html(`${title} ${$this.attr('data-selectedversion')}`);
        // $('.aside-content').html(`<form role="form" class="form-horizontal pt-3" enctype="multipart/form-data" id="asideForm">
        //     <div class="full-height-column-wrapper">
        //         <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
        //             <div class="text-center">
        //                 <i class="fa-regular fa-circle-notch fa-spin fs-5"></i>
        //                 <div class="d-block fs-7 mt-2">Loading...</div>
        //             </div>
        //         </div>
        //     </div>
        // </form>`);

        // $('.overlay-aside').addClass('show').trigger('shown');
        // if($this.attr('data-type') == 'PROGRESS'){
        //     $('#globalAside').addClass('show aside-lg').trigger('shown');
        // }
        // else {
        //     $('#globalAside').addClass('show').trigger('shown');
        // }

        // $('body').addClass('overflow-hidden');
        // $('#asideDetailForm').scrollTop(0);
        // $('.asideFooterBtn').html(`<div class="col-8 d-flex justify-content-center justify-content-md-end mb-2 px-0">
        //     <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
        // </div>`);

        // try {
        //     const response = await fetch(url, {
        //         method: 'GET',
        //         headers: {
        //             'Content-Type': 'application/json',
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        //             'Accept': 'application/json',
        //             'Referer': window.location.href
        //         },
        //     });

        //     const result = await response.json();
        //     if (response.status === 200) {
        //         $('#asideForm').html(result.data.form);
        //     }
        //     else {
        //         console.error('HTTP Error:', response.status);
        //     }
        // } catch (error) {
        //     return;
        // }
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

                    // let countdownInterval = setInterval(() => {
                    //     countdown--;
                    //     document.getElementById('snackbar-countdown').textContent = countdown;
                    //     if (countdown < 0) {
                    //         clearInterval(countdownInterval);
                    //         window.location.href = result.redirect_uri;
                    //     }
                    // }, 1000);
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
                                'url': `/doc_approval/getProgress?type=${$this.attr('data-type')}&token=${$this.attr('data-token')}`,
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
                'url': `/doc_approval/getInspection?type=${$this.attr('data-type')}&token=${$this.attr('data-token')}`,
            };
            viewProgress(params);
        }
    }
    else if($this.attr('data-type') == 'CANCEL' ) {
        $('.modal').modal('hide');
        $('#modalMessageTitle').html('');
        $('#modalMessageBody').html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="formAction">
                                        <input type="hidden" id="tokenFormAction" name="tokenForm" autocomplete="false" value="${$(this).attr('data-token')}">
                                        <div class="form-group mb-3">
                                            <label for="commentAction" class="form-label required" id="commentActionLabel">Reason (required) :</label>
                                            <textarea class="form-control autosize singleLine" spellcheck="false" placeholder="Reason to cancel" maxlength="255" id="commentAction" name="commentAction" style="height: 53px;"></textarea>
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
            'source': 'WEB',
            'form': $this.attr('data-form'),
            'token': $this.attr('data-token'),
            'type': $this.attr('data-type'),
        };

        newForm(params);
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
        'url': `/doc_approval/getProgress?type=${$this.attr('data-type')}&token=${$this.attr('data-token')}`,
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
    let actionType = $this.attr('data-type');
    formData.append('actionType', actionType);
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
                if(actionType == 'APPROVE') {
                    const rightContent = `/framePdf?token=${result.data.tokenForm}`;
                    $('.right-column').html(`<object class="w-100 h-100" id="subfile_frame" data="${rightContent}" type="text/html"><param name="allowfullscreen" value="true"></object>`);
                }

                $('.card-footer-fixed').html(result['footerButton']);
                hidePopup();
                Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result['message']}` });

                if(result.rowId != null) {
                    let titleContainer = $(`#${result.rowId}`).find('div.card-header').find('div.card-title');
                    if(!titleContainer.hasClass('done')) {
                        let badgeStatus = '';
                        if(result['signerStatus'] == 'FULLY APPROVED') {
                            badgeStatus = `<span class="badge bg-success fs-9 fw-semibold">FULLY APPROVED</span>`;
                        }
                        else if(result['signerStatus'] == 'PARTIAL APPROVED') {
                            badgeStatus = `<span class="badge bg-info fs-9 fw-semibold">PARTIAL APPROVED</span>`;
                        }
                        else if(result['signerStatus'] == 'FULLY REJECTED') {
                            badgeStatus = `<span class="badge bg-danger fs-9 fw-semibold">FULLY REJECTED</span>`;
                        }
                        else if(result['signerStatus'] == 'SEND BACK TO REVISE') {
                            badgeStatus = `<span class="badge bg-warning fs-9 fw-semibold">SEND BACK TO REVISE</span>`;
                        }

                        if(titleContainer.find('div.badge-status').length > 0) {
                            titleContainer.find('div.badge-status').append(`${badgeStatus}`);
                        }
                        else {
                            titleContainer.append(`<div class="float-end badge-status">${badgeStatus}</div>`);
                        }
                        titleContainer.addClass('done');
                    }
                }
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

$(document).on('click', '.popperButton', function () {
    let $this = $(this);
    if($this.attr('data-type') == 'PRINT') {
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
});

$(document).on('click', function (e) {
    if (!$(e.target).closest('.popper-container, .popper-button, .approveBtn, .rejectBtn, .sendBackBtn').length) {
        hidePopup();
    }
});

async function getDocumentList(params) {
    const button = params['button'];
    const action = params['action'];
    let tokenForm = button.getAttribute('data-token');
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

$(document).on('click', '.asideButton', async function () {
    let $this = $(this);
    if($this.attr('data-type') == 'PRINT' || $this.attr('data-type') == 'DOWNLOAD') {
        const printBackdrop = $(`
            <div class="modal-backdrop" style="position: fixed; top: 0; left: 0; z-index: 9999; background: rgba(255,255,255,0.9)">
                <div class="modal-content" style="width: 100%; height: 100%;display: flex; justify-content: center; align-items: center;">
                    <div class="center-container">
                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                        <div class="d-block fs-7 mt-2">Preparing document...</div>
                    </div>
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
});

async function getSendBackOptions(params) {
    let option = `<option></option>`;
    try {
        const response = await fetch(`/doc_approval/getSendBackOptions?tokenForm=${params['tokenForm']}`, {
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
            url = `/doc_approval/getProgress?type=${$this.attr('data-type')}&token=${$this.attr('data-token')}`;
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
