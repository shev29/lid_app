// bootstrap.Modal.prototype._enforceFocus = function() {
//     return;
// };
var debounceTimeout;
var appHeight = (function() {
    let vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
})();
window.addEventListener('resize', appHeight);

window.select2SearchingMessage = 'Loading results...';
var Utils = $.fn.select2.amd.require('select2/utils');
var Dropdown = $.fn.select2.amd.require('select2/dropdown');
var AttachBody = $.fn.select2.amd.require('select2/dropdown/attachBody');
var DropdownSearch = $.fn.select2.amd.require('select2/dropdown/search');
var dropdownAdapter = Utils.Decorate(
    Utils.Decorate(Dropdown, DropdownSearch),
    AttachBody
);

$.fn.btnLoading = async function(callback) {
    var $clickedButton = $(this);
    var originalText = $clickedButton.html();
    $clickedButton.closest('div').find('button').prop('disabled', true);
    $clickedButton.html('<i class="fas fa-spinner fa-spin"></i> Please wait');

    try {
        await callback();
    } catch (error) {
        console.error("Something went wrong:", error);
    } finally {
        $clickedButton.closest('div').find('button').prop('disabled', false);
        $clickedButton.html(originalText);
    }
};

$(document).on('click', '.notificationHeader', function (event) {
    $('.modal').modal('hide');
    $('#modalMdGeneralTitle').html('Notifications');
    $('#modalMdGeneralBody').html('');
    $('#modalMdGeneralFooter').html(`<div class="container-fluid p-0">
            <div class="row justify-content-center w-100 mx-0">
                <div class="col-8 d-flex justify-content-center mb-2 px-0">
                    <button type="button" class="btn btn-default w-100 me-2 changePasswordGroup" data-coreui-dismiss="modal" title="Close">
                        <i class="fas fa-xmark"></i> Close
                    </button>
                </div>
            </div>
        </div>`);
    $('#modalMdGeneralDialog').removeClass('top-20');
    $('#modalMdGeneralDialog').removeClass('modal-dialog-scrollable').addClass('modal-dialog-scrollable');
    $('#modalMdGeneral').modal('show');
});

$(document).on('click', '#listProfile', function (event) {
    $('.modal').modal('hide');
    $('#modalMdGeneralTitle').html('Profile');
    $('#modalMdGeneralBody').html(`Coming soon...`);

    $('#modalMdGeneralFooter').html(`<div class="container-fluid p-0">
                                        <div class="row justify-content-center w-100 mx-0">
                                            <div class="col-8 d-flex justify-content-center mb-2 px-0">
                                                <button type="button" class="btn btn-default w-100 me-2 changePasswordGroup" data-coreui-dismiss="modal" title="Close">
                                                    <i class="fas fa-xmark"></i> Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>`);
    $('#modalMdGeneralDialog').removeClass('top-20').addClass('top-20');
    $('#modalMdGeneralDialog').removeClass('modal-dialog-scrollable');
    $('#modalMdGeneral').modal('show');
});

$(document).on('click', '#listChangePassword', function (event) {
    $('.modal').modal('hide');
    $('#modalMdGeneralTitle').html('Change Password');
    $('#modalMdGeneralBody').html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="formChangePassword">
                                    <input type="hidden" name="token" value="${$(this).attr('data-token')}">
                                    <div class="form-group mb-3">
                                        <label for="newPassword" class="form-label required" id="">New Password :</label>
                                        <div class="form-group d-flex align-items-center position-relative">
                                            <input type="password" class="form-control" id="newPassword" name="newPassword" autocomplete="false">
                                            <span class="position-absolute end-0 top-0 h-100 d-flex"><button type="button" class="btn btn-light showPassword" data-current="hide">Show</button></span>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="newPasswordConfirm" class="form-label required" id="">Confirm New Password :</label>
                                        <div class="form-group d-flex align-items-center position-relative">
                                            <input type="password" class="form-control" id="newPasswordConfirm" name="newPasswordConfirm" autocomplete="false">
                                            <span class="position-absolute end-0 top-0 h-100 d-flex"><button type="button" class="btn btn-light showPassword" data-current="hide">Show</button></span>
                                        </div>
                                    </div>
                                </form>`);

    $('#modalMdGeneralFooter').html(`<div class="container-fluid p-0">
                                        <div class="row justify-content-center w-100 mx-0">
                                            <div class="col-6 d-flex justify-content-center mb-2 px-0">
                                                <button type="button" class="btn btn-default w-100 me-2 changePasswordGroup" data-coreui-dismiss="modal" title="Close">
                                                    <i class="fas fa-xmark"></i> Close
                                                </button>
                                            </div>
                                            <div class="col-6 d-flex justify-content-center mb-2 px-0">
                                                <button type="button" class="btn btn-secondary w-100 changePasswordGroup" id="changePassword" data-type="" data-form="" data-token="" title="">
                                                    Change Password <i class="fa-solid fa-arrow-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>`);
    $('#modalMdGeneralDialog').removeClass('top-20').addClass('top-20');
    $('#modalMdGeneralDialog').removeClass('modal-dialog-scrollable');
    $('#modalMdGeneral').modal('show');
});

$(document).on('click', '#changePassword', function () {
    let $this = $(this);
    // let thisHtml = $this.html();
    Snackbar.close();
    clearValidation();
    const formData = new FormData($('#formChangePassword')[0]);
    formData.append('type', 'CHANGE');

    $(this).btnLoading(async function() {
        try {
            $('.changePasswordGroup').prop('disabled', true);
            $this.html('<i class="fas fa-spinner fa-spin"></i> Please wait');
            const response = await fetch('/updatePassword', {
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
                let countdown = 4;
                Snackbar.show({
                    pos: 'bottom-center',
                    duration: '6000',
                    text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result['message']} and automatically logout in <span id="snackbar-countdown">${countdown}</span> seconds.`
                });

                let countdownInterval = setInterval(() => {
                    countdown--;
                    document.getElementById('snackbar-countdown').textContent = countdown;
                    if (countdown <= 1) {
                        clearInterval(countdownInterval);
                        window.location.href = result['redirect'];
                    }
                }, 1000);
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
                Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed ${response.status} : ${result['message']}` });
            }
        }
        catch (error) {
            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}` });
        }
        finally {
            $('.changePasswordGroup').prop('disabled', false);
        }
    });
});

$(document).on('click', '.showPassword', function () {
    if($(this).attr('data-current') == 'hide') {
        $(this).closest('div').find('input').attr('type', 'text');
        $(this).html('Hide');
        $(this).attr('data-current', 'show');
    }
    else {
        $(this).closest('div').find('input').attr('type', 'password');
        $(this).html('Show');
        $(this).attr('data-current', 'hide');
    }
});

// var initialState = { page: window.location.href };
// var stateCounter = 0;
// history.replaceState(initialState, null, initialState.page);

// function pushState() {
//     stateCounter++;
//     var state = { page: window.location.href, counter: stateCounter };
//     history.pushState(state, null, state.page);
// }

// $(window).on('popstate', function (event) {
//     var state = event.originalEvent.state;
//     var $openAside = $('.form-aside.show');
//     var $openModal = $('.modal.show');
//     var $openFullscreen = $('.fullscreen-container.show');

//     if ($openFullscreen.length) {
//         $openFullscreen.removeClass('show').trigger('hidden');
//     }
//     else if ($openAside.length) {
//         $openAside.removeClass('show').trigger('aside:hidden');
//         $(".overlay-aside").removeClass("show");
//     }
//     else if ($openModal.length) {
//         $('.modal').modal('hide');
//     }

//     if (state && state.counter > 0) {
//         stateCounter--;
//         history.pushState(
//             { page: window.location.href, counter: stateCounter },
//             null,
//             window.location.href
//         );
//     }

//     event.preventDefault();
// });

$(document).on('shown.coreui.modal', '.modal', function (e) {
    if ($(this).find('.modal-dialog-scrollable').length > 0) {
        $(this).css('overflow-y', 'hidden');
    }

    // $.fn.modal.Constructor.prototype._enforceFocus = function() {};
    // $(document).off('focusin.modal');
    // pushState();
});

$(document).on('hidden.coreui.modal', '.modal', function (e) {
    $(this).css('overflow-y', '');
});

// $(document).on('focusin.cui.modal', function (e) {
//     // if ($(e.target).closest('.cui-modal').length === 0) {
//         e.stopImmediatePropagation();
//         console.log('focus');
//     // }


// });

// $(document).on('aside:shown', function () {
//     // $('.modal-backdrop').addClass('disable-pointer');
//     // pushState();
//     console.log('show aside');
// });

// $(document).on('shown', '.fullscreen-container', function () {
//     pushState();
// });

// $(document).on('click', '.nav-item', function(event) {
//     event.preventDefault();
//     event.stopImmediatePropagation();
// });

function asideHide () {
    $('.aside-sidebar').remove();
    $('.form-aside').removeClass('show aside-lg').trigger('aside:hidden');
    $('.form-aside').removeClass('show aside-xl').trigger('aside:hidden');
    $('.form-aside').removeClass('show aside-xxl').trigger('aside:hidden');
    $('.overlay-aside').removeClass('show');
    $('.aside-content').html('');
    $('body').removeClass('overflow-hidden');
    Snackbar.close();
}

function clearValidation(){
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
}

function handleValidationErrors(errors) {
    clearValidation();
    $.each(errors, function(field, messages) {
        // Handle array fields like itemOrder.0, itemOrder.1, etc.
        // const match = field.match(/^(.*?)\.(\d+)$/);
        // if (match) {
        //     const fieldName = match[1] + '[]';
        //     const index = match[2];
        //     const ele = $(`[name="${fieldName}"]`).eq(index);

        //     if (ele.length) {
        //         ele.addClass('is-invalid');
        //         ele.after(`<div class="invalid-feedback">${messages.join(', ')}</div>`);
        //     }
        // }
        // else {
        //     const ele = $(`[name="${fieldName}"]`);

        // }

        let ele;
        const match = field.match(/^(.*?)\.(\d+)$/);
        if (match) {
            const fieldName = match[1] + '[]';
            const index = match[2];

            if($(`[name="${fieldName}"]`).attr('type') === 'file' && selectedFiles.length > 0){
                ele = $(`.fileListItem`).eq(index);
            }
            else {
                ele = $(`[name="${fieldName}"]`).eq(index);
                if (ele.hasClass('vscomp-hidden-input')) {
                    ele = ele.closest('.vscomp-ele');
                }
            }
        }
        else {
            ele = $(`[name="${field}"]`);
        }

        if (ele.length) {
            let feedback;
            if (ele.hasClass('date-picker-input')) {
                ele.parent().addClass('is-invalid');
                ele.parent().parent().addClass('is-invalid');
                feedback = ele.parent().next('.invalid-feedback');
                if (feedback.length === 0) {
                    feedback = $(`<div class="invalid-feedback fw-normal">${messages.join('<br>')}</div>`);
                    ele.parent().parent().after(feedback);
                }
            }
            else if (ele.closest('.form-group').hasClass('d-flex')) {
                ele.addClass('is-invalid');
                ele.closest('.form-group').addClass('is-invalid');
                feedback =  ele.closest('.form-group').parent().find('.invalid-feedback');
                if (feedback.length === 0) {
                    feedback = $(`<div class="invalid-feedback fw-normal">${messages.join('<br>')}</div>`);
                    ele.closest('.form-group').parent().append(feedback);
                }
            }
            else if (ele.hasClass('select2')) {
                ele.addClass('is-invalid');
                // ele.next().addClass('is-invalid');
                ele.next().find('.select2-selection').addClass('is-invalid');
                feedback = ele.parent().find('.invalid-feedback');
                if (feedback.length === 0) {
                    feedback = $(`<div class="invalid-feedback fw-normal">${messages.join('<br>')}</div>`);
                    ele.parent().append(feedback);
                }
            }
            else {
                ele.addClass('is-invalid');
                if(ele.parent().hasClass('form-group')){
                    feedback = ele.parent('.form-group').find('.invalid-feedback');
                    if (feedback.length === 0) {
                        feedback = $(`<div class="invalid-feedback fw-normal">${messages.join('<br>')}</div>`);
                        ele.parent('.form-group').append(feedback);
                    }
                }
                else {
                    feedback = ele.parent().find('.invalid-feedback');
                    if (feedback.length === 0) {
                        feedback = $(`<div class="invalid-feedback fw-normal">${messages.join('<br>')}</div>`);
                        ele.parent().append(feedback);
                    }
                }
            }

            // feedback.html(messages.join('<br>'));
            // feedback.html('sadasd');
        }
    });
}

$(document).on('input change', '.is-invalid', function(event) {
    Snackbar.close();
    if ($(this).hasClass('select2')) {
        $(this).parent().find('.invalid-feedback').remove();
        $(this).removeClass('is-invalid');
        $(this).next().find('.select2-selection').removeClass('is-invalid');
    }
    else {
        $(this).removeClass('is-invalid');
        $(this).closest('.form-group').find('.invalid-feedback').remove();
        $(this).closest('.form-group').parent().find('.invalid-feedback').remove();
    }
});

$(document).on('dateChange.coreui.date-picker', '.date-picker', function(event) {
    Snackbar.close();
    // const selectedDate = event.detail.date;
    $(this).removeClass('is-invalid');
    $(this).find('div').removeClass('is-invalid');
    const feedback = $(this).next('.invalid-feedback');
    if (feedback.length > 0) {
        feedback.remove();
    }
    // console.log('Selected date:', selectedDate);
});

function toSentenceCase(str) {
    str = str.trim().toLowerCase();
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function toTitleCase(str) {
    let words = str.trim().toLowerCase().split(' ');
    for (let i = 0; i < words.length; i++) {
        words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1);
    }
    return words.join(' ');
}

$(document).on('keypress', '.singleLine', function(event) {
    // clear();
    var regex = new RegExp("^[a-zA-Z0-9\_\'\-\.\,\!\&\%\/\ ]+$");
    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
    if (!regex.test(key) || event.which == '13') {
        event.preventDefault();
        return false;
    }
});

$(document).on('input', '.singleLine', function (e) {
    const sanitizedValue = $(this).val().replace("\n", ' ');
    $(this).val(sanitizedValue);
});

$(document).on('input', '.toUpperCase', function(event) {
    $(this).val($(this).val().toUpperCase());
});

$(document).on('input', '.numberValue', function (e) {
    const sanitizedValue = $(this).val().replace(/[^0-9.]/g, '');

    // Memastikan hanya ada 1 titik
    const parts = sanitizedValue.split('.');
    if (parts.length > 2) {
        $(this).val(parts[0] + '.' + parts.slice(1).join(''));
    } else {
        $(this).val(sanitizedValue);
    }
});

$(document).on('input', '.currencyValue', function (e) {
	let $this = $(this);
	if($this.val().length == 1){
		$this.val($this.val().replace(/^~+/, ""));
	}
	else{
		$this.val($this.val().replace(/^0+/, ""));
	}

	$this.val(currencyFormat($this.val()));
	let thisLength = $this.val().length;
	if ($this.val().charAt((thisLength - 1)) == ')') {
		this.setSelectionRange(thisLength, (thisLength - 1));
		this.focus();
	}
	else {
		this.setSelectionRange(thisLength, thisLength);
		this.focus();
	}
});

function currencyToNumber(currencyString) {
    let cleanString = currencyString.replace(/[^0-9.-]+/g, "");
    cleanString = cleanString.replace(/,/g, "");
    return parseFloat(cleanString);
}

function formatCurrency(number, fraction = null) {
    return number.toLocaleString('en-US', {
        minimumFractionDigits: fraction ?? 2,
        maximumFractionDigits: fraction ?? 2
    });
}

function sumAndFormatCurrency(...amounts) {
    const sum = amounts.reduce((total, amount) => {
      const cleanAmount = amount.replace(/,/g, '');
      const numberAmount = cleanAmount.includes('.') ? cleanAmount : cleanAmount + '.00';
      return total + parseFloat(numberAmount);
    }, 0);

    return sum.toLocaleString('en-US', {
      style: 'currency',
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
}

function currencyFormat(value, fraction = null) {
	var first = value.charAt(0);
	var last = value.charAt(value.length - 1);
	$return = 0;
    $return = value.replace(/[^\d.]/g, "")
                .replace(/\.(?=.*\.)/g, "")
                .replace(/^(\d*\.\d{0,2}).*$/, "$1")
                .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	if (first == '-') {
		first = '(';
	}

	if (first == '(') {
		if ($return.length > 0 && $return == '0') {
			$return = '(';
		}
		else if ($return.length > 0 && $return != '0') {
			$return = '(' + $return + ')';
		}
		else {
			$return = '(' + $return;
		}
	}

	return $return;
}

$(document).on('click', '.modalBackBtn', function (event) {
    $('.modal').modal('hide');
    if($(this).attr('data-modal-id') != '') {
        $(`#${$(this).attr('data-modal-id')}`).modal('show');
    }
});

$(document).on('click', '.fullscreenHeaderCloseBtn', function (event) {
    event.stopImmediatePropagation();
    $('.fullscreen-container').removeClass('show').addClass('hide').trigger('hidden');
});


$(document).on('click', '.fullscreenHeaderHideAsideBtn', function (event) {
    event.stopImmediatePropagation();
    $('.fullscreen-container-aside').removeClass('show').addClass('hide').trigger('hidden');
    $('.view-file').removeClass('active');
});

$(document).on('click', '.fullscreenHeaderRotateCW', function (event) {
    let obj = document.getElementById('subfile_frame');
    let pageRotateCw = obj.contentDocument?.getElementById('pageRotateCw');
    if (pageRotateCw) {
        pageRotateCw.click();
    }
});

$(document).on('click', '.fullscreenHeaderRotateCCW', function (event) {
    let obj = document.getElementById('subfile_frame');
    let pageRotateCcw = obj.contentDocument?.getElementById('pageRotateCcw');
    if (pageRotateCcw) {
        pageRotateCcw.click();
    }
});

$(document).on('click', '.fullscreenHeaderPrint', function (event) {
    let obj = document.getElementById('subfile_frame');
    let printButton = obj.contentDocument?.getElementById('printButton');
    if (printButton) {
        printButton.click();
    }
});

$(document).on('click', '.fullscreenHeaderDownload', function (event) {
    let obj = document.getElementById('subfile_frame');
    let printButton = obj.contentDocument?.getElementById('downloadButton');
    if (printButton) {
        printButton.click();
    }
});

$(document).on('click', '.toggleAside', function (event) {
    $('body').addClass('overflow-hidden');
    $('.overlay-aside').addClass('show');
    $('.aside-title').html($(this).attr('data-asidetitle'));
    $('.form-aside').addClass('show').trigger('aside:shown');
    $('.form-aside').scrollTop(0);
});

$(document).on('click', '.overlay-aside', function (event) {
    event.stopImmediatePropagation();
    let reopenAside = false;
    let params;

    const asideShow = $('aside.show');
    if(asideShow.find('div.aside-sidebar').length == 1) {
        let asideSidebar = asideShow.find('div.aside-sidebar');
        reopenAside = asideSidebar.find('#reopenAside').val();
        if(reopenAside == 'VENDOR_REFERENCE') {
            params = {
                'dataVendor': asideSidebar.find('#dataVendor').val(),
                'reloadTable': asideSidebar.find('#reloadTable').val(),
                'sortBy': asideSidebar.find('#sortBy').val(),
            };
        }
    }

    asideHide();
    if(reopenAside == 'VENDOR_REFERENCE') {
        setTimeout(() => {
            $(`.masterVendor[data-type="REFERENCE"][data-vendor="${params['dataVendor']}"]`).attr('data-sortby', params['sortBy']);
            $(`.masterVendor[data-type="REFERENCE"][data-vendor="${params['dataVendor']}"]`).trigger('click');
        }, '200');
    }
});

$(document).on('click', '.closeBtnAside, .asideHeaderCloseBtn', function (event) {
    event.stopImmediatePropagation();
    let reopenAside = false;
    let params;
    if($(this).closest('aside.form-aside').find('div.aside-sidebar').length == 1) {
        let asideSidebar = $(this).closest('aside.form-aside').find('div.aside-sidebar');
        reopenAside = asideSidebar.find('#reopenAside').val();
        if(reopenAside == 'VENDOR_REFERENCE') {
            params = {
                'dataVendor': asideSidebar.find('#dataVendor').val(),
                'reloadTable': asideSidebar.find('#reloadTable').val(),
                'sortBy': asideSidebar.find('#sortBy').val(),
            };
        }
    }

    if($('.fullscreen-container-aside').hasClass('show')) {
        $('.fullscreen-container-aside').removeClass('show').addClass('hide').trigger('hidden');
        setTimeout(() => {
            asideHide();
        }, '350');
    }
    else {
        asideHide();
    }

    if(reopenAside == 'VENDOR_REFERENCE') {
        setTimeout(() => {
            $(`.masterVendor[data-type="REFERENCE"][data-vendor="${params['dataVendor']}"]`).attr('data-sortby', params['sortBy']);
            $(`.masterVendor[data-type="REFERENCE"][data-vendor="${params['dataVendor']}"]`).trigger('click');
        }, '200');
    }
});

$(document).on('click', '.checkboxInputAll', function (event) {
    event.stopImmediatePropagation();
    let $this = $(this);
    if ($this.prop('checked') === true) {
        $this.closest('.form-group').find('input.checkboxInput').prop('checked', true).trigger('change');
    }
    else {
        $this.closest('.form-group').find('input.checkboxInput').prop('checked', false).trigger('change');
    }
});

$(document).on('click', '.checkboxInput', function (event) {
    if (event.which === 3) {
        return;
    }

    event.stopImmediatePropagation();
    if (event.detail > 1) {
        event.preventDefault();
    }
    else {
        let $this = $(this);
        if ($this.prop('checked') === true) {
            let totalCheckboxes = $this.closest('.form-group').find('input.checkboxInput').length;
            let checkedCheckboxes = $this.closest('.form-group').find('input.checkboxInput:checked').length;
            if (totalCheckboxes === checkedCheckboxes) {
                $this.closest('.form-group').find('input.checkboxInputAll').prop('checked', true);
            }
        }
        else {
            $this.closest('.form-group').find('input.checkboxInputAll').prop('checked', false);
        }
    }
});

$(document).on('click', '.rowTableCheckboxAll', function (event) {
    event.stopImmediatePropagation();
    let $this = $(this);
    if ($this.prop('checked') === true) {
        $this.closest('thead').next('tbody').find('input.rowTableCheckbox').prop('checked', true).trigger('change');
    }
    else {
        $this.closest('thead').next('tbody').find('input.rowTableCheckbox').prop('checked', false).trigger('change');
    }
});

$(document).on('click', '.rowTableCustom', function (event) {
    event.preventDefault();
});

$(document).on('change', '.rowTableCheckbox', function (event) {
    if($(this).prop('checked') === true) {
        $(this).closest('tr.rowTableCustom').removeClass('active').addClass('active');
    }
    else {
        $(this).closest('tr.rowTableCustom').removeClass('active');
    }
});

$(document).on('mousedown', '.rowTableCustom', function (event) {
    if (event.which === 3) {
        return;
    }

    event.stopImmediatePropagation();
    if (event.detail > 1) {
        event.preventDefault();
    }
    else {
        let $this = $(this);
        let $tbody = $this.closest('tbody');
        if ($this.find('input.rowTableCheckbox').prop('checked') === true) {
            $this.find('input.rowTableCheckbox').prop('checked', false).trigger('change');
            $tbody.prev('thead').find('input.rowTableCheckboxAll').prop('checked', false);
        }
        else {
            $this.find('input.rowTableCheckbox').prop('checked', true).trigger('change');
            let totalCheckboxes = $tbody.find('.rowTableCheckbox').length;
            let checkedCheckboxes = $tbody.find('.rowTableCheckbox:checked').length;

            if (totalCheckboxes === checkedCheckboxes) {
                $tbody.prev('thead').find('input.rowTableCheckboxAll').prop('checked', true);
            }
        }
    }
});

// Helper untuk FormData
function FormDataFromObject(obj) {
    const formData = new FormData();
    Object.entries(obj).forEach(([key, value]) => {
        formData.append(key, value);
    });
    return formData;
}

function nl2br (str, replaceMode, isXhtml) {
    var breakTag = (isXhtml) ? '<br />' : '<br>';
    var replaceStr = (replaceMode) ? '$1'+ breakTag : '$1'+ breakTag +'$2';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, replaceStr);
}

function getFileIcon(mimeType, fileName) {
    let iconClass = "";
    const checkExtension = (extensions) =>
        extensions.some((ext) => fileName.toLowerCase().endsWith(ext));
    if (mimeType.includes("pdf")) {
        iconClass = "far fa-file-pdf";
    } else if (mimeType.includes("image")) {
        iconClass = "far fa-file-image";
    } else if (
        mimeType.includes("excel") ||
        mimeType.includes("spreadsheetml")
    ) {
        iconClass = "far fa-file-excel";
    } else if (mimeType.includes("wordprocessingml")) {
        iconClass = "far fa-file-word";
    } else if (
        mimeType.includes("ms-outlook") ||
        mimeType.includes("ms-tnef") ||
        mimeType === "message/rfc822" ||
        mimeType.includes("vnd.ms-outlook") ||
        checkExtension([".msg", ".eml"])
    ) {
        iconClass = "far fa-envelope";
    } else {
        iconClass = "far fa-file";
    }
    return iconClass;
}

function formatResultRemote(data) {
    if (data.loading) return data.text;
    if (!data.description1 && !data.description2) return data.text;
    return $(`<div class="select2-result">
                <div class="select2-result__title">${data.text}</div>
                <div class="select2-result__description">
                    <div class="description-line">${data.description1 || ''}</div>
                    <div class="description-line">${data.description2 || ''}</div>
                </div>
            </div>`);
}

function formatResult(data) {
    if (!data.id) return data.text;
    let description1 = $(data.element).data('description1') || '';
    let description2 = $(data.element).data('description2') || '';
    let nextFlow = $(data.element).data('nextFlow') || '';

    return $(`
        <div class="select2-result-item">
            <div class="select2-result-item__title fw-medium">${data.text}</div>
            <div class="select2-result__description">
                <div class="description-line">${description1}</div>
                <div class="description-line">${description2}</div>
            </div>
        </div>
    `);
}

function formatSelection(data) {
    return data.text || data.id;
}

function escapeInput(input) {
    if (typeof input === 'string' && (input.includes('csrf_token') || input.includes('_token'))) {
        return input;
    }

    const keywords = {
        'HEAD': '_HEA_D_',
        'Head': '_Hea_d_',
        'head': '_hea_d_',
        'FOR': '_FO_R_',
        'For': '_Fo_r_',
        'for': '_fo_r_',
        'REPLACE': '_REPLAC_E_',
        'Replace': '_Replac_e_',
        'replace': '_replac_e_',
        'UPDATE': '_UPDAT_E_',
        'Update': '_Updat_e_',
        'update': '_updat_e_',
        'ORDER': '_ORDE_R_',
        'Order': '_Orde_r_',
        'order': '_orde_r_',
        'GROUP': '_GROU_P_',
        'Group': '_Grou_p_',
        'group': '_grou_p_',
        'DOCUMENT': '_DOCUMEN_T_',
        'Document': '_Documen_t_',
        'document': '_documen_t_',
        'REQUIRE': '_REQUIR_E_',
        'Require': '_Requir_e_',
        'require': '_requir_e_',
        'IMPORT': '_IMPOR_T_',
        'Import': '_Impor_t_',
        'import': '_impor_t_',
        'USER': '_USE_R_',
        'User': '_Use_r_',
        'user': '_use_r_',
        'ADMIN': '_ADMI_N_',
        'Admin': '_Admi_n_',
        'admin': '_admi_n_',
        'SYSTEM': '_SYSTE_M_',
        'System': '_Syste_m_',
        'system': '_syste_m_',
        'DROP': '_DRO_P_',
        'Drop': '_Dro_p_',
        'drop': '_dro_p_',
    };

    let result = input;

    for (const [word, replacement] of Object.entries(keywords)) {
        const regex = new RegExp('\\b' + escapeRegExp(word) + '\\b', 'g');
        result = result.replace(regex, replacement);
    }

    result = encodeURIComponent(result);

    const specialChars = {
        "'": '_SINGLE_QUOTE_',
        '"': '_DOUBLE_QUOTE_',
        '(': '_PAREN_OPEN_',
        ')': '_PAREN_CLOSE_',
    };

    for (const [char, encoded] of Object.entries(specialChars)) {
        const encodedChar = encodeURIComponent(char);
        result = result.replace(new RegExp(escapeRegExp(encodedChar), 'g'), encoded);
    }

    return result;
}

function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function escapeFilename(filename) {
    if (!filename || typeof filename !== 'string') {
        return filename;
    }

    const keywords = {
        'HEAD': '_HEA_D_',
        'Head': '_Hea_d_',
        'head': '_hea_d_',
        'FOR': '_FO_R_',
        'For': '_Fo_r_',
        'for': '_fo_r_',
        'REPLACE': '_REPLAC_E_',
        'Replace': '_Replac_e_',
        'replace': '_replac_e_',
        'UPDATE': '_UPDAT_E_',
        'Update': '_Updat_e_',
        'update': '_updat_e_',
        'ORDER': '_ORDE_R_',
        'Order': '_Orde_r_',
        'order': '_orde_r_',
        'GROUP': '_GROU_P_',
        'Group': '_Grou_p_',
        'group': '_grou_p_',
        'DOCUMENT': '_DOCUMEN_T_',
        'Document': '_Documen_t_',
        'document': '_documen_t_',
        'REQUIRE': '_REQUIR_E_',
        'Require': '_Requir_e_',
        'require': '_requir_e_',
        'IMPORT': '_IMPOR_T_',
        'Import': '_Impor_t_',
        'import': '_impor_t_',
        'USER': '_USE_R_',
        'User': '_Use_r_',
        'user': '_use_r_',
        'ADMIN': '_ADMI_N_',
        'Admin': '_Admi_n_',
        'admin': '_admi_n_',
        'SYSTEM': '_SYSTE_M_',
        'System': '_Syste_m_',
        'system': '_syste_m_',
        'DROP': '_DRO_P_',
        'Drop': '_Dro_p_',
        'drop': '_dro_p_',
    };

    let result = filename;

    for (const [word, replacement] of Object.entries(keywords)) {
        const regex = new RegExp('\\b' + escapeRegExp(word) + '\\b', 'g');
        result = result.replace(regex, replacement);
    }

    // result = encodeURIComponent(result);

    const specialChars = {
        "'": '_SINGLE_QUOTE_',
        '"': '_DOUBLE_QUOTE_',
        '(': '_PAREN_OPEN_',
        ')': '_PAREN_CLOSE_',
    };

    for (const [char, encoded] of Object.entries(specialChars)) {
        const encodedChar = encodeURIComponent(char);
        result = result.replace(new RegExp(escapeRegExp(encodedChar), 'g'), encoded);
    }

    return result;
}

// // Render image files
// function renderImage(fileContent) {
//     const container = document.getElementById('file-container');
//     const img = document.createElement('img');
//     img.src = `data:image/png;base64,${fileContent}`;
//     img.style.maxWidth = '100%';
//     container.appendChild(img);
// }