var maxDateCurrentOptions = {
    locale: "en-US",
    inputDateFormat: (date) => dayjs(date).locale("en").format("DD-MM-YYYY"),
    inputDateParse: (date) => dayjs(date, "DD-MM-YYYY", "id").toDate(),
    maxDate: dayjs(new Date()),
    showAdjacementDays: false,
};

var maxDateNullOptions = {
    locale: "en-US",
    inputDateFormat: (date) => dayjs(date).locale("en").format("DD-MM-YYYY"),
    inputDateParse: (date) => dayjs(date, "DD-MM-YYYY", "id").toDate(),
    showAdjacementDays: false,
};
let popperInstance;
const commentBuffer = [];
var selectedFiles = [];
var selectedFilesProperties = [];
var maxFileSize = 5 * 1024 * 1024;

var preselectValue = [];
var archivePurchasingTable = (function () {
    let $container = $("#archivePurchasingTableContainer");
    let containerHeight = $container.height();
    let headerHeight = 190;
    let footerFixedHeight = 60;
    let scrollHeight = containerHeight - headerHeight;
    // let tableContainerHeight = $container.find('.table-container').height();
    var tableId = "archivePurchasingTable";
    var table = $(`#${tableId}`)
        .DataTable({
            scrollY: scrollHeight + "px",
            scrollCollapse: true,
            paging: true,
            processing: true,
            serverSide: true,
            responsive: false,
            ordering: false,
            dom: '<"row"<"col-sm-12"tr>><"dt-footer-fixed"<"d-flex justify-content-between align-items-center mt-2"ilp>>',
            pageLength: 25,

            ajax: {
                url: "purchasingHistory",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                data: function (d) {
                    let $form = $(`#${tableId}`)
                        .closest(".table-container")
                        .find(".formSearchTable");
                    let formData = $form.serializeArray();
                    formData.forEach(function (item) {
                        d[item.name] = item.value;
                    });
                },
            },
            drawCallback: function (settings) {
                $(this).parent().scrollTop(0);
                let tableContainerHeight = $(this)
                    .closest(".table-container")
                    .height();
                let tableFormHeight = $(this)
                    .closest(".table-container")
                    .find("form.form-horizontal")
                    .outerHeight(true);
                let theadHeight = $(this)
                    .closest(".dt-scroll")
                    .find(".dt-scroll-head")
                    .outerHeight(true);
                let footerFixedHeight = $(this)
                    .closest(".dt-container")
                    .find(".dt-footer-fixed")
                    .height();
                let scrollHeight =
                    tableContainerHeight -
                    tableFormHeight -
                    theadHeight -
                    footerFixedHeight;
                $(this)
                    .parent()
                    .css({
                        height: scrollHeight + "px",
                        "max-height": scrollHeight + "px",
                    });
            },
            columns: [
                {
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    searchable: false,
                    orderable: false,
                    className: "text-center",
                    width: "4%",
                },
                {
                    data: "applicationTitle",
                    type: "string",
                    searchable: false,
                    orderable: false,
                    width: "19%",
                },
                {
                    data: "poNumber",
                    type: "string",
                    searchable: false,
                    orderable: false,
                    width: "13%",
                },
                {
                    data: "orderDate",
                    type: "string",
                    searchable: false,
                    orderable: false,
                    width: "9%",
                },
                {
                    data: "vendor",
                    type: "string",
                    searchable: false,
                    orderable: false,
                    width: "18%",
                },
                {
                    data: "invoiceNumber",
                    type: "string",
                    searchable: false,
                    orderable: false,
                    width: "12%",
                },
                {
                    data: "totalAmount",
                    type: "string",
                    searchable: false,
                    orderable: false,
                    className: "text-end",
                    width: "15%",
                },
                {
                    data: "status",
                    type: "string",
                    searchable: false,
                    orderable: false,
                    className: "text-center",
                    width: "5%",
                },
                {
                    data: "action",
                    name: "action",
                    searchable: false,
                    orderable: false,
                    className: "text-center",
                    width: "5%",
                },
            ],
        })
        .on("preXhr.dt", function (e, settings, data) {
            $(`#${settings.sTableId} tbody`).html(
                generateSkeletonRows(settings)
            );
        })
        .on("xhr.dt", function (e, settings, data) {
            $(`#${settings.sTableId} tbody`).find(".skeleton-row").remove();
        })
        .on("error.dt", function (e, settings, data) {
            $(`#${settings.sTableId} tbody`).find(".skeleton-row").remove();
        });

    let customSearch = $(`#${tableId}_wrapper`).prev(".formSearchTable");
    let wrapper = $(`#${tableId}_wrapper .dt-search`);
    wrapper.empty();
    wrapper.append(customSearch);
    return table;
})();

$(document).on("change", "#selectCompany", function (event) {
    archivePurchasingTable.ajax.reload();
});

$(document).on("change", "#applicant", function (event) {
    archivePurchasingTable.ajax.reload();
});

async function actionTable(token, action) {
    if (action == "VIEW") {
        const params = {
            token: token,
            source: "WEB",
            form: "APPLICATION_FORM",
        };
        viewForm(params);
    }
}

async function viewForm(params) {
    const token = params["token"];
    const form = params["form"];
    const source = params["source"];
    Snackbar.close();
    if (form == "FORM") {
        $("#modalDocumentFormBody")
            .removeClass("pt-0 pb-5 p-0 overflow-x-hidden overflow-y-hidden")
            .addClass("pt-0 pb-5 overflow-x-hidden overflow-y-auto");
        return false;
    } else if (
        form == "ORDER_FORM" ||
        form == "COMPARISON_FORM" ||
        form == "APPLICATION_FORM" ||
        form == "APPLICATION_FORM_COMPLETED"
    ) {
        // if(form == 'ORDER_FORM'){
        $("#modalDocumentFormBody")
            .removeClass("pt-0 pb-5 p-0 overflow-x-hidden overflow-y-hidden")
            .addClass("p-0 overflow-y-hidden overflow-x-hidden");
        // }
        // else {
        //     $('#modalDocumentFormBody').removeClass('pt-0 pb-5 p-0 overflow-x-hidden overflow-y-hidden').addClass('p-0 overflow-y-auto overflow-x-hidden');
        // }
        $("#modalDocumentFormTitle").html(
            `<div class="skeleton mb-0" style="width: 250px; height: 24px"></div>`
        );
        $(".actionHeaderContainer").html(
            `<div class="skeleton mb-0" style="width: 100px; height: 24px"></div>`
        );
        $("#modalDocumentFormBody").html(`<div class="row min-vh-75">
                                    <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                        <div class="center-container">
                                            <div class="stripes-red-blue stripes-red-blue-md"></div>
                                            <div class="d-block fs-7 mt-2">Loading...</div>
                                        </div>
                                    </div>
                                </div>`);
        $("#modalDocumentFormFooter").html(`<div class="container-fluid p-0">
                                                <div class="row justify-content-center w-100 mx-0">
                                                    <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                        <div class="skeleton mb-0 w-100 w-md-auto me-md-2"></div>
                                                    </div>
                                                    <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-start mb-2 px-0">
                                                            <div class="skeleton mb-0 w-100 w-md-auto me-md-2"></div>
                                                    </div>
                                                </div>
                                            </div>`);

        if (!$("#modalDocumentForm").hasClass("show")) {
            $(".modal").modal("hide");
            $("#modalDocumentForm").modal("show");
        }
    } else if (form == "ORDER_FORM_TAB") {
        $("#modalDocumentFormBody")
            .removeClass("pt-0 pb-5 p-0 overflow-y-hidden")
            .addClass("p-0 overflow-y-hidden");
        if ($.trim($("#orderFormContent").html()).length === 0) {
            $("#orderFormContent").html(`<div class="row min-vh-75">
                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                    <div class="center-container">
                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                        <div class="d-block fs-7 mt-2">Loading...</div>
                    </div>
                </div>
            </div>`);
        } else {
            return false;
        }
    } else if (form == "COMPARISON_FORM_TAB") {
        $("#modalDocumentFormBody")
            .removeClass("pt-0 pb-5 p-0 overflow-y-hidden")
            .addClass("p-0 overflow-y-hidden");
        if ($.trim($("#comparisonFormContent").html()).length === 0) {
            $("#comparisonFormContent").html(`<div class="row min-vh-75">
                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                    <div class="center-container">
                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                        <div class="d-block fs-7 mt-2">Loading...</div>
                    </div>
                </div>
            </div>`);
        } else {
            return false;
        }
    }

    try {
        const response = await fetch(
            `/proc_pur/viewForm?source=${source}&form=${form}&token=${token}`,
            {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                    Referer: window.location.href,
                },
            }
        );

        const data = await response.json();
        if (response.status === 401) {
            $(".card-footer-fixed").addClass("border-top-0");
            let countdown = 5;
            Snackbar.show({
                pos: "bottom-center",
                duration: "6000",
                text: `<i class="fa-solid fa-circle-exclamation fa-lg fa-fw text-warning"></i> Session expired, please refresh this page or it will automatically refresh in <span id="snackbar-countdown">${countdown}</span> seconds.`,
            });

            let countdownInterval = setInterval(() => {
                countdown--;
                document.getElementById("snackbar-countdown").textContent =
                    countdown;
                if (countdown < 0) {
                    clearInterval(countdownInterval);
                    window.location.href = data.redirect_uri;
                }
            }, 1000);
        } else if (
            form == "ORDER_FORM" ||
            form == "COMPARISON_FORM" ||
            form == "APPLICATION_FORM" ||
            form == "APPLICATION_FORM_COMPLETED"
        ) {
            if (data.status === 200) {
                $("#modalDocumentFormTitle").html(data.data.title);
                $(".actionHeaderContainer").html(`
                    <button class="btn btn-default btn-sm d-none d-md-inline actionBtnHeader" data-token="${data.data.tokenAttachment}" data-type="PRINT" data-selectedversion="${data.data.selectedVersionLabel}" title="Print Documents"><i class="fa-solid fa-print fa-fw"></i></button>
                    <button class="btn btn-default btn-sm actionBtnHeader" data-token="${data.data.tokenAttachment}" data-type="DOWNLOAD" data-selectedversion="${data.data.selectedVersionLabel}" title="Downloads"><i class="fa-solid fa-arrow-down-to-line fa-fw"></i></button>
                    <button class="btn btn-default btn-sm actionBtnHeader" data-token="${data.data.tokenAttachment}" data-type="ATTACHMENTS" data-type-flow="PURCHASING" data-selectedversion="${data.data.selectedVersionLabel}"><i class="fa-solid fa-bars-progress"></i> View Attachments</button>`);
                $("#modalDocumentFormBody").html(
                    `<object class="w-100 h-100" id="subfile_frame" data="/framePdf?token=${data.data.tokenForm}" type="text/html"><param name="allowfullscreen" value="true"></object>`
                );
                // {$('#modalDocumentFormBody').html(data.data.form);}
                $("#modalDocumentFormFooter").html(data.data.footerButton);
                $("#modalDocumentFormBody").scrollTop(0);
                $(".table-responsive").scrollLeft(0);
                $(".autosize").autosize().trigger("change");
            } else if (data.status === 404) {
                $("#modalDocumentFormTitle").html("Detail Form");
                $(".actionHeaderContainer").html("");
                $("#modalDocumentFormBody").html(data.data.form);
                $("#modalDocumentFormFooter").html(data.data.footerButton);
            } else {
                $("#modalDocumentFormTitle").html("Detail Form");
                $(".actionHeaderContainer").html("");
                $("#modalDocumentFormBody").html(`<div class="row min-vh-75">
                                                    <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                        <div class="center-container">
                                                            <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                            <div class="d-block fs-7 mt-2">Failed to get data</div>
                                                        </div>
                                                    </div>
                                                </div>`);
                $("#modalDocumentFormFooter")
                    .html(`<div class="container-fluid p-0">
                                                        <div class="row justify-content-center w-100 mx-0">
                                                            <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                                <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                    <i class="fas fa-xmark"></i> Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>`);
            }
        } else if (form == "ORDER_FORM_TAB" || form == "COMPARISON_FORM_TAB") {
            if (data.status === 200) {
                // $('#orderFormContent').html(data.data.form);
                // $('#modalDocumentFormBody').removeClass('pt-0 pb-5 p-0 overflow-hidden').addClass('p-0');
                $("#modalDocumentFormBody")
                    .removeClass("pt-0 pb-5 p-0 overflow-y-hidden")
                    .addClass("p-0 overflow-y-hidden");
                if (form == "ORDER_FORM_TAB") {
                    $("#orderFormContent").html(
                        `<object class="w-100 h-100" id="subfile_frame" data="/framePdf?token=${data.data.tokenForm}" type="text/html"><param name="allowfullscreen" value="true"></object>`
                    );
                } else {
                    $("#comparisonFormContent").html(
                        `<object class="w-100 h-100" id="subfile_frame" data="/framePdf?token=${data.data.tokenForm}" type="text/html"><param name="allowfullscreen" value="true"></object>`
                    );
                }
            } else {
                let formMessage = "Failed to get data";
                if (response.status === 404) {
                    formMessage = "Form not available";
                }

                if (form == "ORDER_FORM_TAB") {
                    $("#orderFormContent").html(`<div class="row min-vh-75">
                        <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                            <div class="center-container">
                                <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                <div class="d-block fs-7 mt-2">${formMessage}</div>
                            </div>
                        </div>
                    </div>`);
                } else {
                    $("#comparisonFormContent")
                        .html(`<div class="row min-vh-75">
                        <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                            <div class="center-container">
                                <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                <div class="d-block fs-7 mt-2">${formMessage}</div>
                            </div>
                        </div>
                    </div>`);
                }
            }
        }
    } catch (error) {
        if (
            form == "ORDER_FORM" ||
            form == "COMPARISON_FORM" ||
            form == "APPLICATION_FORM" ||
            form == "APPLICATION_FORM_COMPLETED"
        ) {
            $("#modalDocumentFormTitle").html("Detail Form");
            $(".actionHeaderContainer").html("");
            $("#modalDocumentFormBody").html(`<div class="row min-vh-75">
                                                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                    <div class="center-container">
                                                        <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                        <div class="d-block fs-7 mt-2">Failed to get data</div>
                                                    </div>
                                                </div>
                                            </div>`);
            $("#modalDocumentFormFooter")
                .html(`<div class="container-fluid p-0">
                                                    <div class="row justify-content-center w-100 mx-0">
                                                        <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                            <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                <i class="fas fa-xmark"></i> Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>`);
        } else if (form == "ORDER_FORM_TAB") {
            $("#orderFormContent").html(`<div class="row min-vh-75">
                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                    <div class="center-container">
                        <i class="fa-light fa-file-circle-xmark fs-1"></i>
                        <div class="d-block fs-7 mt-2">Failed to get data</div>
                    </div>
                </div>
            </div>`);
        } else if (form == "COMPARISON_FORM_TAB") {
            $("#comparisonFormContent").html(`<div class="row min-vh-75">
                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                    <div class="center-container">
                        <i class="fa-light fa-file-circle-xmark fs-1"></i>
                        <div class="d-block fs-7 mt-2">Failed to get data</div>
                    </div>
                </div>
            </div>`);
        }
    } finally {
    }
}

$(document)
    .off("click", ".actionBtnHeader")
    .on("click", ".actionBtnHeader", async function (event) {
        let $this = $(this);
        if ($this.attr("data-type") == "ATTACHMENTS") {
            asideHide();
            $(".aside-title").html(
                `Attachment List ${$this.attr("data-selectedversion")}`
            );
            $(".aside-content")
                .html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
                        <div class="container-content pb-5">
                            <div class="full-height-column-wrapper d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                <div class="center-container">
                                    <div class="stripes-red-blue stripes-red-blue-md"></div>
                                    <div class="d-block fs-7 mt-2">Loading...</div>
                                </div>
                            </div>
                        </div>
                    </form>`);

            $(".overlay-aside").addClass("show").trigger("shown");
            $("#globalAside").addClass("show").trigger("shown");
            $("body").addClass("overflow-hidden");
            $("#asideDetailForm").scrollTop(0);
            $(".autosize").autosize({ append: "\n" });

            $(".asideFooterBtn")
                .html(`<div class="col-12 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
                                    </div>`);

            const params = {
                type: $this.attr("data-type-flow"),
                token: $this.attr("data-token"),
            };
            const attachmentList = await getAttachment(params);
            $("#asideForm").html(attachmentList.data.form);
        } else if (
            $this.attr("data-type") == "PROGRESS" ||
            $this.attr("data-type") == "INSPECTION"
        ) {
            let url = "",
                title = "";
            if ($this.attr("data-type") == "PROGRESS") {
                title = "Progress";
                url = `/doc_approval/getProgress?token=${$this.attr(
                    "data-token"
                )}`;
            } else if ($this.attr("data-type") == "INSPECTION") {
                title = "Inspection Form";
                url = `/doc_approval/getInspection?token=${$this.attr(
                    "data-token"
                )}`;
            }

            asideHide();
            $(".aside-title").html(title);
            $(".aside-content")
                .html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
            <div class="container-content pb-5">
                <div class="full-height-column-wrapper d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                    <div class="center-container">
                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                        <div class="d-block fs-7 mt-2">Loading...</div>
                    </div>
                </div>
            </div>
        </form>`);

            $(".overlay-aside").addClass("show").trigger("shown");
            if ($this.attr("data-type") == "PROGRESS") {
                $("#globalAside").addClass("show aside-lg").trigger("shown");
            } else {
                $("#globalAside").addClass("show").trigger("shown");
            }

            $("body").addClass("overflow-hidden");
            $("#asideDetailForm").scrollTop(0);
            $(".asideFooterBtn")
                .html(`<div class="col-12 d-flex justify-content-center justify-content-md-end mb-2 px-0">
            <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
        </div>`);

            try {
                const response = await fetch(url, {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                        Accept: "application/json",
                        Referer: window.location.href,
                    },
                });

                const result = await response.json();
                if (response.status === 200) {
                    $("#asideForm").html(result.data.form);
                } else {
                    console.error("HTTP Error:", response.status);
                }
            } catch (error) {
                return;
            }
        } else if (
            $this.attr("data-type") == "PRINT" ||
            $this.attr("data-type") == "DOWNLOAD"
        ) {
            asideHide();
            let asideTitle = "",
                asideButton = "";
            if ($this.attr("data-type") == "PRINT") {
                asideTitle = "Print Documents";
                asideButton = `<button type="button" class="btn btn-info w-100 w-md-auto asideButton" data-type="${$this.attr(
                    "data-type"
                )}" data-token="" title="Print Selected Documents">Print</button>`;
            } else {
                asideTitle = "Download";
                asideButton = `<button type="button" class="btn btn-danger w-100 w-md-auto asideButton" data-type="${$this.attr(
                    "data-type"
                )}" data-token="" title="Download Selected">Download</button>`;
            }

            $(".aside-title").html(`${asideTitle}`);
            $(".aside-content")
                .html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
            <div class="container-content pb-5">
                <div class="full-height-column-wrapper d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                    <div class="center-container">
                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                        <div class="d-block fs-7 mt-2">Loading...</div>
                    </div>
                </div>
            </div>
        </form>`);

            $(".overlay-aside").addClass("show").trigger("shown");
            $("#globalAside").addClass("show").trigger("shown");
            $("body").addClass("overflow-hidden");
            $("#asideDetailForm").scrollTop(0);
            $(".autosize").autosize({ append: "\n" });

            $(".asideFooterBtn")
                .html(`<div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
                                    </div>
                                    <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        ${asideButton}
                                    </div>`);

            const params = {
                dataType: $this.attr("data-type"),
                token: $this.attr("data-token"),
            };
            const documentList = await getDocumentList(params);
            $("#asideForm").html(documentList.data.form);
        } else if ($this.attr("data-type") == "EXPORT_DATA_TABLE") {
            asideHide();
            $("#asideDetailForm").html(`<div class="row min-vh-75">
                                        <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                            <div class="center-container">
                                                <div class="stripes-red-blue stripes-red-blue-md"></div>
                                                <div class="d-block fs-7 mt-2">Loading...</div>
                                            </div>
                                        </div>
                                    </div>`);
            $("#globalAside")
                .find(".aside-header")
                .find(".aside-title")
                .html($this.attr("title"));
            $(".overlay-aside").addClass("show").trigger("shown");
            $("#globalAside").addClass("show aside-lg").trigger("shown");
            $("body").addClass("overflow-hidden");
            $("#asideDetailForm").scrollTop(0);

            const tableId = $this.attr("data-table");
            const formContainer = $(`#${tableId}`)
                .closest(".table-container")
                .find("form.formSearchTable");
            const table = $(`#${tableId}`).DataTable();
            let filterCompany = formContainer
                .find('select[name="company"]')
                .html();
            let company = formContainer.find('select[name="company"]').val();
            let applicant = formContainer
                .find('select[name="applicant"]')
                .val();
            let status = formContainer.find('input[name="status"]').val();
            let orderStartDate = formContainer
                .find('input[name="orderStartDate"]')
                .val();
            let orderEndDate = formContainer
                .find('input[name="orderEndDate"]')
                .val();
            let receivedStartDate = formContainer
                .find('input[name="receivedStartDate"]')
                .val();
            let receivedEndDate = formContainer
                .find('input[name="receivedEndDate"]')
                .val();
            let invStartDate = formContainer
                .find('input[name="invStartDate"]')
                .val();
            let invEndDate = formContainer
                .find('input[name="invEndDate"]')
                .val();
            let sortBy = formContainer.find('input[name="sortBy"]').val();
            let startAmount = formContainer
                .find('input[name="startAmount"]')
                .val();
            let endAmount = formContainer.find('input[name="endAmount"]').val();
            let ccy = formContainer.find('input[name="ccy"]').val();

            if (orderStartDate && orderEndDate) {
                orderStartDate = new Date(orderStartDate);
                orderEndDate = new Date(orderEndDate);
            }

            if (receivedStartDate && receivedEndDate) {
                receivedStartDate = new Date(receivedStartDate);
                receivedEndDate = new Date(receivedEndDate);
            }

            if (invStartDate && invEndDate) {
                invStartDate = new Date(invStartDate);
                invEndDate = new Date(invEndDate);
            }

            startAmount =
                startAmount !== "" ? currencyFormat(startAmount, 0) : "";
            endAmount = endAmount !== "" ? currencyFormat(endAmount, 0) : "";

            let form = `<div class="form-group mb-3">
                        <label for="exportFormat" class="form-label">Format Data<span class="required"></span> :</label>
                        <select class="select2 select2Export" name="exportFormat" id="exportFormat">
                            <option></option>
                            <option value="xlsx" selected>XLSX</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="exportCompany" class="form-label">Company<span class="required"></span> :</label>
                        <select class="select2" name="exportCompany" id="exportCompany"></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="exportApplicant" class="form-label">PO Applicant<span class="required"></span> :</label>
                        <div class="skeleton" id="exportApplicant"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="exportPoStatus" class="form-label">PO Status<span class="required"></span> :</label>
                        <select class="select2 select2Export" name="exportPoStatus" id="exportPoStatus">
                            <option></option>
                            <option value="22" selected>INVOICED</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="exportSortBy" class="form-label">Sort By<span class="required"></span> :</label>
                        <select class="select2 select2Export" name="exportSortBy" id="exportSortBy">
                            <option></option>
                            <option value="LATEST">LATEST ARCHIVED</option>
                            <option value="OLDEST">OLDEST ARCHIVED</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="exportOrderDate" class="form-label">Order Date :<span class="fst-italic fw-normal fs-9"> (Left blank if selecting all dates)</span></label>
                        <div class="form-group" id="exportOrderDate" data-coreui-name="exportOrderDate" data-coreui-toggle="date-range-picker" data-coreui-date="" data-coreui-timepicker="false"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="exportReceivedDate" class="form-label">Received Date :<span class="fst-italic fw-normal fs-9"> (Left blank if selecting all dates)</span></label>
                        <div class="form-group" id="exportReceivedDate" data-coreui-name="exportReceivedDate" data-coreui-toggle="date-range-picker" data-coreui-date="" data-coreui-timepicker="false"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="exportInvDate" class="form-label">Invoice Date :<span class="fst-italic fw-normal fs-9"> (Left blank if selecting all dates)</span></label>
                        <div class="form-group" id="exportInvDate" data-coreui-name="exportInvDate" data-coreui-toggle="date-range-picker" data-coreui-date="" data-coreui-timepicker="false"></div>
                    </div>`;

            $("#asideDetailForm").html(
                `<form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideFormExportData">${form}</form>`
            );

            $("#exportCompany").html(filterCompany);
            $("#exportCompany")
                .select2({
                    dropdownParent: $("#globalAside"),
                    allowClear: false,
                    placeholder: "-- Select --",
                })
                .on("select2:select", async function (e) {
                    if (
                        $("#exportApplicant").hasClass(
                            "select2-hidden-accessible"
                        )
                    ) {
                        $("#exportApplicant")
                            .val(null)
                            .empty()
                            .trigger("change");
                        $("#exportApplicant").select2("destroy");
                    }

                    $("#exportApplicant").replaceWith(
                        `<div class="skeleton" id="exportApplicant"></div>`
                    );
                    let optionApplicant = await getUserSelection({
                        type: "PO_APPLICANT",
                        companyId: $(this).val(),
                        selectedId: applicant,
                    });
                    $("#exportApplicant").replaceWith(
                        `<select class="select2" name="exportApplicant" id="exportApplicant"></select>`
                    );
                    $("#exportApplicant").select2({
                        dropdownParent: $("#globalAside"),
                        allowClear: false,
                        placeholder: "-- Select --",
                        data: optionApplicant,
                        templateResult: formatResultRemote,
                        templateSelection: function (data) {
                            return data.text || data.id;
                        },
                        escapeMarkup: function (markup) {
                            return markup;
                        },
                    });
                })
                .val(company)
                .trigger("change");

            $("#exportFormat").select2({
                dropdownParent: $("#globalAside"),
                allowClear: false,
                placeholder: "-- Select --",
            });

            $("#exportPoStatus").select2({
                dropdownParent: $("#globalAside"),
                allowClear: false,
                placeholder: "-- Select --",
            });

            $("#exportSortBy")
                .select2({
                    dropdownParent: $("#globalAside"),
                    allowClear: false,
                    placeholder: "-- Select --",
                })
                .val(sortBy)
                .trigger("change");

            if (startAmount != "" && endAmount != "") {
                if (ccy === "ALL" || ccy === "IDR") {
                    $("#filterAmountCcy").val("IDR").trigger("change");
                } else {
                    $("#filterAmountCcy").val(ccy).trigger("change");
                }
            }

            const optionsOrderDatePicker = {
                locale: "en-US",
                inputDateFormat: (date) =>
                    dayjs(date).locale("en").format("DD-MM-YYYY"),
                inputDateParse: (date) =>
                    dayjs(date, "DD-MM-YYYY", "id").toDate(),
                // minDate: dayjs(new Date()),
                maxDate: dayjs(new Date()),
                showAdjacementDays: false,
                container: "#globalAside",
                placement: "top-start",
                startDate: orderStartDate,
                endDate: orderEndDate,
                ranges: {
                    Today: [new Date(), new Date()],
                    Yesterday: [
                        new Date(new Date().setDate(new Date().getDate() - 1)),
                        new Date(new Date().setDate(new Date().getDate() - 1)),
                    ],
                    "Last 7 Days": [
                        new Date(new Date().setDate(new Date().getDate() - 6)),
                        new Date(new Date()),
                    ],
                    "Last 30 Days": [
                        new Date(new Date().setDate(new Date().getDate() - 29)),
                        new Date(new Date()),
                    ],
                    "Last 90 Days": [
                        new Date(new Date().setDate(new Date().getDate() - 89)),
                        new Date(new Date()),
                    ],
                    "This Month": [
                        new Date(new Date().setDate(1)),
                        new Date(
                            new Date().getFullYear(),
                            new Date().getMonth() + 1,
                            0
                        ),
                    ],
                    "Last Month": [
                        new Date(
                            new Date().getFullYear(),
                            new Date().getMonth() - 1,
                            1
                        ),
                        new Date(
                            new Date().getFullYear(),
                            new Date().getMonth(),
                            0
                        ),
                    ],
                    "This Year": [
                        new Date(new Date().getFullYear(), 0),
                        new Date(new Date()),
                    ],
                    "Last Year": [
                        new Date(new Date().getFullYear() - 1, 0),
                        new Date(new Date().getFullYear(), 0, 0),
                    ],
                },
            };

            const optionsReceivedDatePicker = {
                locale: "en-US",
                inputDateFormat: (date) =>
                    dayjs(date).locale("en").format("DD-MM-YYYY"),
                inputDateParse: (date) =>
                    dayjs(date, "DD-MM-YYYY", "id").toDate(),
                // minDate: dayjs(new Date()),
                maxDate: dayjs(new Date()),
                showAdjacementDays: false,
                container: "#globalAside",
                placement: "top-start",
                startDate: receivedStartDate,
                endDate: receivedEndDate,
                ranges: {
                    Today: [new Date(), new Date()],
                    Yesterday: [
                        new Date(new Date().setDate(new Date().getDate() - 1)),
                        new Date(new Date().setDate(new Date().getDate() - 1)),
                    ],
                    "Last 7 Days": [
                        new Date(new Date().setDate(new Date().getDate() - 6)),
                        new Date(new Date()),
                    ],
                    "Last 30 Days": [
                        new Date(new Date().setDate(new Date().getDate() - 29)),
                        new Date(new Date()),
                    ],
                    "Last 90 Days": [
                        new Date(new Date().setDate(new Date().getDate() - 89)),
                        new Date(new Date()),
                    ],
                    "This Month": [
                        new Date(new Date().setDate(1)),
                        new Date(
                            new Date().getFullYear(),
                            new Date().getMonth() + 1,
                            0
                        ),
                    ],
                    "Last Month": [
                        new Date(
                            new Date().getFullYear(),
                            new Date().getMonth() - 1,
                            1
                        ),
                        new Date(
                            new Date().getFullYear(),
                            new Date().getMonth(),
                            0
                        ),
                    ],
                    "This Year": [
                        new Date(new Date().getFullYear(), 0),
                        new Date(new Date()),
                    ],
                    "Last Year": [
                        new Date(new Date().getFullYear() - 1, 0),
                        new Date(new Date().getFullYear(), 0, 0),
                    ],
                },
            };

            const optionsInvDatePicker = {
                locale: "en-US",
                inputDateFormat: (date) =>
                    dayjs(date).locale("en").format("DD-MM-YYYY"),
                inputDateParse: (date) =>
                    dayjs(date, "DD-MM-YYYY", "id").toDate(),
                // minDate: dayjs(new Date()),
                maxDate: dayjs(new Date()),
                showAdjacementDays: false,
                container: "#globalAside",
                placement: "top-start",
                startDate: invStartDate,
                endDate: invEndDate,
                ranges: {
                    Today: [new Date(), new Date()],
                    Yesterday: [
                        new Date(new Date().setDate(new Date().getDate() - 1)),
                        new Date(new Date().setDate(new Date().getDate() - 1)),
                    ],
                    "Last 7 Days": [
                        new Date(new Date().setDate(new Date().getDate() - 6)),
                        new Date(new Date()),
                    ],
                    "Last 30 Days": [
                        new Date(new Date().setDate(new Date().getDate() - 29)),
                        new Date(new Date()),
                    ],
                    "Last 90 Days": [
                        new Date(new Date().setDate(new Date().getDate() - 89)),
                        new Date(new Date()),
                    ],
                    "This Month": [
                        new Date(new Date().setDate(1)),
                        new Date(
                            new Date().getFullYear(),
                            new Date().getMonth() + 1,
                            0
                        ),
                    ],
                    "Last Month": [
                        new Date(
                            new Date().getFullYear(),
                            new Date().getMonth() - 1,
                            1
                        ),
                        new Date(
                            new Date().getFullYear(),
                            new Date().getMonth(),
                            0
                        ),
                    ],
                    "This Year": [
                        new Date(new Date().getFullYear(), 0),
                        new Date(new Date()),
                    ],
                    "Last Year": [
                        new Date(new Date().getFullYear() - 1, 0),
                        new Date(new Date().getFullYear(), 0, 0),
                    ],
                },
            };

            new coreui.DateRangePicker(
                document.getElementById("exportOrderDate"),
                optionsOrderDatePicker
            );
            new coreui.DateRangePicker(
                document.getElementById("exportReceivedDate"),
                optionsReceivedDatePicker
            );
            new coreui.DateRangePicker(
                document.getElementById("exportInvDate"),
                optionsInvDatePicker
            );
            setTimeout(() => {
                let dropdown = document.querySelector(".daterangepicker");
                if (dropdown) {
                    dropdown.setAttribute("data-popper-placement", "top-start");
                }
            }, 100);

            if ($("#exportApplicant").hasClass("select2-hidden-accessible")) {
                $("#exportApplicant").val(null).empty().trigger("change");
                $("#exportApplicant").select2("destroy");
            }

            let optionApplicant = await getUserSelection({
                type: "PO_APPLICANT",
                companyId: company,
                selectedId: applicant,
            });
            $("#exportApplicant").replaceWith(
                `<select class="select2" name="exportApplicant" id="exportApplicant"></select>`
            );
            $("#exportApplicant").select2({
                dropdownParent: $("#globalAside"),
                allowClear: false,
                placeholder: "-- Select --",
                data: optionApplicant,
                templateResult: formatResultRemote,
                templateSelection: function (data) {
                    return data.text || data.id;
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
            });

            $(".asideFooterBtn")
                .html(`<div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
                                    </div>
                                    <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-success w-100 w-md-auto me-2 actionBtn" data-type="EXPORT_DATA_TABLE" data-table="${table}" title="Export Data">Export Data</button>
                                    </div>`);
        } else if ($this.attr("data-type") == "INVOICED") {
            asideHide();
            $(".aside-title").html(
                `Invoiced Purchase : ${$this.attr("data-no")}`
            );
            $(".aside-content")
                .html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
                                    <input type="hidden" name="tokenForm" value="${$this.attr(
                                        "data-token"
                                    )}">
                                    <div id="invoiceContainer">
                                        <div class="row row-multi-col mb-3 pt-2 pb-3 bg-white invoiceSection">
                                            <div class="form-group mb-3">
                                                <label for="invoiceNo" class="form-label">Invoice No.<span class="required"></span> :</label>
                                                <input type="text" class="form-control" name="invoiceNo[]" spellcheck="false" autocomplete="off">
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="invoicedDateOrder0" class="form-label invoicedDateOrder">Invoice Date<span class="required"></span> :</label>
                                                <div class="form-group date-picker" id="invoicedDateOrder0" data-coreui-date="" data-coreui-name="invoicedDateOrder[]"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="invoiceDueDate0" class="form-label invoiceDueDate">Due Date<span class="required"></span> :</label>
                                                <div class="form-group date-picker" id="invoiceDueDate0" data-coreui-date="" data-coreui-name="invoiceDueDate[]"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label class="form-label">Invoice Amount<span class="required"></span> :</label>
                                                <div class="form-group d-flex align-items-center position-relative">
                                                    <input type="text" class="form-control d-flex currencyValue" name="invoiceAmount[]" data-item="" spellcheck="false" autocomplete="off" style="padding-right: 50px;">
                                                    <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-3">${$this.attr(
                                                        "data-ccy"
                                                    )}</span>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <div class="">
                                                    <div id="dropZone" class="form-group border p-4 text-center bg-light">
                                                        <input type="file" class="form-control" name="attachmentFile0">
                                                    </div>
                                                </div>
                                                <div id="fileList" class="list-group mt-2 fileList">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end align-items-center gap-2">
                                                <button type="button" class="btn btn-default btn-sm fs-8 fw-medium invoiceButton" data-type="REMOVE_INVOICE"><i class="fa-solid fa-trash-can-list fa-fw"></i> Remove</button>
                                                <button type="button" class="btn btn-secondary btn-sm fs-8 fw-medium invoiceButton" data-type="ADD_INVOICE" data-ccy="${$this.attr(
                                                    "data-ccy"
                                                )}"><i class="fa-regular fa-plus fa-fw"></i> Add Invoice</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>`);

            $(".overlay-aside").addClass("show").trigger("shown");
            $("#globalAside").addClass("show aside-lg").trigger("shown");
            $("body").addClass("overflow-hidden");
            $("#asideDetailForm").scrollTop(0);

            $(".asideFooterBtn")
                .html(`<div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside asideBtn" title="Close">Close</button>
                                    </div>
                                    <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-info w-100 w-md-auto asideBtn actionBtn" data-type="SAVE_INVOICED"></i> Submit Invoice</button>
                                    </div>`);

            new coreui.DatePicker(
                document.getElementById(`invoicedDateOrder0`),
                maxDateCurrentOptions
            );
            new coreui.DatePicker(
                document.getElementById(`invoiceDueDate0`),
                maxDateNullOptions
            );
        }
    });

$(document)
    .off("click", ".invoiceButton")
    .on("click", ".invoiceButton", function (e) {
        let $this = $(this);
        Snackbar.close();
        if ($this.attr("data-type") == "ADD_INVOICE") {
            let x = $("#invoiceContainer").find(".invoiceSection").length;
            $this.closest("div#invoiceContainer")
                .append(`<div class="row row-multi-col mb-3 pt-2 pb-3 bg-white invoiceSection">
                                                <div class="form-group mb-3">
                                                    <label for="invoiceNo" class="form-label">Invoice No.<span class="required"></span> :</label>
                                                    <input type="text" class="form-control" name="invoiceNo[]" spellcheck="false" autocomplete="off">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="invoicedDateOrder${x}" class="form-label">Invoice Date<span class="required"></span> :</label>
                                                    <div class="form-group date-picker invoicedDateOrder" id="invoicedDateOrder${x}" data-coreui-date="" data-coreui-name="invoicedDateOrder[]"></div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="invoiceDueDate${x}" class="form-label invoiceDueDate">Due Date<span class="required"></span> :</label>
                                                    <div class="form-group date-picker" id="invoiceDueDate${x}" data-coreui-date="" data-coreui-name="invoiceDueDate[]"></div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Invoice Amount<span class="required"></span> :</label>
                                                    <div class="form-group d-flex align-items-center position-relative">
                                                        <input type="text" class="form-control d-flex currencyValue" name="invoiceAmount[]" data-item="" spellcheck="false" autocomplete="off" style="padding-right: 50px;">
                                                        <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-3">${$this.attr(
                                                            "data-ccy"
                                                        )}</span>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <div class="">
                                                        <div id="dropZone" class="form-group border p-4 text-center bg-light">
                                                            <input type="file" class="form-control" name="attachmentFile${x}">
                                                        </div>
                                                    </div>
                                                    <div id="fileList" class="list-group mt-2 fileList">
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-end align-items-center gap-2">
                                                    <button type="button" class="btn btn-default btn-sm fs-8 fw-medium invoiceButton" data-type="REMOVE_INVOICE"><i class="fa-solid fa-trash-can-list fa-fw"></i> Remove</button>
                                                    <button type="button" class="btn btn-secondary btn-sm fs-8 fw-medium invoiceButton" data-type="ADD_INVOICE" data-ccy="${$this.attr(
                                                        "data-ccy"
                                                    )}"><i class="fa-regular fa-plus fa-fw"></i> Add Invoice</button>
                                                </div>
                                            </div>`);
            $(".autosize").autosize({ append: "\n" });
            $("#asideDetailForm").animate(
                {
                    scrollTop: $("#asideDetailForm")[0].scrollHeight,
                },
                500
            );

            new coreui.DatePicker(
                document.getElementById(`invoicedDateOrder${x}`),
                maxDateCurrentOptions
            );
            new coreui.DatePicker(
                document.getElementById(`invoiceDueDate${x}`),
                maxDateNullOptions
            );
        } else if ($this.attr("data-type") == "REMOVE_INVOICE") {
            if (
                $this.closest("div#invoiceContainer").find("div.invoiceSection")
                    .length > 1
            ) {
                $this.closest("div.invoiceSection").remove();
            } else {
                Snackbar.show({
                    pos: "bottom-center",
                    text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : At least 1 Invoice must be filled`,
                });
                return false;
            }
        }
    });

$(document)
    .off("click", ".asideButton")
    .on("click", ".asideButton", async function (event) {
        let $this = $(this);
        if (
            $this.attr("data-type") == "PRINT" ||
            $this.attr("data-type") == "DOWNLOAD"
        ) {
            const printBackdrop = $(`
            <div class="modal-backdrop" style="position: fixed; top: 0; left: 0; z-index: 9999; background: rgba(255,255,255,0.9)">
                <div class="modal-content" style="width: 100%; height: 100%;display: flex; justify-content: center; align-items: center;">
                <div class="full-height-column-wrapper d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                    <div class="center-container">
                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                    </div>
                    <p class="mt-3 fw-700 fs-5 text-company-emphasis">Preparing document...</p>
                </div>
            </div>
        `);
            $("body").append(printBackdrop);

            const formData = new FormData($("#asideForm")[0]);
            formData.append("actionType", $this.attr("data-type"));
            formData.append("module", "DOCUMENT_APPROVAL");

            const escapedData = new FormData();
            for (let [key, value] of formData.entries()) {
                if (value instanceof File) {
                    const safeFileName = escapeFilename(value.name);
                    const safeFile = new File([value], safeFileName, {
                        type: value.type,
                    });
                    escapedData.append(key, safeFile);
                } else if (key.endsWith("[]")) {
                    // Handle array fields
                    const baseKey = key.replace("[]", "");
                    const currentValues = escapedData.getAll(baseKey) || [];
                    escapedData.delete(baseKey); // Hapus yang lama
                    currentValues.push(escapeInput(value));
                    currentValues.forEach((v) =>
                        escapedData.append(baseKey + "[]", v)
                    );
                } else {
                    // Handle regular fields
                    escapedData.append(key, escapeInput(value));
                }
            }

            $(this).btnLoading(async function () {
                try {
                    $this.prop("disabled", true);
                    $this.html(
                        '<i class="fas fa-spinner fa-spin"></i> Please wait'
                    );
                    const response = await fetch("/generatePdf", {
                        method: "POST",
                        body: escapedData,
                        headers: {
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                            Accept: "application/json",
                            Referer: window.location.href,
                        },
                    });

                    const result = await response.json();
                    if (response.status === 200) {
                        if (Object.keys(result.data).length > 0) {
                            if ($this.attr("data-type") == "PRINT") {
                                let pdfUrl = `/printPdf?token=${result.data.token}`;
                                const existingIframe =
                                    document.querySelector("body > iframe");
                                if (existingIframe) {
                                    existingIframe.remove();
                                }
                                const iframe = document.createElement("iframe");
                                iframe.src = pdfUrl;
                                iframe.style.display = "none";
                                document.body.appendChild(iframe);

                                iframe.onload = function () {
                                    try {
                                        iframe.contentWindow.focus();
                                        iframe.contentWindow.print();
                                        printBackdrop.remove();
                                    } catch (e) {
                                        const newWindow = window.open(
                                            "",
                                            "_blank"
                                        );
                                        if (newWindow) {
                                            const embed =
                                                newWindow.document.createElement(
                                                    "embed"
                                                );
                                            embed.src = pdfUrl;
                                            embed.type = "application/pdf";
                                            embed.width = "100%";
                                            embed.height = "100%";
                                            newWindow.document.body.appendChild(
                                                embed
                                            );

                                            newWindow.onload = function () {
                                                newWindow.focus();
                                                newWindow.print();
                                                printBackdrop.remove();
                                            };
                                        } else {
                                            printBackdrop.remove();
                                            console.error(
                                                "Failed to open new window. Check pop-up blocker settings."
                                            );
                                        }
                                    }
                                };
                            } else {
                                const url = document.createElement("a");
                                url.href = `/downloadPdf?token=${result.data.token}`;
                                url.setAttribute("download", "");
                                document.body.appendChild(url);
                                url.click();
                                document.body.removeChild(url);
                                printBackdrop.remove();
                            }
                        } else {
                            printBackdrop.remove();
                            Snackbar.show({
                                pos: "bottom-center",
                                duration: "5000",
                                text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed to preparing documents',
                            });
                        }
                    } else if (response.status === 401) {
                        printBackdrop.remove();
                        Snackbar.show({
                            pos: "bottom-center",
                            duration: "6000",
                            text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Unauthorized access, please login or refresh this page',
                        });
                    } else if (response.status === 422) {
                        printBackdrop.remove();
                        handleValidationErrors(result.errors);
                        Snackbar.show({
                            pos: "bottom-center",
                            text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : ${result["message"]}`,
                        });
                    } else if (response.status === 419) {
                        printBackdrop.remove();
                        handleValidationErrors(result.errors);
                        Snackbar.show({
                            pos: "bottom-center",
                            text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : CSRF token mismatch, please refresh this page',
                        });
                    } else {
                        printBackdrop.remove();
                        Snackbar.show({
                            pos: "bottom-center",
                            text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed ${response.status} : ${response.statusText}`,
                        });
                    }
                } catch (error) {
                    printBackdrop.remove();
                    Snackbar.show({
                        pos: "bottom-center",
                        text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}`,
                    });
                } finally {
                    $this.prop("disabled", false);
                }
            });
        } else if ($this.attr("data-type") == "NEXT_REVISE_FORM") {
            clearValidation();
            selectedFiles = [];
            selectedFilesProperties = [];
            selectedFilesDocument = [];
            selectedFilesPropertiesDocument = [];
            costCenterOption = [];
            $("#fileList").empty();

            if ($this.attr("data-revise-type") != "") {
                if (
                    $this.attr("data-revise-type") ==
                    "REVISE_COMPARISON_REVISE_APPLICATION"
                ) {
                    $form = "COMPARISON_FORM";
                } else {
                    $form = "APPLICATION_FORM";
                }

                const params = {
                    source: "WEB",
                    form: $form,
                    token: $this.attr("data-token"),
                    type: $this.attr("data-revise-type"),
                };

                newForm(params);
            } else {
                Snackbar.show({
                    pos: "bottom-center",
                    text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : Select Revision Type`,
                });
                return false;
            }
        }
    });

$(document)
    .off("click", ".actionBtn")
    .on("click", ".actionBtn", async function (event) {
        let $this = $(this);
        if ($this.attr("data-type") == "SEND_BACK") {
            asideHide();
            $(".aside-title").html(`Send Back Form`);
            $(".aside-content")
                .html(`<form role="form" class="form-horizontal mt-3" enctype="multipart/form-data" id="formAction">
                                            <input type="hidden" id="tokenFormAction" name="tokenForm" autocomplete="false" value="${$this.attr(
                                                "data-token"
                                            )}">
                                            <input type="hidden" name="type" autocomplete="false" value="PURCHASING">
                                            <div class="alert alert-info mb-3 w-100 p-2" id="alertFormAction" role="alert"><span class="fw-semibold">“Send Back”</span> means the form must be REVISE</div>
                                            <div class="form-group mb-3" id="sendBackContainer">
                                                <label for="sendBackTo" class="form-label required">Send Back To :</label>
                                                <div class="skeleton"></div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="commentAction" class="form-label required" id="commentActionLabel">Reason (required) :</label>
                                                <textarea class="form-control autosize singleLine" spellcheck="false" placeholder="Reason to send back..." maxlength="255" id="commentAction" name="commentAction" style="height: 0px;"></textarea>
                                            </div>
                                        </form>`);

            $(".overlay-aside").addClass("show").trigger("shown");
            $("#globalAside").addClass("show").trigger("shown");
            $("body").addClass("overflow-hidden");
            $("#asideDetailForm").scrollTop(0);
            $(".autosize").autosize({ append: "\n" });

            $(".asideFooterBtn")
                .html(`<div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside asideBtn" title="Close">Close</button>
                                        </div>
                                        <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-secondary w-100 w-md-auto asideBtn confirmAction" data-type="${$this.attr(
                                                "data-type"
                                            )}" data-form="" data-token="" title="">Send Back Form</button>
                                        </div>`);

            const formParams = {
                tokenForm: $("#tokenFormAction").val(),
            };
            const sendBackOption = await getSendBackOptions(formParams);
            $("#sendBackContainer")
                .find("div.skeleton")
                .replaceWith(
                    '<div class="virtualSelectSendBackTo" name="sendBackTo"" id="sendBackTo"></div>'
                );
            VirtualSelect.init({
                ele: "#sendBackTo",
                search: false,
                hideClearButton: true,
                options: sendBackOption,
                selectedValue:
                    sendBackOption.length == 1 ? sendBackOption[0].value : null,
            });
        } else if (
            $this.attr("data-type") == "PROGRESS" ||
            $this.attr("data-type") == "INSPECTION"
        ) {
            let url = "",
                title = "";
            if ($this.attr("data-type") == "PROGRESS") {
                title = "Progress";
                url = `/doc_approval/getProgress?token=${$this.attr(
                    "data-token"
                )}`;
            } else if ($this.attr("data-type") == "INSPECTION") {
                title = "Inspection Form";
                url = `/doc_approval/getInspection?token=${$this.attr(
                    "data-token"
                )}`;
            }

            asideHide();
            $(".aside-title").html(title);
            $(".aside-content")
                .html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
                    <div class="container-content pb-5">
                        <div class="full-height-column-wrapper d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                            <div class="center-container">
                                <div class="stripes-red-blue stripes-red-blue-md"></div>
                                <div class="d-block fs-7 mt-2">Loading...</div>
                            </div>
                        </div>
                    </div>
                </form>`);

            $(".overlay-aside").addClass("show").trigger("shown");
            if ($this.attr("data-type") == "PROGRESS") {
                $("#globalAside").addClass("show aside-lg").trigger("shown");
            } else {
                $("#globalAside").addClass("show").trigger("shown");
            }

            $("body").addClass("overflow-hidden");
            $("#asideDetailForm").scrollTop(0);
            $(".asideFooterBtn")
                .html(`<div class="col-12 d-flex justify-content-center justify-content-md-end mb-2 px-0">
            <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
        </div>`);

            try {
                const response = await fetch(url, {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                        Accept: "application/json",
                        Referer: window.location.href,
                    },
                });

                const result = await response.json();
                if (response.status === 200) {
                    $("#asideForm").html(result.data.form);
                } else {
                    console.error("HTTP Error:", response.status);
                }
                asideForm;
            } catch (error) {
                return;
            }
        } else if ($this.attr("data-type") == "CANCEL") {
            $(".modal").modal("hide");
            $("#modalMessageTitle").html("Cancel Form");
            $("#modalMessageBody")
                .html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="formAction">
                                        <input type="hidden" id="tokenFormAction" name="tokenForm" autocomplete="false" value="${$(
                                            this
                                        ).attr("data-token")}">
                                        <div class="form-group mb-3">
                                            <label for="commentAction" class="form-label required" id="commentActionLabel">Reason (required) :</label>
                                            <textarea class="form-control autosize singleLine" spellcheck="false" placeholder="Reason to cancel" maxlength="255" id="commentAction" name="commentAction" style="height: 53px;"></textarea>
                                        </div>
                                    </form>`);

            $("#modalMessageFooter").html(`<div class="container-fluid p-0">
                                            <div class="row justify-content-center w-100 mx-0">
                                                <div class="col-6 d-flex justify-content-center mb-2 px-0">
                                                    <button type="button" class="btn btn-default w-100 me-2" data-coreui-dismiss="modal" title="Close">
                                                        <i class="fas fa-xmark"></i> Close
                                                    </button>
                                                </div>
                                                <div class="col-6 d-flex justify-content-center mb-2 px-0">
                                                    <button type="button" class="btn btn-secondary w-100 confirmAction" data-type="${$(
                                                        this
                                                    ).attr(
                                                        "data-type"
                                                    )}" data-form="" data-token="" title="">
                                                        Cancel Form <i class="fa-solid fa-arrow-right"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>`);
            $("#modalMessageDialog").removeClass("top-20").addClass("top-20");
            $("#modalMessageDialog").removeClass("modal-dialog-scrollable");
            $("#modalMessage").modal("show");
            $(".autosize").autosize().trigger("change");
        } else if ($this.attr("data-type") == "REVISE") {
            clearValidation();
            if ($this.attr("data-form") == "APPLICATION_FORM") {
                asideHide();
                $(".aside-title").html(
                    `Revise PO No : ${$this.attr("data-no")}`
                );
                $(".aside-content")
                    .html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
                <div class="card card-hover mb-3">
                    <div class="card-body p-2-2 fs-7">
                        <div class="m-2 data-details">
                            <div class="data-row">
                                <label class="text-wrap lh-base" style="display: block; padding-left: 1.5em; text-indent: -1.5em;cursor:pointer">
                                    <input type="checkbox" class="form-check-input checkboxInput reviseApplicationCheckbox" value="REVISE_COMPARISON_REVISE_APPLICATION">
                                    Revise Comparison Form and Revise Application Form
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card card-hover mb-3">
                    <div class="card-body p-2-2 fs-7">
                        <div class="m-2 data-details">
                            <div class="data-row">
                                <label class="text-wrap lh-base" style="display: block; padding-left: 1.5em; text-indent: -1.5em;cursor:pointer">
                                    <input type="checkbox" class="form-check-input checkboxInput reviseApplicationCheckbox" value="OLD_COMPARISON_REVISE_APPLICATION">
                                    Use Old Comparison Form and Revise Application Form
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card card-hover mb-3">
                    <div class="card-body p-2-2 fs-7">
                        <div class="m-2 data-details">
                            <div class="data-row">
                                <label class="text-wrap lh-base" style="display: block; padding-left: 1.5em; text-indent: -1.5em;cursor:pointer">
                                    <input type="checkbox" class="form-check-input checkboxInput reviseApplicationCheckbox" value="DELETE_COMPARISON_REVISE_APPLICATION">
                                    Delete Comparison Form and Revise Application Form
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>`);

                $(".overlay-aside").addClass("show").trigger("shown");
                $("#globalAside").addClass("show").trigger("shown");
                $("body").addClass("overflow-hidden");
                $("#asideDetailForm").scrollTop(0);
                $(".asideFooterBtn")
                    .html(`<div class="row justify-content-center w-100 mx-0 asideFooterBtn"><div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
                                    </div>
                                    <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-secondary w-100 w-md-auto asideButton" data-type="NEXT_REVISE_FORM" data-form="${$this.attr(
                                            "data-form"
                                        )}" data-token="${$this.attr(
                    "data-token"
                )}" data-revise-type="" title="Next Revise Form">Next</button>
                                    </div></div>`);
            } else {
                selectedFiles = [];
                selectedFilesProperties = [];
                selectedFilesDocument = [];
                selectedFilesPropertiesDocument = [];
                costCenterOption = [];
                $("#fileList").empty();

                const params = {
                    source: "WEB",
                    form: $this.attr("data-form"),
                    token: $this.attr("data-token"),
                    type: $this.attr("data-type"),
                };

                newForm(params);
            }
        } else if (
            $this.attr("data-type") == "VIEW" ||
            $this.attr("data-type") == "VIEW_ONGOING"
        ) {
            $(".card-link-content").removeClass("active");
            Snackbar.close();
            $this.addClass("active");

            if ($this.attr("data-form") == "ORDER_FORM") {
                if ($this.attr("data-type") == "VIEW") {
                    Snackbar.show({
                        pos: "bottom-center",
                        duration: "6000",
                        text: `<i class="fa-regular fa-circle-notch fa-spin fs-6 fa-fw text-info"></i> Processing...`,
                    });

                    const token = $this.attr("data-token");
                    const form = $this.attr("data-form");
                    const source = $this.attr("data-source");

                    try {
                        const response = await fetch(
                            `/proc_pur/getComparison?type=PURCHASING&form=${form}&token=${token}`,
                            {
                                method: "GET",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": $(
                                        'meta[name="csrf-token"]'
                                    ).attr("content"),
                                    Accept: "application/json",
                                    Referer: window.location.href,
                                },
                            }
                        );

                        const result = await response.json();
                        if (response.status === 401) {
                            $(".card-footer-fixed").addClass("border-top-0");
                            Snackbar.close();
                            let countdown = 5;
                            Snackbar.show({
                                pos: "bottom-center",
                                duration: "6000",
                                text: `<i class="fa-solid fa-circle-exclamation fa-lg fa-fw text-warning"></i> Session expired, please refresh this page or it will automatically refresh in <span id="snackbar-countdown">${countdown}</span> seconds.`,
                            });

                            let countdownInterval = setInterval(() => {
                                countdown--;
                                document.getElementById(
                                    "snackbar-countdown"
                                ).textContent = countdown;
                                if (countdown < 0) {
                                    clearInterval(countdownInterval);
                                    window.location.href = result.redirect_uri;
                                }
                            }, 1000);
                        } else {
                            Snackbar.close();
                            if (result.status === 200) {
                                if (
                                    result.data.processedStatus === true &&
                                    result.data.multiVendor === true
                                ) {
                                    $("#modalMessageTitle").html(
                                        result.data.title
                                    );
                                    $("#modalMessageBody").html(
                                        result.data.listItem
                                    );
                                    $("#modalMessageFooter")
                                        .html(`<div class="container-fluid p-0">
                                    <div class="row justify-content-center w-100 mx-0">
                                        <div class="col-sm-12 col-md-8 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                <i class="fas fa-xmark"></i> Close
                                            </button>
                                        </div>
                                    </div>
                                </div>`);
                                    $("#modalMessageDialog").removeClass(
                                        "top-20"
                                    );
                                    $("#modalMessageDialog")
                                        .removeClass("modal-dialog-scrollable")
                                        .addClass("modal-dialog-scrollable");
                                    $("#modalMessage").modal("show");
                                } else {
                                    event.stopImmediatePropagation();
                                    const params = {
                                        source: "WEB",
                                        form: result.data.form,
                                        token: result.data.tokenForm,
                                        type: $this.attr("data-type"),
                                    };

                                    if ($this.attr("data-type") == "NEW") {
                                        newForm(params);
                                    } else {
                                        viewForm(params);
                                    }
                                }
                            } else if (result.status === 404) {
                                Snackbar.show({
                                    pos: "bottom-center",
                                    duration: "6000",
                                    text: `<i class="fa-solid fa-circle-exclamation fa-lg fa-fw text-error"></i> 404 Not found`,
                                });
                            } else {
                                Snackbar.show({
                                    pos: "bottom-center",
                                    duration: "6000",
                                    text: `<i class="fa-solid fa-circle-exclamation fa-lg fa-fw text-error"></i> Failed to get data`,
                                });
                            }
                        }
                    } catch (error) {
                        Snackbar.show({
                            pos: "bottom-center",
                            text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}`,
                        });
                    }
                } else if ($this.attr("data-type") == "VIEW_ONGOING") {
                    event.stopImmediatePropagation();
                    const params = {
                        form: $this.attr("data-form"),
                        token: $this.attr("data-token"),
                        type: $this.attr("data-type"),
                    };

                    viewForm(params);
                }
            } else {
                const params = {
                    source: $this.attr("data-source"),
                    form: $this.attr("data-form"),
                    token: $this.attr("data-token"),
                    type: $this.attr("data-type"),
                };

                if ($this.attr("data-type") == "NEW") {
                    newForm(params);
                } else {
                    viewForm(params);
                }
            }
        } else if ($this.attr("data-type") == "EXPORT_DATA_TABLE") {
            Snackbar.close();
            clearValidation();
            const printBackdrop = $(`
            <div class="modal-backdrop" style="position: fixed; top: 0; left: 0; z-index: 9999; background: rgba(245,245,245,0.8)">
                <div class="modal-content" style="width: 100%; height: 100%;display: flex; justify-content: center; align-items: center;">
                    <div class="center-container">
                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                    </div>
                    <p class="mt-3 fw-700 fs-5 text-company-emphasis">Preparing data...</p>
                </div>
            </div>
        `);
            $("body").append(printBackdrop);

            const formData = new FormData($("#asideFormExportData")[0]);
            formData.append("actionType", $this.attr("data-type"));
            formData.append("module", "DOCUMENT_APPROVAL");

            const escapedData = new FormData();
            for (let [key, value] of formData.entries()) {
                if (value instanceof File) {
                    const safeFileName = escapeFilename(value.name);
                    const safeFile = new File([value], safeFileName, {
                        type: value.type,
                    });
                    escapedData.append(key, safeFile);
                } else if (key.endsWith("[]")) {
                    // Handle array fields
                    const baseKey = key.replace("[]", "");
                    const currentValues = escapedData.getAll(baseKey) || [];
                    escapedData.delete(baseKey); // Hapus yang lama
                    currentValues.push(escapeInput(value));
                    currentValues.forEach((v) =>
                        escapedData.append(baseKey + "[]", v)
                    );
                } else {
                    // Handle regular fields
                    escapedData.append(key, escapeInput(value));
                }
            }

            $(this).btnLoading(async function () {
                try {
                    $this.prop("disabled", true);
                    $this.html(
                        '<i class="fas fa-spinner fa-spin"></i> Please wait'
                    );
                    const response = await fetch("/proc_pur/exportData", {
                        method: "POST",
                        body: escapedData,
                        headers: {
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                            Accept: "application/json",
                            Referer: window.location.href,
                        },
                    });

                    if (!response.ok) {
                        const result = await response.json().catch(() => null);

                        // Unauthorized (401)
                        if (response.status === 401) {
                            Snackbar.show({
                                pos: "bottom-center",
                                duration: 6000,
                                text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Unauthorized access, please login or refresh this page',
                            });
                        }

                        // Validation Error (422)
                        else if (response.status === 422) {
                            if (result && result.errors) {
                                // Jika ingin menampilkan error validasi di form
                                handleValidationErrors(result.errors);
                            }
                            Snackbar.show({
                                pos: "bottom-center",
                                text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed: ${
                                    result?.message || "Validation error"
                                }`,
                            });
                        }

                        // CSRF Token Mismatch (419)
                        else if (response.status === 419) {
                            Snackbar.show({
                                pos: "bottom-center",
                                text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error: CSRF token mismatch, please refresh this page',
                            });
                        }

                        // Other Errors (500, 404, etc)
                        else {
                            Snackbar.show({
                                pos: "bottom-center",
                                text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed ${
                                    response.status
                                }: ${result?.message || response.statusText}`,
                            });
                        }

                        return;
                    }

                    // Success: Download File
                    const blob = await response.blob();
                    const filename =
                        response.headers
                            .get("content-disposition")
                            ?.split("filename=")[1]
                            ?.replace(/"/g, "") || "export.xlsx";

                    const downloadUrl = window.URL.createObjectURL(blob);
                    const link = document.createElement("a");
                    link.href = downloadUrl;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    window.URL.revokeObjectURL(downloadUrl);
                    link.remove();

                    Snackbar.show({
                        pos: "bottom-center",
                        text: '<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> Successfully Exported Data',
                    });
                } catch (error) {
                    Snackbar.show({
                        pos: "bottom-center",
                        text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error: ${
                            error.message || "Failed to export"
                        }`,
                    });
                } finally {
                    printBackdrop.remove();
                    $this.prop("disabled", false);
                    $this.html("Export Data");
                }
            });
        } else if ($this.attr("data-type") == "SAVE_INVOICED") {
            $(this).btnLoading(async function () {
                try {
                    $(".actionBtn").prop("disabled", true);
                    $this.html(
                        '<i class="fas fa-spinner fa-spin"></i> Please wait'
                    );

                    let formData;
                    formData = new FormData($("#asideForm")[0]);
                    formData.append("type", "PURCHASING");
                    formData.append("actionType", $this.attr("data-type"));

                    const escapedData = new FormData();
                    for (let [key, value] of formData.entries()) {
                        if (value instanceof File) {
                            const safeFileName = escapeFilename(value.name);
                            const safeFile = new File([value], safeFileName, {
                                type: value.type,
                            });
                            escapedData.append(key, safeFile);
                        } else if (key.endsWith("[]")) {
                            // Handle array fields
                            const baseKey = key.replace("[]", "");
                            const currentValues =
                                escapedData.getAll(baseKey) || [];
                            escapedData.delete(baseKey); // Hapus yang lama
                            currentValues.push(escapeInput(value));
                            currentValues.forEach((v) =>
                                escapedData.append(baseKey + "[]", v)
                            );
                        } else {
                            // Handle regular fields
                            escapedData.append(key, escapeInput(value));
                        }
                    }

                    const response = await fetch("/doc_approval/updateAction", {
                        method: "POST",
                        body: escapedData,
                        headers: {
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                            Accept: "application/json",
                            Referer: window.location.href,
                        },
                    });

                    const result = await response.json();
                    if (response.status === 200) {
                        asideHide();
                        archivePurchasingTable.ajax.reload();
                        Snackbar.show({
                            pos: "bottom-center",
                            text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result["message"]}`,
                        });
                        $(".modal").modal("hide");
                    } else if (response.status === 401) {
                        Snackbar.show({
                            pos: "bottom-center",
                            duration: "6000",
                            text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Unauthorized access, please login or refresh this page',
                        });
                    } else if (response.status === 422) {
                        handleValidationErrors(result.errors);
                        Snackbar.show({
                            pos: "bottom-center",
                            text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : ${result["message"]}`,
                        });
                    } else if (response.status === 419) {
                        handleValidationErrors(result.errors);
                        Snackbar.show({
                            pos: "bottom-center",
                            text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : CSRF token mismatch, please refresh this page',
                        });
                    } else {
                        Snackbar.show({
                            pos: "bottom-center",
                            text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed ${response.status} : ${response.statusText}`,
                        });
                    }
                } catch (error) {
                    Snackbar.show({
                        pos: "bottom-center",
                        text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}`,
                    });
                } finally {
                    $(".actionBtn").prop("disabled", false);
                }
            });
        } else if (
            $("#popperAction").attr("data-type") != $(this).attr("data-type")
        ) {
            Snackbar.close();
            clearValidation();

            if (popperInstance) {
                hidePopup();
            }

            $("#alertFormAction").addClass("d-none");
            if ($(this).attr("data-type") == "REJECT") {
                $("#alertFormAction").html(
                    '<span class="fw-semibold">“Reject”</span> means the approval form will be CLOSED'
                );
                $("#alertFormAction").removeClass("d-none");
            } else if ($(this).attr("data-type") == "SEND_BACK") {
                $("#alertFormAction").html(
                    '<span class="fw-semibold">“Send Back”</span> means the form must be REVISE'
                );
                $("#alertFormAction").removeClass("d-none");
            }

            const params = {
                button: this,
                action: $(this).attr("data-type"),
                rowId: $(this).attr("data-row"),
            };
            showPopupAction(params);
        }
    });

async function showPopupAction(params) {
    const button = params["button"];
    const action = params["action"];
    const rowId = params["rowId"];
    $("#popperAction").attr("data-type", action);

    if (action == "APPROVE" || action == "REJECT" || action == "SEND_BACK") {
        $("#sendBackContainer").addClass("d-none");
        let lastComment = "";
        let commentIndex = commentBuffer.findIndex(
            (item) => item.action === action
        );
        if (commentIndex !== -1) {
            lastComment = commentBuffer[commentIndex].comment;
        }

        $("#commentAction").val(lastComment).trigger("change");

        if (action == "APPROVE") {
            $("#commentActionLabel").html("Approve Comment (optional) :");
            $("#commentActionLabel").removeClass("required");
            $(".confirmAction").removeClass(
                "btn-info btn-danger btn-secondary"
            );
            $(".confirmAction").addClass("btn-info");
            $(".confirmAction").attr("data-type", action);
            $(".confirmAction").attr("data-row", rowId);
            $(".confirmAction").html("Approve");
        } else if (action == "REJECT") {
            $("#commentActionLabel").html("Reason (required) :");
            $("#commentActionLabel").addClass("required");
            $(".confirmAction").removeClass(
                "btn-info btn-danger btn-secondary"
            );
            $(".confirmAction").addClass("btn-danger");
            $(".confirmAction").attr("data-type", action);
            $(".confirmAction").attr("data-row", rowId);
            $(".confirmAction").html("Reject");
        } else if (action == "SEND_BACK") {
            $("#sendBackContainer")
                .find("div#sendBackTo")
                .replaceWith('<div class="skeleton"></div>');
            $("#sendBackContainer").removeClass("d-none");
            $("#commentActionLabel").html("Reason (required) :");
            $("#commentActionLabel").addClass("required");
            $(".confirmAction").removeClass(
                "btn-info btn-danger btn-secondary"
            );
            $(".confirmAction").addClass("btn-secondary");
            $(".confirmAction").attr("data-type", action);
            $(".confirmAction").attr("data-row", rowId);
            $(".confirmAction").html("Send Back");
        }

        $("#popperAction").removeClass("d-none");
        $(".autosize").autosize().trigger("change");
    }

    popperInstance = Popper.createPopper(button, $("#popperAction")[0], {
        placement: "top",
        modifiers: [
            {
                name: "offset",
                options: {
                    offset: [0, 8],
                },
            },
        ],
    });

    if (action == "SEND_BACK") {
        const formParams = {
            tokenForm: $("#tokenFormAction").val(),
        };
        const sendBackOption = await getSendBackOptions(formParams);
        $("#sendBackContainer")
            .find("div.skeleton")
            .replaceWith(
                '<div class="virtualSelectSendBackTo" name="sendBackTo"" id="sendBackTo"></div>'
            );
        VirtualSelect.init({
            ele: "#sendBackTo",
            search: false,
            hideClearButton: true,
            options: sendBackOption,
            selectedValue:
                sendBackOption.length == 1 ? sendBackOption[0].value : null,
        });
    }
}

function hidePopup() {
    if (popperInstance) {
        let action = $("#popperAction").attr("data-type");
        if (
            action == "APPROVE" ||
            action == "REJECT" ||
            action == "SEND_BACK"
        ) {
            let existingIndex = commentBuffer.findIndex(
                (item) => item.action === action
            );
            if (existingIndex !== -1) {
                // Update existing item
                commentBuffer[existingIndex].comment = $("#commentAction")
                    .val()
                    .trim();
            } else if ($("#commentAction").val().trim() != "") {
                // Push new item
                commentBuffer.push({
                    action: action,
                    comment: $("#commentAction").val().trim(),
                });
            }
        }

        $("#popperAction").attr("data-type", "");
        $(".popper-container").addClass("d-none");

        popperInstance.destroy();
    }
}

async function getSendBackOptions(params) {
    let option = `<option></option>`;
    try {
        const response = await fetch(
            `/doc_approval/getSendBackOptions?type=PURCHASING&tokenForm=${params["tokenForm"]}`,
            {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                    Referer: window.location.href,
                },
            }
        );

        const data = await response.json();
        if (response.status === 200) {
            const options = data.map((row) => ({
                value: row.sign_flow_id,
                label: row.employee_name + " - ORDER FORM " + row.flow_as,
            }));
            return options;
        } else if (response.status === 401) {
            let countdown = 5;
            Snackbar.show({
                pos: "bottom-center",
                duration: "6000",
                text: `<i class="fa-solid fa-circle-exclamation fa-lg fa-fw text-warning"></i> Session expired, please refresh this page or it will automatically refresh in <span id="snackbar-countdown">${countdown}</span> seconds.`,
            });

            let countdownInterval = setInterval(() => {
                countdown--;
                document.getElementById("snackbar-countdown").textContent =
                    countdown;
                if (countdown < 0) {
                    clearInterval(countdownInterval);
                    window.location.href = data.redirect_uri;
                }
            }, 1000);
        } else {
            console.error("HTTP Error:", response.status);
        }
    } catch (error) {
        return;
    }
}

$(document).on("click", ".confirmAction", function () {
    let $this = $(this);
    Snackbar.close();
    clearValidation();
    const formData = new FormData($("#formAction")[0]);
    formData.append("actionType", $this.attr("data-type"));
    formData.append("rowId", $this.attr("data-row"));

    const escapedData = new FormData();
    for (let [key, value] of formData.entries()) {
        if (value instanceof File) {
            const safeFileName = escapeFilename(value.name);
            const safeFile = new File([value], safeFileName, {
                type: value.type,
            });
            escapedData.append(key, safeFile);
        } else if (key.endsWith("[]")) {
            // Handle array fields
            const baseKey = key.replace("[]", "");
            const currentValues = escapedData.getAll(baseKey) || [];
            escapedData.delete(baseKey); // Hapus yang lama
            currentValues.push(escapeInput(value));
            currentValues.forEach((v) => escapedData.append(baseKey + "[]", v));
        } else {
            // Handle regular fields
            escapedData.append(key, escapeInput(value));
        }
    }

    $(this).btnLoading(async function () {
        try {
            $(".confirmAction").prop("disabled", true);
            $this.html('<i class="fas fa-spinner fa-spin"></i> Please wait');
            const response = await fetch("/doc_approval/updateAction", {
                method: "POST",
                body: escapedData,
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    Accept: "application/json",
                    Referer: window.location.href,
                },
            });

            const result = await response.json();
            if (response.status === 200) {
                asideHide();
                Snackbar.show({
                    pos: "bottom-center",
                    text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result["message"]}`,
                });
                $(".modal").modal("hide");
            } else if (response.status === 401) {
                Snackbar.show({
                    pos: "bottom-center",
                    duration: "6000",
                    text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Unauthorized access, please login or refresh this page',
                });
            } else if (response.status === 422) {
                handleValidationErrors(result.errors);
                Snackbar.show({
                    pos: "bottom-center",
                    text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : ${result["message"]}`,
                });
            } else if (response.status === 419) {
                handleValidationErrors(result.errors);
                Snackbar.show({
                    pos: "bottom-center",
                    text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : CSRF token mismatch, please refresh this page',
                });
            } else {
                Snackbar.show({
                    pos: "bottom-center",
                    text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed ${response.status} : ${response.statusText}`,
                });
            }
        } catch (error) {
            Snackbar.show({
                pos: "bottom-center",
                text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}`,
            });
        } finally {
            $(".confirmAction").prop("disabled", false);
        }
    });
});

$(document)
    .off("change", "#fileUpload")
    .on("change", "#fileUpload", function (event) {
        handleFiles(event.target.files);
    });

$(document)
    .off("click", "#selectFileBtn")
    .on("click", "#selectFileBtn", function (event) {
        $("#fileUpload").trigger("click");
    });

$(document)
    .off("dragover", "#dropZone")
    .on("dragover", "#dropZone", function (event) {
        event.preventDefault();
        $(this).addClass("dragover");
    });

$(document)
    .off("dragleave", "#dropZone")
    .on("dragleave", "#dropZone", function (event) {
        event.preventDefault();
        $(this).removeClass("dragover");
    });

$(document)
    .off("drop", "#dropZone")
    .on("drop", "#dropZone", function (event) {
        event.preventDefault();
        Snackbar.close();
        $(this).removeClass("dragover");
        const files = event.originalEvent.dataTransfer.files;
        handleFiles(files);
    });

$(document)
    .off("click", "#clearFiles")
    .on("click", "#clearFiles", function (event) {
        var selectedFiles = [];
        var selectedFilesProperties = [];
        renderFileList();
    });

$(document)
    .off("click", ".deleteFileItem")
    .on("click", ".deleteFileItem", async function (event) {
        const fileItem = $(this).closest(".form-group");
        const index = fileItem.data("index");
        selectedFiles.splice(index, 1);
        selectedFilesProperties.splice(index, 1);
        renderFileList();
    });

async function handleFiles(files) {
    Snackbar.close();
    $("#fileList").append(`
        <div class="file-item list-group-item list-group-item-action" id="fileItemSkeleton">
            <div class="file-details">
                <i class="fa-solid fa-spinner fa-spin file-icon"></i>
                <div>
                    <span class="file-name skeletonText">Please wait...</span>
                </div>
            </div>
        </div>`);

    let renderFile = false;
    const allowedMimeTypes = [
        "application/pdf",
        "image/jpeg",
        "image/png",
        "application/vnd.ms-excel",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "application/vnd.ms-outlook",
        "application/ms-tnef",
        "message/rfc822",
        "",
    ];

    return Promise.resolve()
        .then(() => {
            document
                .querySelector(`#fileItemSkeleton`)
                .scrollIntoView({ behavior: "smooth", block: "start" }, true);
        })
        .then(() => {
            return Promise.all(Array.from(files).map((file) => readFile(file)));
        })
        .then((processedFiles) => {
            processedFiles.forEach(({ file, content }) => {
                let isAllowed = allowedMimeTypes.includes(file.type);
                if (
                    isAllowed &&
                    (file.name.endsWith(".msg") || file.name.endsWith(".eml"))
                ) {
                    isAllowed = true;
                } else if (file.type == "") {
                    isAllowed = false;
                }

                const isFileNameExists = selectedFilesProperties.some(function (
                    property
                ) {
                    return property.name === file.name;
                });

                if (!isAllowed) {
                    Snackbar.show({
                        pos: "bottom-center",
                        duration: "6000",
                        text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> File "${file.name}" is not a valid type.`,
                    });
                    return;
                } else if (file.size > maxFileSize) {
                    Snackbar.show({
                        pos: "bottom-center",
                        duration: "6000",
                        text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> File "${file.name}" exceeds the 5MB size limit.`,
                    });
                } else if (isFileNameExists) {
                    Snackbar.show({
                        pos: "bottom-center",
                        duration: "6000",
                        text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> File "${file.name}" is already selected.`,
                    });
                } else {
                    renderFile = true;
                    selectedFiles.push(file);
                    const properties = {
                        name: file.name,
                        type: file.type,
                        size: file.size,
                        exist: false,
                        content: content, // Menyimpan konten file jika diperlukan
                    };
                    selectedFilesProperties.push(properties);
                }
            });
        })
        .finally(() => {
            if (renderFile) {
                renderFileList();
            } else {
                $("#fileItemSkeleton").remove();
            }
        });
}

function readFile(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (e) =>
            resolve({ file: file, content: e.target.result });
        reader.onerror = (e) => reject(e);
        reader.onprogress = (e) => {
            if (e.lengthComputable) {
                const percentLoaded = Math.round((e.loaded / e.total) * 100);
                $(".skeletonText").text(
                    `Uploading progress : ${percentLoaded}%`
                );
            }
        };
        reader.readAsArrayBuffer(file); // Atau gunakan readAsText() untuk file teks
    });
}

function renderFileList() {
    const fileList = $("#fileList");
    fileList.empty();

    selectedFilesProperties.forEach((file, index) => {
        const fileName = file.name;
        const fileType = fileName.split(".").pop().toLowerCase();
        let iconClass = getFileIcon(file.type, file.name);
        let fileSize = file.size ? `${(file.size / 1024).toFixed(2)} KB` : "";

        let fileItem = "";
        if (file.exist === true) {
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
        } else {
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

$(document)
    .off("click", ".docVersionItem")
    .on("click", ".docVersionItem", function (event) {
        const $this = $(this);
        if (!$this.hasClass("active")) {
            $(".docVersionItem").removeClass("active");
            $this.addClass("active");
            const params = {
                source: "WEB",
                form: $this.attr("data-form"),
                token: $this.attr("data-token"),
                type: "VIEW",
            };
            viewForm(params);
        }
    });

$(document)
    .off("click", ".filterButtonTable")
    .on("click", ".filterButtonTable", async function (event) {
        let $this = $(this);
        const table = $this.attr("data-table");
        asideHide();
        $("#asideDetailForm").html(`<div class="row min-vh-75">
                                    <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                        <div class="center-container">
                                            <div class="stripes-red-blue stripes-red-blue-md"></div>
                                            <div class="d-block fs-7 mt-2">Loading...</div>
                                        </div>
                                    </div>
                                </div>`);
        $("#globalAside")
            .find(".aside-header")
            .find(".aside-title")
            .html($this.attr("title"));
        $(".overlay-aside").addClass("show").trigger("shown");
        $("#globalAside").addClass("show aside-lg").trigger("shown");
        $("body").addClass("overflow-hidden");
        $("#asideDetailForm").scrollTop(0);

        let filterCompany = $this
            .closest("form")
            .find('select[name="company"]')
            .html();
        let company = $this
            .closest("form")
            .find('select[name="company"]')
            .val();
        let applicant = $this
            .closest("form")
            .find('input[name="applicant"]')
            .val();
        let status = $this.closest("form").find('input[name="status"]').val();
        let orderStartDate = $this
            .closest("form")
            .find('input[name="orderStartDate"]')
            .val();
        let orderEndDate = $this
            .closest("form")
            .find('input[name="orderEndDate"]')
            .val();
        let receivedStartDate = $this
            .closest("form")
            .find('input[name="receivedStartDate"]')
            .val();
        let receivedEndDate = $this
            .closest("form")
            .find('input[name="receivedEndDate"]')
            .val();
        let invStartDate = $this
            .closest("form")
            .find('input[name="invStartDate"]')
            .val();
        let invEndDate = $this
            .closest("form")
            .find('input[name="invEndDate"]')
            .val();
        let sortBy = $this.closest("form").find('input[name="sortBy"]').val();
        let startAmount = $this
            .closest("form")
            .find('input[name="startAmount"]')
            .val();
        let endAmount = $this
            .closest("form")
            .find('input[name="endAmount"]')
            .val();
        let ccy = $this.closest("form").find('input[name="ccy"]').val();

        if (orderStartDate && orderEndDate) {
            orderStartDate = new Date(orderStartDate);
            orderEndDate = new Date(orderEndDate);
        }

        if (receivedStartDate && receivedEndDate) {
            receivedStartDate = new Date(receivedStartDate);
            receivedEndDate = new Date(receivedEndDate);
        }

        if (invStartDate && invEndDate) {
            invStartDate = new Date(invStartDate);
            invEndDate = new Date(invEndDate);
        }

        startAmount = startAmount !== "" ? currencyFormat(startAmount, 0) : "";
        endAmount = endAmount !== "" ? currencyFormat(endAmount, 0) : "";

        let form = `<div class="form-group mb-3">
                    <label for="filterQuery" class="form-label">Search Query :</label>
                    <input type="text" class="form-control d-flex searchInput" name="filterQuery" id="filterQuery" value="${$this
                        .closest("form")
                        .find('input[name="search"]')
                        .val()}" spellcheck="false" autocomplete="off" placeholder="Search...">
                </div>
                <div class="form-group mb-3">
                    <label for="filterCompany" class="form-label">Company<span class="required"></span> :</label>
                    <select class="select2" name="filterCompany" id="filterCompany"></select>
                </div>
                <div class="form-group mb-3">
                    <label for="filterApplicant" class="form-label">PO Applicant<span class="required"></span> :</label>
                    <div class="skeleton" id="filterApplicant"></div>
                </div>
                <div class="form-group mb-3">
                    <label for="filterPoStatus" class="form-label">PO Status<span class="required"></span> :</label>
                    <select class="select2" name="filterPoStatus" id="filterPoStatus">
                        <option></option>
                        <option value="ALL">-- ALL STATUS --</option>
                        <option value="22">INVOICED</option>
                        <option value="12">CANCELED</option>
                        <option value="9">REJECTED</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="filterSortBy" class="form-label">Sort By<span class="required"></span> :</label>
                    <select class="select2" name="filterSortBy" id="filterSortBy">
                        <option></option>
                        <option value="LATEST">LATEST ARCHIVED</option>
                        <option value="OLDEST">OLDEST ARCHIVED</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="filterOrderDate" class="form-label">Order Date :<span class="fst-italic fw-normal fs-9"> (Left blank if selecting all dates)</span></label>
                    <div class="form-group" id="filterOrderDate" data-coreui-name="filterOrderDate" data-coreui-toggle="date-range-picker" data-coreui-date="" data-coreui-timepicker="false"></div>
                </div>
                <div class="form-group mb-3">
                    <label for="filterReceivedDate" class="form-label">Received Date :<span class="fst-italic fw-normal fs-9"> (Left blank if selecting all dates)</span></label>
                    <div class="form-group" id="filterReceivedDate" data-coreui-name="filterReceivedDate" data-coreui-toggle="date-range-picker" data-coreui-date="" data-coreui-timepicker="false"></div>
                </div>
                <div class="form-group mb-3">
                    <label for="filterInvDate" class="form-label">Invoice Date :<span class="fst-italic fw-normal fs-9"> (Left blank if selecting all dates)</span></label>
                    <div class="form-group" id="filterInvDate" data-coreui-name="filterInvDate" data-coreui-toggle="date-range-picker" data-coreui-date="" data-coreui-timepicker="false"></div>
                </div>
                <div class="form-group mb-3">
                    <label for="filterInvDate" class="form-label">Total Amount :<span class="fst-italic fw-normal fs-9"> (Left blank if selecting all amount)</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control currencyValue" placeholder="Start amount" id="filterAmountStart" name="filterAmountStart" value="${startAmount}">
                        <span class="input-group-separator separator-arrow-right"></span>
                        <input type="text" class="form-control currencyValue" placeholder="End amount" id="filterAmountEnd" name="filterAmountEnd" value="${endAmount}">
                        <span class="input-group-text p-0">
                            <select class="form-select text-end" id="filterAmountCcy" name="filterAmountCcy">
                                <option value="IDR">IDR</option>
                                <option value="USD">USD</option>
                                <option value="JPY">JPY</option>
                            </select>
                        </span>
                    </div>
                </div>`;

        $("#asideDetailForm").html(`${form}`);

        $("#filterCompany").html(filterCompany);
        $("#filterCompany")
            .select2({
                dropdownParent: $("#globalAside"),
                allowClear: false,
                placeholder: "-- Select --",
            })
            .on("select2:select", async function (e) {
                if (
                    $("#filterApplicant").hasClass("select2-hidden-accessible")
                ) {
                    $("#filterApplicant").val(null).empty().trigger("change");
                    $("#filterApplicant").select2("destroy");
                }

                $("#filterApplicant").replaceWith(
                    `<div class="skeleton" id="filterApplicant"></div>`
                );
                let optionApplicant = await getUserSelection({
                    type: "PO_APPLICANT",
                    companyId: $(this).val(),
                    selectedId: applicant,
                });
                $("#filterApplicant").replaceWith(
                    `<select class="select2" name="filterApplicant" id="filterApplicant"></select>`
                );
                $("#filterApplicant").select2({
                    dropdownParent: $("#globalAside"),
                    allowClear: false,
                    placeholder: "-- Select --",
                    data: optionApplicant,
                    templateResult: formatResultRemote,
                    templateSelection: function (data) {
                        return data.text || data.id;
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    },
                });
            })
            .val(company)
            .trigger("change");

        $("#filterPoStatus")
            .select2({
                dropdownParent: $("#globalAside"),
                allowClear: false,
                placeholder: "-- Select --",
            })
            .val(status)
            .trigger("change");

        $("#filterSortBy")
            .select2({
                dropdownParent: $("#globalAside"),
                allowClear: false,
                placeholder: "-- Select --",
            })
            .val(sortBy)
            .trigger("change");

        if (startAmount != "" && endAmount != "") {
            if (ccy === "ALL" || ccy === "IDR") {
                $("#filterAmountCcy").val("IDR").trigger("change");
            } else {
                $("#filterAmountCcy").val(ccy).trigger("change");
            }
        }

        const optionsOrderDatePicker = {
            locale: "en-US",
            inputDateFormat: (date) =>
                dayjs(date).locale("en").format("DD-MM-YYYY"),
            inputDateParse: (date) => dayjs(date, "DD-MM-YYYY", "id").toDate(),
            // minDate: dayjs(new Date()),
            maxDate: dayjs(new Date()),
            showAdjacementDays: false,
            container: "#globalAside",
            placement: "top-start",
            startDate: orderStartDate,
            endDate: orderEndDate,
            ranges: {
                Today: [new Date(), new Date()],
                Yesterday: [
                    new Date(new Date().setDate(new Date().getDate() - 1)),
                    new Date(new Date().setDate(new Date().getDate() - 1)),
                ],
                "Last 7 Days": [
                    new Date(new Date().setDate(new Date().getDate() - 6)),
                    new Date(new Date()),
                ],
                "Last 30 Days": [
                    new Date(new Date().setDate(new Date().getDate() - 29)),
                    new Date(new Date()),
                ],
                "Last 90 Days": [
                    new Date(new Date().setDate(new Date().getDate() - 89)),
                    new Date(new Date()),
                ],
                "This Month": [
                    new Date(new Date().setDate(1)),
                    new Date(
                        new Date().getFullYear(),
                        new Date().getMonth() + 1,
                        0
                    ),
                ],
                "Last Month": [
                    new Date(
                        new Date().getFullYear(),
                        new Date().getMonth() - 1,
                        1
                    ),
                    new Date(
                        new Date().getFullYear(),
                        new Date().getMonth(),
                        0
                    ),
                ],
                "This Year": [
                    new Date(new Date().getFullYear(), 0),
                    new Date(new Date()),
                ],
                "Last Year": [
                    new Date(new Date().getFullYear() - 1, 0),
                    new Date(new Date().getFullYear(), 0, 0),
                ],
            },
        };

        const optionsReceivedDatePicker = {
            locale: "en-US",
            inputDateFormat: (date) =>
                dayjs(date).locale("en").format("DD-MM-YYYY"),
            inputDateParse: (date) => dayjs(date, "DD-MM-YYYY", "id").toDate(),
            // minDate: dayjs(new Date()),
            maxDate: dayjs(new Date()),
            showAdjacementDays: false,
            container: "#globalAside",
            placement: "top-start",
            startDate: receivedStartDate,
            endDate: receivedEndDate,
            ranges: {
                Today: [new Date(), new Date()],
                Yesterday: [
                    new Date(new Date().setDate(new Date().getDate() - 1)),
                    new Date(new Date().setDate(new Date().getDate() - 1)),
                ],
                "Last 7 Days": [
                    new Date(new Date().setDate(new Date().getDate() - 6)),
                    new Date(new Date()),
                ],
                "Last 30 Days": [
                    new Date(new Date().setDate(new Date().getDate() - 29)),
                    new Date(new Date()),
                ],
                "Last 90 Days": [
                    new Date(new Date().setDate(new Date().getDate() - 89)),
                    new Date(new Date()),
                ],
                "This Month": [
                    new Date(new Date().setDate(1)),
                    new Date(
                        new Date().getFullYear(),
                        new Date().getMonth() + 1,
                        0
                    ),
                ],
                "Last Month": [
                    new Date(
                        new Date().getFullYear(),
                        new Date().getMonth() - 1,
                        1
                    ),
                    new Date(
                        new Date().getFullYear(),
                        new Date().getMonth(),
                        0
                    ),
                ],
                "This Year": [
                    new Date(new Date().getFullYear(), 0),
                    new Date(new Date()),
                ],
                "Last Year": [
                    new Date(new Date().getFullYear() - 1, 0),
                    new Date(new Date().getFullYear(), 0, 0),
                ],
            },
        };

        const optionsInvDatePicker = {
            locale: "en-US",
            inputDateFormat: (date) =>
                dayjs(date).locale("en").format("DD-MM-YYYY"),
            inputDateParse: (date) => dayjs(date, "DD-MM-YYYY", "id").toDate(),
            // minDate: dayjs(new Date()),
            maxDate: dayjs(new Date()),
            showAdjacementDays: false,
            container: "#globalAside",
            placement: "top-start",
            startDate: invStartDate,
            endDate: invEndDate,
            ranges: {
                Today: [new Date(), new Date()],
                Yesterday: [
                    new Date(new Date().setDate(new Date().getDate() - 1)),
                    new Date(new Date().setDate(new Date().getDate() - 1)),
                ],
                "Last 7 Days": [
                    new Date(new Date().setDate(new Date().getDate() - 6)),
                    new Date(new Date()),
                ],
                "Last 30 Days": [
                    new Date(new Date().setDate(new Date().getDate() - 29)),
                    new Date(new Date()),
                ],
                "Last 90 Days": [
                    new Date(new Date().setDate(new Date().getDate() - 89)),
                    new Date(new Date()),
                ],
                "This Month": [
                    new Date(new Date().setDate(1)),
                    new Date(
                        new Date().getFullYear(),
                        new Date().getMonth() + 1,
                        0
                    ),
                ],
                "Last Month": [
                    new Date(
                        new Date().getFullYear(),
                        new Date().getMonth() - 1,
                        1
                    ),
                    new Date(
                        new Date().getFullYear(),
                        new Date().getMonth(),
                        0
                    ),
                ],
                "This Year": [
                    new Date(new Date().getFullYear(), 0),
                    new Date(new Date()),
                ],
                "Last Year": [
                    new Date(new Date().getFullYear() - 1, 0),
                    new Date(new Date().getFullYear(), 0, 0),
                ],
            },
        };

        new coreui.DateRangePicker(
            document.getElementById("filterOrderDate"),
            optionsOrderDatePicker
        );
        new coreui.DateRangePicker(
            document.getElementById("filterReceivedDate"),
            optionsReceivedDatePicker
        );
        new coreui.DateRangePicker(
            document.getElementById("filterInvDate"),
            optionsInvDatePicker
        );
        setTimeout(() => {
            let dropdown = document.querySelector(".daterangepicker");
            if (dropdown) {
                dropdown.setAttribute("data-popper-placement", "top-start");
            }
        }, 100);

        if ($("#filterApplicant").hasClass("select2-hidden-accessible")) {
            $("#filterApplicant").val(null).empty().trigger("change");
            $("#filterApplicant").select2("destroy");
        }

        let optionApplicant = await getUserSelection({
            type: "PO_APPLICANT",
            companyId: company,
            selectedId: applicant,
        });
        $("#filterApplicant").replaceWith(
            `<select class="select2" name="filterApplicant" id="filterApplicant"></select>`
        );
        $("#filterApplicant").select2({
            dropdownParent: $("#globalAside"),
            allowClear: false,
            placeholder: "-- Select --",
            data: optionApplicant,
            templateResult: formatResultRemote,
            templateSelection: function (data) {
                return data.text || data.id;
            },
            escapeMarkup: function (markup) {
                return markup;
            },
        });

        $(
            ".asideFooterBtn"
        ).html(`<div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                    <button type="button" class="btn btn-default w-100 w-md-auto me-2 resetFilterTable" data-table="${table}" title="Reset Filter">Reset Filter</button>
                                </div>
                                <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                    <button type="button" class="btn btn-info w-100 w-md-auto me-2 showResultFilterTable" data-table="${table}" title="Show Filter Result">Show Result</button>
                                </div>`);
    });

$(document).on("click", ".resetFilterTable", function (event) {
    let $this = $(this);
    const tableId = $this.attr("data-table");
    const formContainer = $(`#${tableId}`)
        .closest(".table-container")
        .find("form.formSearchTable");
    const table = $(`#${tableId}`).DataTable();

    let companyDefault = formContainer
        .find('select[name="company"]')
        .find("option[selected]")
        .val();
    let applicantDefault = formContainer
        .find('input[name="applicantDefault"]')
        .val();

    formContainer
        .find('select[name="company"]')
        .val(companyDefault)
        .trigger("change");
    formContainer.find('input[name="applicant"]').val(applicantDefault);
    formContainer.find('input[name="status"]').val("ALL");
    formContainer.find('input[name="sortBy"]').val("LATEST");
    formContainer.find('input[name="orderStartDate"]').val("");
    formContainer.find('input[name="orderEndDate"]').val("");
    formContainer.find('input[name="receivedStartDate"]').val("");
    formContainer.find('input[name="receivedEndDate"]').val("");
    formContainer.find('input[name="invStartDate"]').val("");
    formContainer.find('input[name="invEndDate"]').val("");
    formContainer.find('input[name="startAmount"]').val("");
    formContainer.find('input[name="endAmount"]').val("ALL");

    formContainer
        .find("button.filterButtonTable")
        .removeClass("red-dot-checked");
    asideHide();
    table.ajax.reload();
});

$(document).on("click", ".showResultFilterTable", function (event) {
    let $this = $(this);
    const tableId = $this.attr("data-table");
    const formContainer = $(`#${tableId}`)
        .closest(".table-container")
        .find("form.formSearchTable");
    const table = $(`#${tableId}`).DataTable();
    let searchQuery = $("#asideDetailForm").find("#filterQuery").val().trim();
    let companyDefault = formContainer
        .find('select[name="company"]')
        .find("option[selected]")
        .val();
    let company = $("#asideDetailForm").find("#filterCompany").val();
    let applicantDefault = formContainer
        .find('input[name="applicantDefault"]')
        .val();
    let applicant = $("#asideDetailForm").find("#filterApplicant").val();
    let status = $("#asideDetailForm").find("#filterPoStatus").val();
    let sortBy = $("#asideDetailForm").find("#filterSortBy").val();
    let orderStartDate = "",
        orderEndDate = "";
    let receivedStartDate = "",
        receivedEndDate = "";
    let invStartDate = "",
        invEndDate = "";
    let startAmount = $("#asideDetailForm")
        .find("#filterAmountStart")
        .val()
        .replace(",", "");
    let endAmount = $("#asideDetailForm")
        .find("#filterAmountEnd")
        .val()
        .replace(",", "");
    let ccy = $("#asideDetailForm").find("#filterAmountCcy").val();

    const orderPicker = coreui.DateRangePicker.getInstance(
        document.getElementById("filterOrderDate")
    );
    if (orderPicker._startDate && orderPicker._endDate) {
        orderStartDate = dayjs(orderPicker._startDate).format("YYYY-MM-DD");
        orderEndDate = dayjs(orderPicker._endDate).format("YYYY-MM-DD");
    }

    const receivedPicker = coreui.DateRangePicker.getInstance(
        document.getElementById("filterReceivedDate")
    );
    if (receivedPicker._startDate && receivedPicker._endDate) {
        receivedStartDate = dayjs(receivedPicker._startDate).format(
            "YYYY-MM-DD"
        );
        receivedEndDate = dayjs(receivedPicker._endDate).format("YYYY-MM-DD");
    }

    const invPicker = coreui.DateRangePicker.getInstance(
        document.getElementById("filterInvDate")
    );
    if (invPicker._startDate && invPicker._endDate) {
        invStartDate = dayjs(invPicker._startDate).format("YYYY-MM-DD");
        invEndDate = dayjs(invPicker._endDate).format("YYYY-MM-DD");
    }

    formContainer.find('input[name="search"]').val(searchQuery);
    if (searchQuery == "") {
        formContainer.find('input[name="search"]').attr("data-iscleared", true);
        formContainer
            .find('input[name="search"]')
            .closest("div.form-group")
            .find("button.searchButtonClearTable")
            .removeClass("d-none")
            .addClass("d-none");
    } else {
        formContainer
            .find('input[name="search"]')
            .attr("data-iscleared", false);
        formContainer
            .find('input[name="search"]')
            .closest("div.form-group")
            .find("button.searchButtonClearTable")
            .removeClass("d-none");
    }

    formContainer.find('select[name="company"]').val(company).trigger("change");
    formContainer.find('input[name="applicant"]').val(applicant);
    formContainer.find('input[name="status"]').val(status);
    formContainer.find('input[name="sortBy"]').val(sortBy);
    formContainer.find('input[name="orderStartDate"]').val(orderStartDate);
    formContainer.find('input[name="orderEndDate"]').val(orderEndDate);
    formContainer
        .find('input[name="receivedStartDate"]')
        .val(receivedStartDate);
    formContainer.find('input[name="receivedEndDate"]').val(receivedEndDate);
    formContainer.find('input[name="invStartDate"]').val(invStartDate);
    formContainer.find('input[name="invEndDate"]').val(invEndDate);
    formContainer.find('input[name="startAmount"]').val(startAmount);
    formContainer.find('input[name="endAmount"]').val(endAmount);

    if (startAmount != "" && endAmount != "") {
        formContainer.find('input[name="ccy"]').val(ccy);
    } else {
        formContainer.find('input[name="ccy"]').val("ALL");
    }

    formContainer
        .find("button.filterButtonTable")
        .removeClass("red-dot-checked");
    if (
        company != companyDefault ||
        applicant != applicantDefault ||
        status != "ALL" ||
        sortBy != "LATEST" ||
        orderStartDate != "" ||
        orderEndDate != "" ||
        receivedStartDate != "" ||
        receivedEndDate != "" ||
        invStartDate != "" ||
        invEndDate != "" ||
        startAmount != "" ||
        endAmount != ""
    ) {
        formContainer
            .find("button.filterButtonTable")
            .addClass("red-dot-checked");
    }

    asideHide();
    table.ajax.reload();
});

$(document).on("mousedown", ".view-file", function (e) {
    if (e.detail > 1) {
        e.preventDefault();
    }
});

$(document)
    .off("click", ".view-file")
    .on("click", ".view-file", async function (event) {
        let $this = $(this);
        event.stopImmediatePropagation();
        const selectedText = window.getSelection().toString().trim();
        if (selectedText || $this.hasClass("active")) {
            event.preventDefault();
            return false;
        }

        $(".view-file").removeClass("active");
        $this.addClass("active");
        $(".fullscreen-title").html($this.find("span.file-name").html());
        $("#fullscreen-content-aside").html(`<div class="loading-content">
                                            <div class="center-container text-white">
                                                <div class="stripes-red-blue stripes-red-blue-md"></div>
                                                <div class="d-block fs-7 mt-2">Loading...</div>
                                            </div>
                                        </div>`);
        $("body").addClass("overflow-hidden");
        $(".fullscreen-container-aside")
            .removeClass("hide")
            .addClass("show")
            .trigger("shown");
        try {
            const response = await fetch(
                `/render?token=${$this.attr("data-token")}`,
                {
                    method: "GET",
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        Accept: "application/json",
                        Referer: window.location.href,
                    },
                }
            );

            const data = await response.json();
            if (response.status === 200) {
                if (data.status === 200) {
                    // const { mimeType, fileExtension, fileContent } = fileInfo;
                    // const container = document.getElementById('file-container');
                    // container.innerHTML = ''; // Clear previous content
                    $("#fullscreen-content-aside").html(
                        `<object id="subfile_frame" data="${data.data.frameSrc}" type="text/html"><param name="allowfullscreen" value="true"></object>`
                    );
                } else if (data.status === 404) {
                    $(".loading-content").html(
                        `<p class="mt-2 text-company fs-7"><i class="fa-regular fa-file-circle-xmark fa-lg fa-fw"></i> ${data.message}</p>`
                    );
                }
            } else if (response.status === 401) {
                Snackbar.show({
                    pos: "bottom-center",
                    duration: "6000",
                    text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Unauthorized access, please login or refresh this page',
                });
                $(".loading-content").html(
                    `<p class="mt-2 text-company fs-7"><i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Unauthorized access, please login or refresh this page</p>`
                );
            } else if (response.status === 422) {
                Snackbar.show({
                    pos: "bottom-center",
                    text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : ${data.message}`,
                });
                $(".loading-content").html(`Failed : ${data.message}</p>`);
            } else if (response.status === 419) {
                Snackbar.show({
                    pos: "bottom-center",
                    text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : CSRF token mismatch, please refresh this page',
                });
                $(".loading-content").html(
                    `<p class="mt-2 text-company fs-7"><i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : CSRF token mismatch, please refresh this page</p>`
                );
            } else {
                Snackbar.show({
                    pos: "bottom-center",
                    text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${response.status} ${response.statusText}`,
                });
                $(".loading-content").html(
                    `<p class="mt-2 text-company fs-7"> ${response.status} ${response.statusText}</p>`
                );
            }
        } catch (error) {
            Snackbar.show({
                pos: "bottom-center",
                text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}`,
            });
            $(".loading-content").html(
                `<p class="mt-2 text-company fs-7"><i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}</p>`
            );
        }
    });

$(document)
    .off("click", ".view-file-fullscreen")
    .on("click", ".view-file-fullscreen", async function (event) {
        let $this = $(this);
        event.stopImmediatePropagation();
        $(".fullscreen-title").html($this.find("span.file-name").html());
        $("#fullscreen-content-container").html(`<div class="loading-content">
                                                <div class="center-container text-white">
                                                    <div class="stripes-red-blue stripes-red-blue-md"></div>
                                                    <div class="d-block fs-7 mt-2">Loading...</div>
                                                </div>
                                            </div>`);
        $("body").addClass("overflow-hidden");
        $(".fullscreen-container")
            .removeClass("hide")
            .addClass("show")
            .trigger("shown");
        try {
            const response = await fetch(
                `/render?token=${$this
                    .find("div.file-details")
                    .attr("data-token")}`,
                {
                    method: "GET",
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        Accept: "application/json",
                        Referer: window.location.href,
                    },
                }
            );

            const data = await response.json();
            if (response.status === 200) {
                if (data.status === 200) {
                    // const { mimeType, fileExtension, fileContent } = fileInfo;
                    // const container = document.getElementById('file-container');
                    // container.innerHTML = ''; // Clear previous content
                    $("#fullscreen-content-container").html(
                        `<object id="subfile_frame" data="${data.data.frameSrc}" type="text/html"><param name="allowfullscreen" value="true"></object>`
                    );
                } else if (data.status === 404) {
                    $(".loading-content").html(
                        `<p class="mt-2 text-company fs-7"><i class="fa-regular fa-file-circle-xmark fa-lg fa-fw"></i> ${data.message}</p>`
                    );
                }
            } else if (response.status === 401) {
                Snackbar.show({
                    pos: "bottom-center",
                    duration: "6000",
                    text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Unauthorized access, please login or refresh this page',
                });
                $(".loading-content").html(
                    `<p class="mt-2 text-company fs-7"><i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Unauthorized access, please login or refresh this page</p>`
                );
            } else if (response.status === 422) {
                Snackbar.show({
                    pos: "bottom-center",
                    text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : ${data.message}`,
                });
                $(".loading-content").html(`Failed : ${data.message}</p>`);
            } else if (response.status === 419) {
                Snackbar.show({
                    pos: "bottom-center",
                    text: '<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : CSRF token mismatch, please refresh this page',
                });
                $(".loading-content").html(
                    `<p class="mt-2 text-company fs-7"><i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : CSRF token mismatch, please refresh this page</p>`
                );
            } else {
                Snackbar.show({
                    pos: "bottom-center",
                    text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${response.status} ${response.statusText}`,
                });
                $(".loading-content").html(
                    `<p class="mt-2 text-company fs-7"> ${response.status} ${response.statusText}</p>`
                );
            }
        } catch (error) {
            Snackbar.show({
                pos: "bottom-center",
                text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}`,
            });
            $(".loading-content").html(
                `<p class="mt-2 text-company fs-7"><i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}</p>`
            );
        }
    });

async function getDocumentList(params) {
    const dataType = params["dataType"];
    const tokenForm = params["token"];
    try {
        const response = await fetch(
            `/doc_approval/getDocumentList?token=${tokenForm}`,
            {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                    Referer: window.location.href,
                },
            }
        );

        const result = await response.json();
        if (response.status === 200) {
            return result;
        } else {
            console.error("HTTP Error:", response.status);
        }
    } catch (error) {
        return;
    }
}

async function getAttachment(params) {
    try {
        const response = await fetch(
            `/doc_approval/getAttachment?type=${params["type"]}&token=${params["token"]}`,
            {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                    Referer: window.location.href,
                },
            }
        );

        const result = await response.json();
        if (response.status === 200) {
            return result;
        } else {
            console.error("HTTP Error:", response.status);
        }
    } catch (error) {
        return;
    }
}

async function getUserSelection(params) {
    try {
        const response = await fetch(
            `/proc_pur/userSelection?type=${params["type"]}&companyId=${params["companyId"]}&selectedId=${params["selectedId"]}`,
            {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                    Referer: window.location.href,
                },
            }
        );

        const result = await response.json();
        if (response.status === 200) {
            return result;
        } else {
            console.error("HTTP Error:", response.status);
        }
    } catch (error) {
        return;
    }
}
