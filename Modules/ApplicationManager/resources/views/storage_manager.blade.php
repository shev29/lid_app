@extends('layouts.app')

@section('extra_css')
<style>
    @media (max-width: 991.98px) {
        body {
            --height-content-full: calc(100vh - (var(--cui-header-height) + 1rem));
        }

        .container-content {
            height: var(--height-content-full);
            height: calc((var(--vh, 1vh) * 100) - (var(--cui-header-height) + 1rem));
            padding-bottom: 1rem;
        }
    }
    @media (min-width: 991.99px) {
        body {
            overflow: hidden !important;
            --height-content-full: calc(100vh - (var(--cui-header-height) + 1rem));
        }

        .container-content {
            height: var(--height-content-full);
            height: calc((var(--vh, 1vh) * 100) - (var(--cui-header-height) + 1rem));
            padding-bottom: 1rem;
        }
    }

    .breadcrumb-item, .breadcrumb-link {
        color: var(--cui-body-color);
        font-size: .85rem;
        font-weight: 500;
    }

    .folder-table {
        color: #ffc107;
    }

    .file-icon {
        font-size: 1.25rem;
        display: flex;
        align-items: center;

    }

    .file-name {
        user-select: none;
    }

    .row-content {
        height: 100%;
        overflow: hidden;
    }

    .full-height-column-wrapper {
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .context-menu {
        position: absolute;
        background: white;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        display: none;
        min-width: 150px;
    }

    .context-menu-item {
        padding: 8px 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
    }

    .context-menu-item:hover {
        background-color: #f8f9fa;
    }

    .context-menu-separator {
        height: 1px;
        background-color: #dee2e6;
    }

    .sortable {
        cursor: pointer;
        user-select: none;
    }

    .sortable:hover {
        background-color: #86929f;
    }

    .sort-icon {
        margin-left: 5px;
        opacity: 0.9;
    }

    .sortable.active .sort-icon {
        opacity: 1;
    }

    .file-row {
        cursor: pointer;
        color: var(--cui-body-color);
        font-size: .75rem;
    }

    tbody tr.file-row.selected td {
        background-color: #e6f7ff !important;
    }

    .folder-item {
        padding: 5px 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .folder-item:hover {
        background-color: #dfdfdf;
    }

    .folder-item.selected {
        background-color: #e3f2fd;
    }

    .folder-tree {
        background-color: #fafafa;
    }

    .toolbar {
        position: sticky;
        top: 0;
        z-index: 100;
    }

    #storageTable thead th {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    #storageTable {
        table-layout: fixed;
        width: 100%;
    }

    #storageTable tbody td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .folder-tree {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.5rem;
        background-color: #fff;
    }

    .folder-wrapper {
        margin-bottom: 2px;
    }

    .folder-item {
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        transition: background-color 0.15s ease-in-out;
        user-select: none;
    }

    .folder-item:hover {
        background-color: #f8f9fa;
    }

    .folder-item.selected {
        background-color: #e3f2fd;
        color: #1976d2;
    }

    .folder-content {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .expand-icon {
        width: 12px;
        font-size: 0.75rem;
        color: #6c757d;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .expand-icon:hover {
        color: #495057;
    }

    .expand-icon-placeholder {
        width: 12px;
        height: 12px;
        display: inline-block;
    }

    .folder-icon {
        color: #ffc107;
        font-size: 0.875rem;
        transition: color 0.2s ease;
    }

    .folder-item[data-expanded="true"] .folder-icon {
        color: #fd7e14;
    }

    .folder-name {
        flex: 1;
        font-size: 0.875rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .folder-children {
        margin-left: 0;
        transition: all 0.2s ease-in-out;
    }

    /* Loading state */
    .folder-item.loading .expand-icon {
        animation: spin 1s linear infinite;
    }

    /* Loading content styles */
    .folder-children .text-center {
        font-size: 0.8rem;
        color: #6c757d;
    }

    .folder-children .fa-spinner {
        color: #007bff;
    }

    /* Hover effects for better UX */
    .folder-item:not(.selected):hover .folder-icon {
        color: #fd7e14;
    }

    /* Animation for expanding/collapsing */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .folder-children[style*="block"] {
        animation: fadeIn 0.2s ease-in-out;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

</style>
@endsection

@section('content')
    <div class="container-content px-3">
        <div class="row row-content">
            <div class="col-12 h-100">
                <div class="card full-height-column-wrapper" id="storageTableContainer">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fs-6 fw-medium">
                                <div class="card-title card-title-right-column m-0 d-flex align-items-center gap-1">Storage Management</div>
                            </div>
                            <div class="d-flex gap-1 justify-content-between align-items-center">
                                <button class="btn btn-sm btn-secondary" type="button"  id="uploadButton" title="Upload Files"><i class="fa-regular fa-arrow-up-from-line fa-lg fa-fw"></i> Upload Files</button>

                                <button class="btn btn-sm btn-info" type="button" id="createFolderButton" title="New Folder"><i class="fa-regular fa-folder-plus fa-lg fa-fw"></i> New Folder</button>

                                <button class="btn btn-sm btn-success" type="button" id="refreshButton" title="Refresh"><i class="fa-regular fa-arrows-rotate fa-lg fa-fw"></i> Refresh</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="table-container" style="height: 100%;">
                            <div class="row align-items-center my-2">
                                <div class="col-md-12">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb mb-0" id="breadcrumb">
                                            <li class="breadcrumb-item active">Storage</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                            <div class="table-responsive" id="tableScrollContainer">
                                <table id="storageTable" class="table table-striped table-hover" style="margin-bottom: 0;">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th width="5%">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                                </div>
                                            </th>
                                            <th width="40%" class="sortable" data-sort="name">Name <i class="fa-regular fa-arrow-down-a-z sort-icon"></i></th>
                                            <th width="15%" class="sortable" data-sort="size">Size <i class="fa-regular fa-arrow-down-1-9 sort-icon"></i></th>
                                            <th width="20%" class="sortable" data-sort="modified_at">Modified <i class="fa-regular fa-arrow-down-1-9 sort-icon"></i></th>
                                            <th width="20%" class="sortable" data-sort="created_at">Created <i class="fa-regular fa-arrow-down-1-9 sort-icon"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody id="fileList">
                                        <tr class="loading-row">
                                            <td colspan="5" class="text-center py-4">
                                                <div class="center-container">
                                                    <div class="stripes-red-blue stripes-red-blue-md"></div>
                                                    <div class="d-block fs-7 mt-2">Loading...</div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Context Menu -->
    <div class="context-menu" id="contextMenu">
        <div class="context-menu-item" data-action="open">
            <i class="fa-regular fa-folder-open fa-fw"></i> Open
        </div>
        <div class="context-menu-item" data-action="download">
            <i class="fa-regular fa-arrow-down-to-line fa-fw"></i> Download
        </div>
        <div class="context-menu-item" data-action="download-zip">
            <i class="fa-regular fa-file-zipper fa-fw"></i> Download as ZIP
        </div>
        <div class="context-menu-separator"></div>
        <div class="context-menu-item" data-action="rename">
            <i class="fa-regular fa-text-size fa-fw"></i> Rename
        </div>
        <div class="context-menu-item" data-action="duplicate">
            <i class="fa-regular fa-copy fa-fw"></i> Duplicate
        </div>
        <div class="context-menu-item" data-action="move">
            <i class="fa-regular fa-arrow-up-right-from-square fa-fw"></i> Move
        </div>
        <div class="context-menu-separator"></div>
        <div class="context-menu-item text-danger" data-action="delete">
            <i class="fa-regular fa-trash-can fa-fw"></i> Delete
        </div>
    </div>

    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Upload Files</h6>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" title="Close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fileInput" class="form-label">Choose files</label>
                        <input type="file" class="form-control" id="fileInput" multiple>
                    </div>
                    <div class="upload-progress" style="display: none;">
                        <div class="progress mb-2">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small class="text-muted">Uploading...</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex flex-column flex-md-row justify-content-between w-100 gap-2 mb-2">
                        <button type="button" class="btn btn-default flex-grow-1 mb-2" data-bs-dismiss="modal" title="Close">
                            <i class="fas fa-xmark"></i> Close
                        </button>
                        <button type="button" class="btn btn-secondary flex-grow-1 mb-2" id="uploadFiles">
                            Upload <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createFolderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Create New Folder</h6>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" title="Close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="folderName" class="form-label">Folder Name</label>
                        <input type="text" class="form-control" id="folderName" placeholder="Enter folder name">
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex flex-column flex-md-row justify-content-between w-100 gap-2 mb-2">
                        <button type="button" class="btn btn-default flex-grow-1 mb-2" data-bs-dismiss="modal" title="Close">
                            <i class="fas fa-xmark"></i> Close
                        </button>
                        <button type="button" class="btn btn-info flex-grow-1 mb-2" id="createFolder">
                            Create <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="renameModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Rename</h6>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" title="Close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="newName" class="form-label">New Name</label>
                        <input type="text" class="form-control" id="newName">
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex flex-column flex-md-row justify-content-between w-100 gap-2 mb-2">
                        <button type="button" class="btn btn-default flex-grow-1 mb-2" data-bs-dismiss="modal" title="Close">
                            <i class="fas fa-xmark"></i> Close
                        </button>
                        <button type="button" class="btn btn-success flex-grow-1 mb-2" id="renameItem">
                            Rename <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="moveModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Move Items</h6>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" title="Close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Destination Folder</label>
                        <div class="folder-tree border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <div class="folder-item" data-path="">
                                <i class="fa-regular fa-folder"></i> Storage
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="selectedDestination" value="">
                </div>
                <div class="modal-footer">
                    <div class="d-flex flex-column flex-md-row justify-content-between w-100 gap-2 mb-2">
                        <button type="button" class="btn btn-default flex-grow-1 mb-2" data-bs-dismiss="modal" title="Close">
                            <i class="fas fa-xmark"></i> Close
                        </button>
                        <button type="button" class="btn btn-danger flex-grow-1 mb-2" id="confirmMoveBtn">
                            Move <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Delete Confirmation</h6>
                    <button type="button" class="btn-close btn-close-modal" data-bs-dismiss="modal" title="Close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center mb-3">
                        <strong>Are you sure you want to delete <span id="deleteItemCount">0</span> item(s)?</strong>
                    </p>
                    <div class="alert alert-warning d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <small>This action cannot be undone. All selected files and folders will be permanently deleted.</small>
                    </div>
                    <div id="deleteItemsList" class="mt-3" style="max-height: 200px; overflow-y: auto;">
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex flex-column flex-md-row justify-content-between w-100 gap-2 mb-2">
                        <button type="button" class="btn btn-default flex-grow-1 mb-2" data-bs-dismiss="modal" title="Cancel">
                            <i class="fas fa-xmark"></i> Close
                        </button>
                        <button type="button" class="btn btn-danger flex-grow-1 mb-2" id="confirmDeleteBtn">
                            Delete <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra_js')
<script>
class FileManager {
    constructor() {
        this.currentPath = '';
        this.selectedItems = new Set();
        this.sortBy = 'name';
        this.sortDirection = 'asc';
        this.contextMenuTarget = null;

        this.init();
    }

    init() {
        this.bindEvents();
        this.loadFiles();
    }

    bindEvents() {
        // Upload button
        document.getElementById('uploadButton').addEventListener('click', () => {
            new bootstrap.Modal(document.getElementById('uploadModal')).show();
        });

        // Create folder button
        document.getElementById('createFolderButton').addEventListener('click', () => {
            new bootstrap.Modal(document.getElementById('createFolderModal')).show();
        });

        // Refresh button
        document.getElementById('refreshButton').addEventListener('click', () => {
            this.loadFiles();
        });

        // Select all checkbox
        document.getElementById('selectAll').addEventListener('change', (e) => {
            this.selectAll(e.target.checked);
        });

        // Sort headers
        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', () => {
                this.sort(header.dataset.sort);
            });
        });

        // Upload files
        document.getElementById('uploadFiles').addEventListener('click', () => {
            this.uploadFiles();
        });

        // Create folder
        document.getElementById('createFolder').addEventListener('click', () => {
            this.createFolder();
        });

        // Rename item
        document.getElementById('renameItem').addEventListener('click', () => {
            this.renameItem();
        });

        // Move item
        document.getElementById('confirmMoveBtn').addEventListener('click', () => {
            this.executeMoveItems();
        });

        // Confirm delete
        document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
            this.executeDelete();
        });

        // Context menu
        document.addEventListener('contextmenu', (e) => {
            const row = e.target.closest('tr.file-row');
            if (row) {
                e.preventDefault();
                this.showContextMenu(e, row);
            }
        });

        // Hide context menu
        document.addEventListener('click', () => {
            this.hideContextMenu();
        });

        // Context menu actions
        document.querySelectorAll('.context-menu-item').forEach(item => {
            item.addEventListener('click', (e) => {
                this.handleContextMenuAction(e.target.closest('.context-menu-item').dataset.action);
            });
        });

        // Breadcrumb navigation
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('breadcrumb-link')) {
                e.preventDefault();
                this.navigateTo(e.target.dataset.path);
            }
        });

        // // Folder tree in move modal
        // document.addEventListener('click', (e) => {
        //     if (e.target.closest('.folder-item')) {
        //         const folderItem = e.target.closest('.folder-item');
        //         document.querySelectorAll('.folder-item').forEach(item => {
        //             item.classList.remove('selected');
        //         });
        //         folderItem.classList.add('selected');
        //         document.getElementById('selectedDestination').value = folderItem.dataset.path;
        //     }
        // });
    }

    async loadFiles() {
        try {
            const response = await fetch(`/app_manager/storage_browse?path=${encodeURIComponent(this.currentPath)}&sort=${this.sortBy}&direction=${this.sortDirection}`);
            const data = await response.json();

            this.renderFiles(data.items);
            this.updateBreadcrumbs(data.breadcrumbs);
            this.selectedItems.clear();
            this.updateSelectAllCheckbox();
        } catch (error) {
            console.error('Error loading files:', error);
            this.showAlert('Error loading files', 'danger');
        }
    }

    renderFiles(items) {
        const tbody = document.getElementById('fileList');
        tbody.innerHTML = '';

        if (items.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="fa-regular fa-folder-open" style="font-size: 2rem;"></i>
                        <br>This folder is empty
                    </td>
                </tr>
            `;
            return;
        }

        items.forEach(item => {
            const row = document.createElement('tr');
            row.className = 'file-row';
            row.dataset.name = item.name;
            row.dataset.path = item.path;
            row.dataset.type = item.type;

            row.innerHTML = `
                <td>
                    <div class="form-check">
                        <input class="form-check-input item-checkbox" type="checkbox" value="${item.name}">
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center file-row">
                        <i class="${item.icon} file-icon"></i>
                        <span class="file-name">${item.name}</span>
                    </div>
                </td>
                <td>${item.size_human}</td>
                <td>${item.modified_at}</td>
                <td>${item.created_at}</td>
            `;

            // Double click to open folder
            if (item.type === 'folder') {
                row.addEventListener('dblclick', () => {
                    this.navigateTo(item.path);
                });
            }

            // Single click to select
            row.addEventListener('click', (e) => {
                if (e.target.type !== 'checkbox') {
                    this.toggleSelection(row);
                }
            });

            // Checkbox change
            row.querySelector('.item-checkbox').addEventListener('change', (e) => {
                if (e.target.checked) {
                    this.selectedItems.add(item.name);
                    row.classList.add('selected');
                } else {
                    this.selectedItems.delete(item.name);
                    row.classList.remove('selected');
                }
                this.updateSelectAllCheckbox();
            });

            tbody.appendChild(row);
        });
    }

    updateBreadcrumbs(breadcrumbs) {
        const breadcrumbContainer = document.getElementById('breadcrumb');
        breadcrumbContainer.innerHTML = '';

        breadcrumbs.forEach((crumb, index) => {
            const li = document.createElement('li');
            li.className = 'breadcrumb-item';

            if (index === breadcrumbs.length - 1) {
                li.className += ' active';
                li.textContent = crumb.name;
            } else {
                li.innerHTML = `<a href="#" class="breadcrumb-link" data-path="${crumb.path}">${crumb.name}</a>`;
            }

            breadcrumbContainer.appendChild(li);
        });
    }

    navigateTo(path) {
        this.currentPath = path;
        this.loadFiles();
    }

    toggleSelection(row) {
        const checkbox = row.querySelector('.item-checkbox');
        checkbox.checked = !checkbox.checked;
        checkbox.dispatchEvent(new Event('change'));
    }

    selectAll(checked) {
        document.querySelectorAll('.item-checkbox').forEach(checkbox => {
            checkbox.checked = checked;
            checkbox.dispatchEvent(new Event('change'));
        });
    }

    updateSelectAllCheckbox() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        const selectAllCheckbox = document.getElementById('selectAll');

        if (checkedBoxes.length === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedBoxes.length === checkboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    sort(column) {
        if (this.sortBy === column) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortBy = column;
            this.sortDirection = 'asc';
        }

        // Update sort indicators
        document.querySelectorAll('.sortable').forEach(header => {
            header.classList.remove('active');
            const icon = header.querySelector('.sort-icon');
            if(header.dataset.sort === 'name') {
                icon.className = 'fa-regular fa-arrow-down-a-z sort-icon';
            }
            else {
                icon.className = 'fa-regular fa-arrow-down-1-9 sort-icon';
            }
        });

        const activeHeader = document.querySelector(`[data-sort="${column}"]`);
        activeHeader.classList.add('active');
        const activeIcon = activeHeader.querySelector('.sort-icon');
        if(column == 'name') {
            activeIcon.className = this.sortDirection === 'asc' ? 'fa-regular fa-arrow-down-a-z sort-icon' : 'fa-regular fa-arrow-down-z-a sort-icon';
        }
        else {
            activeIcon.className = this.sortDirection === 'asc' ? 'fa-regular fa-arrow-down-1-9 sort-icon' : 'fa-regular fa-arrow-down-9-1 sort-icon';
        }

        this.loadFiles();
    }

    showContextMenu(event, row) {
        this.contextMenuTarget = row;
        const contextMenu = document.getElementById('contextMenu');

        if (!contextMenu || !row) return;

        // Select the row if not already selected
        if (!row.classList.contains('selected')) {
            this.selectedItems.clear();

            document.querySelectorAll('.file-row').forEach(r => r.classList.remove('selected'));
            document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);

            row.classList.add('selected');
            const checkbox = row.querySelector('.item-checkbox');
            if (checkbox) {
                checkbox.checked = true;
            }

            this.selectedItems.add(row.dataset.name);
        }

        // Show/hide menu items based on folder or file
        const isFolder = row.dataset.type === 'folder';
        const downloadItem = contextMenu.querySelector('[data-action="download"]');
        const openItem = contextMenu.querySelector('[data-action="open"]');

        if (downloadItem) downloadItem.style.display = isFolder ? 'none' : 'block';
        if (openItem) openItem.style.display = isFolder ? 'block' : 'none';

        // Display context menu
        contextMenu.style.display = 'block';
        contextMenu.style.left = '0px';
        contextMenu.style.top = '0px';

        const menuRect = contextMenu.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        const margin = 8; // optional margin from edge

        let posX = event.pageX;
        let posY = event.pageY;

        // Adjust position if menu overflows to the right
        if (posX + menuRect.width > viewportWidth) {
            posX = viewportWidth - menuRect.width - margin;
        }

        // Adjust position if menu overflows to the bottom
        if (posY + menuRect.height > viewportHeight) {
            posY = viewportHeight - menuRect.height - margin;
        }

        // 4. Apply corrected position
        contextMenu.style.left = `${posX}px`;
        contextMenu.style.top = `${posY}px`;
    }

    hideContextMenu() {
        document.getElementById('contextMenu').style.display = 'none';
    }

    handleContextMenuAction(action) {
        this.hideContextMenu();

        switch (action) {
            case 'open':
                if (this.contextMenuTarget.dataset.type === 'folder') {
                    this.navigateTo(this.contextMenuTarget.dataset.path);
                }
                break;
            case 'download':
                this.downloadFile(this.contextMenuTarget.dataset.name);
                break;
            case 'download-zip':
                this.downloadZip();
                break;
            case 'rename':
                this.showRenameModal();
                break;
            case 'duplicate':
                this.duplicateItems();
                break;
            case 'move':
                this.showMoveModal();
                break;
            case 'delete':
                this.deleteItems();
                break;
        }
    }

    async uploadFiles() {
        const fileInput = document.getElementById('fileInput');
        const files = fileInput.files;

        if (files.length === 0) {
            this.showAlert('Please select files to upload', 'warning');
            return;
        }

        const formData = new FormData();
        for (let file of files) {
            formData.append('files[]', file);
        }
        formData.append('path', this.currentPath);

        try {
            const response = await fetch('/app_manager/storage_upload', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('Files uploaded successfully', 'success', 'fa-circle-check');
                bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
                fileInput.value = '';
                this.loadFiles();
            } else {
                this.showAlert(result.error || 'Upload failed', 'danger');
            }
        } catch (error) {
            console.error('Upload error:', error);
            this.showAlert('Upload failed', 'danger');
        }
    }

    async createFolder() {
        const folderName = document.getElementById('folderName').value.trim();

        if (!folderName) {
            this.showAlert('Please enter a folder name', 'warning');
            return;
        }

        try {
            const response = await fetch('/app_manager/storage_create_dir', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    name: folderName,
                    path: this.currentPath
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('Folder created successfully', 'success', 'fa-circle-check');
                bootstrap.Modal.getInstance(document.getElementById('createFolderModal')).hide();
                document.getElementById('folderName').value = '';
                this.loadFiles();
            } else {
                this.showAlert(result.error || 'Failed to create folder', 'danger');
            }
        } catch (error) {
            console.error('Create folder error:', error);
            this.showAlert('Failed to create folder', 'danger');
        }
    }

    showRenameModal() {
        if (this.selectedItems.size !== 1) {
            this.showAlert('Please select exactly one item to rename', 'warning');
            return;
        }

        const itemName = Array.from(this.selectedItems)[0];
        document.getElementById('newName').value = itemName;
        new bootstrap.Modal(document.getElementById('renameModal')).show();
    }

    async renameItem() {
        const oldName = Array.from(this.selectedItems)[0];
        const newName = document.getElementById('newName').value.trim();

        if (!newName) {
            this.showAlert('Please enter a new name', 'warning');
            return;
        }

        try {
            const response = await fetch('/app_manager/storage_rename', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    old_name: oldName,
                    new_name: newName,
                    path: this.currentPath
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('Item renamed successfully', 'success', 'fa-circle-check');
                bootstrap.Modal.getInstance(document.getElementById('renameModal')).hide();
                this.loadFiles();
            } else {
                this.showAlert(result.error || 'Failed to rename item', 'danger');
            }
        } catch (error) {
            console.error('Rename error:', error);
            this.showAlert('Failed to rename item', 'danger');
        }
    }

    async duplicateItems() {
        if (this.selectedItems.size === 0) {
            this.showAlert('Please select items to duplicate', 'warning');
            return;
        }

        try {
            const response = await fetch('/app_manager/storage_duplicate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    items: Array.from(this.selectedItems),
                    path: this.currentPath
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('Items duplicated successfully', 'success', 'fa-circle-check');
                this.loadFiles();
            } else {
                this.showAlert(result.error || 'Failed to duplicate items', 'danger');
            }
        } catch (error) {
            console.error('Duplicate error:', error);
            this.showAlert('Failed to duplicate items', 'danger');
        }
    }

    async showMoveModal() {
        if (this.selectedItems.size === 0) {
            this.showAlert('Please select items to move', 'warning');
            return;
        }

        // Clear previous selection
        this.selectedMoveDestination = '';

        // Load folder tree
        await this.loadFolderTree();
        new bootstrap.Modal(document.getElementById('moveModal')).show();
    }

    async loadFolderTree() {
        try {
            const response = await fetch('/app_manager/folder_tree');
            const data = await response.json();

            if (!data.success) {
                console.error('Failed to load folder tree:', data.error);
                return;
            }

            const treeContainer = document.querySelector('.folder-tree');
            treeContainer.innerHTML = '';

            // Create root folder
            const rootFolder = document.createElement('div');
            rootFolder.className = 'folder-item selected';
            rootFolder.dataset.path = '';
            rootFolder.dataset.expanded = 'true';
            rootFolder.dataset.hasChildren = 'true';
            rootFolder.innerHTML = `
                <div class="folder-content">
                    <i class="fas fa-chevron-down expand-icon"></i>
                    <i class="fas fa-folder-open folder-icon"></i>
                    <span class="folder-name">Storage</span>
                </div>
            `;
            treeContainer.appendChild(rootFolder);

            // Create container for root children
            const rootChildren = document.createElement('div');
            rootChildren.className = 'folder-children';
            rootChildren.dataset.parentPath = '';
            treeContainer.appendChild(rootChildren);

            // Render immediate children
            this.renderFolderTree(data.folders, rootChildren, 1);

            // Add click event listeners
            this.addFolderTreeEvents();

            // Set initial selection
            this.selectedMoveDestination = '';
        } catch (error) {
            console.error('Error loading folder tree:', error);
        }
    }

    renderFolderTree(folders, container, level) {
        folders.forEach(folder => {
            // Create folder item wrapper
            const folderWrapper = document.createElement('div');
            folderWrapper.className = 'folder-wrapper';

            // Create folder item
            const folderDiv = document.createElement('div');
            folderDiv.className = 'folder-item';
            folderDiv.dataset.path = folder.path;
            folderDiv.dataset.expanded = 'false';
            folderDiv.dataset.level = level;
            folderDiv.dataset.hasChildren = folder.has_children ? 'true' : 'false';
            folderDiv.style.paddingLeft = (level * 20) + 'px';

            // Show expand icon only if folder has children
            const expandIcon = folder.has_children
                ? '<i class="fas fa-chevron-right expand-icon"></i>'
                : '<i class="expand-icon-placeholder"></i>';

            folderDiv.innerHTML = `
                <div class="folder-content">
                    ${expandIcon}
                    <i class="fas fa-folder folder-icon"></i>
                    <span class="folder-name">${folder.name}</span>
                </div>
            `;

            // Create children container (initially hidden)
            const childrenContainer = document.createElement('div');
            childrenContainer.className = 'folder-children';
            childrenContainer.dataset.parentPath = folder.path;
            childrenContainer.style.display = 'none';

            folderWrapper.appendChild(folderDiv);
            folderWrapper.appendChild(childrenContainer);
            container.appendChild(folderWrapper);
        });
    }

    addFolderTreeEvents() {
        const treeContainer = document.querySelector('.folder-tree');
        let lastClickedItem = null;
        let lastClickTime = 0;

        treeContainer.addEventListener('click', async (e) => {
            e.stopImmediatePropagation();
            const folderItem = e.target.closest('.folder-item');
            if (!folderItem) return;

            const currentTime = Date.now();
            const isDoubleClick = (folderItem === lastClickedItem) && (currentTime - lastClickTime < 300);

            if (isDoubleClick) {
                // Cancel pending single click action
                e.preventDefault();
                return;
            }

            lastClickedItem = folderItem;
            lastClickTime = currentTime;

            const expandIcon = folderItem.querySelector('.expand-icon');
            const folderIcon = folderItem.querySelector('.folder-icon');
            const childrenContainer = folderItem.parentElement.querySelector('.folder-children');

            // Handle expand/collapse when clicking on expand icon
            if (e.target.closest('.expand-icon')) {
                await this.toggleFolder(folderItem, expandIcon, folderIcon, childrenContainer);
                return;
            }

            // Single click - select folder
            this.selectFolder(folderItem);
        });

        treeContainer.addEventListener('dblclick', async (e) => {
            e.stopImmediatePropagation();
            const folderItem = e.target.closest('.folder-item');
            if (!folderItem || !folderItem.dataset.hasChildren === 'true') return;

            const expandIcon = folderItem.querySelector('.expand-icon');
            const folderIcon = folderItem.querySelector('.folder-icon');
            const childrenContainer = folderItem.parentElement.querySelector('.folder-children');

            // Double click - expand/collapse folder
            await this.toggleFolder(folderItem, expandIcon, folderIcon, childrenContainer);
        });
    }

    async toggleFolder(folderItem, expandIcon, folderIcon, childrenContainer) {
        // console.log(folderItem.dataset.expanded);
        const isExpanded = folderItem.dataset.expanded === 'true';
        // const isExpanded = childrenContainer.style.display === 'block' ? true : false;

        if (isExpanded) {
            // Collapse
            folderItem.dataset.expanded = 'false';
            expandIcon.className = 'fas fa-chevron-right expand-icon';
            folderIcon.className = 'fas fa-folder folder-icon';
            childrenContainer.style.display = 'none';
        }
        else {
            // Expand
            folderItem.dataset.expanded = 'true';
            expandIcon.className = 'fas fa-chevron-down expand-icon';
            folderIcon.className = 'fas fa-folder-open folder-icon';
            childrenContainer.style.display = 'block';

            // Load children if not already loaded
            if (childrenContainer.children.length === 0) {
                await this.loadFolderChildren(folderItem.dataset.path, childrenContainer);
            }
        }
    }

    selectFolder(folderItem) {
        // Remove selected class from all folders
        document.querySelectorAll('.folder-item').forEach(f => f.classList.remove('selected'));

        // Add selected class to clicked folder
        folderItem.classList.add('selected');

        // Store selected destination
        this.selectedMoveDestination = folderItem.dataset.path;

        // Update move button text
        const moveBtn = document.getElementById('confirmMoveBtn');
        if (moveBtn) {
            const folderName = folderItem.querySelector('.folder-name').textContent;
            moveBtn.innerHTML = `Move to <i class="fa-regular fa-arrow-right"></i> "${folderName}"`;
        }
    }

    async loadFolderChildren(folderPath, container) {
        try {
            // Add loading state
            container.innerHTML = '<div class="text-center py-2"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';

            const response = await fetch(`/app_manager/folder_tree?path=${encodeURIComponent(folderPath)}`);
            const data = await response.json();

            // Clear loading state
            container.innerHTML = '';

            if (data.success && data.folders) {
                const level = parseInt(container.parentElement.querySelector('.folder-item').dataset.level) + 1;
                this.renderFolderTree(data.folders, container, level);
            }
            else {
                container.innerHTML = '<div class="text-muted text-center py-2">No subfolders</div>';
            }
        } catch (error) {
            console.error('Error loading folder children:', error);
            container.innerHTML = '<div class="text-danger text-center py-2">Error loading folders</div>';
        }
    }

    // Function to get selected folder path for move operation
    getSelectedMoveDestination() {
        return this.selectedMoveDestination || '';
    }

    // Function to execute move operation
    async executeMoveItems() {
        const destination = this.getSelectedMoveDestination();

        try {
            const response = await fetch('/app_manager/storage_move', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    items: Array.from(this.selectedItems),
                    source_path: this.currentPath,
                    destination_path: destination
                })
            });

            const result = await response.json();

            if (result.success) {
                this.selectedMoveDestination = '';

                this.showAlert('Items moved successfully', 'success', 'fa-circle-check');
                this.loadFiles();

                // Hide modal
                const moveModal = bootstrap.Modal.getInstance(document.getElementById('moveModal'));
                if (moveModal) {
                    moveModal.hide();
                }
            } else {
                this.showAlert(result.error || 'Failed to move items', 'danger');
            }
        } catch (error) {
            console.error('Move error:', error);
            this.showAlert('Failed to move items', 'danger');
        }
    }

    async deleteItems() {
        if (this.selectedItems.size === 0) {
            this.showAlert('Please select items to delete', 'warning');
            return;
        }

        // Update modal content with selected items
        this.prepareDeleteModal();

        // Show the modal instead of confirm dialog
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    prepareDeleteModal() {
        // Update item count
        document.getElementById('deleteItemCount').textContent = this.selectedItems.size;

        // Create list of selected items (optional - if you want to show what will be deleted)
        const itemsList = document.getElementById('deleteItemsList');
        if (itemsList) {
            itemsList.innerHTML = '';

            if (this.selectedItems.size <= 10) {
                const listElement = document.createElement('div');
                listElement.className = 'list-group';

                Array.from(this.selectedItems).forEach(item => {
                    const listItem = document.createElement('div');
                    listItem.className = 'list-group-item d-flex align-items-center';
                    listItem.innerHTML = `
                        <i class="fas fa-file me-2"></i>
                        <span class="text-truncate">${item}</span>
                    `;
                    listElement.appendChild(listItem);
                });

                itemsList.appendChild(listElement);
            }
        }
    }

    async executeDelete() {
        try {
            const response = await fetch('/app_manager/storage_delete', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    items: Array.from(this.selectedItems),
                    path: this.currentPath
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('Items deleted successfully', 'success', 'fa-circle-check');
                this.loadFiles();
            } else {
                this.showAlert(result.error || 'Failed to delete items', 'danger');
            }
        } catch (error) {
            console.error('Delete error:', error);
            this.showAlert('Failed to delete items', 'danger');
        }

        const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        if (deleteModal) {
            deleteModal.hide();
        }
    }

    downloadFile(filename) {
        const formData = new FormData();
        formData.append('path', this.currentPath);
        formData.append('filename', filename);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        this.downloadWithXHR('storage_download', formData, filename);
    }

    downloadZip() {
        if (this.selectedItems.size === 0) {
            this.showAlert('Please select items to download', 'warning');
            return;
        }

        const formData = new FormData();
        formData.append('path', this.currentPath);

        // Gunakan array format (lebih efisien)
        this.selectedItems.forEach(item => {
            formData.append('items[]', item);
        });

        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        const zipFilename = `download_${new Date().getTime()}.zip`;
        this.downloadWithXHR('storage_download_zip', formData, zipFilename);
    }

    downloadWithXHR(url, formData, filename) {
        const xhr = new XMLHttpRequest();

        xhr.open('POST', url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.responseType = 'blob';

        // Progress tracking untuk download
        xhr.onprogress = (e) => {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                this.updateProgress(percentComplete, e.loaded, e.total);
            }
        };

        xhr.onload = () => {
            this.hideProgress();

            if (xhr.status === 200) {
                const blob = xhr.response;
                const downloadUrl = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = downloadUrl;
                link.download = filename;

                document.body.appendChild(link);
                link.click();

                window.URL.revokeObjectURL(downloadUrl);
                document.body.removeChild(link);

                this.showAlert('Download completed successfully', 'success');
            } else {
                this.showAlert(`Download failed: HTTP ${xhr.status}`, 'error');
            }
        };

        xhr.onerror = () => {
            this.hideProgress();
            this.showAlert('Download failed: Network error', 'error');
        };

        xhr.ontimeout = () => {
            this.hideProgress();
            this.showAlert('Download failed: Request timeout', 'error');
        };

        // Set timeout 30 menit untuk file besar
        xhr.timeout = 30 * 60 * 1000;

        this.showProgress();
        xhr.send(formData);
    }

    // Progress UI methods
    showProgress() {
        const progressHtml = `
            <div id="download-progress" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                    <h3 class="text-lg font-semibold mb-4">Downloading...</h3>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                        <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span id="progress-percent">0%</span>
                        <span id="progress-size">0 MB</span>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', progressHtml);
    }

    updateProgress(percent, loaded, total) {
        const progressBar = document.getElementById('progress-bar');
        const progressPercent = document.getElementById('progress-percent');
        const progressSize = document.getElementById('progress-size');

        if (progressBar) progressBar.style.width = `${percent}%`;
        if (progressPercent) progressPercent.textContent = `${Math.round(percent)}%`;
        if (progressSize) {
            const loadedMB = (loaded / (1024 * 1024)).toFixed(2);
            const totalMB = total ? ` / ${(total / (1024 * 1024)).toFixed(2)}` : '';
            progressSize.textContent = `${loadedMB}${totalMB} MB`;
        }
    }

    hideProgress() {
        const progressElement = document.getElementById('download-progress');
        if (progressElement) {
            progressElement.remove();
        }
    }

    showAlert(message, type = 'danger', icon = 'fa-triangle-exclamation') {
        Snackbar.show({ pos: 'bottom-center', duration: '5000', text: `<i class="fa-solid ${icon} fa-lg fa-fw text-${type}"></i> ${message}` });
    }
}

// Initialize file manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    let $container = $('#storageTableContainer');
    let containerHeight = $container.height(); // misalnya 600px
    let headerHeight = 100;
    let footerFixedHeight = 0;

    let scrollHeight = containerHeight - headerHeight - footerFixedHeight;

    $('#tableScrollContainer').css({
        height: scrollHeight + 'px',
        maxHeight: scrollHeight + 'px'
    });

    new FileManager();
});
</script>
@endsection