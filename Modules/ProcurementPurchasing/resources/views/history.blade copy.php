@extends('layouts.app')

@section('extra_css')
{{-- <style>
    .data-details {
        display: grid;
        grid-template-columns: auto auto 1fr;
        gap: 0.5rem;
        align-items: start;
        line-height: 1 !important;
    }

    .data-row {
        display: contents;
    }

    .data-label {
        font-weight: 600;
        white-space: nowrap;
    }

    .data-separator {
        justify-self: center;
    }
</style> --}}
<style>
    #context-menu {
        min-width: 150px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    #context-menu .list-group-item {
        cursor: pointer;
    }
    .row-checkbox {
        cursor: pointer;
    }
    #purchasingHistoryTable tbody tr {
        cursor: pointer;
    }
    /* Custom styling for pagination */
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #dee2e6;
    }
    .dataTables_wrapper .dataTables_info {
        margin-top: 1rem;
    }
    /* Search box styling */
    #table-search {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    .input-group-text {
        background-color: #fff;
    }
    .snackbar-container .action {
        color: #fff;
        font-weight: bold;
        margin-left: 10px;
    }
</style>
@endsection

{{-- @section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb my-0">
            <li class="breadcrumb-item"><span>Document Approval</span></li>
            <li class="breadcrumb-item active"><span>Ongoing</span></li>
        </ol>
    </nav>
@endsection --}}

@section('content')
    <div class="container-lg px-3">
        <div class="card mb-4">
            <div class="card-header pb-0">
                <div class="card-title">Purchasing Archive</div>
            </div>
            <div class="card-body">
                <!-- Search Box -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="cil-search"></i>
                            </span>
                            <input type="text" id="table-search" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                </div>

                <!-- Context Menu -->
                <div id="context-menu" class="position-fixed bg-white shadow rounded p-2" style="display: none; z-index: 1000;">
                    <div class="list-group list-group-flush">
                        <button class="list-group-item list-group-item-action" data-action="edit">Edit</button>
                        <button class="list-group-item list-group-item-action" data-action="view">View Details</button>
                        <button class="list-group-item list-group-item-action" data-action="approve">Approve</button>
                    </div>
                </div>

                <div class="table-responsive" style="overflow-y: hidden;">
                    <table id="purchasingHistoryTable" class="table table-striped table-hover" style="width:100%">
                        <thead class="table-info">
                            {{-- <tr>
                                <th>
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th>No</th>
                                <th>PO No.</th>
                                <th>PO Date</th>
                                <th>Vendor</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr> --}}
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th>PO Number</th>
                                <th>Submitted Date</th>
                                <th>Vendor</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDocumentForm" data-coreui-backdrop="static" data-coreui-keyboard="false" data-coreui-focus="false" tabindex="-1"
        aria-labelledby="modalDocumentForm" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-xl my-md-2-2">
            <div class="modal-content h-100">
                <div class="modal-header" id="modalDocumentFormHeader">
                    <h6 class="modal-title d-flex align-items-center gap-1" id="modalDocumentFormTitle">Form Title</h6>
                    <div class="position-absolute end-0 me-5 actionHeaderContainer">
                    </div>
                    <button type="button" class="btn-close btn-close-modal" data-coreui-dismiss="modal"  title="Close" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0 pb-5" id="modalDocumentFormBody">
                </div>
                <div class="modal-footer" id="modalDocumentFormFooter"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalMessage" data-coreui-focus="false" tabindex="-1" aria-labelledby="modalMessage" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-md my-md-2-2" id="modalMessageDialog">
            <div class="modal-content h-100">
                <div class="modal-header" id="modalMessageHeader" style="background-color:#fff !important; border-bottom: none !important;">
                    <h6 class="modal-title modal-title-black" id="modalMessageTitle"></h6>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal"  title="Close" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0 pb-3" id="modalMessageBody">
                </div>
                <div class="modal-footer" id="modalMessageFooter" style="background-color:#fff !important; border-top: none !important;">
                </div>
            </div>
        </div>
    </div>

@endsection
@section('extra_js')
    <script src="{{ asset('js/app/scrollbar-custom.js?v=9.1') }}"></script>
    <script>
        dayjs.locale('id');
        dayjs.extend(window.dayjs_plugin_customParseFormat);
        const optionsRequiredDatePicker = {
            locale: 'en-US',
            inputDateFormat: date => dayjs(date).locale('en').format('DD-MM-YYYY'),
            inputDateParse: date => dayjs(date, 'DD-MM-YYYY', 'id').toDate(),
            // minDate: dayjs(new Date()),
            showAdjacementDays: false,
        }

        // history_table();

        let selectedRows = new Set();
        let table = $('#purchasingHistoryTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('purchasingHistory') }}",
            columns: [
                {
                    data: 'checkbox',
                    name: 'checkbox',
                    orderable: false,
                    searchable: false,
                    width: '30px'
                },
                { data: 'application_number', name: 'a.application_number' },
                { data: 'application_submitted_date', name: 'a.application_submitted_date' },
                { data: 'vendor_name', name: 'a.vendor_name' },
                { data: 'application_grand_total', name: 'a.application_grand_total' },
                { data: 'application_form_status', name: 'a.application_form_status' }
            ],
            order: [[1, 'desc']],  // Sort by application number by default
            dom: '<"row"<"col-sm-12"tr>><"row"<"col-sm-4"l><"col-sm-4"i><"col-sm-4"p>>',
            language: {
                search: '',
                searchPlaceholder: 'Search...',
                // paginate: {
                //     previous: '<i class="cil-arrow-left"></i>',
                //     next: '<i class="cil-arrow-right"></i>'
                // }
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
        });
        // function history_table(filter = null) {
        //     // if ($.fn.DataTable.isDataTable('#purchasingHistoryTable')) {
        //     //     $('#purchasingHistoryTable').DataTable().destroy();
        //     //     $('#purchasingHistoryTable tbody').empty();
        //     //     $('#purchasingHistoryTable tbody').html('');
        //     // }

        //     // $('#purchasingHistoryTable').dataTable({
        //     //     "responsive": true,
        //     //     "dom": '<"right"l><"left"f>rtip<"clear">',
        //     //     "processing": true,
        //     //     "serverSide": true,
        //     //     "order": [],
        //     //     // "fixedColumns": true,
        //     //     "iDisplayLength": 10,
        //     //     "bAutoWidth": false,
        //     //     "ajax": {
        //     //         "url": `/proc_pur/purchasingHistory?token=`,
        //     //         "type": "GET",
        //     //         "data": function(data) {
        //     //             // var formData = $('#form1').serializeArray();
        //     //             // $.each(formData, function(key, val) {
        //     //             //     if (val.name.indexOf('[]') > -1) {
        //     //             //         let name = val.name.split('[');
        //     //             //         data[val.name] = $('#' + name[0]).val();
        //     //             //     } else {
        //     //             //         data[val.name] = val.value;
        //     //             //     }
        //     //             // });
        //     //         }
        //     //     },
        //     //     "columnDefs": [	{"targets": 0,"width": "4%",'className': 'dt-body-center',"orderable": false},
        //     //                     {"targets": 1,"width": "20%","orderable": false},
        //     //                     {"targets": 2,"width": "10%","orderable": false},
        //     //                     {"targets": 3,"width": "25%","orderable": false},
        //     //                     {"targets": 4,"width": "15%","orderable": false},
        //     //                     {"targets": 5,"width": "20%","orderable": false},
        //     //                     {"targets": 6,"width": "6%","orderable": false},
        //     //                 ],
        //     // });

        //     let table = $('#purchasingHistoryTable').DataTable({
        //         processing: true,
        //         serverSide: true,
        //         responsive: true,
        //         ajax: "{{ route('purchasingHistory') }}",
        //         columns: [
        //             {
        //                 data: 'checkbox',
        //                 name: 'checkbox',
        //                 orderable: false,
        //                 searchable: false,
        //                 width: '30px'
        //             },
        //             { data: 'application_number', name: 'a.application_number' },
        //             { data: 'application_submitted_date', name: 'a.application_submitted_date' },
        //             { data: 'vendor_name', name: 'a.vendor_name' },
        //             { data: 'application_grand_total', name: 'a.application_grand_total' },
        //             { data: 'application_form_status', name: 'a.application_form_status' }
        //         ],
        //         order: [[1, 'desc']],  // Sort by application number by default
        //         dom: '<"row"<"col-sm-12"tr>><"row"<"col-sm-4"l><"col-sm-4"i><"col-sm-4"p>>',
        //         language: {
        //             search: '',
        //             searchPlaceholder: 'Search...',
        //             paginate: {
        //                 previous: '<i class="cil-arrow-left"></i>',
        //                 next: '<i class="cil-arrow-right"></i>'
        //             }
        //         },
        //         pageLength: 10,
        //         lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
        //     });
        // }

        // Custom search box
        $('#table-search').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Select All Checkbox
        $('#select-all').on('click', function() {
            let isChecked = $(this).prop('checked');
            $('.row-checkbox').prop('checked', isChecked);

            if (isChecked) {
                $('.row-checkbox').each(function() {
                    selectedRows.add($(this).val());
                });
            } else {
                selectedRows.clear();
            }

            updateBulkActionBar();
        });

        // Row Click (Left Click)
        $('#purchasingHistoryTable tbody').on('click', 'tr', function(e) {
            if (e.which === 3) return; // Ignore right clicks

            let checkbox = $(this).find('.row-checkbox');
            let isChecked = !checkbox.prop('checked');
            checkbox.prop('checked', isChecked);

            if (isChecked) {
                selectedRows.add(checkbox.val());
            } else {
                selectedRows.delete(checkbox.val());
            }

            updateBulkActionBar();
        });

        // Context Menu (Right Click)
        $('#purchasingHistoryTable tbody').on('contextmenu', 'tr', function(e) {
            e.preventDefault();

            let contextMenu = $('#context-menu');
            contextMenu.css({
                top: e.pageY + 'px',
                left: e.pageX + 'px'
            }).show();

            // Store the row data for action handlers
            contextMenu.data('row-id', $(this).find('.row-checkbox').val());
        });

        // Hide context menu when clicking outside
        $(document).on('click', function() {
            $('#context-menu').hide();
        });

        // Context Menu Actions
        $('#context-menu button').on('click', function() {
            let action = $(this).data('action');
            let rowId = $('#context-menu').data('row-id');

            // Handle different actions
            switch(action) {
                case 'edit':
                    // Implement edit action
                    console.log('Edit application:', rowId);
                    break;
                case 'view':
                    // Implement view action
                    console.log('View application details:', rowId);
                    break;
                case 'approve':
                    // Implement approve action
                    console.log('Approve application:', rowId);
                    break;
            }

            $('#context-menu').hide();
        });

        // Update Bulk Action Bar
        function updateBulkActionBar() {
            if (selectedRows.size > 0) {
                // Show Snackbar with action buttons
                Snackbar.show({
                    pos: 'bottom-center',
                    text: selectedRows.size + ' items selected',
                    actionText: '<button class="btn btn-sm btn-danger me-2">Delete</button><button class="btn btn-sm btn-primary">Edit</button>',
                    actionTextColor: '#fff',
                    backgroundColor: '#323232',
                    duration: 0,  // 0 = Permanent until closed
                    textColor: '#fff',
                    customClass: 'snackbar-buttons',
                    onActionClick: function(element) {
                        // Check which button was clicked
                        let action = $(element).text().toLowerCase();
                        console.log('Bulk action:', action, 'for IDs:', Array.from(selectedRows));
                        // Implement bulk actions here
                    }
                });
            } else {
                // Close Snackbar
                Snackbar.close();
            }
        }

        // Handle Bulk Actions
        $('#bulk-action-snackbar button').on('click', function() {
            let action = $(this).text().toLowerCase();
            console.log('Bulk action:', action, 'for IDs:', Array.from(selectedRows));
            // Implement bulk actions here
        });

        // $('#purchasingHistoryTable').DataTable();
        // async function ceisaAuth() {
        //     Snackbar.show({ text: 'Connecting to CEISA 4.0 gateway' });
        //     try {
        //         const response = await fetch('/ceisah2h/dokumen_pabean/ceisa_auth', {
        //             method: 'GET',
        //             headers: {
        //                 'Content-Type': 'application/json',
        //                 'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //             },
        //         });

        //         const data = await response.json();
        //         if (response.ok) {
        //             accessToken = data['accessToken'];
        //             authType = data['authType'];
        //             Snackbar.show({ text: data['message'] });
        //         } else {
        //             Snackbar.show({ text: data['message'] });
        //         }
        //     } catch (error) {
        //         console.error('Error:', error);
        //         Snackbar.show({ text: 'An error occurred while fetching data.' });
        //     }
        // }

        // $(document).ready(function() {
        //     ceisaAuth();
        // });
    </script>
@endsection
