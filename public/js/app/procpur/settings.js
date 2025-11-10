var containerContentScrollbarInstance;
var preselectValue = [];

window.addEventListener('load', function() {
    containerContentScrollbarInstance = new ScrollbarCustom('#body-container', {bottom: 0, left:null, overflowX: 'scroll', overflowY: 'none'});
    containerContentScrollbarInstance.forceUpdate();
    const container = document.querySelector('#body-container');
    $('#body-container').scrollLeft(0);
});

var orderFormApplicantTable = (function() {
    let $container = $('#orderFormApplicantContainer');
    let containerHeight = $container.height();
    let headerHeight = 190;
    let footerFixedHeight = 60;
    let scrollHeight = containerHeight - headerHeight;
    // let tableContainerHeight = $container.find('.table-container').height();
    var tableId = 'orderFormApplicantTable';
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
            url: 'orderFormApplicantTable',
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
            {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false, className: 'text-center', width: '5%'},
            {data: 'employeeId', type: 'string', searchable: false, orderable: false, width: '20%'},
            {data: 'employeeName', type: 'string', searchable: false, orderable: false, width: '35%'},
            {data: 'departmentName', type: 'string', searchable: false, orderable: false, width: '30%'},
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
})();

var poApprovalRuleTable = (function() {
    let $container = $('#poApprovalRuleContainer');
    let containerHeight = $container.height();
    let headerHeight = 190;
    let scrollHeight = containerHeight - headerHeight;
    var tableId = 'poApprovalRuleTable';
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
            url: 'poApprovalRuleTable',
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
            {data: 'priorityOrder', type: 'string', searchable: false, orderable: false, className: 'text-center draggable', width: '10%'},
            {data: 'employeeName', type: 'string', searchable: false, orderable: false, width: '28%'},
            {data: 'ruleGroupName', type: 'string', searchable: false, orderable: false, width: '28%'},
            {data: 'id', type: 'string', className: 'text-center', earchable: false, orderable: false, width: '14%'},
            {data: 'status', type: 'string', searchable: false, orderable: false, className: 'text-center', width: '10%'},
            {data: 'action', name: 'action', searchable: false, orderable: false, className: 'text-center', width: '10%'}
        ],
        rowReorder: {
            update: false,
        }

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
    table.on('row-reorder', async function (e, diff, edit) {
        if (diff.length === 0) return false;
        $(table.table().node()).css('opacity', '0.6').css('pointer-events', 'none');
        Snackbar.show({
            pos: 'bottom-center',
            duration: '6000',
            text: '<i class="fa-regular fa-circle-notch fa-spin fs-7"></i> Processing update...'
        });

        let scrollBody = $(table.table().node()).parent();
        let currentScrollTop = scrollBody.scrollTop();

        try {
            const employeeIdSrc = edit.triggerRow.data().employeeId;
            for (let x = 0; x < diff.length; x++) {
                if (x < diff.length - 1 &&
                    table.row(diff[x].node).data().employeeId !==
                    table.row(diff[x + 1].node).data().employeeId) {
                    throw new Error('Failed: Reorder only in Applicant group');
                }
            }

            // Ambil semua data tabel saat ini
            const allRowsData = [];
            table.rows().every(function() {
                allRowsData.push(this.data());
            });

            const positionToPriority = {};
            for (let i = 0; i < allRowsData.length; i++) {
                positionToPriority[i] = allRowsData[i].priority;
            }

            const order = [];
            for (let x = 0; x < diff.length; x++) {
                const rowData = table.row(diff[x].node).data();
                if (rowData.employeeId !== employeeIdSrc) continue;

                const newPosition = diff[x].newPosition;
                const newPriority = positionToPriority[newPosition];
                order.push({
                    id: rowData.id,
                    employeeId: rowData.employeeId,
                    oldPriority: rowData.priority,
                    newPriority: newPriority
                });
            }

            const response = await fetch('/proc_pur/savePoApprovalRule', {
                method: 'POST',
                body: new FormDataFromObject({
                    order: JSON.stringify(order),
                    updateType: 'PRIORITY_ORDER',
                    filterPoRuleCompany: $('#filterPoRuleCompany').val(),
                    _method: 'PUT',
                }),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Referer': window.location.href,
                }
            });

            const result = await response.json();
            if (response.status === 200) {
                table.draw(false);

                Snackbar.show({
                    pos: 'bottom-center',
                    duration: '3000',
                    text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result['message']}`
                });
            }
            else {
                throw new Error(result['message'] || `Failed ${response.status}: ${response.statusText}`);
            }
        }
        catch (error) {
            Snackbar.show({
                pos: 'bottom-center',
                duration: '5000',
                text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> ${error.message}`
            });

            table.ajax.reload(() => {
                scrollBody.scrollTop(currentScrollTop);
            }, false);

            return false;
        }
        finally {
            $(table.table().node()).css('opacity', '1').css('pointer-events', 'auto');
        }
    });

    return table;
})();

var inspectorTable = (function() {
    let $container = $('#inspectorTableContainer');
    let containerHeight = $container.height();
    let headerHeight = 190;
    let scrollHeight = containerHeight - headerHeight;
    var tableId = 'inspectorTable';
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
            url: 'inspectorTable',
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
            {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false, className: 'text-center', width: '5%'},
            {data: 'employeeId', type: 'string', searchable: false, orderable: false, width: '20%'},
            {data: 'employeeName', type: 'string', searchable: false, orderable: false, width: '35%'},
            {data: 'departmentName', type: 'string', searchable: false, orderable: false, width: '30%'},
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
})();

const vendorReferenceTable = {
    table: null,
    init: function() {
        let $container = $('#asideDetailForm');
        let containerHeight = $container.height();
        let headerHeight = 190;
        let footerFixedHeight = 60;
        let scrollHeight = containerHeight - headerHeight;
        var tableId = 'vendorReferenceTable';

        $('#vendorDetailsContainer').css('height', (containerHeight - 150) + 'px');
        this.table = $(`#${tableId}`).DataTable({
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
                url: 'purchasingVendorReference',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (d) {
                    let $table = $(`#${tableId}`);
                    let $form = $table.closest('.table-container').find('.formSearchTable');
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
                {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false, className: 'text-center', width: '5%'},
                {data: 'vendor_account', type: 'string', searchable: false, orderable: false, width: '20%'},
                {data: 'vendor_name', type: 'string', searchable: false, orderable: false, width: '30%'},
                {data: 'group', type: 'string', searchable: false, orderable: false, width: '10%'},
                {data: 'currency', type: 'string', searchable: false, orderable: false, width: '10%'},
                {data: 'component_names', type: 'string', searchable: false, orderable: false, width: '20%'},
                {data: 'action', name: 'action', searchable: false, orderable: false, className: 'text-center', width: '5%'},

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

        return this.table;
    },

    reload: function() {
        if (this.table) {
            const scrollBody = $(this.table.table().node()).parent();
            const currentScrollTop = scrollBody.scrollTop();

            this.table.ajax.reload(() => {
                scrollBody.scrollTop(currentScrollTop);
            }, false);
        }
    }
};

$(document).on('click', '#vendorTab', function() {
    if ($.fn.DataTable.isDataTable('#vendorReferenceTable')) {
        vendorReferenceTable.reload();
    }
    else {
        vendorReferenceTable.init();
    }
});

$(document).on('change', '#filterOrderFormCompany', function (event) {
    orderFormApplicantTable.ajax.reload();
});

$(document).on('change', '#filterPoRuleCompany', function (event) {
    poApprovalRuleTable.ajax.reload();
});

$(document).on('change', '#filterInspectorCompany', function (event) {
    inspectorTable.ajax.reload();
});

$(document).on('click', '.actionBtnHeader', async function (event) {
    let $this = $(this);
    let dataType = $this.attr('data-type');
    if(dataType == 'ADD_ORDER_FORM_APPLICANT' || dataType == 'EDIT_ORDER_FORM_APPLICANT') {
        asideHide();
        let title = '', tokenForm = '', button = '';
        if($this.attr('data-type') == 'ADD_ORDER_FORM_APPLICANT') {
            title = 'Add Order Form Applicant';
            button = `<button type="button" class="btn btn-info w-100 w-md-auto asideButton" data-type="${dataType}" data-token="" title="Save New Applicant">Save</button>`;
        }
        else {
            title = 'Edit Order Form Applicant';
            tokenForm = $this.attr('data-token');
            button = `<button type="button" class="btn btn-secondary w-100 w-md-auto asideButton" data-type="${dataType}" data-token="${$this.attr('data-token')}" title="Edit Applicant">Update</button>`;
        }

        $('.aside-title').html(title);
        $('.aside-content').html(`<form role="form" class="form-horizontal mb-3" enctype="multipart/form-data" id="asideForm">
                                    <input type="hidden" name="tokenForm" value="${tokenForm}">
                                    <div class="form-group mb-3">
                                        <label for="employeeApplicantOrder" class="form-label">Order Form Applicant<span class="required"></span> :</label>
                                        <div class="skeleton w-100" id="employeeApplicantOrder"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="employeeDepartmentOrder" class="form-label">Department Order Form<span class="required"></span> :</label>
                                        <div class="skeleton w-100" id="employeeDepartmentOrder"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="firstCheckerOrder" class="form-label">First Checker Option List<span class="required"></span> :</label>
                                        <div class="skeleton w-100" id="firstCheckerOrder"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="secondCheckerOrder" class="form-label">Second Checker Option List (optional) :</label>
                                        <div class="skeleton w-100" id="secondCheckerOrder"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="ccOrder" class="form-label">Cc Option List (optional) :</label>
                                        <div class="skeleton w-100" id="ccOrder"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="approverOrder" class="form-label">Approver Option List<span class="required"></span> :</label>
                                        <div class="skeleton w-100" id="approverOrder"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="poApplicant" class="form-label">PO Applicant (Order Form Receiver)<span class="required"></span> :</label>
                                        <div class="skeleton w-100" id="poApplicant"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="inspectionReceiver" class="form-label">Inspection Receiver<span class="required"></span> :</label>
                                        <div class="skeleton w-100" id="inspectionReceiver"></div>
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
                                        ${button}
                                    </div>`);

        var preselectedApplicant = [], preselectedDepartment = [];
        var preselectedChecker1 = [], preselectedChecker2 = [], preselectedApprover = [], preselectedCc = [], preselectedPoApplicant = [];
        var preselectedInspectionReceiver = [];
        const params = {
            'dataForm': 'ORDER_FORM',
            'company': $('#filterOrderFormCompany').val(),
        };

        if($this.attr('data-type') == 'EDIT_ORDER_FORM_APPLICANT') {
            try {
                const response = await fetch(`/doc_approval/getEmployee?dataForm=ORDER_FORM&dataType=APPLICANT_MATRIX&dataMatrix=APPROVAL_FLOW&tokenForm=${$this.attr('data-token')}`, {
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
                    if (result.data.length > 0) {
                        result.data.forEach((item, index) => {
                            if(item['flow'] == 'APPLICANT') {
                                preselectedApplicant.push({
                                    id: item['employeeId'],
                                    text: item['employeeName'],
                                    description1: item['positionName'],
                                    description2: (item['departmentNameApplicant']) ? `Dept. : ${item['departmentNameApplicant']}` : '--',
                                });

                                preselectedDepartment.push({
                                    id: item['departmentId'],
                                    text: item['departmentName'],
                                    description1: `Dept. ID : ${(item['departmentId']) ? item['departmentId'] : '--'}`,
                                    description2: `Cost center : ${(item['costCenter']) ? item['costCenter'] : '--'}`,
                                });
                            }
                            else if(item['flow'] == 'CHECKER_1') {
                                preselectedChecker1.push({
                                    id: item['employeeId'],
                                    text: item['employeeName']
                                });
                            }
                            else if(item['flow'] == 'CHECKER_2') {
                                preselectedChecker2.push({
                                    id: item['employeeId'],
                                    text: item['employeeName']
                                });
                            }
                            else if(item['flow'] == 'CC') {
                                preselectedCc.push({
                                    id: item['employeeId'],
                                    text: item['employeeName']
                                });
                            }
                            else if(item['flow'] == 'APPROVER') {
                                preselectedApprover.push({
                                    id: item['employeeId'],
                                    text: item['employeeName']
                                });
                            }
                            else if(item['flow'] == 'RECEIVER') {
                                preselectedPoApplicant.push({
                                    id: item['employeeId'],
                                    text: item['employeeName']
                                });
                            }
                            else if(item['flow'] == 'INSPECTION_RECEIVER') {
                                preselectedInspectionReceiver.push({
                                    id: item['employeeId'],
                                    text: item['employeeName']
                                });
                            }
                        });
                    }
                }
                else if (result.status === 404) {
                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed 404 : Not found` });
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
                            window.location.href = result.redirect_uri;
                        }
                    }, 1000);
                }
                else {
                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed to get data` });
                }
            }
            catch (error) {
                Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}` });
            }
        }

        $('#employeeApplicantOrder').replaceWith(`<select class="select2" name="employeeApplicantOrder[]" id="employeeApplicantOrder" data-type=""></select>`);
        if(preselectedApplicant.length > 0) {
            $('#employeeApplicantOrder').select2({
                dropdownParent: $('#globalAside'),
                minimumResultsForSearch: Infinity,
                allowClear: false,
                placeholder: '-- Select --',
                data: preselectedApplicant,
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
            $('#employeeApplicantOrder').select2({
                dropdownParent: $('#globalAside'),
                placeholder: '-- Select --',
                multiple: true,
                closeOnSelect: false,
                allowClear: false,
                minimumResultsForSearch: 0,
                dropdownAdapter: dropdownAdapter,
                language: {
                    searching: function () {
                        return window.select2SearchingMessage;
                    },
                    noResults: function () {
                        if (window.select2SearchingMessage === 'Type minimum 3 characters') {
                            return 'Type minimum 3 characters';
                        }
                        return 'No results found';
                    }
                },
                ajax: {
                    transport: function (params, success, failure) {
                        const term = params.data.term || '';
                        const page = params.data.page || 1;

                        if (term.length > 0 && term.length < 3) {
                            window.select2SearchingMessage = 'Type minimum 3 characters';
                            setTimeout(() => {
                                $('.select2-results__option.loading-results').text(window.select2SearchingMessage);
                            }, 0);

                            return null;
                        }

                        window.select2SearchingMessage = 'Loading results...';
                        const request = $.ajax({
                            url: '/doc_approval/getEmployee',
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                term: term,
                                page: page,
                                dataForm: 'ORDER_FORM',
                                dataType: 'APPLICANT',
                                company: $('#filterOrderFormCompany').val()
                            },
                            success: success,
                            error: failure
                        });

                        return request;
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.data || [],
                            pagination: {
                                more: data.more || false
                            }
                        };
                    },
                    delay: 250,
                    cache: true
                },
                templateResult: formatResultRemote,
                templateSelection: function (data) {
                    return data.text || data.id;
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            }).on('select2:select', function (e) {
                setTimeout(function () {
                    const input = $('.select2-container--open .select2-search__field');
                    if (input.length) {
                        input.focus();
                    }
                }, 0);
            });
        }

        if(preselectedDepartment.length > 0) {
            $('#employeeDepartmentOrder').replaceWith(`<select class="select2" name="employeeDepartmentOrder" id="employeeDepartmentOrder" data-type=""></select>`);
            $('#employeeDepartmentOrder').select2({
                dropdownParent: $('#globalAside'),
                minimumResultsForSearch: Infinity,
                allowClear: false,
                placeholder: '-- Select --',
                data: preselectedDepartment,
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
            let optionDepartment = await getDepartment({'companyId': $('#filterOrderFormCompany').val(),'departmentId': ''});
            $('#employeeDepartmentOrder').replaceWith(`<select class="select2" name="employeeDepartmentOrder" id="employeeDepartmentOrder" data-type=""><option></option></select>`);
            $('#employeeDepartmentOrder').select2({
                dropdownParent: $('#globalAside'),
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
            });
        }

        $('#firstCheckerOrder').replaceWith(`<select class="select2 w-100" name="firstCheckerOrder[]" id="firstCheckerOrder" data-type=""></select>`);
        $('#firstCheckerOrder').select2({
            dropdownParent: $('#globalAside'),
            placeholder: '-- Select --',
            multiple: true,
            closeOnSelect: false,
            allowClear: false,
            minimumResultsForSearch: 0,
            dropdownAdapter: dropdownAdapter,
            language: {
                searching: function () {
                    return window.select2SearchingMessage;
                },
                noResults: function () {
                    if (window.select2SearchingMessage === 'Type minimum 3 characters') {
                        return 'Type minimum 3 characters';
                    }
                    return 'No results found';
                }
            },
            ajax: {
                transport: function (params, success, failure) {
                    const term = params.data.term || '';
                    const page = params.data.page || 1;

                    if (term.length > 0 && term.length < 3) {
                        window.select2SearchingMessage = 'Type minimum 3 characters';
                        setTimeout(() => {
                            $('.select2-results__option.loading-results').text(window.select2SearchingMessage);
                        }, 0);

                        return null;
                    }

                    window.select2SearchingMessage = 'Loading results...';
                    const request = $.ajax({
                        url: '/doc_approval/getEmployee',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: term,
                            page: page,
                            dataForm: 'ORDER_FORM',
                            dataType: 'REVIEWER_1',
                            company: $('#filterOrderFormCompany').val()
                        },
                        success: success,
                        error: failure
                    });

                    return request;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data || [],
                        pagination: {
                            more: data.more || false
                        }
                    };
                },
                delay: 250,
                cache: true
            },
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).on('select2:select', function (e) {
            setTimeout(function () {
                const input = $('.select2-container--open .select2-search__field');
                if (input.length) {
                    input.focus();
                }
            }, 0);
        });
        if(preselectedChecker1.length > 0) {
            preselectedChecker1.forEach(item => {
                const optionExists = $(`#firstCheckerOrder option[value='${item.id}']`).length > 0;
                if (!optionExists) {
                    const option = new Option(item.text, item.id, true, true);
                    $('#firstCheckerOrder').append(option);
                }
            });

            $('#firstCheckerOrder').trigger('change');
        }

        $('#secondCheckerOrder').replaceWith(`<select class="select2 w-100" name="secondCheckerOrder[]" id="secondCheckerOrder" data-type=""></select>`);
        $('#secondCheckerOrder').select2({
            dropdownParent: $('#globalAside'),
            placeholder: '-- Select --',
            multiple: true,
            closeOnSelect: false,
            allowClear: false,
            minimumResultsForSearch: 0,
            dropdownAdapter: dropdownAdapter,
            language: {
                searching: function () {
                    return window.select2SearchingMessage;
                },
                noResults: function () {
                    if (window.select2SearchingMessage === 'Type minimum 3 characters') {
                        return 'Type minimum 3 characters';
                    }
                    return 'No results found';
                }
            },
            ajax: {
                transport: function (params, success, failure) {
                    const term = params.data.term || '';
                    const page = params.data.page || 1;

                    if (term.length > 0 && term.length < 3) {
                        window.select2SearchingMessage = 'Type minimum 3 characters';
                        setTimeout(() => {
                            $('.select2-results__option.loading-results').text(window.select2SearchingMessage);
                        }, 0);

                        return null;
                    }

                    window.select2SearchingMessage = 'Loading results...';
                    const request = $.ajax({
                        url: '/doc_approval/getEmployee',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: term,
                            page: page,
                            dataForm: 'ORDER_FORM',
                            dataType: 'REVIEWER_2',
                            company: $('#filterOrderFormCompany').val()
                        },
                        success: success,
                        error: failure
                    });

                    return request;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data || [],
                        pagination: {
                            more: data.more || false
                        }
                    };
                },
                delay: 250,
                cache: true
            },
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).on('select2:select', function (e) {
            setTimeout(function () {
                const input = $('.select2-container--open .select2-search__field');
                if (input.length) {
                    input.focus();
                }
            }, 0);
        });
        if(preselectedChecker2.length > 0) {
            preselectedChecker2.forEach(item => {
                const optionExists = $(`#secondCheckerOrder option[value='${item.id}']`).length > 0;
                if (!optionExists) {
                    const option = new Option(item.text, item.id, true, true);
                    $('#secondCheckerOrder').append(option);
                }
            });

            $('#secondCheckerOrder').trigger('change');
        }

        $('#approverOrder').replaceWith(`<select class="select2 w-100" name="approverOrder[]" id="approverOrder" data-type=""></select>`);
        $('#approverOrder').select2({
            dropdownParent: $('#globalAside'),
            placeholder: '-- Select --',
            multiple: true,
            closeOnSelect: false,
            allowClear: false,
            minimumResultsForSearch: 0,
            dropdownAdapter: dropdownAdapter,
            language: {
                searching: function () {
                    return window.select2SearchingMessage;
                },
                noResults: function () {
                    if (window.select2SearchingMessage === 'Type minimum 3 characters') {
                        return 'Type minimum 3 characters';
                    }
                    return 'No results found';
                }
            },
            ajax: {
                transport: function (params, success, failure) {
                    const term = params.data.term || '';
                    const page = params.data.page || 1;

                    if (term.length > 0 && term.length < 3) {
                        window.select2SearchingMessage = 'Type minimum 3 characters';
                        setTimeout(() => {
                            $('.select2-results__option.loading-results').text(window.select2SearchingMessage);
                        }, 0);

                        return null;
                    }

                    window.select2SearchingMessage = 'Loading results...';
                    const request = $.ajax({
                        url: '/doc_approval/getEmployee',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: term,
                            page: page,
                            dataForm: 'ORDER_FORM',
                            dataType: 'APPROVER',
                            company: $('#filterOrderFormCompany').val()
                        },
                        success: success,
                        error: failure
                    });

                    return request;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data || [],
                        pagination: {
                            more: data.more || false
                        }
                    };
                },
                delay: 250,
                cache: true
            },
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).on('select2:select', function (e) {
            setTimeout(function () {
                const input = $('.select2-container--open .select2-search__field');
                if (input.length) {
                    input.focus();
                }
            }, 0);
        });
        if(preselectedApprover.length > 0) {
            preselectedApprover.forEach(item => {
                const optionExists = $(`#approverOrder option[value='${item.id}']`).length > 0;
                if (!optionExists) {
                    const option = new Option(item.text, item.id, true, true);
                    $('#approverOrder').append(option);
                }
            });

            $('#approverOrder').trigger('change');
        }

        $('#ccOrder').replaceWith(`<select class="select2 w-100" name="ccOrder[]" id="ccOrder" data-type=""></select>`);
        $('#ccOrder').select2({
            dropdownParent: $('#globalAside'),
            placeholder: '-- Select --',
            multiple: true,
            closeOnSelect: false,
            allowClear: false,
            minimumResultsForSearch: 0,
            dropdownAdapter: dropdownAdapter,
            language: {
                searching: function () {
                    return window.select2SearchingMessage;
                },
                noResults: function () {
                    if (window.select2SearchingMessage === 'Type minimum 3 characters') {
                        return 'Type minimum 3 characters';
                    }
                    return 'No results found';
                }
            },
            ajax: {
                transport: function (params, success, failure) {
                    const term = params.data.term || '';
                    const page = params.data.page || 1;

                    if (term.length > 0 && term.length < 3) {
                        window.select2SearchingMessage = 'Type minimum 3 characters';
                        setTimeout(() => {
                            $('.select2-results__option.loading-results').text(window.select2SearchingMessage);
                        }, 0);

                        return null;
                    }

                    window.select2SearchingMessage = 'Loading results...';
                    const request = $.ajax({
                        url: '/doc_approval/getEmployee',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: term,
                            page: page,
                            dataForm: 'ORDER_FORM',
                            dataType: 'CC',
                            company: $('#filterOrderFormCompany').val()
                        },
                        success: success,
                        error: failure
                    });

                    return request;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data || [],
                        pagination: {
                            more: data.more || false
                        }
                    };
                },
                delay: 250,
                cache: true
            },
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).on('select2:select', function (e) {
            setTimeout(function () {
                const input = $('.select2-container--open .select2-search__field');
                if (input.length) {
                    input.focus();
                }
            }, 0);
        });
        if(preselectedCc.length > 0) {
            preselectedCc.forEach(item => {
                const optionExists = $(`#ccOrder option[value='${item.id}']`).length > 0;
                if (!optionExists) {
                    const option = new Option(item.text, item.id, true, true);
                    $('#ccOrder').append(option);
                }
            });

            $('#ccOrder').trigger('change');
        }

        $('#poApplicant').replaceWith(`<select class="select2 w-100" name="poApplicant[]" id="poApplicant" data-type=""></select>`);
        $('#poApplicant').select2({
            dropdownParent: $('#globalAside'),
            placeholder: '-- Select --',
            multiple: true,
            closeOnSelect: false,
            allowClear: false,
            minimumResultsForSearch: 0,
            dropdownAdapter: dropdownAdapter,
            language: {
                searching: function () {
                    return window.select2SearchingMessage;
                },
                noResults: function () {
                    if (window.select2SearchingMessage === 'Type minimum 3 characters') {
                        return 'Type minimum 3 characters';
                    }
                    return 'No results found';
                }
            },
            ajax: {
                transport: function (params, success, failure) {
                    const term = params.data.term || '';
                    const page = params.data.page || 1;

                    if (term.length > 0 && term.length < 3) {
                        window.select2SearchingMessage = 'Type minimum 3 characters';
                        setTimeout(() => {
                            $('.select2-results__option.loading-results').text(window.select2SearchingMessage);
                        }, 0);

                        return null;
                    }

                    window.select2SearchingMessage = 'Loading results...';
                    const request = $.ajax({
                        url: '/doc_approval/getEmployee',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: term,
                            page: page,
                            dataForm: 'ORDER_FORM',
                            dataType: 'PO_APPLICANT',
                            company: $('#filterOrderFormCompany').val()
                        },
                        success: success,
                        error: failure
                    });

                    return request;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data || [],
                        pagination: {
                            more: data.more || false
                        }
                    };
                },
                delay: 250,
                cache: true
            },
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).on('select2:select', function (e) {
            setTimeout(function () {
                const input = $('.select2-container--open .select2-search__field');
                if (input.length) {
                    input.focus();
                }
            }, 0);
        });
        if(preselectedPoApplicant.length > 0) {
            preselectedPoApplicant.forEach(item => {
                const optionExists = $(`#poApplicant option[value='${item.id}']`).length > 0;
                if (!optionExists) {
                    const option = new Option(item.text, item.id, true, true);
                    $('#poApplicant').append(option);
                }
            });

            $('#poApplicant').trigger('change');
        }

        $('#inspectionReceiver').replaceWith(`<select class="select2 w-100" name="inspectionReceiver[]" id="inspectionReceiver" data-type=""></select>`);
        $('#inspectionReceiver').select2({
            dropdownParent: $('#globalAside'),
            placeholder: '-- Select --',
            multiple: true,
            closeOnSelect: false,
            allowClear: false,
            minimumResultsForSearch: 0,
            dropdownAdapter: dropdownAdapter,
            language: {
                searching: function () {
                    return window.select2SearchingMessage;
                },
                noResults: function () {
                    if (window.select2SearchingMessage === 'Type minimum 3 characters') {
                        return 'Type minimum 3 characters';
                    }
                    return 'No results found';
                }
            },
            ajax: {
                transport: function (params, success, failure) {
                    const term = params.data.term || '';
                    const page = params.data.page || 1;

                    if (term.length > 0 && term.length < 3) {
                        window.select2SearchingMessage = 'Type minimum 3 characters';
                        setTimeout(() => {
                            $('.select2-results__option.loading-results').text(window.select2SearchingMessage);
                        }, 0);

                        return null;
                    }

                    window.select2SearchingMessage = 'Loading results...';
                    const request = $.ajax({
                        url: '/doc_approval/getEmployee',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: term,
                            page: page,
                            dataForm: 'INSPECTION_FORM',
                            dataType: 'RECEIVER',
                            company: $('#filterOrderFormCompany').val()
                        },
                        success: success,
                        error: failure
                    });

                    return request;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data || [],
                        pagination: {
                            more: data.more || false
                        }
                    };
                },
                delay: 250,
                cache: true
            },
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });
        if(preselectedInspectionReceiver.length > 0) {
            preselectedInspectionReceiver.forEach(item => {
                const optionExists = $(`#inspectionReceiver option[value='${item.id}']`).length > 0;
                if (!optionExists) {
                    const option = new Option(item.text, item.id, true, true);
                    $('#inspectionReceiver').append(option);
                }
            });

            $('#inspectionReceiver').trigger('change');
        }
    }
    else if(dataType == 'ADD_PO_APPROVAL_RULE' || dataType == 'EDIT_PO_APPROVAL_RULE') {
        asideHide();
        let title = '', tokenForm = '', button = '';
        if($this.attr('data-type') == 'ADD_PO_APPROVAL_RULE') {
            title = `Add PO Approval Rule`;
            button = `<button type="button" class="btn btn-info w-100 w-md-auto asideButton" data-type="${dataType}" data-token="" title="Save PO Rule">Save</button>`;
        }
        else {
            title = `Edit PO Approval Rule`;
            tokenForm = $this.attr('data-token');
            button = `<button type="button" class="btn btn-secondary w-100 w-md-auto asideButton" data-type="${dataType}" data-token="${$this.attr('data-token')}" title="Update PO Approval Rule">Update</button>`;
        }

        $('.aside-title').html(title);
        $('.aside-content').html(`<form role="form" class="form-horizontal mb-3" enctype="multipart/form-data" id="asideForm">
                                    <input type="hidden" name="tokenForm" value="${tokenForm}">
                                    <div class="form-group col-12 col-md-11 mb-3">
                                        <label for="poApprovalRuleName" class="form-label">Rule Name<span class="required"></span> :</label>
                                        <div class="skeleton w-100" id="poApprovalRuleName"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="employeeDepartmentOrder" class="form-label">Rule Condition<span class="required"></span> :</label>
                                        <div class="col-12">
                                            <div id="fieldRuleContainer">
                                                <div class="fieldRuleRow d-flex">
                                                    <input type="hidden" class="ruleCondition" id="conditionId0" name="conditionId[]" value="">
                                                    <input type="hidden" class="groupColor" id="groupColor0" name="groupColor[]" value="">
                                                    <div class="col-11 p-1 d-flex ruleConditionContainer">
                                                        <div class="col-4 pe-1">
                                                            <div class="skeleton w-100 fieldRule mb-0"></div>
                                                        </div>
                                                        <div class="col-3 pe-1">
                                                            <div class="skeleton w-100 operatorRule mb-0"></div>
                                                        </div>
                                                        <div class="col-5 pe-0 valueRuleContainer">
                                                            <div class="skeleton w-100 valueRule mb-0"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-1 d-flex gap-1">
                                                        <div class="dropdown">
                                                            <button type="button" class="btn btn-md btn-transparent my-1" data-bs-boundary="viewport"data-coreui-toggle="dropdown" aria-expanded="false" title="">
                                                                <i class="fa-solid fa-chevron-down fa-lg"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="">
                                                                <li class="dropdown-header py-1 px-2 m-0 mb-2 fs-8 fw-medium border-bottom"><i class="fa-solid fa-palette"></i> 'Or' group rule</li>

                                                                <li><span class="dropdown-item groupRuleOption" data-bg=""><i class="fa-solid fa-square text-white fa-fw"></i> Clear group 'Or'</li>

                                                                <li><span class="dropdown-item groupRuleOption" data-bg="bg-danger"><i class="fa-solid fa-square text-danger fa-fw"></i> Red group 'Or'</li>
                                                                <li><span class="dropdown-item groupRuleOption" data-bg="bg-success"><i class="fa-solid fa-square text-success fa-fw"></i> Green group 'Or'</li>
                                                                <li><span class="dropdown-item groupRuleOption" data-bg="bg-info"><i class="fa-solid fa-square text-info fa-fw"></i> Blue group 'Or'</li>
                                                                <li><span class="dropdown-item groupRuleOption" data-bg="bg-warning"><i class="fa-solid fa-square text-warning fa-fw"></i> Yellow group 'Or'</li>
                                                            </ul>
                                                        </div>
                                                        <button type="button" class="btn btn-md btn-transparent my-1" id="addRuleCondition" data-type="" data-form="" data-token=""><i class="fa-solid fa-plus fa-lg"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card col-12 col-md-11 card-floating-title mt-4 mb-4">
                                        <span class="card-title fs-8">Rule Action Option List<span class="required"></span> :</span>
                                        <div class="card-body">
                                            <div class="d-flex mb-3">
                                                <div class="col-12 d-flex" id="">
                                                    <div class="col-4 pe-1">
                                                        <select class="select2 actionType" name="actionType[]" data-type="">
                                                            <option value="1">1 - APPLICANT</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-8">
                                                        <div class="skeleton w-100" id="selectPoApplicant"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex mb-3">
                                                <div class="col-12 d-flex" id="">
                                                    <div class="col-4 pe-1">
                                                        <select class="select2 actionType" name="actionType[]" data-type="">
                                                            <option value="3">2 - REVIEWER</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-8 pe-0">
                                                        <div class="skeleton w-100" id="selectPoReviewer"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex mb-3">
                                                <div class="col-12 d-flex" id="">
                                                    <div class="col-4 pe-1">
                                                        <select class="select2 actionType" name="actionType[]" data-type="">
                                                            <option value="5">3 - CONFIRMER 1</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-8 pe-0">
                                                        <div class="skeleton w-100" id="selectPoConfirmer1"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex mb-3">
                                                <div class="col-12 d-flex" id="">
                                                    <div class="col-4 pe-1">
                                                        <select class="select2 actionType" name="actionType[]" data-type="">
                                                            <option value="11">4 - REVIEW ADMIN</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-8 pe-0">
                                                        <div class="skeleton w-100" id="selectPoReviewAdmin"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex mb-3">
                                                <div class="col-12 d-flex" id="">
                                                    <div class="col-4 pe-1">
                                                        <select class="select2 actionType" name="actionType[]" data-type="">
                                                            <option value="6">5 - CONFIRMER 2</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-8 pe-0">
                                                        <div class="skeleton w-100" id="selectPoConfirmer2"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex mb-3">
                                                <div class="col-12 d-flex" id="">
                                                    <div class="col-4 pe-1">
                                                        <select class="select2 actionType" name="actionType[]" data-type="">
                                                            <option value="7">6 - ACKNOWLEDGER</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-8 pe-0">
                                                        <div class="skeleton w-100" id="selectPoAcknowledger"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="col-12 d-flex" id="">
                                                    <div class="col-4 pe-1">
                                                        <select class="select2 actionType" name="actionType[]" data-type="">
                                                            <option value="8">7 - APPROVER</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-8 pe-0">
                                                        <div class="skeleton w-100" id="selectPoApprover"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>`);

        $('.overlay-aside').addClass('show').trigger('shown');
        $('#globalAside').addClass('show aside-xl').trigger('shown');
        $('body').addClass('overflow-hidden');
        $('#asideDetailForm').scrollTop(0);
        $('.asideFooterBtn').html(`<div class="col-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
                                    </div>
                                    <div class="col-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        ${button}
                                    </div>`);
        $('.select2').select2({
            dropdownParent: $('#globalAside'),
            minimumResultsForSearch: Infinity,
            allowClear: false,
            placeholder: '-- Select --'
        });

        const optionLogicalOperator = [
                                {id: 'AND', text: 'AND'},
                                {id: 'OR', text: 'OR'},
                        ];

        let preRuleName = '';
        let preselectCondition = [], preselectColor = [], preselectField = [], preselectOperator = [], preselectedApplicant = [], preselectedPoReviewer = [], preselectedPoConfirmer1 = [], preselectedPoReviewAdmin = [], preselectedPoConfirmer2 = [], preselectedAcknowledger = [], preselectedApprover = [];
        preselectValue = [];

        if($this.attr('data-type') == 'EDIT_PO_APPROVAL_RULE') {
            try {
                const response = await fetch(`/doc_approval/getEmployee?dataForm=APPLICATION_PO_FORM&dataType=APPLICANT_MATRIX&tokenForm=${$this.attr('data-token')}`, {
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
                    if (result.data.rule.length > 0) {
                        preRuleName = result.data.rule[0].ruleGroupName;

                        result.data.rule.forEach((item, index) => {
                            preselectCondition.push({id: item['conditionId']});
                            preselectColor.push({id: item['groupColor']});
                            preselectField.push({
                                id: item['fieldId'],
                                text: item['fieldName'],
                                nextFlow: item['nextFlow']
                            });
                            preselectOperator.push({
                                id: item['operator'],
                                text: item['operator']
                            });
                            preselectValue.push({
                                id: item['valueId'],
                                text: item['value']
                            });
                        });
                    }

                    if (result.data.action.length > 0) {
                        result.data.action.forEach((item, index) => {
                            if(item['flow'] == 'APPLICANT') {
                                preselectedApplicant.push({
                                    id: item['employeeId'],
                                    text: item['employeeName']
                                });
                            }
                            else if(item['flow'] == 'REVIEWER') {
                                preselectedPoReviewer.push({
                                    id: item['employeeId'],
                                    text: item['employeeName']
                                });
                            }
                            else if(item['flow'] == 'CONFIRMER_1') {
                                preselectedPoConfirmer1.push({
                                    id: item['employeeId'],
                                    text: item['employeeName']
                                });
                            }
                            else if(item['flow'] == 'REVIEW_ADMIN') {
                                preselectedPoReviewAdmin.push({
                                    id: item['employeeId'],
                                    text: item['employeeName']
                                });
                            }
                            else if(item['flow'] == 'CONFIRMER_2') {
                                preselectedPoConfirmer2.push({
                                    id: item['employeeId'],
                                    text: item['employeeName']
                                });
                            }
                            else if(item['flow'] == 'ACKNOWLEDGER') {
                                preselectedAcknowledger.push({
                                    id: item['employeeId'],
                                    text: item['employeeName']
                                });
                            }
                            else if(item['flow'] == 'APPROVER') {
                                preselectedApprover.push({
                                    id: item['employeeId'],
                                    text: item['employeeName']
                                });
                            }
                        });
                    }
                }
                else if (result.status === 404) {
                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed 404 : Not found` });
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
                            window.location.href = result.redirect_uri;
                        }
                    }, 1000);
                }
                else {
                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed to get data` });
                }
            }
            catch (error) {
                Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}` });
            }
        }

        $('#poApprovalRuleName').replaceWith(`<input type="text" class="form-control" id="poApprovalRuleName" name="poApprovalRuleName" value="${preRuleName}" spellcheck="false" autocomplete="off" placeholder=""></input>`);
        // $('#selectPoApplicant').select2({
        //     dropdownParent: $('#globalAside'),
        //     placeholder: 'Required',
        //     multiple: false,
        //     minimumInputLength: 3,
        //     minimumResultsForSearch: 5,
        //     ajax: {
        //         url: `/doc_approval/getEmployee`,
        //         dataType: 'json',
        //         delay: 500,
        //         data: function (params) {
        //             return {
        //                 term: params.term,
        //                 page: params.page || 1,
        //                 dataForm: 'APPLICATION_PO_FORM',
        //                 dataType: 'ALL_EMPLOYEE_COMPANY',
        //                 company: $('#filterPoRuleCompany').val()
        //             };
        //         },
        //         processResults: function (data, params) {
        //             params.page = params.page || 1;
        //             return {
        //                 results: data.items.map(row => ({
        //                     id: row.value,
        //                     text: row.label
        //                 })),
        //                 pagination: {
        //                     more: data.more
        //                 }
        //             };
        //         },
        //         cache: true
        //     }
        // });

        $('.fieldRule').replaceWith(`<select class="select2 w-100 fieldRule" name="fieldRule[]" id="fieldRule0" data-row="0" data-valueid="" data-value=""></select>`);
        $('.operatorRule').replaceWith(`<select class="select2 w-100 operatorRule" name="operatorRule[]" id="operatorRule0" data-type=""><option></option></select>`);
        $('.valueRule').replaceWith(`<input type="text" class="form-control valueRule toUpperCase" name="valueRule[]" id="valueRule0" spellcheck="false" autocomplete="off">`);

        for(let x = 1; x < preselectCondition.length; x++) {
            $('#fieldRuleContainer').append(`<div class="fieldRuleRow d-flex">
                                                    <input type="hidden" class="ruleCondition" id="conditionId${x}" name="conditionId[]" value="">
                                                    <input type="hidden" class="groupColor" id="groupColor${x}" name="groupColor[]" value="">
                                                    <div class="col-11 p-1 d-flex ruleConditionContainer">
                                                        <div class="col-4 pe-1">
                                                            <select class="select2 w-100 fieldRule" name="fieldRule[]" id="fieldRule${x}" data-row="${x}" data-valueid="" data-value=""></select>
                                                        </div>
                                                        <div class="col-3 pe-1">
                                                            <select class="select2 w-100 operatorRule" name="operatorRule[]" id="operatorRule${x}" data-type=""><option></option></select>
                                                        </div>
                                                        <div class="col-5 pe-0 valueRuleContainer">
                                                            <div class="skeleton w-100 valueRule mb-0"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-1 d-flex gap-1">
                                                        <div class="dropdown">
                                                            <button type="button" class="btn btn-md btn-transparent my-1" data-bs-boundary="viewport"data-coreui-toggle="dropdown" aria-expanded="false" title="">
                                                                <i class="fa-solid fa-chevron-down fa-lg"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="">
                                                                <li class="dropdown-header py-1 px-2 m-0 mb-2 fs-8 fw-medium border-bottom"><i class="fa-solid fa-palette"></i> 'Or' group rule</li>

                                                                <li><span class="dropdown-item groupRuleOption" data-bg=""><i class="fa-solid fa-square text-white fa-fw"></i> Clear group 'Or'</li>

                                                                <li><span class="dropdown-item groupRuleOption" data-bg="bg-danger"><i class="fa-solid fa-square text-danger fa-fw"></i> Red group 'Or'</li>
                                                                <li><span class="dropdown-item groupRuleOption" data-bg="bg-success"><i class="fa-solid fa-square text-success fa-fw"></i> Green group 'Or'</li>
                                                                <li><span class="dropdown-item groupRuleOption" data-bg="bg-info"><i class="fa-solid fa-square text-info fa-fw"></i> Blue group 'Or'</li>
                                                                <li><span class="dropdown-item groupRuleOption" data-bg="bg-warning"><i class="fa-solid fa-square text-warning fa-fw"></i> Yellow group 'Or'</li>
                                                            </ul>
                                                        </div>
                                                        <button type="button" class="btn btn-md btn-transparent my-1" id="removeRuleCondition" data-type="" data-form="" data-token=""><i class="fa-solid fa-minus fa-lg"></i></button>
                                                    </div>
                                                </div>`);
        }

        if(preselectCondition.length > 0) {
            preselectCondition.forEach((item, index) => {
                $(`#conditionId${index}`).val(item['id']);
            });

            preselectColor.forEach((item, index) => {
                $(`#groupColor${index}`).closest('div.fieldRuleRow').find(`.dropdown-item[data-bg="${item['id']}"]`).click();
            });
        }

        initSelect2FieldRule('.fieldRule', preselectField);
        initSelect2OperatorRule('.operatorRule', preselectOperator);

        $('#selectPoApplicant').replaceWith(`<select class="select2 w-100" name="selectPoApplicant[]" id="selectPoApplicant" data-type=""></select>`);
        if(preselectedApplicant.length > 0) {
            $('#selectPoApplicant').select2({
                dropdownParent: $('#globalAside'),
                minimumResultsForSearch: Infinity,
                allowClear: false,
                placeholder: '-- Select --',
                data: [{
                    id: preselectedApplicant[0].id,
                    text: preselectedApplicant[0].text,
                }],
            });
        }
        else {
            $('#selectPoApplicant').select2({
                dropdownParent: $('#globalAside'),
                placeholder: '-- Select --',
                multiple: true,
                closeOnSelect: false,
                allowClear: false,
                minimumResultsForSearch: 0,
                dropdownAdapter: dropdownAdapter,
                language: {
                    searching: function () {
                        return window.select2SearchingMessage;
                    },
                    noResults: function () {
                        if (window.select2SearchingMessage === 'Type minimum 3 characters') {
                            return 'Type minimum 3 characters';
                        }
                        return 'No results found';
                    }
                },
                ajax: {
                    transport: function (params, success, failure) {
                        const term = params.data.term || '';
                        const page = params.data.page || 1;

                        if (term.length > 0 && term.length < 3) {
                            window.select2SearchingMessage = 'Type minimum 3 characters';
                            setTimeout(() => {
                                $('.select2-results__option.loading-results').text(window.select2SearchingMessage);
                            }, 0);

                            return null;
                        }

                        window.select2SearchingMessage = 'Loading results...';
                        const request = $.ajax({
                            url: '/doc_approval/getEmployee',
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                term: term,
                                page: page,
                                dataForm: 'APPLICATION_PO_FORM',
                                dataType: 'APPLICANT',
                                company: $('#filterPoRuleCompany').val()
                            },
                            success: success,
                            error: failure
                        });

                        return request;
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.data || [],
                            pagination: {
                                more: data.more || false
                            }
                        };
                    },
                    delay: 250,
                    cache: true
                },
                templateResult: formatResultRemote,
                templateSelection: function (data) {
                    return data.text || data.id;
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            }).on('select2:select', function (e) {
                setTimeout(function () {
                    const input = $('.select2-container--open .select2-search__field');
                    if (input.length) {
                        input.focus();
                    }
                }, 0);
            });
            // if(preselectedApplicant.length > 0) {
            //     preselectedApplicant.forEach(item => {
            //         const optionExists = $(`#selectPoApplicant option[value='${item.id}']`).length > 0;
            //         if (!optionExists) {
            //             const option = new Option(item.text, item.id, true, true);
            //             $('#selectPoApplicant').append(option);
            //         }
            //     });
            //     $('#selectPoApplicant').trigger('change');
            // }
        }

        $('#selectPoReviewer').replaceWith(`<select class="select2 w-100" name="selectPoReviewer[]" id="selectPoReviewer" data-type=""></select>`);
        $('#selectPoReviewer').select2({
            dropdownParent: $('#globalAside'),
            placeholder: '-- Select --',
            multiple: true,
            closeOnSelect: false,
            allowClear: false,
            minimumResultsForSearch: 0,
            dropdownAdapter: dropdownAdapter,
            language: {
                searching: function () {
                    return window.select2SearchingMessage;
                },
                noResults: function () {
                    if (window.select2SearchingMessage === 'Type minimum 3 characters') {
                        return 'Type minimum 3 characters';
                    }
                    return 'No results found';
                }
            },
            ajax: {
                transport: function (params, success, failure) {
                    const term = params.data.term || '';
                    const page = params.data.page || 1;

                    if (term.length > 0 && term.length < 3) {
                        window.select2SearchingMessage = 'Type minimum 3 characters';
                        setTimeout(() => {
                            $('.select2-results__option.loading-results').text(window.select2SearchingMessage);
                        }, 0);

                        return null;
                    }

                    window.select2SearchingMessage = 'Loading results...';
                    const request = $.ajax({
                        url: '/doc_approval/getEmployee',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: term,
                            page: page,
                            dataForm: 'APPLICATION_PO_FORM',
                            dataType: 'REVIEWER',
                            company: $('#filterPoRuleCompany').val()
                        },
                        success: success,
                        error: failure
                    });

                    return request;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data || [],
                        pagination: {
                            more: data.more || false
                        }
                    };
                },
                delay: 250,
                cache: true
            },
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).on('select2:select', function (e) {
            setTimeout(function () {
                const input = $('.select2-container--open .select2-search__field');
                if (input.length) {
                    input.focus();
                }
            }, 0);
        });
        if(preselectedPoReviewer.length > 0) {
            preselectedPoReviewer.forEach(item => {
                const optionExists = $(`#selectPoReviewer option[value='${item.id}']`).length > 0;
                if (!optionExists) {
                    const option = new Option(item.text, item.id, true, true);
                    $('#selectPoReviewer').append(option);
                }
            });

            $('#selectPoReviewer').trigger('change');
        }

        $('#selectPoConfirmer1').replaceWith(`<select class="select2 w-100" name="selectPoConfirmer1[]" id="selectPoConfirmer1" data-type=""></select>`);
        $('#selectPoConfirmer1').select2({
            dropdownParent: $('#globalAside'),
            placeholder: '-- Select --',
            multiple: true,
            closeOnSelect: false,
            allowClear: false,
            minimumResultsForSearch: 0,
            dropdownAdapter: dropdownAdapter,
            language: {
                searching: function () {
                    return window.select2SearchingMessage;
                },
                noResults: function () {
                    if (window.select2SearchingMessage === 'Type minimum 3 characters') {
                        return 'Type minimum 3 characters';
                    }
                    return 'No results found';
                }
            },
            ajax: {
                transport: function (params, success, failure) {
                    const term = params.data.term || '';
                    const page = params.data.page || 1;

                    if (term.length > 0 && term.length < 3) {
                        window.select2SearchingMessage = 'Type minimum 3 characters';
                        setTimeout(() => {
                            $('.select2-results__option.loading-results').text(window.select2SearchingMessage);
                        }, 0);

                        return null;
                    }

                    window.select2SearchingMessage = 'Loading results...';
                    const request = $.ajax({
                        url: '/doc_approval/getEmployee',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: term,
                            page: page,
                            dataForm: 'APPLICATION_PO_FORM',
                            dataType: 'CONFIRMER_1',
                            company: $('#filterPoRuleCompany').val()
                        },
                        success: success,
                        error: failure
                    });

                    return request;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data || [],
                        pagination: {
                            more: data.more || false
                        }
                    };
                },
                delay: 250,
                cache: true
            },
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).on('select2:select', function (e) {
            setTimeout(function () {
                const input = $('.select2-container--open .select2-search__field');
                if (input.length) {
                    input.focus();
                }
            }, 0);
        });
        if(preselectedPoConfirmer1.length > 0) {
            preselectedPoConfirmer1.forEach(item => {
                const optionExists = $(`#selectPoConfirmer1 option[value='${item.id}']`).length > 0;
                if (!optionExists) {
                    const option = new Option(item.text, item.id, true, true);
                    $('#selectPoConfirmer1').append(option);
                }
            });

            $('#selectPoConfirmer1').trigger('change');
        }

        $('#selectPoReviewAdmin').replaceWith(`<select class="select2 w-100" name="selectPoReviewAdmin[]" id="selectPoReviewAdmin" data-type=""></select>`);
        $('#selectPoReviewAdmin').select2({
            dropdownParent: $('#globalAside'),
            placeholder: '-- Select --',
            multiple: true,
            closeOnSelect: false,
            allowClear: false,
            minimumResultsForSearch: 0,
            dropdownAdapter: dropdownAdapter,
            language: {
                searching: function () {
                    return window.select2SearchingMessage;
                },
                noResults: function () {
                    if (window.select2SearchingMessage === 'Type minimum 3 characters') {
                        return 'Type minimum 3 characters';
                    }
                    return 'No results found';
                }
            },
            ajax: {
                transport: function (params, success, failure) {
                    const term = params.data.term || '';
                    const page = params.data.page || 1;

                    if (term.length > 0 && term.length < 3) {
                        window.select2SearchingMessage = 'Type minimum 3 characters';
                        setTimeout(() => {
                            $('.select2-results__option.loading-results').text(window.select2SearchingMessage);
                        }, 0);

                        return null;
                    }

                    window.select2SearchingMessage = 'Loading results...';
                    const request = $.ajax({
                        url: '/doc_approval/getEmployee',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: term,
                            page: page,
                            dataForm: 'APPLICATION_PO_FORM',
                            dataType: 'REVIEW_ADMIN',
                            company: $('#filterPoRuleCompany').val()
                        },
                        success: success,
                        error: failure
                    });

                    return request;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data || [],
                        pagination: {
                            more: data.more || false
                        }
                    };
                },
                delay: 250,
                cache: true
            },
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).on('select2:select', function (e) {
            setTimeout(function () {
                const input = $('.select2-container--open .select2-search__field');
                if (input.length) {
                    input.focus();
                }
            }, 0);
        });
        if(preselectedPoReviewAdmin.length > 0) {
            preselectedPoReviewAdmin.forEach(item => {
                const optionExists = $(`#selectPoReviewAdmin option[value='${item.id}']`).length > 0;
                if (!optionExists) {
                    const option = new Option(item.text, item.id, true, true);
                    $('#selectPoReviewAdmin').append(option);
                }
            });

            $('#selectPoReviewAdmin').trigger('change');
        }

        $('#selectPoConfirmer2').replaceWith(`<select class="select2 w-100" name="selectPoConfirmer2[]" id="selectPoConfirmer2" data-type=""></select>`);
        $('#selectPoConfirmer2').select2({
            dropdownParent: $('#globalAside'),
            placeholder: '-- Select --',
            multiple: true,
            closeOnSelect: false,
            allowClear: false,
            minimumResultsForSearch: 0,
            dropdownAdapter: dropdownAdapter,
            language: {
                searching: function () {
                    return window.select2SearchingMessage;
                },
                noResults: function () {
                    if (window.select2SearchingMessage === 'Type minimum 3 characters') {
                        return 'Type minimum 3 characters';
                    }
                    return 'No results found';
                }
            },
            ajax: {
                transport: function (params, success, failure) {
                    const term = params.data.term || '';
                    const page = params.data.page || 1;

                    if (term.length > 0 && term.length < 3) {
                        window.select2SearchingMessage = 'Type minimum 3 characters';
                        setTimeout(() => {
                            $('.select2-results__option.loading-results').text(window.select2SearchingMessage);
                        }, 0);

                        return null;
                    }

                    window.select2SearchingMessage = 'Loading results...';
                    const request = $.ajax({
                        url: '/doc_approval/getEmployee',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: term,
                            page: page,
                            dataForm: 'APPLICATION_PO_FORM',
                            dataType: 'CONFIRMER_2',
                            company: $('#filterPoRuleCompany').val()
                        },
                        success: success,
                        error: failure
                    });

                    return request;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data || [],
                        pagination: {
                            more: data.more || false
                        }
                    };
                },
                delay: 250,
                cache: true
            },
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).on('select2:select', function (e) {
            setTimeout(function () {
                const input = $('.select2-container--open .select2-search__field');
                if (input.length) {
                    input.focus();
                }
            }, 0);
        });
        if(preselectedPoConfirmer2.length > 0) {
            preselectedPoConfirmer2.forEach(item => {
                const optionExists = $(`#selectPoConfirmer2 option[value='${item.id}']`).length > 0;
                if (!optionExists) {
                    const option = new Option(item.text, item.id, true, true);
                    $('#selectPoConfirmer2').append(option);
                }
            });

            $('#selectPoConfirmer2').trigger('change');
        }

        $('#selectPoAcknowledger').replaceWith(`<select class="select2 w-100" name="selectPoAcknowledger[]" id="selectPoAcknowledger" data-type=""></select>`);
        $('#selectPoAcknowledger').select2({
            dropdownParent: $('#globalAside'),
            placeholder: '-- Select --',
            multiple: true,
            closeOnSelect: false,
            allowClear: false,
            minimumResultsForSearch: 0,
            dropdownAdapter: dropdownAdapter,
            language: {
                searching: function () {
                    return window.select2SearchingMessage;
                },
                noResults: function () {
                    if (window.select2SearchingMessage === 'Type minimum 3 characters') {
                        return 'Type minimum 3 characters';
                    }
                    return 'No results found';
                }
            },
            ajax: {
                transport: function (params, success, failure) {
                    const term = params.data.term || '';
                    const page = params.data.page || 1;

                    if (term.length > 0 && term.length < 3) {
                        window.select2SearchingMessage = 'Type minimum 3 characters';
                        setTimeout(() => {
                            $('.select2-results__option.loading-results').text(window.select2SearchingMessage);
                        }, 0);

                        return null;
                    }

                    window.select2SearchingMessage = 'Loading results...';
                    const request = $.ajax({
                        url: '/doc_approval/getEmployee',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: term,
                            page: page,
                            dataForm: 'APPLICATION_PO_FORM',
                            dataType: 'ACKNOWLEDGER',
                            company: $('#filterPoRuleCompany').val()
                        },
                        success: success,
                        error: failure
                    });

                    return request;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data || [],
                        pagination: {
                            more: data.more || false
                        }
                    };
                },
                delay: 250,
                cache: true
            },
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).on('select2:select', function (e) {
            setTimeout(function () {
                const input = $('.select2-container--open .select2-search__field');
                if (input.length) {
                    input.focus();
                }
            }, 0);
        });
        if(preselectedAcknowledger.length > 0) {
            preselectedAcknowledger.forEach(item => {
                const optionExists = $(`#selectPoAcknowledger option[value='${item.id}']`).length > 0;
                if (!optionExists) {
                    const option = new Option(item.text, item.id, true, true);
                    $('#selectPoAcknowledger').append(option);
                }
            });

            $('#selectPoAcknowledger').trigger('change');
        }

        $('#selectPoApprover').replaceWith(`<select class="select2 w-100" name="selectPoApprover[]" id="selectPoApprover" data-type=""></select>`);
        $('#selectPoApprover').select2({
            dropdownParent: $('#globalAside'),
            placeholder: '-- Select --',
            multiple: true,
            closeOnSelect: false,
            allowClear: false,
            minimumResultsForSearch: 0,
            dropdownAdapter: dropdownAdapter,
            language: {
                searching: function () {
                    return window.select2SearchingMessage;
                },
                noResults: function () {
                    if (window.select2SearchingMessage === 'Type minimum 3 characters') {
                        return 'Type minimum 3 characters';
                    }
                    return 'No results found';
                }
            },
            ajax: {
                transport: function (params, success, failure) {
                    const term = params.data.term || '';
                    const page = params.data.page || 1;

                    if (term.length > 0 && term.length < 3) {
                        window.select2SearchingMessage = 'Type minimum 3 characters';
                        setTimeout(() => {
                            $('.select2-results__option.loading-results').text(window.select2SearchingMessage);
                        }, 0);

                        return null;
                    }

                    window.select2SearchingMessage = 'Loading results...';
                    const request = $.ajax({
                        url: '/doc_approval/getEmployee',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: term,
                            page: page,
                            dataForm: 'APPLICATION_PO_FORM',
                            dataType: 'APPROVER',
                            company: $('#filterPoRuleCompany').val()
                        },
                        success: success,
                        error: failure
                    });

                    return request;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data || [],
                        pagination: {
                            more: data.more || false
                        }
                    };
                },
                delay: 250,
                cache: true
            },
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).on('select2:select', function (e) {
            setTimeout(function () {
                const input = $('.select2-container--open .select2-search__field');
                if (input.length) {
                    input.focus();
                }
            }, 0);
        });
        if(preselectedApprover.length > 0) {
            preselectedApprover.forEach(item => {
                const optionExists = $(`#selectPoApprover option[value='${item.id}']`).length > 0;
                if (!optionExists) {
                    const option = new Option(item.text, item.id, true, true);
                    $('#selectPoApprover').append(option);
                }
            });

            $('#selectPoApprover').trigger('change');
        }
    }
    else if(dataType == 'ADD_INSPECTOR' || dataType == 'EDIT_INSPECTOR') {
        asideHide();
        let title = '', tokenForm = '', button = '';
        if($this.attr('data-type') == 'ADD_INSPECTOR') {
            title = 'Add Inspection Flow';
            button = `<button type="button" class="btn btn-info w-100 w-md-auto asideButton" data-type="${dataType}" data-token="" title="Save New Inspection Flow">Save</button>`;
        }
        else {
            title = 'Edit Inspection Flow';
            tokenForm = $this.attr('data-token');
            button = `<button type="button" class="btn btn-secondary w-100 w-md-auto asideButton" data-type="${dataType}" data-token="${$this.attr('data-token')}" title="Edit Inspection Flow">Update</button>`;
        }

        $('.aside-title').html(title);
        $('.aside-content').html(`<form role="form" class="form-horizontal mb-3" enctype="multipart/form-data" id="asideForm">
                                    <input type="hidden" name="tokenForm" value="${tokenForm}">
                                    <div class="form-group mb-3">
                                        <label for="inspectionReceiver" class="form-label">Inspection Receiver<span class="required"></span> :</label>
                                        <div class="skeleton w-100" id="inspectionReceiver"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="inspectionDepartmentOrder" class="form-label">Department Order Form<span class="required"></span> :</label>
                                        <div class="skeleton w-100" id="inspectionDepartmentOrder"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="inspectionChecker" class="form-label">Inspection Checker Option List<span class="required"></span> :</label>
                                        <div class="skeleton w-100" id="inspectionChecker"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="inspectionConfirmer" class="form-label">Inspection Confirmer Option List<span class="required"></span> :</label>
                                        <div class="skeleton w-100" id="inspectionConfirmer"></div>
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
                                        ${button}
                                    </div>`);

        var preselectedDepartment = [];
        var preselectedInspectionReceiver = [], preselectedInspectionChecker = [], preselectedInspectionConfirmer = [];
        const params = {
            'dataForm': 'ORDER_FORM',
            'company': $('#filterInspectorCompany').val(),
        };

        if($this.attr('data-type') == 'EDIT_INSPECTOR') {
            try {
                const response = await fetch(`/doc_approval/getEmployee?dataForm=INSPECTION_FORM&dataType=INSPECTION_MATRIX&dataMatrix=APPROVAL_FLOW&tokenForm=${$this.attr('data-token')}`, {
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
                    if (result.data.length > 0) {
                        result.data.forEach((item, index) => {
                            if(item['flow'] == 'INSPECTION_RECEIVER') {
                                preselectedInspectionReceiver.push({
                                    id: item['employeeId'],
                                    text: item['employeeName'],
                                    description1: item['positionName'],
                                    description2: (item['departmentNameApplicant']) ? `Dept. : ${item['departmentNameApplicant']}` : '--',
                                });

                                preselectedDepartment.push({
                                    id: item['departmentId'],
                                    text: item['departmentName'],
                                    description1: `Dept. ID : ${(item['departmentId']) ? item['departmentId'] : '--'}`,
                                    description2: `Cost center : ${(item['costCenter']) ? item['costCenter'] : '--'}`,
                                });
                            }
                            else if(item['flow'] == 'INSPECTION_CHECKER') {
                                preselectedInspectionChecker.push({
                                    id: item['employeeId'],
                                    text: item['employeeName']
                                });
                            }
                            else if(item['flow'] == 'INSPECTION_CONFIRMER') {
                                preselectedInspectionConfirmer.push({
                                    id: item['employeeId'],
                                    text: item['employeeName']
                                });
                            }
                        });
                    }
                }
                else if (result.status === 404) {
                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed 404 : Not found` });
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
                            window.location.href = result.redirect_uri;
                        }
                    }, 1000);
                }
                else {
                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed to get data` });
                }
            }
            catch (error) {
                Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}` });
            }
        }

        $('#inspectionReceiver').replaceWith(`<select class="select2" name="inspectionReceiver[]" id="inspectionReceiver" data-type=""></select>`);
        if(preselectedInspectionReceiver.length > 0) {
            $('#inspectionReceiver').select2({
                dropdownParent: $('#globalAside'),
                minimumResultsForSearch: Infinity,
                allowClear: false,
                placeholder: '-- Select --',
                data: preselectedInspectionReceiver,
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
            $('#inspectionReceiver').select2({
                dropdownParent: $('#globalAside'),
                placeholder: '-- Select --',
                multiple: true,
                closeOnSelect: false,
                allowClear: false,
                minimumResultsForSearch: 0,
                dropdownAdapter: dropdownAdapter,
                language: {
                    searching: function () {
                        return window.select2SearchingMessage;
                    },
                    noResults: function () {
                        if (window.select2SearchingMessage === 'Type minimum 3 characters') {
                            return 'Type minimum 3 characters';
                        }
                        return 'No results found';
                    }
                },
                ajax: {
                    transport: function (params, success, failure) {
                        const term = params.data.term || '';
                        const page = params.data.page || 1;

                        if (term.length > 0 && term.length < 3) {
                            window.select2SearchingMessage = 'Type minimum 3 characters';
                            setTimeout(() => {
                                $('.select2-results__option.loading-results').text(window.select2SearchingMessage);
                            }, 0);

                            return null;
                        }

                        window.select2SearchingMessage = 'Loading results...';
                        const request = $.ajax({
                            url: '/doc_approval/getEmployee',
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                term: term,
                                page: page,
                                dataForm: 'INSPECTION_FORM',
                                dataType: 'INSPECTION_RECEIVER',
                                company: $('#filterInspectorCompany').val()
                            },
                            success: success,
                            error: failure
                        });

                        return request;
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.data || [],
                            pagination: {
                                more: data.more || false
                            }
                        };
                    },
                    delay: 250,
                    cache: true
                },
                templateResult: formatResultRemote,
                templateSelection: function (data) {
                    return data.text || data.id;
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            }).on('select2:select', function (e) {
                setTimeout(function () {
                    const input = $('.select2-container--open .select2-search__field');
                    if (input.length) {
                        input.focus();
                    }
                }, 0);
            });
        }

        if(preselectedDepartment.length > 0) {
            $('#inspectionDepartmentOrder').replaceWith(`<select class="select2" name="inspectionDepartmentOrder" id="inspectionDepartmentOrder" data-type=""></select>`);
            $('#inspectionDepartmentOrder').select2({
                dropdownParent: $('#globalAside'),
                minimumResultsForSearch: Infinity,
                allowClear: false,
                placeholder: '-- Select --',
                data: preselectedDepartment,
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
            let optionDepartment = await getDepartment({'companyId': $('#filterInspectorCompany').val(),'departmentId': ''});
            $('#inspectionDepartmentOrder').replaceWith(`<select class="select2" name="inspectionDepartmentOrder" id="inspectionDepartmentOrder" data-type=""><option></option></select>`);
            $('#inspectionDepartmentOrder').select2({
                dropdownParent: $('#globalAside'),
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
            });
        }

        $('#inspectionChecker').replaceWith(`<select class="select2 w-100" name="inspectionChecker[]" id="inspectionChecker" data-type=""></select>`);
        $('#inspectionChecker').select2({
            dropdownParent: $('#globalAside'),
            placeholder: '-- Select --',
            multiple: true,
            closeOnSelect: false,
            allowClear: false,
            minimumResultsForSearch: 0,
            dropdownAdapter: dropdownAdapter,
            language: {
                searching: function () {
                    return window.select2SearchingMessage;
                },
                noResults: function () {
                    if (window.select2SearchingMessage === 'Type minimum 3 characters') {
                        return 'Type minimum 3 characters';
                    }
                    return 'No results found';
                }
            },
            ajax: {
                transport: function (params, success, failure) {
                    const term = params.data.term || '';
                    const page = params.data.page || 1;

                    if (term.length > 0 && term.length < 3) {
                        window.select2SearchingMessage = 'Type minimum 3 characters';
                        setTimeout(() => {
                            $('.select2-results__option.loading-results').text(window.select2SearchingMessage);
                        }, 0);

                        return null;
                    }

                    window.select2SearchingMessage = 'Loading results...';
                    const request = $.ajax({
                        url: '/doc_approval/getEmployee',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: term,
                            page: page,
                            dataForm: 'INSPECTION_FORM',
                            dataType: 'INSPECTION_CHECKER',
                            company: $('#filterInspectorCompany').val()
                        },
                        success: success,
                        error: failure
                    });

                    return request;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data || [],
                        pagination: {
                            more: data.more || false
                        }
                    };
                },
                delay: 250,
                cache: true
            },
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).on('select2:select', function (e) {
            setTimeout(function () {
                const input = $('.select2-container--open .select2-search__field');
                if (input.length) {
                    input.focus();
                }
            }, 0);
        });
        if(preselectedInspectionChecker.length > 0) {
            preselectedInspectionChecker.forEach(item => {
                const optionExists = $(`#inspectionChecker option[value='${item.id}']`).length > 0;
                if (!optionExists) {
                    const option = new Option(item.text, item.id, true, true);
                    $('#inspectionChecker').append(option);
                }
            });

            $('#inspectionChecker').trigger('change');
        }

        $('#inspectionConfirmer').replaceWith(`<select class="select2 w-100" name="inspectionConfirmer[]" id="inspectionConfirmer" data-type=""></select>`);
        $('#inspectionConfirmer').select2({
            dropdownParent: $('#globalAside'),
            placeholder: '-- Select --',
            multiple: true,
            closeOnSelect: false,
            allowClear: false,
            minimumResultsForSearch: 0,
            dropdownAdapter: dropdownAdapter,
            language: {
                searching: function () {
                    return window.select2SearchingMessage;
                },
                noResults: function () {
                    if (window.select2SearchingMessage === 'Type minimum 3 characters') {
                        return 'Type minimum 3 characters';
                    }
                    return 'No results found';
                }
            },
            ajax: {
                transport: function (params, success, failure) {
                    const term = params.data.term || '';
                    const page = params.data.page || 1;

                    if (term.length > 0 && term.length < 3) {
                        window.select2SearchingMessage = 'Type minimum 3 characters';
                        setTimeout(() => {
                            $('.select2-results__option.loading-results').text(window.select2SearchingMessage);
                        }, 0);

                        return null;
                    }

                    window.select2SearchingMessage = 'Loading results...';
                    const request = $.ajax({
                        url: '/doc_approval/getEmployee',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: term,
                            page: page,
                            dataForm: 'INSPECTION_FORM',
                            dataType: 'INSPECTION_CONFIRMER',
                            company: $('#filterInspectorCompany').val()
                        },
                        success: success,
                        error: failure
                    });

                    return request;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data || [],
                        pagination: {
                            more: data.more || false
                        }
                    };
                },
                delay: 250,
                cache: true
            },
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).on('select2:select', function (e) {
            setTimeout(function () {
                const input = $('.select2-container--open .select2-search__field');
                if (input.length) {
                    input.focus();
                }
            }, 0);
        });
        if(preselectedInspectionConfirmer.length > 0) {
            preselectedInspectionConfirmer.forEach(item => {
                const optionExists = $(`#inspectionConfirmer option[value='${item.id}']`).length > 0;
                if (!optionExists) {
                    const option = new Option(item.text, item.id, true, true);
                    $('#inspectionConfirmer').append(option);
                }
            });

            $('#inspectionConfirmer').trigger('change');
        }
    }
});

$(document).on('click', '.asideButton', async function () {
    let $this = $(this);
    let dataType = $this.attr('data-type');
    if(dataType == 'ADD_ORDER_FORM_APPLICANT' || dataType == 'EDIT_ORDER_FORM_APPLICANT' || dataType == 'ENABLE_ORDER_FORM_APPLICANT' || dataType == 'DISABLE_ORDER_FORM_APPLICANT' || dataType == 'DELETE_ORDER_FORM_APPLICANT') {
        var formData;
        if(dataType == 'ENABLE_ORDER_FORM_APPLICANT' || dataType == 'DISABLE_ORDER_FORM_APPLICANT' || dataType == 'DELETE_ORDER_FORM_APPLICANT') {
            formData = new FormData();
            formData.append('type', dataType);
            formData.append('tokenForm', $this.attr('data-token'));
        }
        else {
            Snackbar.close();
            clearValidation();
            formData = new FormData($('#asideForm')[0]);
            formData.append('type', dataType);
            formData.append('filterOrderFormCompany', $('#filterOrderFormCompany').val());
        }

        $this.btnLoading(async function() {
            if (dataType === 'EDIT_ORDER_FORM_APPLICANT' || dataType == 'ENABLE_ORDER_FORM_APPLICANT' || dataType == 'DISABLE_ORDER_FORM_APPLICANT' || dataType == 'DELETE_ORDER_FORM_APPLICANT') {
                formData.append('_method', 'PUT');
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

            try {
                $('.asideButton').prop('disabled', true);
                $this.html('<i class="fas fa-spinner fa-spin"></i> Please wait');
                const response = await fetch('/proc_pur/saveOrderFormApplicant', {
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
                    if(dataType == 'ENABLE_ORDER_FORM_APPLICANT' || dataType == 'DISABLE_ORDER_FORM_APPLICANT' || dataType == 'DELETE_ORDER_FORM_APPLICANT') {
                        $('.modal').modal('hide');
                    }
                    else {
                        asideHide();
                    }

                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result['message']}`});

                    let table = $('#orderFormApplicantTable').DataTable();
                    let scrollBody = $(table.table().node()).parent();
                    let currentScrollTop = scrollBody.scrollTop();
                    table.ajax.reload(function () {
                        scrollBody.scrollTop(currentScrollTop);
                    }, false);
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
                $('.asideButton').prop('disabled', false);
            }
        });
    }
    else if(dataType == 'ADD_PO_APPROVAL_RULE' || dataType == 'EDIT_PO_APPROVAL_RULE' || dataType == 'ENABLE_PO_APPROVAL_RULE' || dataType == 'DISABLE_PO_APPROVAL_RULE' || dataType == 'DELETE_PO_APPROVAL_RULE') {
        var formData;
        if(dataType == 'ENABLE_PO_APPROVAL_RULE' || dataType == 'DISABLE_PO_APPROVAL_RULE' || dataType == 'DELETE_PO_APPROVAL_RULE') {
            formData = new FormData();
            formData.append('type', dataType);
            formData.append('tokenForm', $this.attr('data-token'));
            formData.append('filterPoRuleCompany', $('#filterPoRuleCompany').val());
        }
        else {
            Snackbar.close();
            clearValidation();
            formData = new FormData($('#asideForm')[0]);
            formData.append('type', dataType);
            formData.append('filterPoRuleCompany', $('#filterPoRuleCompany').val());
        }

        $this.btnLoading(async function() {
            if (dataType === 'EDIT_PO_APPROVAL_RULE' || dataType == 'ENABLE_PO_APPROVAL_RULE' || dataType == 'DISABLE_PO_APPROVAL_RULE' || dataType == 'DELETE_PO_APPROVAL_RULE') {
                formData.append('updateType', 'FORM');
                formData.append('_method', 'PUT');
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

            try {
                $('.asideButton').prop('disabled', true);
                $this.html('<i class="fas fa-spinner fa-spin"></i> Please wait');
                const response = await fetch('/proc_pur/savePoApprovalRule', {
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
                    if(dataType == 'ENABLE_PO_APPROVAL_RULE' || dataType == 'DISABLE_PO_APPROVAL_RULE' || dataType == 'DELETE_PO_APPROVAL_RULE') {
                        $('.modal').modal('hide');
                    }
                    else {
                        asideHide();
                    }

                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result['message']}`});

                    let table = $('#poApprovalRuleTable').DataTable();
                    let scrollBody = $(table.table().node()).parent();
                    let currentScrollTop = scrollBody.scrollTop();
                    table.ajax.reload(function () {
                        scrollBody.scrollTop(currentScrollTop);
                    }, false);
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
                $('.asideButton').prop('disabled', false);
            }
        });
    }
    else if(dataType == 'ADD_INSPECTOR' || dataType == 'EDIT_INSPECTOR' || dataType == 'ENABLE_INSPECTOR' || dataType == 'DISABLE_INSPECTOR' || dataType == 'DELETE_INSPECTOR') {
        var formData;
        if(dataType == 'ENABLE_INSPECTOR' || dataType == 'DISABLE_INSPECTOR' || dataType == 'DELETE_INSPECTOR') {
            formData = new FormData();
            formData.append('type', dataType);
            formData.append('tokenForm', $this.attr('data-token'));
        }
        else {
            Snackbar.close();
            clearValidation();
            formData = new FormData($('#asideForm')[0]);
            formData.append('type', dataType);
            formData.append('filterInspectorCompany', $('#filterInspectorCompany').val());
        }

        $this.btnLoading(async function() {
            if (dataType === 'EDIT_INSPECTOR' || dataType == 'ENABLE_INSPECTOR' || dataType == 'DISABLE_INSPECTOR' || dataType == 'DELETE_INSPECTOR') {
                formData.append('_method', 'PUT');
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

            try {
                $('.asideButton').prop('disabled', true);
                $this.html('<i class="fas fa-spinner fa-spin"></i> Please wait');
                const response = await fetch('/proc_pur/saveInspector', {
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
                    if(dataType == 'ENABLE_INSPECTOR' || dataType == 'DISABLE_INSPECTOR' || dataType == 'DELETE_INSPECTOR') {
                        $('.modal').modal('hide');
                    }
                    else {
                        asideHide();
                    }

                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result['message']}`});

                    let table = $('#inspectorTable').DataTable();
                    let scrollBody = $(table.table().node()).parent();
                    let currentScrollTop = scrollBody.scrollTop();
                    table.ajax.reload(function () {
                        scrollBody.scrollTop(currentScrollTop);
                    }, false);
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
                $('.asideButton').prop('disabled', false);
            }
        });
    }
});

$(document).off('click', '.masterVendor').on('click', '.masterVendor', async function (e) {
    let type = $(this).attr('data-type');
    if(type == 'EDIT') {
        if($(this).attr('data-id') == '') {
            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Select record to edit` });
            return false;
        }
    }

    asideHide();
    $('.aside-title').empty();
    $('.aside-content').empty();
    if(type == 'NEW' || type == 'EDIT') {
        let title = '', btnText = '', btnClass = '';
        if(type == 'NEW') {
            title = 'New Vendor';
            btnText = 'Save';
            btnClass = 'btn-info';
        }
        else if(type == 'EDIT') {
            title = 'Edit Vendor';
            btnText = 'Update';
            btnClass = 'btn-secondary';
        }

        $('#globalAside').find('.aside-title').html(title);
        $('#globalAside').find('.aside-content').html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
                                    <input type="hidden" id="vendorToken" name="tokenForm" value="">
                                    <div class="form-group mb-3">
                                        <label for="vendorAccount" class="form-label">Vendor Account<span class="required"></span> :</label>
                                        <div class="skeleton w-100" id="vendorAccount"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="vendorCompanyName" class="form-label">Vendor Name<span class="required"></span> :</label>
                                        <div class="skeleton w-100" id="vendorCompanyName"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="vendorGroup" class="form-label">Group<span class="required"></span> :</label>
                                        <select class="select2" name="vendorGroup" id="vendorGroup">
                                            <option></option>
                                            <option value="LOCAL">LOCAL</option>
                                            <option value="OVERSEAS">OVERSEAS</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="vendorCcy" class="form-label">Currency<span class="required"></span> :</label>
                                        <select class="select2" name="vendorCcy" id="vendorCcy">
                                            <option></option>
                                            <option value="IDR">IDR</option>
                                            <option value="USD">USD</option>
                                            <option value="JPY">JPY</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="vendorComponent" class="form-label">Component / Type of Service : (optional)</label>
                                        <div class="skeleton w-100" id="vendorComponent"></div>
                                    </div>

                                    <ul class="nav nav-pills pt-2" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link pillTabDetailForm fs-8 px-3 py-1 active" id="vendorAddressTab" data-form="" data-scroll-top="" data-coreui-toggle="pill" data-coreui-target="#vendorAddressContent" type="button" role="tab" aria-controls="vendorAddressContent" aria-selected="true">Vendor Address<span class="required"></span></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link pillTabDetailForm fs-8 px-3 py-1" id="vendorPicTab" data-form="" data-token="" data-scroll-top="" data-coreui-toggle="pill" data-coreui-target="#vendorPicContent" type="button" role="tab" aria-controls="vendorPicContent" aria-selected="false" tabindex="-1">Vendor PIC<span class="required"></span></button>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div class="tab-pane fade w-100 h-100 active show" id="vendorAddressContent" role="tabpanel" aria-labelledby="vendorAddressTab" tabindex="0">
                                            <div class="row row-multi-col mb-3 pt-2 pb-1 bg-white vendorAddressContainer">
                                                <div class="form-group mb-2">
                                                    <label for="vendorAddress" class="form-label">Vendor Address<span class="required"></span> : <span class="fw-normal fst-italic">(Press Enter to new line address)</span></label>
                                                    <input type="hidden" name="vendorAddressId[]" value="">
                                                    <textarea class="form-control autosize" data-limit-rows="true" rows="2" spellcheck="false" maxlength="255" name="vendorAddress[]"></textarea>
                                                </div>
                                                <div class="col-sm-6 mb-sm-0">
                                                    <div class="form-group mb-2">
                                                        <label for="vendorPhone" class="form-label">Phone :</label>
                                                        <input type="text" class="form-control" name="vendorPhone[]" pattern="[0-9]*" inputmode="numeric" placeholder="Optional" style="-webkit-appearance: textfield; -moz-appearance: textfield; appearance: textfield;">
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                                        <button type="button" class="btn btn-default btn-sm fs-8 fw-medium vendorDetailButton" data-type="REMOVE_ADDRESS"><i class="fa-solid fa-trash-can-list fa-fw"></i> Remove</button>
                                                        <button type="button" class="btn btn-secondary btn-sm fs-8 fw-medium vendorDetailButton" data-type="ADD_ADDRESS"><i class="fa-regular fa-plus fa-fw"></i> Add Address</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade w-100 h-100" id="vendorPicContent" role="tabpanel" aria-labelledby="vendorPicTab" tabindex="1">
                                            <div class="row row-multi-col mb-3 pt-2 pb-1 bg-white vendorPicContainer">
                                                <div class="col-sm-6 mb-sm-0">
                                                    <div class="form-group mb-2">
                                                        <label for="vendorPicName" class="form-label">Vendor PIC Name<span class="required"></span> :</label>
                                                        <input type="hidden" name="vendorPicId[]" value="">
                                                        <input type="text" class="form-control" name="vendorPicName[]" placeholder="Required">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 mb-sm-0">
                                                    <div class="form-group mb-2">
                                                        <label for="vendorPicEmail" class="form-label">PIC Email :</label>
                                                        <input type="email" class="form-control" name="vendorPicEmail[]" placeholder="Optional">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 mb-sm-0">
                                                    <div class="form-group mb-2">
                                                        <label for="vendorPicPhone" class="form-label">PIC Phone :</label>
                                                        <input type="text" class="form-control" name="vendorPicPhone[]" pattern="[0-9]*" inputmode="numeric" placeholder="Optional" style="-webkit-appearance: textfield; -moz-appearance: textfield; appearance: textfield;">
                                                    </div>
                                                </div>

                                                <div class="col-12 mb-3">
                                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                                        <button type="button" class="btn btn-default btn-sm fs-8 fw-medium vendorDetailButton" data-type="REMOVE_PIC"><i class="fa-solid fa-trash-can-list fa-fw"></i> Remove</button>
                                                        <button type="button" class="btn btn-secondary btn-sm fs-8 fw-medium vendorDetailButton" data-type="ADD_PIC"><i class="fa-regular fa-plus fa-fw"></i> Add PIC</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>`);
        $('#globalAside').find('.asideFooterBtn').html(`<div class="col-6 col-md-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                            <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside asideBtn" title="Close">Close</button>
                                                        </div>
                                                        <div class="col-6 col-md-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                            <button type="button" class="btn ${btnClass} w-100 w-md-auto asideBtn saveVendor" data-type="${type}"></i> ${btnText}</button>
                                                        </div>`);
    }

    $('.overlay-aside').addClass('show').trigger('shown');

    if(type == 'NEW' || type == 'EDIT') {
        setTimeout(() => {
            $('#globalAside').addClass('show aside-lg').trigger('shown');
        }, '200');

        $('body').addClass('overflow-hidden');
        $('#asideDetailForm').scrollTop(0);
        $('.autosize').autosize({ append: "\n" });

        var Utils = $.fn.select2.amd.require('select2/utils');
        var Dropdown = $.fn.select2.amd.require('select2/dropdown');
        var DropdownSearch = $.fn.select2.amd.require('select2/dropdown/search');
        var AttachBody = $.fn.select2.amd.require('select2/dropdown/attachBody');
        var dropdownAdapter = Utils.Decorate(Utils.Decorate(Dropdown, DropdownSearch), AttachBody);

        let vendorComponent = [], vendorGroup = '', vendorCcy = '';

        if(type == 'EDIT') {
            const params = {
                'id': $(this).attr('data-id'),
                'type': 'ALL',
            };

            const getDetails = await getVendorDetails(params);
            $('#vendorAccount').replaceWith(`<input type="text" class="form-control" id="vendorAccount" name="vendorAccount">`);
            $('#vendorCompanyName').replaceWith(`<input type="text" class="form-control" id="vendorCompanyName" name="vendorCompanyName">`);

            const vendor = getDetails.vendor;
            $('#vendorToken').val(getDetails.token);
            $('#vendorAccount').val(vendor.vendorAccount);
            $('#vendorCompanyName').val(vendor.vendorName);

            vendorComponent = getDetails.vendorComponents;
            vendorGroup = vendor.group;
            vendorCcy = vendor.currency;

            if(getDetails.vendorAddress.length > 0) {
                $('#vendorAddressContent').html('');
                getDetails.vendorAddress.forEach(function(item, index) {
                    $('#vendorAddressContent').append(`<div class="row row-multi-col mb-3 pt-2 pb-1 bg-white vendorAddressContainer">
                                                            <div class="form-group mb-2">
                                                                <label for="vendorAddress" class="form-label">Vendor Address<span class="required"></span> : <span class="fw-normal fst-italic">(Press Enter to new line address)</span></label>
                                                                <input type="hidden" name="vendorAddressId[]" value="${item.addressId !== null ? item.addressId : ''}">
                                                                <textarea class="form-control autosize" data-limit-rows="true" rows="2" spellcheck="false" maxlength="255" name="vendorAddress[]">${item.addressName !== null ? item.addressName : ''}</textarea>
                                                            </div>
                                                            <div class="col-sm-6 mb-sm-0">
                                                                <div class="form-group mb-2">
                                                                    <label for="vendorPhone" class="form-label">Phone :</label>
                                                                    <input type="text" class="form-control" name="vendorPhone[]" pattern="[0-9]*" inputmode="numeric" placeholder="Optional" value="${item.vendorPhone !== null ? item.vendorPhone : ''}" style="-webkit-appearance: textfield; -moz-appearance: textfield; appearance: textfield;">
                                                                </div>
                                                            </div>
                                                            <div class="col-12 mb-3">
                                                                <div class="d-flex justify-content-end align-items-center gap-2">
                                                                    <button type="button" class="btn btn-default btn-sm fs-8 fw-medium vendorDetailButton" data-type="REMOVE_ADDRESS"><i class="fa-solid fa-trash-can-list fa-fw"></i> Remove</button>
                                                                    <button type="button" class="btn btn-secondary btn-sm fs-8 fw-medium vendorDetailButton" data-type="ADD_ADDRESS"><i class="fa-regular fa-plus fa-fw"></i> Add Address</button>
                                                                </div>
                                                            </div>
                                                        </div>`);
                });
            }

            if(getDetails.vendorPic.length > 0) {
                $('#vendorPicContent').html('');
                getDetails.vendorPic.forEach(function(item, index) {
                    $('#vendorPicContent').append(`<div class="row row-multi-col mb-3 pt-2 pb-1 bg-white vendorPicContainer">
                                                        <div class="col-sm-6 mb-sm-0">
                                                            <div class="form-group mb-2">
                                                                <label for="vendorPicName" class="form-label">Vendor PIC Name<span class="required"></span> :</label>
                                                                <input type="hidden" name="vendorPicId[]" value="${item.picId !== null ? item.picId : ''}">
                                                                <input type="text" class="form-control" name="vendorPicName[]" value="${item.picName !== null ? item.picName : ''}" placeholder="Required">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 mb-sm-0">
                                                            <div class="form-group mb-2">
                                                                <label for="vendorPicEmail" class="form-label">PIC Email :</label>
                                                                <input type="email" class="form-control" name="vendorPicEmail[]" value="${item.picEmail !== null ? item.picEmail : ''}" placeholder="Optional">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 mb-sm-0">
                                                            <div class="form-group mb-2">
                                                                <label for="vendorPicPhone" class="form-label">PIC Phone :</label>
                                                                <input type="text" class="form-control" name="vendorPicPhone[]" pattern="[0-9]*" inputmode="numeric" placeholder="Optional" value="${item.picPhone !== null ? item.picPhone : ''}" style="-webkit-appearance: textfield; -moz-appearance: textfield; appearance: textfield;">
                                                            </div>
                                                        </div>

                                                        <div class="col-12 mb-3">
                                                            <div class="d-flex justify-content-end align-items-center gap-2">
                                                                <button type="button" class="btn btn-default btn-sm fs-8 fw-medium vendorDetailButton" data-type="REMOVE_PIC"><i class="fa-solid fa-trash-can-list fa-fw"></i> Remove</button>
                                                                <button type="button" class="btn btn-secondary btn-sm fs-8 fw-medium vendorDetailButton" data-type="ADD_PIC"><i class="fa-regular fa-plus fa-fw"></i> Add PIC</button>
                                                            </div>
                                                        </div>
                                                    </div>`);
                });
            }

            $('.autosize').autosize({ append: "\n" });
        }
        else {
            $('#vendorAccount').replaceWith(`<input type="text" class="form-control" id="vendorAccount" name="vendorAccount"></input>`);
            $('#vendorCompanyName').replaceWith(`<input type="text" class="form-control" id="vendorCompanyName" name="vendorCompanyName"></input>`);
        }

        let selectedComponents = [];
        if(vendorComponent && vendorComponent.length > 0) {
            selectedComponents = vendorComponent.map(item => ({
                id: item.componentId,
                text: item.componentName
            }));
        }

        $('#vendorGroup').select2({
            dropdownParent: $('#globalAside'),
            placeholder: '-- Select --',
            allowClear: false,
        }).val(vendorGroup).trigger('change');

        $('#vendorCcy').select2({
            dropdownParent: $('#globalAside'),
            placeholder: '-- Select --',
            allowClear: false,
        }).val(vendorCcy).trigger('change');

        $('#vendorComponent').replaceWith(`<select class="select2 w-100" name="vendorComponent[]" id="vendorComponent" data-type=""></select>`);
        $('#vendorComponent').select2({
            dropdownParent: $('#globalAside'),
            placeholder: '-- Select --',
            multiple: true,
            tags: true,
            closeOnSelect: false,
            allowClear: true,
            minimumResultsForSearch: 0,
            ajax: {
                url: '/proc_pur/getPurchaseTypeComponents',
                dataType: 'json',
                delay: 500,
                data: function(params) {
                    return {
                        term: params.term,
                        page: params.page || 1
                    }
                },
                transport: function (params, success, failure) {
                    let term =  $.trim(params.data.term);
                    term = params.data.term || '';

                    // Cegah AJAX jika term < 2 dan bukan kosong
                    if (term !== '' && term.length < 3) {
                        $('.loading-results').remove();
                        $('.createOption').closest('li').remove();
                        return null;
                        // return success({ data: [], more: false });
                    }

                    return $.ajax(params).done(success).fail(failure);
                },
                processResults: function (response, params) {
                    params.page = params.page || 1;
                    return {
                        results: response.data.map(item => ({
                            id: String(item.id),
                            text: item.text
                        })),
                        pagination: {
                            more: response.more
                        }
                    };
                },
                cache: true
            },
            createTag: function (params) {
                const term = $.trim(params.term);
                if (term === '') return null;
                const exists = $('#vendorComponent').find('option').filter(function () {
                    return $(this).text().toLowerCase() === term.toLowerCase();
                }).length > 0;

                if (exists || term.length < 4) {
                    return null;
                }

                return {
                    id: 'new:' + term,
                    text: term,
                    newTag: true
                };
            },
            templateResult: function (data) {
                if (data.loading) return data.text;
                if (data.newTag) return $(`<span class="createOption fw-medium"><i class="fa-solid fa-plus"></i> Create : ${data.text}</span>`);
                return data.text;
            }

        }).on('select2:select', function (e) {
            Snackbar.close();
            const data = e.params.data;
            if (data.id.startsWith('new:')) {
                const newName = data.id.replace('new:', '');
                $.ajax({
                    type: 'POST',
                    url: '/proc_pur/addPurchaseTypeComponents',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        name: newName
                    },
                    success: function (response) {
                        const select = $('#vendorComponent');
                        let currentValues = select.val() || [];
                        currentValues = currentValues.filter(v => v !== data.id);
                        $('.createOption').closest('li').remove();

                        if (select.find(`option[value="${response.id}"]`).length === 0) {
                            const newOption = new Option(response.text, response.id, true, true);
                            select.append(newOption);
                        }

                        currentValues.push(String(response.id));
                        select.val(currentValues).trigger('change');
                        select.select2('close');
                        select.select2('open');
                        Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> Successfully created`});
                    },
                    error: function () {
                        alert('Failed create new component.');
                        const selected = $('#vendorComponent').val().filter(v => v !== data.id);
                        $('#vendorComponent').val(selected).trigger('change');
                    }
                });
            }
            setTimeout(function () {
                const searchField = $('.select2-container--open .select2-search__field');
                searchField.val('').focus();
            }, 0);
        }).on('select2:unselect', function () {
            setTimeout(function () {
                const searchField = $('.select2-container--open .select2-search__field');
                searchField.val('').focus();
            }, 0);
        });

        if(selectedComponents.length > 0) {
            selectedComponents.forEach(item => {
                const optionExists = $(`#vendorComponent option[value='${item.id}']`).length > 0;
                if (!optionExists) {
                    const option = new Option(item.text, item.id, true, true);
                    $('#vendorComponent').append(option);
                }
            });

            $('#vendorComponent').trigger('change');
        }
    }
});

$(document).off('click', '.saveVendor').on('click', '.saveVendor', function (e) {
    let $this = $(this);
    Snackbar.close();
    clearValidation();
    const formData = new FormData($('#asideForm')[0]);
    formData.append('type', $this.attr('data-type'));

    $(this).btnLoading(async function() {
        if ($this.attr('data-type') === 'EDIT') {
            formData.append('_method', 'PUT');
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

        try {
            $('.asideBtn').prop('disabled', true);
            $this.html('<i class="fas fa-spinner fa-spin"></i> Please wait');
            const response = await fetch('/proc_pur/saveVendor', {
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
                if($this.attr('data-type') == 'NEW') {
                    $('#vendorCompanyName').focus();
                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result['message']}`});
                    $('#vendorAddressContent').html(`<div class="row row-multi-col mb-3 pt-2 pb-1 bg-white vendorAddressContainer">
                                                            <div class="form-group mb-2">
                                                                <label for="vendorAddress" class="form-label">Vendor Address<span class="required"></span> : <span class="fw-normal fst-italic">(Press Enter to new line address)</span></label>
                                                                <input type="hidden" name="vendorAddressId[]" value="">
                                                                <textarea class="form-control autosize" data-limit-rows="true" rows="2" spellcheck="false" maxlength="255" name="vendorAddress[]"></textarea>
                                                            </div>
                                                            <div class="col-sm-6 mb-sm-0">
                                                                <div class="form-group mb-2">
                                                                    <label for="vendorPhone" class="form-label">Phone :</label>
                                                                    <input type="text" class="form-control" name="vendorPhone[]" pattern="[0-9]*" inputmode="numeric" placeholder="Optional" style="-webkit-appearance: textfield; -moz-appearance: textfield; appearance: textfield;">
                                                                </div>
                                                            </div>
                                                            <div class="col-12 mb-3">
                                                                <div class="d-flex justify-content-end align-items-center gap-2">
                                                                    <button type="button" class="btn btn-default btn-sm fs-8 fw-medium vendorDetailButton" data-type="REMOVE_ADDRESS"><i class="fa-solid fa-trash-can-list fa-fw"></i> Remove</button>
                                                                    <button type="button" class="btn btn-secondary btn-sm fs-8 fw-medium vendorDetailButton" data-type="ADD_ADDRESS"><i class="fa-regular fa-plus fa-fw"></i> Add Address</button>
                                                                </div>
                                                            </div>
                                                        </div>`);

                    $('#vendorPicContent').html(`<div class="row row-multi-col mb-3 pt-2 pb-1 bg-white vendorPicContainer">
                                                        <div class="col-sm-6 mb-sm-0">
                                                            <div class="form-group mb-2">
                                                                <label for="vendorPicName" class="form-label">Vendor PIC Name<span class="required"></span> :</label>
                                                                <input type="hidden" name="vendorPicId[]" value="">
                                                                <input type="text" class="form-control" name="vendorPicName[]" placeholder="Required">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 mb-sm-0">
                                                            <div class="form-group mb-2">
                                                                <label for="vendorPicEmail" class="form-label">PIC Email :</label>
                                                                <input type="email" class="form-control" name="vendorPicEmail[]" placeholder="Optional">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 mb-sm-0">
                                                            <div class="form-group mb-2">
                                                                <label for="vendorPicPhone" class="form-label">PIC Phone :</label>
                                                                <input type="text" class="form-control" name="vendorPicPhone[]" pattern="[0-9]*" inputmode="numeric" placeholder="Optional" style="-webkit-appearance: textfield; -moz-appearance: textfield; appearance: textfield;">
                                                            </div>
                                                        </div>

                                                        <div class="col-12 mb-3">
                                                            <div class="d-flex justify-content-end align-items-center gap-2">
                                                                <button type="button" class="btn btn-default btn-sm fs-8 fw-medium vendorDetailButton" data-type="REMOVE_PIC"><i class="fa-solid fa-trash-can-list fa-fw"></i> Remove</button>
                                                                <button type="button" class="btn btn-secondary btn-sm fs-8 fw-medium vendorDetailButton" data-type="ADD_PIC"><i class="fa-regular fa-plus fa-fw"></i> Add PIC</button>
                                                            </div>
                                                        </div>
                                                    </div>`);
                    $('#asideForm')[0].reset();
                    $('#vendorComponent').val(null).trigger('change');
                    $('#vendorGroup').val(null).trigger('change');
                    $('#vendorCcy').val(null).trigger('change');

                    let table = $('#vendorReferenceTable').DataTable();
                    let scrollBody = $(table.table().node()).parent();
                    let currentScrollTop = scrollBody.scrollTop();
                    table.ajax.reload(function () {
                        scrollBody.scrollTop(currentScrollTop);
                    }, false);
                }
                else if ($this.attr('data-type') == 'EDIT') {
                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result['message']}`});
                    if($this.closest('aside.form-aside').find('div.aside-sidebar').length == 1) {
                        let asideSidebar = $this.closest('aside.form-aside').find('div.aside-sidebar');
                        asideSidebar.find('#reloadTable').val('vendorReferenceTable');
                        asideSidebar.find('#sortBy').val('LATEST_UPDATED');
                    }
                    setTimeout(() => {
                        $this.closest('div.asideFooterBtn').find('button.closeBtnAside').trigger('click');
                    }, 1000);
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
        finally {
            $('.asideBtn').prop('disabled', false);
        }
    });
});

async function processNextFlowSelection($select, selectedData, eventType = 'unknown') {
    let nextFlow = null;
    let selectedValue = null;
    let fullData = null;

    if (selectedData && typeof selectedData === 'object') {
        // Data dari Select2 (select2:select event atau preselect)
        nextFlow = selectedData.nextFlow;
        selectedValue = selectedData.id;
        fullData = selectedData;
        if (nextFlow) {
            $select.attr('data-nextflow', nextFlow);
            $select.next('.select2-container').attr('data-nextflow', nextFlow);
        }
    }
    else {
        // Data dari change event atau preselect sederhana
        selectedValue = $select.val();
        if (!selectedValue) {
            $select.removeAttr('data-nextflow');
            $select.next('.select2-container').removeAttr('data-nextflow');
            return;
        }

        // Coba ambil dari attribute yang sudah ada
        nextFlow = $select.attr('data-nextflow');

        // Jika belum ada, coba dari Select2 data
        if (!nextFlow) {
            const select2Data = $select.select2('data');
            if (select2Data && select2Data.length > 0 && select2Data[0].nextFlow) {
                nextFlow = select2Data[0].nextFlow;
                fullData = select2Data[0];

                $select.attr('data-nextflow', nextFlow);
                $select.next('.select2-container').attr('data-nextflow', nextFlow);
            }
        }
    }

    var row = $select.attr('id').replace('fieldRule', '');
    const fieldRuleRow = $select.closest('div.fieldRuleRow');
    fieldRuleRow.find('.valueRuleContainer').html(`<div class="skeleton w-100 valueRule mb-0"></div>`);
    if(nextFlow === 'OPTION') {
        try {
            const response = await fetch(`/proc_pur/getRuleFlow?dataForm=APPLICATION_PO_FORM&dataType=FIELD_OPTION&fieldId=${selectedValue}`, {
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
                if (result.data.length > 0) {
                    let options = '';
                    options = result.data.map(row => ({
                            id: row.fieldOptionId,
                            text: row.optionName,
                    }));

                    fieldRuleRow.find('.valueRuleContainer').html(`<select class="select2 w-100 valueRule valueRuleSelect" name="valueRule[]" id="valueRule${row}"><option></option></select>`);
                    $(`#valueRule${row}`).select2({
                        dropdownParent: $('#globalAside'),
                        allowClear: false,
                        placeholder: '-- Select --',
                        data: options,
                    });

                    if(preselectValue.length > 0) {
                        $(`#valueRule${row}`).val(preselectValue[row].id).trigger('change');
                    }
                }
                else {
                    fieldRuleRow.find('.valueRuleContainer').html(`<input type="text" class="form-control valueRule toUpperCase" name="valueRule[]" id="valueRule${row}" spellcheck="false" autocomplete="off">`);
                }
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
    else {
        let inputClass = 'toUpperCase', inputValue = '';
        if(nextFlow === 'CURRENCY') {
            inputClass = 'currencyValue';
        }
        else if(nextFlow === 'NUMBER') {
            inputClass = 'numberValue';
        }

        if(preselectValue.length > 0) {
            if (row in preselectValue) {
                inputValue = preselectValue[row].text;
                inputValue = (nextFlow === 'CURRENCY') ? currencyFormat(inputValue) : inputValue;
            }
        }

        fieldRuleRow.find('.valueRuleContainer').html(`<input type="text" class="form-control valueRule ${inputClass}" name="valueRule[]" id="valueRule${row}" value="${inputValue}" spellcheck="false" autocomplete="off">`);
    }
}

$(document).on('change', '.fieldRule', async function (e) {
    // Cek apakah ini triggered oleh Select2 events
    if (e.isTrigger || $(this).data('select2-triggered')) {
        // Skip jika sudah dihandle oleh Select2 events
        $(this).removeData('select2-triggered');
        return;
    }

    const $select = $(this);
    await processNextFlowSelection($select, null, 'change');
});

$(document).on('select2:select select2:clear', '.fieldRule', function(e) {
    $(this).data('select2-triggered', true);
});

$(document).on('click', '.groupRuleOption', function (event) {
    const $this = $(this);
    const bgColor = $this.attr('data-bg');
    if ($this.hasClass('active')) {
        return;
    }
    else{
        $this.closest('ul').find('.groupRuleOption').removeClass('active');
        $this.closest('div.fieldRuleRow').find('div.ruleConditionContainer').removeClass('bg-danger bg-success bg-warning bg-info');
        if(bgColor != '') {
            $this.addClass('active');
            $this.closest('div.fieldRuleRow').find('div.ruleConditionContainer').addClass(bgColor);
        }
        $this.closest('div.fieldRuleRow').find('input.groupColor').val($this.attr('data-bg'));
    }
});

$(document).on('click', '#addRuleCondition', function (event) {
    const $this = $(this);
    if($('.fieldRuleRow').length >= 5) {
        Snackbar.show({
            pos: 'bottom-center',
            text: `<i class="fa-solid fa-circle-exclamation fa-lg fa-fw text-warning"></i> Maximum 5 rule conditions`
        });
        return false;
    }

    let lastCondition = $('.fieldRuleRow .ruleCondition').last();
    let lastId = lastCondition.attr('id');
    let seq = parseInt(lastId.replace('conditionId', ''), 10);
    let x = seq + 1;

    $this.closest('div#fieldRuleContainer').append(`<div class="fieldRuleRow d-flex">
                                                        <input type="hidden" class="ruleCondition" id="conditionId${x}" name="conditionId[]" value="">
                                                        <input type="hidden" class="groupColor" id="groupColor${x}" name="groupColor[]" value="">
                                                        <div class="col-11 p-1 d-flex ruleConditionContainer">
                                                            <div class="col-4 pe-1">
                                                                <select class="select2 w-100 fieldRule" name="fieldRule[]" id="fieldRule${x}" data-row="${x}" data-valueid="" data-value=""></select>
                                                            </div>
                                                            <div class="col-3 pe-1">
                                                                <select class="select2 w-100 operatorRule" name="operatorRule[]" id="operatorRule${x}" data-type=""><option></option></select>
                                                            </div>
                                                            <div class="col-5 pe-0 valueRuleContainer">
                                                                <input type="text" class="form-control valueRule toUpperCase" name="valueRule[]" id="valueRule${x}" spellcheck="false" autocomplete="off">
                                                            </div>
                                                        </div>
                                                        <div class="col-1 d-flex gap-1">
                                                            <div class="dropdown">
                                                                <button type="button" class="btn btn-md btn-transparent my-1" data-bs-boundary="viewport"data-coreui-toggle="dropdown" aria-expanded="false" title="">
                                                                    <i class="fa-solid fa-chevron-down fa-lg"></i>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="">
                                                                    <li class="dropdown-header py-1 px-2 m-0 mb-2 fs-8 fw-medium border-bottom"><i class="fa-solid fa-palette"></i> 'Or' group rule</li>

                                                                    <li><span class="dropdown-item groupRuleOption" data-bg=""><i class="fa-solid fa-square text-white fa-fw"></i> Clear group 'Or'</li>

                                                                    <li><span class="dropdown-item groupRuleOption" data-bg="bg-danger"><i class="fa-solid fa-square text-danger fa-fw"></i> Red group 'Or'</li>
                                                                    <li><span class="dropdown-item groupRuleOption" data-bg="bg-success"><i class="fa-solid fa-square text-success fa-fw"></i> Green group 'Or'</li>
                                                                    <li><span class="dropdown-item groupRuleOption" data-bg="bg-info"><i class="fa-solid fa-square text-info fa-fw"></i> Blue group 'Or'</li>
                                                                    <li><span class="dropdown-item groupRuleOption" data-bg="bg-warning"><i class="fa-solid fa-square text-warning fa-fw"></i> Yellow group 'Or'</li>
                                                                </ul>
                                                            </div>
                                                            <button type="button" class="btn btn-md btn-transparent my-1" id="removeRuleCondition" data-type="" data-form="" data-token=""><i class="fa-solid fa-minus fa-lg"></i></button>
                                                        </div>
                                                    </div>`);

    // $('.fieldRule:not(.select2-hidden-accessible)').select2({});
    initSelect2FieldRule(`#fieldRule${x}`, []);
    initSelect2OperatorRule(`#operatorRule${x}`, []);
});

$(document).on('click', '#removeRuleCondition', function (event) {
    const $this = $(this);
    Snackbar.close();
    $this.closest('div.fieldRuleRow').remove();
});

function initSelect2FieldRule(selector, preselectField) {
    $(selector).select2({
        dropdownParent: $('#globalAside'),
        placeholder: '-- Select --',
        allowClear: false,
        closeOnSelect: true,
        minimumResultsForSearch: 0,
        language: {
            searching: function () {
                return window.select2SearchingMessage;
            },
            noResults: function () {
                if (window.select2SearchingMessage === 'Type minimum 3 characters') {
                    return 'Type minimum 3 characters';
                }
                return 'No results found';
            }
        },
        ajax: {
            transport: function (params, success, failure) {
                const term = params.data.term || '';
                const page = params.data.page || 1;

                if (term.length > 0 && term.length < 3) {
                    window.select2SearchingMessage = 'Type minimum 3 characters';
                    setTimeout(() => {
                        $('.select2-results__option.loading-results').text(window.select2SearchingMessage);
                    }, 0);

                    return null;
                }

                window.select2SearchingMessage = 'Loading results...';
                const request = $.ajax({
                    url: '/proc_pur/getRuleFlow',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        term: term,
                        page: page,
                        dataForm: 'APPLICATION_PO_FORM',
                        dataType: 'FIELD',
                        company: $('#filterPoRuleCompany').val()
                    },
                    success: success,
                    error: failure
                });

                return request;
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                const processedResults = (data.data || []).map(function(item) {
                    return {
                        id: item.id,
                        text: item.text,
                        nextFlow: item.nextFlow,
                    };
                });

                return {
                    results: processedResults,
                    pagination: {
                        more: data.more || false
                    }
                };

                // return {
                //     results: data.data || [],
                //     pagination: {
                //         more: data.more || false
                //     }
                // };
            },
            delay: 250,
            cache: true
        },
        templateResult: formatResultRemote,
        templateSelection: function (data) {
            return data.text || data.id;
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    }).on('select2:select', function (e) {
        const selectedData = e.params.data;
        const $select = $(this);
        processNextFlowSelection($select, selectedData, 'select2:select');
    }).on('select2:clear', function (e) {
        $(this).removeAttr('data-nextflow');
        $(this).next('.select2-container').removeAttr('data-nextflow');
    });
    if(preselectField.length > 0) {
        preselectField.forEach((item, index) => {
            let optionExists = $(`#fieldRule${index} option[value='${item.id}']`).length > 0;
            if (!optionExists) {
                let option = new Option(item.text, item.id, true, true);
                // $(option).attr('data-nextflow', item.nextFlow);
                $(`#fieldRule${index}`).append(option);
            }
            if (item.nextFlow) $(`#fieldRule${index}`).attr('data-nextflow', item.nextFlow);
            processNextFlowSelection($(`#fieldRule${index}`), null, 'select2:select');
        });
    }
}

function initSelect2OperatorRule(selector, preselectOperator) {
    const optionOperator = [
            {id: '<', text: '<', description1: 'Less than'},
            {id: '=', text: '=', description1: 'Equal to'},
            {id: '>', text: '>', description1: 'Greater than'},
            {id: '<=', text: '<=', description1: 'Less than or equal to'},
            {id: '>=', text: '>=', description1: 'Greater than or equal to'},
            {id: '<>', text: '<>', description1: 'Not equal to'},
    ];

    $(selector).select2({
        dropdownParent: $('#globalAside'),
        placeholder: 'Required',
        multiple: false,
        data: optionOperator,
        templateResult: formatResultRemote,
        templateSelection: function (data) {
            return data.text || data.id;
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });
    if(preselectOperator.length > 0) {
        preselectOperator.forEach((item, index) => {
            $(`#operatorRule${index}`).val(item.id).trigger('change');
        });
    }
}

async function actionTable(token, action) {
    var $this = $(this);
    if(action == 'DISABLE_ORDER_FORM_APPLICANT' || action == 'ENABLE_ORDER_FORM_APPLICANT' || action == 'DELETE_ORDER_FORM_APPLICANT') {
        $('.modal').modal('hide');
        $('#modalMessageTitle').html('');
        let btnClass;
        if(action == 'DISABLE_ORDER_FORM_APPLICANT') {
            $('#modalMessageBody').html(`<div class="swal2-icon swal2-warning swal2-icon-show d-flex mt-0 mb-3"><div class="swal2-icon-content">!</div></div>
                                        <h6 class="swal2-title text-center" id="swal2-title" style="display: block;">Disable Order Form Applicant Flow?</h6>`);
            btnClass = `btn-warning`;
        }
        else if(action == 'DELETE_ORDER_FORM_APPLICANT') {
            $('#modalMessageBody').html(`<div class="swal2-icon swal2-error swal2-icon-show d-flex mt-0 mb-3"><div class="swal2-icon-content">!</div></div>
                                        <h6 class="swal2-title text-center" id="swal2-title" style="display: block;">Delete Order Form Applicant Flow?</h6>`);
            btnClass = `btn-danger`;
        }
        else {
            $('#modalMessageBody').html(`<div class="swal2-icon swal2-success swal2-icon-show d-flex mt-0 mb-3"><div class="swal2-icon-content">!</div></div>
                <h6 class="swal2-title text-center" id="swal2-title" style="display: block;">Enable Order Form Applicant Flow?</h6>`);
            btnClass = `btn-success`;
        }

        $('#modalMessageFooter').html(`<div class="container-fluid p-0">
                                            <div class="row justify-content-center w-100 mx-0">
                                                <div class="col-6 d-flex justify-content-center mb-2 px-0">
                                                    <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                        <i class="fas fa-xmark"></i> Close
                                                    </button>
                                                </div>
                                                <div class="col-6 d-flex justify-content-center mb-2 px-0">
                                                    <button type="button" class="btn ${btnClass} w-100 asideButton" data-type="${action}" data-token="${token}" title="Yes">
                                                        Yes <i class="fa-solid fa-arrow-right"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>`);

        $('#modalMessageDialog').removeClass('top-20').addClass('top-20');
        $('#modalMessageDialog').removeClass('modal-dialog-scrollable');
        $('#modalMessage').modal('show');
    }
    else if(action == 'DISABLE_PO_APPROVAL_RULE' || action == 'ENABLE_PO_APPROVAL_RULE' || action == 'DELETE_PO_APPROVAL_RULE') {
        $('.modal').modal('hide');
        $('#modalMessageTitle').html('');
        let btnClass;
        if(action == 'DISABLE_PO_APPROVAL_RULE') {
            $('#modalMessageBody').html(`<div class="swal2-icon swal2-warning swal2-icon-show d-flex mt-0 mb-3"><div class="swal2-icon-content">!</div></div>
                                        <h6 class="swal2-title text-center" id="swal2-title" style="display: block;">Disable PO Approval Rule?</h6>`);
            btnClass = `btn-warning`;
        }
        else if(action == 'DELETE_PO_APPROVAL_RULE') {
            $('#modalMessageBody').html(`<div class="swal2-icon swal2-error swal2-icon-show d-flex mt-0 mb-3"><div class="swal2-icon-content">!</div></div>
                                        <h6 class="swal2-title text-center" id="swal2-title" style="display: block;">Delete PO Approval Rule?</h6>`);
            btnClass = `btn-danger`;
        }
        else {
            $('#modalMessageBody').html(`<div class="swal2-icon swal2-success swal2-icon-show d-flex mt-0 mb-3"><div class="swal2-icon-content">!</div></div>
                <h6 class="swal2-title text-center" id="swal2-title" style="display: block;">Enable PO Approval Rule?</h6>`);
            btnClass = `btn-success`;
        }

        $('#modalMessageFooter').html(`<div class="container-fluid p-0">
                                            <div class="row justify-content-center w-100 mx-0">
                                                <div class="col-6 d-flex justify-content-center mb-2 px-0">
                                                    <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                        <i class="fas fa-xmark"></i> Close
                                                    </button>
                                                </div>
                                                <div class="col-6 d-flex justify-content-center mb-2 px-0">
                                                    <button type="button" class="btn ${btnClass} w-100 asideButton" data-type="${action}" data-token="${token}" title="Yes">
                                                        Yes <i class="fa-solid fa-arrow-right"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>`);

        $('#modalMessageDialog').removeClass('top-20').addClass('top-20');
        $('#modalMessageDialog').removeClass('modal-dialog-scrollable');
        $('#modalMessage').modal('show');
    }
    else if(action == 'DISABLE_INSPECTOR' || action == 'ENABLE_INSPECTOR' || action == 'DELETE_INSPECTOR') {
        $('.modal').modal('hide');
        $('#modalMessageTitle').html('');
        let btnClass;
        if(action == 'DISABLE_INSPECTOR') {
            $('#modalMessageBody').html(`<div class="swal2-icon swal2-warning swal2-icon-show d-flex mt-0 mb-3"><div class="swal2-icon-content">!</div></div>
                                        <h6 class="swal2-title text-center" id="swal2-title" style="display: block;">Disable Inspection Flow?</h6>`);
            btnClass = `btn-warning`;
        }
        else if(action == 'DELETE_INSPECTOR') {
            $('#modalMessageBody').html(`<div class="swal2-icon swal2-error swal2-icon-show d-flex mt-0 mb-3"><div class="swal2-icon-content">!</div></div>
                                        <h6 class="swal2-title text-center" id="swal2-title" style="display: block;">Delete Inspection Flow?</h6>`);
            btnClass = `btn-danger`;
        }
        else {
            $('#modalMessageBody').html(`<div class="swal2-icon swal2-success swal2-icon-show d-flex mt-0 mb-3"><div class="swal2-icon-content">!</div></div>
                <h6 class="swal2-title text-center" id="swal2-title" style="display: block;">Enable Inspection Flow?</h6>`);
            btnClass = `btn-success`;
        }

        $('#modalMessageFooter').html(`<div class="container-fluid p-0">
                                            <div class="row justify-content-center w-100 mx-0">
                                                <div class="col-6 d-flex justify-content-center mb-2 px-0">
                                                    <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                        <i class="fas fa-xmark"></i> Close
                                                    </button>
                                                </div>
                                                <div class="col-6 d-flex justify-content-center mb-2 px-0">
                                                    <button type="button" class="btn ${btnClass} w-100 asideButton" data-type="${action}" data-token="${token}" title="Yes">
                                                        Yes <i class="fa-solid fa-arrow-right"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>`);

        $('#modalMessageDialog').removeClass('top-20').addClass('top-20');
        $('#modalMessageDialog').removeClass('modal-dialog-scrollable');
        $('#modalMessage').modal('show');
    }
}

async function getDepartment(params) {
    const companyId = params['companyId'];
    const departmentId = params['departmentId'];
    try {
        const response = await fetch(`/doc_approval/getDepartment?companyId=${companyId}&departmentId=${departmentId}`, {
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
            let options = '';
            if(departmentId === 'ALL') {
                options = [
                    { id: '', text: '' },
                    { id: 'ALL', text: '-- ALL DEPARTMENT --' }
                ].concat(
                    result.map(row => ({
                        id: row.departmentId,
                        text: row.departmentName,
                        description1: `Dept. ID : ${(row.departmentId) ? row.departmentId : '--'}`,
                        description2: `Cost center : ${(row.costCenter) ? row.costCenter : '--'}`,
                    }))
                );
            }
            else {
                options = result.map(row => ({
                                                id: row.departmentId,
                                                text: row.departmentName,
                                                description1: `Dept. ID : ${(row.departmentId) ? row.departmentId : '--'}`,
                                                description2: `Cost center : ${(row.costCenter) ? row.costCenter : '--'}`,
                                        }));
            }
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
                    window.location.href = result.redirect_uri;
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

async function getEmployee(params) {
    try {
        const response = await fetch(`/doc_approval/getEmployee?dataForm=${params['dataForm']}&dataType=${params['dataType']}&company=${params['company']}`, {
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
                id: row.id,
                text: row.text,
                description1: row.desc,
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