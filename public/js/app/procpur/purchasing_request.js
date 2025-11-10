let dateRangePickerFilter, startDateRange, endDateRange;
var containerContentScrollbarInstance;
var orderColScrollbarInstance;
var appOngoingColScrollbarInstance;
var appApprovedColScrollbarInstance;
var endColScrollbarInstance;
var openOrderScrollbarInstance;
var goodsReceivedScrollbarInstance;

var currentPageOrder = 1;
var isLoadingOrder = false;
var hasMoreDataOrder = true;

var currentPageAppOngoing = 1;
var isLoadingAppOngoing = false;
var hasMoreDataAppOngoing = true;

var currentPageAppCompleted = 1
var isLoadingAppCompleted = false;
var hasMoreDataAppCompleted = true;

var currentPageOpenOrder = 1;
var isLoadingOpenOrder = false;
var hasMoreDataOpenOrder = true;

var currentPageGoodsReceived = 1;
var isLoadingGoodsReceived = false;
var hasMoreDataGoodsReceived = true;

let popperInstance;

var selectedFiles = [];
var selectedFilesProperties = [];
var maxFileSize = 5 * 1024 * 1024;
let poRuleMatrixController = null, poRuleMatrixTimeout = null;

var maxDateCurrentOptions = {
    locale: 'en-US',
    inputDateFormat: date => dayjs(date).locale('en').format('DD-MM-YYYY'),
    inputDateParse: date => dayjs(date, 'DD-MM-YYYY', 'id').toDate(),
    maxDate: dayjs(new Date()),
    showAdjacementDays: false,
}

var maxDateNullOptions = {
    locale: 'en-US',
    inputDateFormat: date => dayjs(date).locale('en').format('DD-MM-YYYY'),
    inputDateParse: date => dayjs(date, 'DD-MM-YYYY', 'id').toDate(),
    showAdjacementDays: false,
}

window.addEventListener('load', function() {
    if($('.order-column').length > 0) {
        containerContentScrollbarInstance = new ScrollbarCustom('#body-container', {bottom: 0, left:null, overflowX: 'scroll', overflowY: 'none'});
        containerContentScrollbarInstance.forceUpdate();

        loadOrderColumn();
        loadAppColumn();
        loadAppCompletedColumn();
        loadOpenOrder();
        loadGoodsReceived();

        const container = document.querySelector('#body-container');
        $('#body-container').scrollLeft(0);

        if ($(window).width() > 560) {
            // Variabel untuk melacak status dragging
            let isDown = false;
            let startX;
            let scrollLeft;
            let hasMoved = false; // Untuk melacak apakah sudah terjadi gerakan

            // Event listener saat mouse ditekan
            container.addEventListener('mousedown', (e) => {
                if(container.classList.contains('no-scroll')) return;
                isDown = true;
                hasMoved = false;
                // container.classList.add('active');
                startX = e.pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;

                // Mencegah event default
                if (hasMoved) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });

            // Event listener saat mouse dilepas
            container.addEventListener('mouseup', (e) => {
                isDown = false;
                container.classList.remove('active');

                // Jika terjadi gerakan, batalkan event klik
                if (hasMoved) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });

            // Event listener saat mouse keluar dari area container
            container.addEventListener('mouseleave', () => {
                isDown = false;
                container.classList.remove('active');
            });

            // Event listener saat mouse bergerak
            container.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                // Set hasMoved ke true karena mouse bergerak
                hasMoved = true;

                e.preventDefault(); // Mencegah perilaku default

                // Mencegah pemilihan teks selama dragging
                if (window.getSelection) {
                    window.getSelection().removeAllRanges();
                } else if (document.selection) {
                    document.selection.empty();
                }

                const x = e.pageX - container.offsetLeft;
                const walk = (x - startX) * 2;
                container.scrollLeft = scrollLeft - walk;
            });

            // Menambahkan event listener untuk mencegah pemilihan teks di seluruh container
            container.addEventListener('selectstart', (e) => {
                if (isDown) {
                    e.preventDefault();
                }
            });
        }

        // TOUCH DEVICE
        container.addEventListener('touchstart', (e) => {
            isDown = true;
            hasMoved = false;
            container.classList.add('active');
            startX = e.touches[0].pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });

        container.addEventListener('touchend', (e) => {
            isDown = false;
            container.classList.remove('active');
            if (hasMoved) {
                e.preventDefault();
            }
        });

        container.addEventListener('touchmove', (e) => {
            if (!isDown) return;
            hasMoved = true;
            const touchY = e.touches[0].pageY;
            const touchX = e.touches[0].pageX;

            const x = touchX - container.offsetLeft;
            const walk = (x - startX) * 2;
            container.scrollLeft = scrollLeft - walk;
        });

        container.addEventListener('touchstart', (e) => {
            isDown = true;
            container.classList.add('active');
            startX = e.touches[0].pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });

        container.addEventListener('touchend', () => {
            isDown = false;
            container.classList.remove('active');
        });

        container.addEventListener('touchmove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.touches[0].pageX - container.offsetLeft;
            const walk = (x - startX) * 2;
            container.scrollLeft = scrollLeft - walk;
        });
    }
});

document.querySelector('#orderFormRequest').addEventListener('scroll', () => {
    const orderFormRequest = document.querySelector('#orderFormRequest');
    const scrollThreshold = 100;

    if (orderFormRequest.scrollTop + orderFormRequest.clientHeight >= orderFormRequest.scrollHeight - scrollThreshold) {
        loadOrderColumn(); // Load more data when near the bottom
    }
});

document.querySelector('#applicationOngoing').addEventListener('scroll', () => {
    const applicationOngoing = document.querySelector('#applicationOngoing');
    const scrollThreshold = 100;

    if (applicationOngoing.scrollTop + applicationOngoing.clientHeight >= applicationOngoing.scrollHeight - scrollThreshold) {
        loadAppColumn(); // Load more data when near the bottom
    }
});

document.querySelector('#applicationCompleted').addEventListener('scroll', () => {
    const applicationCompleted = document.querySelector('#applicationCompleted');
    const scrollThreshold = 100;

    if (applicationCompleted.scrollTop + applicationCompleted.clientHeight >= applicationCompleted.scrollHeight - scrollThreshold) {
        loadAppCompletedColumn(); // Load more data when near the bottom
    }
});

document.querySelector('#openOrderContainer').addEventListener('scroll', () => {
    const openOrderContainer = document.querySelector('#openOrderContainer');
    const scrollThreshold = 100;

    if (openOrderContainer.scrollTop + openOrderContainer.clientHeight >= openOrderContainer.scrollHeight - scrollThreshold) {
        loadOpenOrder(); // Load more data when near the bottom
    }
});

document.querySelector('#goodsReceivedContainer').addEventListener('scroll', () => {
    const goodsReceivedContainer = document.querySelector('#goodsReceivedContainer');
    const scrollThreshold = 100;

    if (goodsReceivedContainer.scrollTop + goodsReceivedContainer.clientHeight >= goodsReceivedContainer.scrollHeight - scrollThreshold) {
        loadGoodsReceived(); // Load more data when near the bottom
    }
});

async function loadOrderColumn() {
    if (isLoadingOrder || !hasMoreDataOrder) return;
    isLoadingOrder = true;
    let skeletonCard = `<div class="text-center skeletonCardOrder">
                            <div class="stripes-red-blue stripes-red-blue-md"></div>
                            <div class="d-block fs-7">Loading...</div>
                        </div>`;

    if(currentPageOrder == 1){
        document.querySelector('#orderFormRequest').innerHTML = `<div class="full-height-column-wrapper">
                                                                <div class="card-body d-flex justify-content-center align-items-center">
                                                                    <div class="center-container">
                                                                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                                                                        <div class="d-block fs-7 mt-2">Loading...</div>
                                                                    </div>
                                                                </div>
                                                            </div>`;
    }
    else{
        document.querySelector('#orderFormRequest').insertAdjacentHTML('beforeend', skeletonCard);
    }

    try {
        let formData;
        let searchInput = $('#searchInputAppOngoing').val().trim();
        formData = new FormData($('#filterOrderFormRequest')[0]);
        formData.append('page', currentPageOrder);
        const queryString = new URLSearchParams(formData).toString();
        const response = await fetch(`/proc_pur/orderFormRequest?${queryString}`, {
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
            if(currentPageOrder == 1) {
                document.querySelector('#orderFormRequest').innerHTML = '';
            }
            else{
                $('.skeletonCardOrder').remove();
            }

            if(currentPageOrder == 1 && searchInput == ''){
                if(data.ongoing >= 1) {
                    let countOngoing = data.ongoing;
                    if(data.ongoing >= 100) {
                        countOngoing = '99+';
                    }
                    $('#badgeCountOrderFormRequest').html(countOngoing);
                    $('#badgeCountOrderFormRequest').removeClass('d-none');
                }
                else {
                    $('#badgeCountOrderFormRequest').addClass('d-none');
                }
            }

            if (data.data.length > 0) {
                data.data.forEach((item, index) => {
                    setTimeout(() => {
                        const newElement = document.createElement('div');
                        newElement.classList.add('fade-in-up');
                        newElement.innerHTML = item.html;
                        document.querySelector('#orderFormRequest').appendChild(newElement);

                        if (index === data.data.length - 1) {
                            requestAnimationFrame(() => {
                                if (!orderColScrollbarInstance) {
                                    orderColScrollbarInstance = new ScrollbarCustom('#orderFormRequest', {
                                        top: null,
                                        right: 0,
                                        overflowX: 'none',
                                        overflowY: 'scroll'
                                    });
                                    orderColScrollbarInstance.forceUpdate();
                                }
                                else {
                                    orderColScrollbarInstance.refresh();
                                }
                            });
                        }
                    }, index * 100);
                });
                currentPageOrder++;
                hasMoreDataOrder = data.hasMorePages;
            }
            else {
                hasMoreDataOrder = false; // No more data to load
                if(currentPageOrder == 1){
                    document.querySelector('#orderFormRequest').innerHTML = `<div class="card full-height-column-wrapper">
                                                                            <div class="card-body d-flex justify-content-center align-items-center">
                                                                                <div class="center-container">
                                                                                    <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                                                    <div class="d-block fs-7 mt-2">No order form request</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>`;
                }
                requestAnimationFrame(() => {
                    if (!orderColScrollbarInstance) {
                        orderColScrollbarInstance = new ScrollbarCustom('#orderFormRequest', {
                            top: null,
                            right: 0,
                            overflowX: 'none',
                            overflowY: 'scroll'
                        });
                        orderColScrollbarInstance.forceUpdate();
                    }
                    else {
                        orderColScrollbarInstance.refresh();
                    }
                });
            }
        }
        else if (data.status === 404) {
            $('.skeletonCardOrder').remove();
            document.querySelector('#orderFormRequest').innerHTML = `<div class="card full-height-column-wrapper">
                                                                    <div class="card-body d-flex justify-content-center align-items-center">
                                                                        <div class="center-container">
                                                                            <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                                            <div class="d-block fs-7 mt-2">${data.message}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>`;
        }
        else {
            $('.skeletonCardOrder').remove();
            console.error('HTTP Error:', response.status);
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        isLoadingOrder = false;
    }
}

async function loadAppColumn(params) {
    if(isLoadingAppOngoing || !hasMoreDataAppOngoing) return;
    isLoadingAppOngoing = true;
    let skeletonCard = `<div class="text-center skeletonCardOrder">
                            <div class="stripes-red-blue stripes-red-blue-md"></div>
                            <div class="d-block fs-7">Loading...</div>
                        </div>`;

    if(currentPageAppOngoing == 1){
        document.querySelector(`#applicationOngoing`).innerHTML = `<div class="full-height-column-wrapper">
                                                                <div class="card-body d-flex justify-content-center align-items-center">
                                                                    <div class="center-container">
                                                                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                                                                        <div class="d-block fs-7 mt-2">Loading...</div>
                                                                    </div>
                                                                </div>
                                                            </div>`;
    }
    else{
        document.querySelector(`#applicationOngoing`).insertAdjacentHTML('beforeend', skeletonCard);
    }

    try {
        let formData;
        let searchInput = $('#searchInputAppOngoing').val().trim();
        formData = new FormData($('#filterApplicationFormRequest')[0]);
        formData.append('page', currentPageAppOngoing);
        const queryString = new URLSearchParams(formData).toString();
        const response = await fetch(`/proc_pur/applicationFormRequest?${queryString}`, {
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
            if(currentPageAppOngoing == 1) {
                document.querySelector(`#applicationOngoing`).innerHTML = '';
            }
            else{
                $('.skeletonCardOrder').remove();
            }

            if(currentPageAppOngoing == 1 && searchInput == ''){
                if(data.ongoing >= 1) {
                    let countOngoing = data.ongoing;
                    if(data.ongoing >= 100) {
                        countOngoing = '99+';
                    }
                    $('#badgeCountApplicationOngoing').html(countOngoing);
                    $('#badgeCountApplicationOngoing').removeClass('d-none');
                }
                else {
                    $('#badgeCountApplicationOngoing').addClass('d-none');
                }
            }

            if (data.data.length > 0) {
                data.data.forEach((item, index) => {
                    setTimeout(() => {
                        const newElement = document.createElement('div');
                        newElement.classList.add('fade-in-up');
                        newElement.innerHTML = item.html;
                        document.querySelector(`#applicationOngoing`).appendChild(newElement);

                        if (index === data.data.length - 1) {
                            requestAnimationFrame(() => {
                                if (!appOngoingColScrollbarInstance) {
                                    appOngoingColScrollbarInstance = new ScrollbarCustom('#applicationOngoing', {
                                        top: null,
                                        right: 0,
                                        overflowX: 'none',
                                        overflowY: 'scroll'
                                    });
                                    appOngoingColScrollbarInstance.forceUpdate();
                                }
                                else {
                                    appOngoingColScrollbarInstance.refresh();
                                }
                            });
                        }
                    }, index * 100);
                });

                currentPageAppOngoing++;
                hasMoreDataAppOngoing = data.hasMorePages;
            }
            else {
                hasMoreDataAppOngoing = false; // No more data to load
                if(currentPageAppOngoing == 1){
                    document.querySelector(`#applicationOngoing`).innerHTML = `<div class="card full-height-column-wrapper">
                                                                            <div class="card-body d-flex justify-content-center align-items-center">
                                                                                <div class="center-container">
                                                                                    <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                                                    <div class="d-block fs-7 mt-2">No ongoing purchasing approval</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>`;
                }

                requestAnimationFrame(() => {
                    if (!appOngoingColScrollbarInstance) {
                        appOngoingColScrollbarInstance = new ScrollbarCustom('#applicationOngoing', {
                            top: null,
                            right: 0,
                            overflowX: 'none',
                            overflowY: 'scroll'
                        });
                        appOngoingColScrollbarInstance.forceUpdate();
                    }
                    else {
                        appOngoingColScrollbarInstance.refresh();
                    }
                });
            }
        }
        else if (data.status === 404) {
            $('.skeletonCardOrder').remove();
            document.querySelector(`#applicationOngoing`).innerHTML = `<div class="card full-height-column-wrapper">
                                                                    <div class="card-body d-flex justify-content-center align-items-center">
                                                                        <div class="center-container">
                                                                            <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                                            <div class="d-block fs-7 mt-2">${data.message}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>`;
        }
        else {
            $('.skeletonCardOrder').remove();
            console.error('HTTP Error:', response.status);
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        isLoadingAppOngoing = false;
    }
}

async function loadAppCompletedColumn(params) {
    if(isLoadingAppCompleted || !hasMoreDataAppCompleted) return;
    isLoadingAppCompleted = true;
    let skeletonCard = `<div class="text-center skeletonCardOrder">
                            <div class="stripes-red-blue stripes-red-blue-md"></div>
                            <div class="d-block fs-7">Loading...</div>
                        </div>`;

    if(currentPageAppCompleted == 1){
        document.querySelector(`#applicationCompleted`).innerHTML = `<div class="full-height-column-wrapper">
                                                                <div class="card-body d-flex justify-content-center align-items-center">
                                                                    <div class="center-container">
                                                                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                                                                        <div class="d-block fs-7 mt-2">Loading...</div>
                                                                    </div>
                                                                </div>
                                                            </div>`;
    }
    else{
        document.querySelector(`#applicationCompleted`).insertAdjacentHTML('beforeend', skeletonCard);
    }

    try {
        let formData;
        let searchInput = $('#searchInputAppCompleted').val().trim();
        formData = new FormData($('#filterApplicationCompletedForm')[0]);
        formData.append('page', currentPageAppCompleted);
        const queryString = new URLSearchParams(formData).toString();
        const response = await fetch(`/proc_pur/applicationCompleted?${queryString}`, {
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
            if(currentPageAppCompleted == 1) {
                document.querySelector(`#applicationCompleted`).innerHTML = '';
            }
            else{
                $('.skeletonCardOrder').remove();
            }

            if(currentPageAppCompleted == 1 && searchInput == ''){
                if(data.ongoing >= 1) {
                    let countOngoing = data.ongoing;
                    if(data.ongoing >= 100) {
                        countOngoing = '99+';
                    }
                    $('#badgeCountApplicationCompleted').html(countOngoing);
                    $('#badgeCountApplicationCompleted').removeClass('d-none');
                }
                else {
                    $('#badgeCountApplicationCompleted').addClass('d-none');
                }
            }

            if (data.data.length > 0) {
                data.data.forEach((item, index) => {
                    setTimeout(() => {
                        const newElement = document.createElement('div');
                        newElement.classList.add('fade-in-up');
                        newElement.innerHTML = item.html;
                        document.querySelector(`#applicationCompleted`).appendChild(newElement);
                    }, index * 100);

                    if (index === data.data.length - 1) {
                        requestAnimationFrame(() => {
                            if (currentPageAppCompleted === 2) {
                                endColScrollbarInstance = new ScrollbarCustom('#applicationCompleted', {
                                    top: null,
                                    right: 0,
                                    overflowX: 'none',
                                    overflowY: 'scroll'
                                });
                                endColScrollbarInstance.forceUpdate();
                            } else {
                                // untuk page 2, 3, dst
                                if (endColScrollbarInstance) {
                                    endColScrollbarInstance.refresh();
                                }
                            }
                        });
                    }
                });

                currentPageAppCompleted++;
                hasMoreDataAppCompleted = data.hasMorePages;
            }
            else {
                hasMoreDataAppCompleted = false; // No more data to load
                if(currentPageAppCompleted == 1){
                    document.querySelector(`#applicationCompleted`).innerHTML = `<div class="card full-height-column-wrapper">
                                                                            <div class="card-body d-flex justify-content-center align-items-center">
                                                                                <div class="center-container">
                                                                                    <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                                                    <div class="d-block fs-7 mt-2">No completed purchasing approval</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>`;
                }
            }
        }
        else if (data.status === 404) {
            $('.skeletonCardOrder').remove();
            document.querySelector(`#applicationCompleted`).innerHTML = `<div class="card full-height-column-wrapper">
                                                                    <div class="card-body d-flex justify-content-center align-items-center">
                                                                        <div class="center-container">
                                                                            <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                                            <div class="d-block fs-7 mt-2">${data.message}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>`;
        }
        else {
            $('.skeletonCardOrder').remove();
            console.error('HTTP Error:', response.status);
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        isLoadingAppCompleted = false;
    }
}

async function loadOpenOrder(params) {
    if(isLoadingOpenOrder || !hasMoreDataOpenOrder) return;
    isLoadingOpenOrder = true;
    let skeletonCard = `<div class="text-center skeletonCardOrder">
                            <div class="stripes-red-blue stripes-red-blue-md"></div>
                            <div class="d-block fs-7">Loading...</div>
                        </div>`;

    if(currentPageOpenOrder == 1){
        document.querySelector(`#openOrderContainer`).innerHTML = `<div class="full-height-column-wrapper">
                                                                <div class="card-body d-flex justify-content-center align-items-center">
                                                                    <div class="center-container">
                                                                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                                                                        <div class="d-block fs-7 mt-2">Loading...</div>
                                                                    </div>
                                                                </div>
                                                            </div>`;
    }
    else{
        document.querySelector(`#openOrderContainer`).insertAdjacentHTML('beforeend', skeletonCard);
    }

    try {
        let formData;
        let searchInput = $('#searchInputOpenOrder').val().trim();
        formData = new FormData($('#filterOpenOrderForm')[0]);
        formData.append('page', currentPageOpenOrder);
        const queryString = new URLSearchParams(formData).toString();
        const response = await fetch(`/proc_pur/applicationCompleted?${queryString}`, {
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
            if(currentPageOpenOrder == 1) {
                document.querySelector(`#openOrderContainer`).innerHTML = '';
            }
            else{
                $('.skeletonCardOrder').remove();
            }

            if(currentPageOpenOrder == 1 && searchInput == ''){
                if(data.ongoing >= 1) {
                    let countOngoing = data.ongoing;
                    if(data.ongoing >= 100) {
                        countOngoing = '99+';
                    }
                    $('#badgeCountOpenOrder').html(countOngoing);
                    $('#badgeCountOpenOrder').removeClass('d-none');
                }
                else {
                    $('#badgeCountOpenOrder').addClass('d-none');
                }
            }

            if (data.data.length > 0) {
                data.data.forEach((item, index) => {
                    setTimeout(() => {
                        const newElement = document.createElement('div');
                        newElement.classList.add('fade-in-up');
                        newElement.innerHTML = item.html;
                        document.querySelector(`#openOrderContainer`).appendChild(newElement);
                    }, index * 100);

                    if (index === data.data.length - 1) {
                        requestAnimationFrame(() => {
                            if (currentPageOpenOrder === 2) {
                                openOrderScrollbarInstance = new ScrollbarCustom('#openOrderContainer', {
                                    top: null,
                                    right: 0,
                                    overflowX: 'none',
                                    overflowY: 'scroll'
                                });
                                openOrderScrollbarInstance.forceUpdate();
                            } else {
                                // untuk page 2, 3, dst
                                if (openOrderScrollbarInstance) {
                                    openOrderScrollbarInstance.refresh();
                                }
                            }
                        });
                    }
                });

                currentPageOpenOrder++;
                hasMoreDataOpenOrder = data.hasMorePages;
            }
            else {
                hasMoreDataOpenOrder = false; // No more data to load
                if(currentPageOpenOrder == 1){
                    document.querySelector(`#openOrderContainer`).innerHTML = `<div class="card full-height-column-wrapper">
                                                                            <div class="card-body d-flex justify-content-center align-items-center">
                                                                                <div class="center-container">
                                                                                    <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                                                    <div class="d-block fs-7 mt-2">No open order data</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>`;
                }
            }
        }
        else if (data.status === 404) {
            $('.skeletonCardOrder').remove();
            document.querySelector(`.openOrderContainer`).innerHTML = `<div class="card full-height-column-wrapper">
                                                                    <div class="card-body d-flex justify-content-center align-items-center">
                                                                        <div class="center-container">
                                                                            <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                                            <div class="d-block fs-7 mt-2">${data.message}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>`;
        }
        else {
            $('.skeletonCardOrder').remove();
            console.error('HTTP Error:', response.status);
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        isLoadingOpenOrder = false;
    }
}

async function loadGoodsReceived(params) {
    if(isLoadingGoodsReceived || !hasMoreDataGoodsReceived) return;
    isLoadingGoodsReceived = true;
    let skeletonCard = `<div class="text-center skeletonCardOrder">
                            <div class="stripes-red-blue stripes-red-blue-md"></div>
                            <div class="d-block fs-7">Loading...</div>
                        </div>`;

    if(currentPageGoodsReceived == 1){
        document.querySelector(`#goodsReceivedContainer`).innerHTML = `<div class="full-height-column-wrapper">
                                                                <div class="card-body d-flex justify-content-center align-items-center">
                                                                    <div class="center-container">
                                                                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                                                                        <div class="d-block fs-7 mt-2">Loading...</div>
                                                                    </div>
                                                                </div>
                                                            </div>`;
    }
    else{
        document.querySelector(`#goodsReceivedContainer`).insertAdjacentHTML('beforeend', skeletonCard);
    }

    try {
        let formData;
        let searchInput = $('#searchInputGoodsReceived').val().trim();
        formData = new FormData($('#filterGoodsReceivedForm')[0]);
        formData.append('page', currentPageGoodsReceived);
        const queryString = new URLSearchParams(formData).toString();
        const response = await fetch(`/proc_pur/applicationCompleted?${queryString}`, {
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
            if(currentPageGoodsReceived == 1) {
                document.querySelector(`#goodsReceivedContainer`).innerHTML = '';
            }
            else{
                $('.skeletonCardOrder').remove();
            }

            if(currentPageGoodsReceived == 1 && searchInput == ''){
                if(data.ongoing >= 1) {
                    let countOngoing = data.ongoing;
                    if(data.ongoing >= 100) {
                        countOngoing = '99+';
                    }
                    $('#badgeCountGoodsReceived').html(countOngoing);
                    $('#badgeCountGoodsReceived').removeClass('d-none');
                }
                else {
                    $('#badgeCountGoodsReceived').addClass('d-none');
                }
            }

            if (data.data.length > 0) {
                data.data.forEach((item, index) => {
                    setTimeout(() => {
                        const newElement = document.createElement('div');
                        newElement.classList.add('fade-in-up');
                        newElement.innerHTML = item.html;
                        document.querySelector(`#goodsReceivedContainer`).appendChild(newElement);
                        if (index === data.data.length - 1) {
                        requestAnimationFrame(() => {
                            if (currentPageGoodsReceived === 2) {
                                goodsReceivedScrollbarInstance = new ScrollbarCustom('#goodsReceivedContainer', {
                                    top: null,
                                    right: 0,
                                    overflowX: 'none',
                                    overflowY: 'scroll'
                                });
                                goodsReceivedScrollbarInstance.forceUpdate();
                            } else {
                                // untuk page 2, 3, dst
                                if (goodsReceivedScrollbarInstance) {
                                    goodsReceivedScrollbarInstance.refresh();
                                }
                            }
                        });
                    }
                    }, index * 100);
                });

                currentPageGoodsReceived++;
                hasMoreDataGoodsReceived = data.hasMorePages;
            }
            else {
                hasMoreDataGoodsReceived = false; // No more data to load
                if(currentPageGoodsReceived == 1){
                    document.querySelector(`#goodsReceivedContainer`).innerHTML = `<div class="card full-height-column-wrapper">
                                                                            <div class="card-body d-flex justify-content-center align-items-center">
                                                                                <div class="center-container">
                                                                                    <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                                                    <div class="d-block fs-7 mt-2">No goods received data</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>`;
                }
            }
        }
        else if (data.status === 404) {
            $('.skeletonCardOrder').remove();
            document.querySelector(`#goodsReceivedContainer`).innerHTML = `<div class="card full-height-column-wrapper">
                                                                    <div class="card-body d-flex justify-content-center align-items-center">
                                                                        <div class="center-container">
                                                                            <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                                            <div class="d-block fs-7 mt-2">${data.message}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>`;
        }
        else {
            $('.skeletonCardOrder').remove();
            console.error('HTTP Error:', response.status);
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        isLoadingGoodsReceived = false;
    }
}

$(document).off('input', '.searchInput').on('input', '.searchInput', function(event) {
    let $this = $(this);
    clearTimeout(debounceTimeout);
    const currentValue = $(this).val().trim();
    let isCleared = $(this).attr('data-iscleared');
    if(currentValue.length === 0) {
        $(this).closest('.form-group').find('button.searchButtonClear').addClass('d-none');
        debounceTimeout = setTimeout(function () {
            if (isCleared == 'false') {
                if($this.attr('data-form') == 'ORDER_FORM_REQUEST') {
                    currentPageOrder = 1;
                    isLoadingOrder = false;
                    hasMoreDataOrder = true;
                    loadOrderColumn();
                }
                else if($this.attr('data-form') == 'APPLICATION_ONGOING') {
                    currentPageAppOngoing = 1;
                    isLoadingAppOngoing = false;
                    hasMoreDataAppOngoing = true;
                    loadAppColumn();
                }
                else if($this.attr('data-form') == 'COMPLETED_APPROVAL') {
                    currentPageAppCompleted = 1;
                    isLoadingAppCompleted = false;
                    hasMoreDataAppCompleted = true;
                    loadAppCompletedColumn();
                }
                else if($this.attr('data-form') == 'OPEN_ORDER') {
                    currentPageOpenOrder = 1
                    isLoadingOpenOrder = false;
                    hasMoreDataOpenOrder = true;
                    loadOpenOrder();
                }
                else if($this.attr('data-form') == 'GOODS_RECEIVED') {
                    currentPageGoodsReceived = 1
                    isLoadingGoodsReceived = false;
                    hasMoreDataGoodsReceived = true;
                    loadGoodsReceived();
                }
            }
        }, 1000);

        $(this).attr('data-iscleared', 'true');
    }
    else {
        $(this).closest('.form-group').find('button.searchButtonClear').removeClass('d-none');
        $(this).attr('data-iscleared', 'false');
    }
});

$(document).off('keydown', '.searchInput').on('keydown', '.searchInput', function(event) {
    const $this = $(this);
    const currentValue = $(this).val().trim();
    let isCleared = $(this).attr('data-iscleared');
    if (event.key === 'Enter' || event.keyCode === 13) {
        event.preventDefault();
        if ((currentValue.length === 0 && isCleared == 'false') || currentValue.length > 0) {
            if($this.attr('data-form') == 'ORDER_FORM_REQUEST') {
                currentPageOrder = 1;
                isLoadingOrder = false;
                hasMoreDataOrder = true;
                loadOrderColumn();
            }
            else if($this.attr('data-form') == 'APPLICATION_ONGOING') {
                currentPageAppOngoing = 1;
                isLoadingAppOngoing = false;
                hasMoreDataAppOngoing = true;
                loadAppColumn();
            }
            else if($this.attr('data-form') == 'COMPLETED_APPROVAL') {
                currentPageAppCompleted = 1;
                isLoadingAppCompleted = false;
                hasMoreDataAppCompleted = true;
                loadAppCompletedColumn();
            }
            else if($this.attr('data-form') == 'OPEN_ORDER') {
                currentPageOpenOrder = 1
                isLoadingOpenOrder = false;
                hasMoreDataOpenOrder = true;
                loadOpenOrder();
            }
            else if($this.attr('data-form') == 'GOODS_RECEIVED') {
                currentPageGoodsReceived = 1
                isLoadingGoodsReceived = false;
                hasMoreDataGoodsReceived = true;
                loadGoodsReceived();
            }

            if(currentValue.length === 0 && isCleared == 'false') {
                $(this).attr('data-iscleared', 'true');
            }
            else {
                $(this).attr('data-iscleared', 'false');
            }
        }

        // event.preventDefault();
    }
});

$(document).off('click', '.searchButton').on('click', '.searchButton', function(event) {
    let $this = $(this);
    let isCleared = $(this).closest('.form-group').find('input.searchInput').attr('data-iscleared');
    if (isCleared == 'false') {
        if($this.attr('data-form') == 'ORDER_FORM_REQUEST') {
            currentPageOrder = 1;
            isLoadingOrder = false;
            hasMoreDataOrder = true;
            loadOrderColumn();
        }
        else if($this.attr('data-form') == 'APPLICATION_ONGOING') {
            currentPageAppOngoing = 1;
            isLoadingAppOngoing = false;
            hasMoreDataAppOngoing = true;
            loadAppColumn();
        }
        else if($this.attr('data-form') == 'COMPLETED_APPROVAL') {
            currentPageAppCompleted = 1;
            isLoadingAppCompleted = false;
            hasMoreDataAppCompleted = true;
            loadAppCompletedColumn();
        }
        else if($this.attr('data-form') == 'OPEN_ORDER') {
            currentPageOpenOrder = 1
            isLoadingOpenOrder = false;
            hasMoreDataOpenOrder = true;
            loadOpenOrder();
        }
        else if($this.attr('data-form') == 'GOODS_RECEIVED') {
            currentPageGoodsReceived = 1
            isLoadingGoodsReceived = false;
            hasMoreDataGoodsReceived = true;
            loadGoodsReceived();
        }
        // $(this).attr('data-iscleared', 'false');
    }
});

$(document).off('click', '.searchButtonClear').on('click', '.searchButtonClear', function(event) {
    let $this = $(this);
    let isCleared = $(this).closest('.form-group').find('input.searchInput').attr('data-iscleared');
    $(this).closest('.form-group').find('input.searchInput').val('');
    $(this).closest('.form-group').find('input.searchInput').focus();
    $(this).addClass('d-none');

    if (isCleared == 'false') {
        if($this.attr('data-form') == 'ORDER_FORM_REQUEST') {
            currentPageOrder = 1;
            isLoadingOrder = false;
            hasMoreDataOrder = true;
            loadOrderColumn();
        }
        else if($this.attr('data-form') == 'APPLICATION_ONGOING') {
            currentPageAppOngoing = 1;
            isLoadingAppOngoing = false;
            hasMoreDataAppOngoing = true;
            loadAppColumn();
        }
        else if($this.attr('data-form') == 'COMPLETED_APPROVAL') {
            currentPageAppCompleted = 1;
            isLoadingAppCompleted = false;
            hasMoreDataAppCompleted = true;
            loadAppCompletedColumn();
        }
        else if($this.attr('data-form') == 'OPEN_ORDER') {
            currentPageOpenOrder = 1
            isLoadingOpenOrder = false;
            hasMoreDataOpenOrder = true;
            loadOpenOrder();
        }
        else if($this.attr('data-form') == 'GOODS_RECEIVED') {
            currentPageGoodsReceived = 1
            isLoadingGoodsReceived = false;
            hasMoreDataGoodsReceived = true;
            loadGoodsReceived();
        }
    }

    $(this).closest('.form-group').find('input.searchInput').attr('data-iscleared', 'true');
});

$(document).off('click', '.filterButton').on('click', '.filterButton', async function(event) {
    let $this = $(this);
    const dataForm = $this.attr('data-form');
    const arrForm = {
                        'ORDER_FORM_REQUEST': 'filterOrderFormRequest',
                        'APPLICATION_ONGOING': 'filterApplicationFormRequest',
                        'COMPLETED_APPROVAL': 'filterApplicationCompletedForm',
                        'OPEN_ORDER': 'filterOpenOrderForm',
                        'GOODS_RECEIVED': 'filterGoodsReceivedForm',
                    };

    asideHide();
    if($(this).attr('data-reference') !== '' && $(this).attr('data-reference') !== undefined) {
        $('#filterAside').append(`<div class="aside-sidebar"><div><span class="aside-sidebar-item">${$(this).attr('data-reference-title')}</span></div></div>`);
    }
    $('#filterAsideForm').html(`<div class="row min-vh-75">
                                    <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                        <div class="center-container">
                                            <div class="stripes-red-blue stripes-red-blue-md"></div>
                                            <div class="d-block fs-7 mt-2">Loading...</div>
                                        </div>
                                    </div>
                                </div>`);
    $('#filterAside').find('.aside-header').find('.aside-title').html($this.attr('title'));
    $('.overlay-aside').addClass('show').trigger('shown');
    $('#filterAside').addClass('show aside-lg').trigger('shown');
    $('body').addClass('overflow-hidden');
    $('#filterAsideForm').scrollTop(0);
    $('.resetFilter').attr('data-form', arrForm[dataForm]);
    $('.showResultFilter').attr('data-form', arrForm[dataForm]);

    let optionReceiver = [], optionSigner = [];

    let filterCompany = $(`#${arrForm[dataForm]} select[name="company"]`).html();
    let company = $(`#${arrForm[dataForm]} select[name="company"]`).val();
    let filterReceiver = $(`#${arrForm[dataForm]} select[name="receiver"]`).html();
    let receiver = $(`#${arrForm[dataForm]} select[name="receiver"]`).val();
    let applicantType = $(`#${arrForm[dataForm]} select[name="applicant"]`).val();
    let startDateRange = $(`#${arrForm[dataForm]} input[name="startDateRange"]`).val();
    let endDateRange = $(`#${arrForm[dataForm]} input[name="endDateRange"]`).val();
    let status = $(`#${arrForm[dataForm]} input[name="status"]`).val();
    let priority = $(`#${arrForm[dataForm]} input[name="priority"]`).val();
    let sortBy = $(`#${arrForm[dataForm]} input[name="sortBy"]`).val();

    if(startDateRange && endDateRange) {
        startDateRange = new Date(startDateRange);
        endDateRange = new Date(endDateRange);
    }

    let form = '';
    if(dataForm == 'ORDER_FORM_REQUEST') {
        form = `<div class="form-group mb-3">
                        <label for="filterQuery" class="form-label">Search Query :</label>
                        <input type="text" class="form-control d-flex searchInput" name="filterQuery" id="filterQuery" value="${$('#filterOrderFormRequest input[name="search"]').val()}" spellcheck="false" autocomplete="off" placeholder="Search...">
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterCompany" class="form-label">Company<span class="required"></span> :</label>
                        <select class="select2" name="filterCompany" id="filterCompany" data-form="filterOrderFormRequest"></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterDepartment" class="form-label">Order Form Department<span class="required"></span> :</label>
                        <div class="skeleton" id="filterDepartment"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterLocation" class="form-label">Order Form Location<span class="required"></span> :</label>
                        <div class="skeleton" id="filterLocation"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterApprovalStatus" class="form-label">Order Form Approval Status<span class="required"></span> :</label>
                        <select class="select2" name="filterApprovalStatus" id="filterApprovalStatus">
                            <option></option>
                            <option value="FULLY_APPROVED">FULLY APPROVED</option>
                            <option value="ONGOING">ONGOING APPROVAL</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterApplicantType" class="form-label">Order Form Applicant Type<span class="required"></span> :</label>
                        <select class="select2" name="filterApplicantType" id="filterApplicantType">
                            <option></option>
                            <option value="ALL">-- ALL APPLICANT --</option>
                            <option value="BY_APPLICANT">BY APPLICANT</option>
                        </select>
                    </div>
                    <div class="form-group mb-3 d-none">
                        <label for="filterApplicant" class="form-label">Order Form Applicant<span class="required"></span> :</label>
                        <select class="select2" name="filterApplicant" id="filterApplicant">
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterSortBy" class="form-label">Sort By<span class="required"></span> :</label>
                        <select class="select2" name="filterSortBy" id="filterSortBy">
                            <option></option>
                            <option value="LATEST">LATEST RECEIVED ORDER FORM</option>
                            <option value="OLDEST">OLDEST RECEIVED ORDER FORM</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterDateRange" class="form-label">Order Form Date :<span class="fst-italic fw-normal fs-9"> (Left blank if selecting all dates)</span></label>
                        <div class="form-group" id="filterDateRange" data-coreui-name="filterDateRange" data-coreui-toggle="date-range-picker" data-coreui-date="" data-coreui-timepicker="false"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterReceiver" class="form-label">Purchasing PIC<span class="required"></span> :</label>
                        <select class="select2" name="filterReceiver" id="filterReceiver"></select>
                    </div>`;
    }
    else if(dataForm == 'APPLICATION_ONGOING') {
        form = `<div class="form-group mb-3">
                        <label for="filterQuery" class="form-label">Search Query :</label>
                        <input type="text" class="form-control d-flex searchInput" name="filterQuery" id="filterQuery" value="${$('#filterApplicationFormRequest input[name="search"]').val()}" spellcheck="false" autocomplete="off" placeholder="Search...">
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterCompany" class="form-label">Company<span class="required"></span> :</label>
                        <select class="select2" name="filterCompany" id="filterCompany" data-form="filterApplicationFormRequest"></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterReceiver" class="form-label">Purchasing PIC<span class="required"></span> :</label>
                        <select class="select2" name="filterReceiver" id="filterReceiver"></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterPriority" class="form-label">PO Priority Level<span class="required"></span> :</label>
                        <select class="select2" name="filterPriority" id="filterPriority">
                            <option></option>
                            <option value="ALL">-- ALL PRIORITY --</option>
                            <option value="2">URGENT</option>
                            <option value="3">NORMAL</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterSortBy" class="form-label">Sort By<span class="required"></span> :</label>
                        <select class="select2" name="filterSortBy" id="filterSortBy">
                            <option></option>
                            <option value="LATEST">LATEST SUBMITTED PO APPROVAL</option>
                            <option value="LATEST">LATEST SUBMITTED PO APPROVAL</option>
                            <option value="PRIORITY">PRIORITY LEVEL</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterDateRange" class="form-label">Submitted PO Date :<span class="fst-italic fw-normal fs-9"> (Left blank if selecting all dates)</span></label>
                        <div class="form-group" id="filterDateRange" data-coreui-name="filterDateRange" data-coreui-toggle="date-range-picker" data-coreui-date="" data-coreui-timepicker="false"></div>
                    </div>`;
    }
    else if(dataForm == 'COMPLETED_APPROVAL') {
        form = `<div class="form-group mb-3">
                        <label for="filterQuery" class="form-label">Search Query :</label>
                        <input type="text" class="form-control d-flex searchInput" name="filterQuery" id="filterQuery" value="${$('#filterApplicationCompletedForm input[name="search"]').val()}" spellcheck="false" autocomplete="off" placeholder="Search...">
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterCompany" class="form-label">Company<span class="required"></span> :</label>
                        <select class="select2" name="filterCompany" id="filterCompany" data-form="filterApplicationCompletedForm"></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterReceiver" class="form-label">Purchasing PIC<span class="required"></span> :</label>
                        <select class="select2" name="filterReceiver" id="filterReceiver"></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterPriority" class="form-label">PO Priority Level<span class="required"></span> :</label>
                        <select class="select2" name="filterPriority" id="filterPriority">
                            <option></option>
                            <option value="ALL">-- ALL PRIORITY --</option>
                            <option value="2">URGENT</option>
                            <option value="3">NORMAL</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterApprovalStatus" class="form-label">PO Status<span class="required"></span> :</label>
                        <select class="select2" name="filterApprovalStatus" id="filterApprovalStatus">
                            <option></option>
                            <option value="ALL">-- ALL STATUS --</option>
                            <option value="APPROVED">APPROVED</option>
                            <option value="NEED_REVISE">NEED REVISE</option>
                            <option value="CANCELED">CANCELED</option>
                            <option value="REJECTED">REJECTED</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterSortBy" class="form-label">Sort By<span class="required"></span> :</label>
                        <select class="select2" name="filterSortBy" id="filterSortBy">
                            <option></option>
                            <option value="LATEST">LATEST DECISION PO APPROVAL</option>
                            <option value="OLDEST">OLDEST DECISION PO APPROVAL</option>
                            <option value="PRIORITY">PRIORITY LEVEL</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterDateRange" class="form-label">Completed Approval Date :<span class="fst-italic fw-normal fs-9"> (Left blank if selecting all dates)</span></label>
                        <div class="form-group" id="filterDateRange" data-coreui-name="filterDateRange" data-coreui-toggle="date-range-picker" data-coreui-date="" data-coreui-timepicker="false"></div>
                    </div>`;
    }
    else if(dataForm == 'OPEN_ORDER') {
        form = `<div class="form-group mb-3">
                        <label for="filterQuery" class="form-label">Search Query :</label>
                        <input type="text" class="form-control d-flex searchInput" name="filterQuery" id="filterQuery" value="${$('#filterOpenOrderForm input[name="search"]').val()}" spellcheck="false" autocomplete="off" placeholder="Search...">
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterCompany" class="form-label">Company<span class="required"></span> :</label>
                        <select class="select2" name="filterCompany" id="filterCompany" data-form="filterOpenOrderForm"></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterReceiver" class="form-label">Purchasing PIC<span class="required"></span> :</label>
                        <select class="select2" name="filterReceiver" id="filterReceiver"></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterPriority" class="form-label">PO Priority Level<span class="required"></span> :</label>
                        <select class="select2" name="filterPriority" id="filterPriority">
                            <option></option>
                            <option value="ALL">-- ALL PRIORITY --</option>
                            <option value="2">URGENT</option>
                            <option value="3">NORMAL</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterSortBy" class="form-label">Sort By<span class="required"></span> :</label>
                        <select class="select2" name="filterSortBy" id="filterSortBy">
                            <option></option>
                            <option value="LATEST">LATEST OPEN ORDER DATE</option>
                            <option value="OLDEST">OLDEST OPEN ORDER DATE</option>
                            <option value="PRIORITY">PRIORITY LEVEL</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterDateRange" class="form-label">Order Date :<span class="fst-italic fw-normal fs-9"> (Left blank if selecting all dates)</span></label>
                        <div class="form-group" id="filterDateRange" data-coreui-name="filterDateRange" data-coreui-toggle="date-range-picker" data-coreui-date="" data-coreui-timepicker="false"></div>
                    </div>`;
    }
    else if(dataForm == 'GOODS_RECEIVED') {
        form = `<div class="form-group mb-3">
                        <label for="filterQuery" class="form-label">Search Query :</label>
                        <input type="text" class="form-control d-flex searchInput" name="filterQuery" id="filterQuery" value="${$('#filterGoodsReceivedForm input[name="search"]').val()}" spellcheck="false" autocomplete="off" placeholder="Search...">
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterCompany" class="form-label">Company<span class="required"></span> :</label>
                        <select class="select2" name="filterCompany" id="filterCompany" data-form="filterGoodsReceivedForm"></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterReceiver" class="form-label">Purchasing PIC<span class="required"></span> :</label>
                        <select class="select2" name="filterReceiver" id="filterReceiver"></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterPriority" class="form-label">PO Priority Level<span class="required"></span> :</label>
                        <select class="select2" name="filterPriority" id="filterPriority">
                            <option></option>
                            <option value="ALL">-- ALL PRIORITY --</option>
                            <option value="2">URGENT</option>
                            <option value="3">NORMAL</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterSortBy" class="form-label">Sort By<span class="required"></span> :</label>
                        <select class="select2" name="filterSortBy" id="filterSortBy">
                            <option></option>
                            <option value="LATEST">LATEST RECEIVED DATE</option>
                            <option value="OLDEST">OLDEST RECEIVED DATE</option>
                            <option value="PRIORITY">PRIORITY LEVEL</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="filterDateRange" class="form-label">Received Goods Date :<span class="fst-italic fw-normal fs-9"> (Left blank if selecting all dates)</span></label>
                        <div class="form-group" id="filterDateRange" data-coreui-name="filterDateRange" data-coreui-toggle="date-range-picker" data-coreui-date="" data-coreui-timepicker="false"></div>
                    </div>`;
    }

    $('#filterAsideForm').html(`${form}`);

    if(dataForm == 'ORDER_FORM_REQUEST') {
        $('#filterApprovalStatus').select2({
            dropdownParent: $('#filterAside'),
            allowClear: false,
            placeholder: '-- Select --',
        }).val(status).trigger('change');

        $('#filterApplicantType').select2({
            dropdownParent: $('#filterAside'),
            allowClear: false,
            placeholder: '-- Select --',
        });
    }
    else if(dataForm == 'COMPLETED_APPROVAL') {
        // let signer = $('#filterApplicationFormRequest input[name="signer"]').val();
        // $('#filterSigner').select2({
        //     dropdownParent: $('#filterAside'),
        //     allowClear: false,
        //     placeholder: '-- Select --',
        //     data: optionSigner,
        // }).val(signer).trigger('change');

        // let status = $(`#${arrForm[dataForm]} input[name="status"]`).html();
        $('#filterApprovalStatus').select2({
            dropdownParent: $('#filterAside'),
            allowClear: false,
            placeholder: '-- Select --',
        }).val(status).trigger('change');
    }

    $('#filterPriority').select2({
        dropdownParent: $('#filterAside'),
        allowClear: false,
        placeholder: '-- Select --',
    }).val(priority).trigger('change');

    $('#filterSortBy').select2({
        dropdownParent: $('#filterAside'),
        allowClear: false,
        placeholder: '-- Select --',
    }).val(sortBy).trigger('change');

    var preselectedApplicant = null, preselectedApplicantText = '';
    if(dataForm == 'ORDER_FORM_REQUEST' && applicantType == 'ALL') {
        $('#filterApplicantType').val(applicantType).trigger('change');
        $('#filterApplicant').select2({
            dropdownParent: $('#filterAside'),
            minimumInputLength: 3,
            allowClear: false,
            placeholder: '-- Select --',
            data: preselectedApplicant ? [{
                id: preselectedApplicant,
                text: preselectedApplicantText,
            }] : [],
            ajax: {
                url : `/doc_approval/getEmployee?dataType=ALL_EMPLOYEE&nik=ALL&dept=ALL`,
                type: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                    'Referer': window.location.href
                },
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term,
                        page: params.page || 1
                    }
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.map(row => ({
                            id: row.id,
                            text: row.text,
                        })),
                        pagination: {
                            more: (params.page * 10) < data.count_filtered
                        }
                    }
                },
            }
        });
    }

    $('#filterCompany').html(filterCompany);
    $('#filterCompany').select2({
        dropdownParent: $('#filterAside'),
        allowClear: false,
        placeholder: '-- Select --',
    }).val(company).trigger('change');

    $('#filterReceiver').html(filterReceiver);
    $('#filterReceiver').select2({
        dropdownParent: $('#filterAside'),
        allowClear: false,
        placeholder: '-- Select --',
    }).val(receiver).trigger('change');

    const optionsRequiredDatePicker = {
        locale: 'en-US',
        inputDateFormat: date => dayjs(date).locale('en').format('DD-MM-YYYY'),
        inputDateParse: date => dayjs(date, 'DD-MM-YYYY', 'id').toDate(),
        // minDate: dayjs(new Date()),
        maxDate: dayjs(new Date()),
        showAdjacementDays: false,
        container: '#globalAside',
        placement: "top-start",
        startDate: startDateRange,
        endDate: endDateRange,
        ranges: {
            Today: [new Date(), new Date()],
            Yesterday: [
                new Date(new Date().setDate(new Date().getDate() - 1)),
                new Date(new Date().setDate(new Date().getDate() - 1))
            ],
            'Last 7 Days': [
                new Date(new Date().setDate(new Date().getDate() - 6)),
                new Date(new Date())
            ],
            'Last 30 Days': [
                new Date(new Date().setDate(new Date().getDate() - 29)),
                new Date(new Date())
            ],
            'This Month': [
                new Date(new Date().setDate(1)),
                new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0)
            ],
            'Last Month': [
                new Date(new Date().getFullYear(), new Date().getMonth() - 1, 1),
                new Date(new Date().getFullYear(), new Date().getMonth(), 0)
            ]
        }
    }
    dateRangePickerFilter = new coreui.DateRangePicker(document.getElementById('filterDateRange'), optionsRequiredDatePicker);
    setTimeout(() => {
        let dropdown = document.querySelector(".daterangepicker");
        if (dropdown) {
            dropdown.setAttribute("data-popper-placement", "top-start");
        }
    }, 100);
});

$(document).on('change', '#filterCompany', async function (event) {
    let $this = $(this);
    let dataForm = $('#filterCompany').attr('data-form');
    if(dataForm == 'filterOrderFormRequest') {
        let dept = $(`#${dataForm} input[name="dept"]`).val();

        if ($('#filterDepartment').hasClass('select2-hidden-accessible')) {
            $('#filterDepartment').val(null).empty().trigger('change');
            $('#filterDepartment').select2('destroy');
        }

        $('#filterDepartment').replaceWith('<div class="skeleton" id="filterDepartment"></div>');
        if($this.val() == 'ALL') {
            $('#filterDepartment').replaceWith('<select class="select2" name="filterDepartment" id="filterDepartment"><option value="ALL" selected="">-- ALL DEPARTMENT --</option></select>')
            $('#filterDepartment').select2({
                dropdownParent: $('#filterAside'),
                allowClear: false,
                placeholder: '-- Select --',
            }).trigger('change');
        }
        else {
            let optionDepartment = await getDepartment({'companyId': $this.val(),'departmentId': 'ALL'});
            $('#filterDepartment').replaceWith(`<select class="select2" name="filterDepartment" id="filterDepartment"><option></option></select>`);
            $('#filterDepartment').select2({
                dropdownParent: $('#filterAside'),
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
            }).val(dept).trigger('change');
        }
    }
});

$(document).on('change', '#filterDepartment', async function (event) {
    let $this = $(this);
    let dataForm = $('#filterCompany').attr('data-form');
    let location = $(`#${dataForm} input[name="location"]`).val();

    if ($('#filterLocation').hasClass('select2-hidden-accessible')) {
        $('#filterLocation').val(null).empty().trigger('change');
        $('#filterLocation').select2('destroy');
    }

    $('#filterLocation').replaceWith('<div class="skeleton" id="filterLocation"></div>');
    if($this.val() == 'ALL') {
        $('#filterLocation').replaceWith('<select class="select2" name="filterLocation" id="filterLocation"><option value="ALL" selected="">-- ALL LOCATION --</option></select>')
        $('#filterLocation').select2({
            dropdownParent: $('#filterAside'),
            allowClear: false,
            placeholder: '-- Select --',
        }).trigger('change');
    }
    else {
        let optionLocation = await getLocation({'companyId': $('#filterCompany').val(), 'departmentId': $this.val(), 'locationId': 'ALL'});
        $('#filterLocation').replaceWith(`<select class="select2" name="filterLocation" id="filterLocation"><option></option></select>`);
        $('#filterLocation').select2({
            dropdownParent: $('#filterAside'),
            allowClear: false,
            placeholder: '-- Select --',
            data: optionLocation,
        }).val(location).trigger('change');
    }
});

$(document).on('change', '#filterApplicantType', function (event) {
    let $this = $(this);
    $('#filterApplicant').closest('div.form-group').addClass('d-none')
    if($this.val() !== 'ALL') {
        $('#filterApplicant').closest('div.form-group').removeClass('d-none')
    }
});

$(document).on('click', '.resetFilter', function (event) {
    const $this = $(this);
    const dataForm = $this.attr('data-form');
    asideHide();

    let company = $(`#${dataForm} select[name="company"]`).find('option[selected]').val();
    $(`#${dataForm} select[name="company"]`).val(company).trigger('change');
    $(`#${dataForm} select[name="applicant"]`).html('<option value="ALL" selected="">-- ALL --</option>').trigger('change');
    $(`#${dataForm} input[name="status"]`).val('ALL');
    $(`#${dataForm} input[name="priority"]`).val('ALL');
    $(`#${dataForm} input[name="dept"]`).val('ALL');
    $(`#${dataForm} input[name="location"]`).val('ALL');
    $(`#${dataForm} input[name="startDateRange"]`).val('');
    $(`#${dataForm} input[name="endDateRange"]`).val('');
    $(`#${dataForm} input[name="sortBy"]`).val('LATEST');

    if(dataForm == 'filterOrderFormRequest') {
        $(`#${dataForm} input[name="status"]`).val('FULLY_APPROVED');
        if($(`#${dataForm}`).find('button.filterButton').hasClass('red-dot-checked') || $(`#${dataForm} input[name="search"]`).val() != '') {
            $(`#${dataForm} input[name="search"]`).val('');
            $(`#${dataForm} input[name="search"]`).attr('data-iscleared', true);
            $(`#${dataForm} input[name="search"]`).closest('div.form-group').find('button.searchButtonClear').removeClass('d-none').addClass('d-none');
            $(`#${dataForm}`).find('button.filterButton').removeClass('red-dot-checked');
            currentPageOrder = 1;
            isLoadingOrder = false;
            hasMoreDataOrder = true;
            loadOrderColumn();
        }
    }
    else if(dataForm == 'filterApplicationFormRequest') {
        // $('#filterApplicationFormRequest input[name="signer"]').val('ALL');
        if($(`#${dataForm}`).find('button.filterButton').hasClass('red-dot-checked') || $(`#${dataForm} input[name="search"]`).val() != '') {
            $(`#${dataForm} input[name="search"]`).val('');
            $(`#${dataForm} input[name="search"]`).attr('data-iscleared', true);
            $(`#${dataForm} input[name="search"]`).closest('div.form-group').find('button.searchButtonClear').removeClass('d-none').addClass('d-none');
            $(`#${dataForm}`).find('button.filterButton').removeClass('red-dot-checked');
            currentPageAppOngoing = 1;
            isLoadingAppOngoing = false;
            hasMoreDataAppOngoing = true;
            loadAppColumn();
        }
    }
    else if(dataForm == 'filterApplicationCompletedForm') {
        if($(`#${dataForm}`).find('button.filterButton').hasClass('red-dot-checked') || $(`#${dataForm} input[name="search"]`).val() != '') {
            $(`#${dataForm} input[name="search"]`).val('');
            $(`#${dataForm} input[name="search"]`).attr('data-iscleared', true);
            $(`#${dataForm} input[name="search"]`).closest('div.form-group').find('button.searchButtonClear').removeClass('d-none').addClass('d-none');
            $(`#${dataForm}`).find('button.filterButton').removeClass('red-dot-checked');
            currentPageAppCompleted = 1
            isLoadingAppCompleted = false;
            hasMoreDataAppCompleted = true;
            loadAppCompletedColumn();
        }
    }
    else if(dataForm == 'filterOpenOrderForm') {
        if($(`#${dataForm}`).find('button.filterButton').hasClass('red-dot-checked') || $(`#${dataForm} input[name="search"]`).val() != '') {
            $(`#${dataForm} input[name="search"]`).val('');
            $(`#${dataForm} input[name="search"]`).attr('data-iscleared', true);
            $(`#${dataForm} input[name="search"]`).closest('div.form-group').find('button.searchButtonClear').removeClass('d-none').addClass('d-none');
            $(`#${dataForm}`).find('button.filterButton').removeClass('red-dot-checked');
            currentPageOpenOrder = 1
            isLoadingOpenOrder = false;
            hasMoreDataOpenOrder = true;
            loadOpenOrder();
        }
    }
    else if(dataForm == 'filterGoodsReceivedForm') {
        if($(`#${dataForm}`).find('button.filterButton').hasClass('red-dot-checked') || $(`#${dataForm} input[name="search"]`).val() != '') {
            $(`#${dataForm} input[name="search"]`).val('');
            $(`#${dataForm} input[name="search"]`).attr('data-iscleared', true);
            $(`#${dataForm} input[name="search"]`).closest('div.form-group').find('button.searchButtonClear').removeClass('d-none').addClass('d-none');
            $(`#${dataForm}`).find('button.filterButton').removeClass('red-dot-checked');
            currentPageGoodsReceived = 1
            isLoadingGoodsReceived = false;
            hasMoreDataGoodsReceived = true;
            loadGoodsReceived();
        }
    }
});

$(document).on('click', '.showResultFilter', function (event) {
    let $this = $(this);
    asideHide();
    let searchQuery = $('#filterAsideForm').find('#filterQuery').val().trim();
    const dataForm = $this.attr('data-form');
    if(dataForm == 'filterOrderFormRequest' || dataForm == 'filterApplicationFormRequest' || dataForm == 'filterApplicationCompletedForm' || dataForm == 'filterOpenOrderForm' || dataForm == 'filterGoodsReceivedForm') {

        $(`#${dataForm}`).find('button.filterButton').removeClass('red-dot-checked');
        let applicant = '', dept = '', location = '', applicantType = '', poApplicant = '';
        if(dataForm == 'filterOrderFormRequest') {
            dept = $('#filterAsideForm').find('#filterDepartment').val();
            location = $('#filterAsideForm').find('#filterLocation').val();
            poApplicant = $('#filterAsideForm').find('#filterReceiver').val();
            applicantType = $('#filterAsideForm').find('#filterApplicantType').val();
            if(applicantType == 'ALL') {
                applicant = '<option value="ALL" selected="">-- ALL --</option>';
            }
            else {
                applicant = `<option value="${$('#filterAsideForm').find('#filterApplicant').val()}" selected="">${$('#filterAsideForm').find('#filterApplicant option:selected').text()}</option>`;
            }
        }

        let companyDefault = $(`#${dataForm} select[name="company"]`).find('option[selected]').val();
        let company = $('#filterAsideForm').find('#filterCompany').val();
        let sortBy = $('#filterAsideForm').find('#filterSortBy').val();
        let priority = $('#filterAsideForm').find('#filterPriority').val();
        let startDateRange = '', endDateRange = '';
        const picker = coreui.DateRangePicker.getInstance(document.getElementById('filterDateRange'));
        if (picker._startDate && picker._endDate) {
            startDateRange = dayjs(picker._startDate).format('YYYY-MM-DD');
            endDateRange = dayjs(picker._endDate).format('YYYY-MM-DD');
        }

        let status = $('#filterAsideForm').find('#filterApprovalStatus').val();
        $(`#${dataForm} input[name="search"]`).val(searchQuery);
        if(searchQuery == '') {
            $(`#${dataForm} input[name="search"]`).attr('data-iscleared', true);
            $(`#${dataForm} input[name="search"]`).closest('div.form-group').find('button.searchButtonClear').removeClass('d-none').addClass('d-none');
        }
        else {
            $(`#${dataForm} input[name="search"]`).attr('data-iscleared', false);
            $(`#${dataForm} input[name="search"]`).closest('div.form-group').find('button.searchButtonClear').removeClass('d-none');
        }

        if(dataForm == 'filterOrderFormRequest') {
            $(`#${dataForm} select[name="applicant"]`).html(applicant).trigger('change');
            $(`#${dataForm} input[name="dept"]`).val(dept);
            $(`#${dataForm} input[name="location"]`).val(location);
            $(`#${dataForm} select[name="receiver"]`).val(poApplicant).trigger('change');
        }

        $(`#${dataForm} select[name="company"]`).val(company).trigger('change');
        $(`#${dataForm} input[name="status"]`).val(status);
        $(`#${dataForm} input[name="priority"]`).val(priority);
        $(`#${dataForm} input[name="sortBy"]`).val(sortBy);
        $(`#${dataForm} input[name="startDateRange"]`).val(startDateRange);
        $(`#${dataForm} input[name="endDateRange"]`).val(endDateRange);

        if(dataForm == 'filterOrderFormRequest' && (company != companyDefault || applicantType != 'ALL' || status != 'FULLY_APPROVED' || dept != 'ALL' || location != 'ALL' || sortBy != 'LATEST' || startDateRange != '' || endDateRange != '' || priority != 'ALL')) {
            $(`#${dataForm}`).find('button.filterButton').addClass('red-dot-checked');
        }
        else if(dataForm != 'filterOrderFormRequest' && (company != companyDefault || status != 'ALL' || sortBy != 'LATEST' || startDateRange != '' || endDateRange != '' || priority != 'ALL')) {
            $(`#${dataForm}`).find('button.filterButton').addClass('red-dot-checked');
        }

        if(dataForm == 'filterOrderFormRequest') {
            currentPageOrder = 1;
            isLoadingOrder = false;
            hasMoreDataOrder = true;
            loadOrderColumn();
        }
        else if(dataForm == 'filterApplicationFormRequest') {
            currentPageAppOngoing = 1;
            isLoadingAppOngoing = false;
            hasMoreDataAppOngoing = true;
            loadAppColumn();
        }
        else if(dataForm == 'filterApplicationCompletedForm') {
            currentPageAppCompleted = 1
            isLoadingAppCompleted = false;
            hasMoreDataAppCompleted = true;
            loadAppCompletedColumn();
        }
        else if(dataForm == 'filterOpenOrderForm') {
            currentPageOpenOrder = 1
            isLoadingOpenOrder = false;
            hasMoreDataOpenOrder = true;
            loadOpenOrder();
        }
        else if(dataForm == 'filterGoodsReceivedForm') {
            currentPageGoodsReceived = 1
            isLoadingGoodsReceived = false;
            hasMoreDataGoodsReceived = true;
            loadGoodsReceived();
        }
    }
});

$(document).off('click', '.card-link-content').on('click', '.card-link-content', async function (event) {
    const $this = $(this);
    $('.card-link-content').removeClass('active');
    $this.addClass('active');
});

$(document).off('dblclick', '.card-link-content').on('dblclick', '.card-link-content', async function (event) {
    const $this = $(this);
    event.stopImmediatePropagation();
    $('.card-link-content').removeClass('active');
    Snackbar.close();
    $this.addClass('active');

    if($this.attr('data-form') == 'ORDER_FORM') {
        if($this.attr('data-type') == 'VIEW' || $this.attr('data-type') == 'VIEW_MULTIPLE') {
            Snackbar.show({
                pos: 'bottom-center',
                duration: '6000',
                text: `<i class="fa-regular fa-circle-notch fa-spin fs-6 fa-fw text-info"></i> Processing...`
            });

            const token = $this.attr('data-token');
            const form = $this.attr('data-form');
            const source = $this.attr('data-source');

            try {
                const response = await fetch(`/proc_pur/getComparison?type=PURCHASING&form=${form}&token=${token}`, {
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
                                'source': 'WEB',
                                'form': result.data.form,
                                'token': result.data.tokenForm,
                                'type': $this.attr('data-type'),
                            };

                            if($this.attr('data-type') == 'NEW') {
                                newForm(params);
                            }
                            else{
                                viewForm(params);
                            }
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
        else if($this.attr('data-type') == 'VIEW_ONGOING') {
            const params = {
                'form': $this.attr('data-form'),
                'token': $this.attr('data-token'),
                'type': $this.attr('data-type'),
            };
            $('.modal').modal('hide');

            viewForm(params);
        }
    }
    else {
        const params = {
            'source': $this.attr('data-source'),
            'form': $this.attr('data-form'),
            'token': $this.attr('data-token'),
            'type': $this.attr('data-type'),
        };

        if($this.attr('data-type') == 'NEW') {
            newForm(params);
        }
        else{
            viewForm(params);
        }
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
        };
        viewForm(params);
    }
});

$(document).off('click', '.multiplePoList').on('click', '.multiplePoList', async function (event) {
    const $this = $(this);
    Snackbar.close();
    const params = {
        'source': $this.attr('data-source'),
        'form': $this.attr('data-form'),
        'token': $this.attr('data-token'),
        'type': $this.attr('data-type'),
    };
    $('.modal').modal('hide');

    if($this.attr('data-type') == 'NEW') {
        newForm(params);
    }
    else {
        viewForm(params);
    }
});

$(document).off('click', '.cardHoverBtn').on('click', '.cardHoverBtn', async function (event) {
    event.stopPropagation();
    const $this = $(this);
    Snackbar.close();

    let tokenForm = $this.closest('.card-hover').attr('data-token');
    let type = '', alert = '', content = '';
    if($this.attr('data-type') == 'CANCEL_ITEM') {
        type = 'Cancel Item';
        alert = `<div class="alert alert-info mb-3 w-100 p-2" role="alert">
                    <span class="fw-semibold">“Canceled Items”</span> will send a notification email to Order Form Requester.
                </div>`;
        content = `<input type="hidden" name="tokenForm" value="${tokenForm}">
                    <div class="form-group mb-3">
                        <label for="selectItemUnprocessed" class="form-label">Item to Cancel<span class="required"></span> :</label>
                        <div class="skeleton" id="skeletonItemUnprocessed"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="itemRowCriteria" class="form-label">Reason <span class="required"></span> :</label>
                        <textarea class="form-control autosize itemMandatory" spellcheck="false" maxlength="255" id="reason" name="reason"></textarea>
                    </div>`;
    }

    $('.aside-title').html(`${type}`);
    $('.aside-content').html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
                                ${alert}
                                ${content}
                            </form>`);

    $('.overlay-aside').addClass('show').trigger('shown');
    $('#globalAside').addClass('show').trigger('shown');
    $('body').addClass('overflow-hidden');
    $('#asideDetailForm').scrollTop(0);
    $('.autosize').autosize({ append: "\n" });
    $('.asideFooterBtn').html(`<div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                    <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside asideBtn" title="Close">Close</button>
                                </div>
                                <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                    <button type="button" class="btn btn-secondary w-100 w-md-auto asideBtn actionCardHoverBtn" data-token="${tokenForm}" data-type="${$this.attr('data-type')}"></i> ${type}</button>
                                </div>`);

    try {
        const response = await fetch(`/proc_pur/itemUnprocessed?token=${tokenForm}`, {
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
            $('#skeletonItemUnprocessed').replaceWith(`<select class="select2" name="selectItemUnprocessed[]" id="selectItemUnprocessed"></select>`);
            var option = data.data.map(function(item) {
                return {
                    id: item.value,
                    text: item.label
                };
            });

            var $select = $('#selectItemUnprocessed');
            $select.select2({
                dropdownParent: $('#globalAside'),
                data: option,
                placeholder: '-- Select --',
                multiple: true,
                closeOnSelect: false,
            }).on('select2:select select2:unselect', function(e) {
                setTimeout(function() {
                    var selectedOptions = $select.select2('data');
                    var totalOptions = $select.find('option').length;

                    if (selectedOptions.length === totalOptions - 1) {
                        $select.select2('close');
                    } else {
                        $select.select2('open');
                    }
                }, 0);
            });

            $select.on('select2:open', function(e) {
                var selectedOptions = $select.select2('data');
                var totalOptions = $select.find('option').length;

                if (selectedOptions.length === totalOptions - 1) {
                    setTimeout(function() {
                        $select.select2('close');
                    }, 0);
                }
            });

            var emptyOption = new Option('', '', true, false);
            $('#selectItemUnprocessed').prepend(emptyOption);
            $('#selectItemUnprocessed').trigger('change');
        }
        else if (data.status === 404) {
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
                    window.location.href = data.redirect_uri;
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
});

$(document).off('click', '.actionCardHoverBtn').on('click', '.actionCardHoverBtn', function (event) {
    let $this = $(this);
    Snackbar.close();
    clearValidation();
    const formData = new FormData($('#asideForm')[0]);
    if ($('#selectItemUnprocessed').select2('data').length === 0) {
        formData.append('selectItemUnprocessed[]', '');
    }
    formData.append('type', $this.attr('data-type'));
    formData.append('_method', 'PUT');

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
            $('.asideBtn').prop('disabled', true);
            $this.html('<i class="fas fa-spinner fa-spin"></i> Please wait');
            const response = await fetch('/proc_pur/updateOrderItem', {
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
                Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result.message}` });
                asideHide();
                $('.aside-content').html('');

                if(result.data.itemCompleted === false) {
                    $('.card-link-content.active').find('ul.itemListOrder').html(result.data.allItems);
                    $('.card-link-content.active').find('span.selectedVendorOrder').html(result.data.selectedVendor);
                }
                else {
                    $('.card-link-content.active').parent().remove();
                }

                $('#modalMessageBody').html(`<div class="row min-vh-75">
                                                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                    <div class="center-container">
                                                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                                                        <div class="d-block fs-7 mt-2">Loading...</div>
                                                    </div>
                                                </div>
                                            </div>`);
                try {
                    const response = await fetch(`/proc_pur/getComparison?source=WEB&form=ORDER_FORM_LIST&token=${$this.attr('data-token')}`, {
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
                                window.location.href = data.redirect_uri;
                            }
                        }, 1000);
                    }
                    else {
                        Snackbar.close();
                        if (data.status === 200) {
                            event.stopImmediatePropagation();
                            $('#modalMessageBody').html(data.data.listItem);
                        }
                        else if (data.status === 404) {
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

async function viewForm(params) {
    const token = params['token'];
    const form = params['form'];
    const source = params['source'];
    Snackbar.close();
    // $('#asideDetailForm').html('');
    // $('.popper-area').html('');
    // if ($(window).width() < 768) {
    //     rightContentSkeleton('FETCH_ASIDE');
    // }
    // else{
    //     rightContentSkeleton('FETCH');
    // }
    if(form == 'FORM') {
        $('#modalDocumentFormBody').removeClass('pt-0 pb-5 p-0 overflow-x-hidden overflow-y-hidden').addClass('pt-0 pb-5 overflow-x-hidden overflow-y-auto');
        return false;
    }
    else if(form == 'ORDER_FORM' || form == 'COMPARISON_FORM' || form == 'APPLICATION_FORM' || form == 'APPLICATION_FORM_COMPLETED') {
        // if(form == 'ORDER_FORM'){
            $('#modalDocumentFormBody').removeClass('pt-0 pb-5 p-0 overflow-x-hidden overflow-y-hidden').addClass('p-0 overflow-y-hidden overflow-x-hidden');
        // }
        // else {
        //     $('#modalDocumentFormBody').removeClass('pt-0 pb-5 p-0 overflow-x-hidden overflow-y-hidden').addClass('p-0 overflow-y-auto overflow-x-hidden');
        // }
        $('#modalDocumentFormTitle').html(`<div class="skeleton mb-0" style="width: 250px; height: 24px"></div>`);
        $('.actionHeaderContainer').html(`<div class="skeleton mb-0" style="width: 100px; height: 24px"></div>`);
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
    }
    else if(form == 'ORDER_FORM_TAB') {
        $('#modalDocumentFormBody').removeClass('pt-0 pb-5 p-0 overflow-y-hidden').addClass('p-0 overflow-y-hidden');
        if($.trim($('#orderFormContent').html()).length === 0) {
            $('#orderFormContent').html(`<div class="row min-vh-75">
                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                    <div class="center-container">
                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                        <div class="d-block fs-7 mt-2">Loading...</div>
                    </div>
                </div>
            </div>`);
        }
        else {
            return false;
        }
    }
    else if(form == 'COMPARISON_FORM_TAB') {
        $('#modalDocumentFormBody').removeClass('pt-0 pb-5 p-0 overflow-y-hidden').addClass('p-0 overflow-y-hidden');
        if($.trim($('#comparisonFormContent').html()).length === 0) {
            $('#comparisonFormContent').html(`<div class="row min-vh-75">
                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                    <div class="center-container">
                        <div class="stripes-red-blue stripes-red-blue-md"></div>
                        <div class="d-block fs-7 mt-2">Loading...</div>
                    </div>
                </div>
            </div>`);
        }
        else {
            return false;
        }
    }

    try {
        const response = await fetch(`/proc_pur/viewForm?source=${source}&form=${form}&token=${token}`, {
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
        else if(form == 'ORDER_FORM' || form == 'COMPARISON_FORM' || form == 'APPLICATION_FORM' || form == 'APPLICATION_FORM_COMPLETED') {
            if (data.status === 200) {
                $('#modalDocumentFormTitle').html(data.data.title);
                $('.actionHeaderContainer').html(`
                    <button class="btn btn-default btn-sm d-none d-md-inline actionBtnHeader" data-token="${data.data.tokenAttachment}" data-type="PRINT" data-selectedversion="${data.data.selectedVersionLabel}" title="Print Documents"><i class="fa-solid fa-print fa-fw"></i></button>
                    <button class="btn btn-default btn-sm actionBtnHeader" data-token="${data.data.tokenAttachment}" data-type="DOWNLOAD" data-selectedversion="${data.data.selectedVersionLabel}" title="Downloads"><i class="fa-solid fa-arrow-down-to-line fa-fw"></i></button>
                    <button class="btn btn-default btn-sm actionBtnHeader" data-token="${data.data.tokenAttachment}" data-type="ATTACHMENTS" data-type-flow="PURCHASING" data-selectedversion="${data.data.selectedVersionLabel}"><i class="fa-solid fa-bars-progress"></i> View Attachments</button>`);
                $('#modalDocumentFormBody').html(`<object class="w-100 h-100" id="subfile_frame" data="/framePdf?token=${data.data.tokenForm}" type="text/html"><param name="allowfullscreen" value="true"></object>`);
                // {$('#modalDocumentFormBody').html(data.data.form);}
                $('#modalDocumentFormFooter').html(data.data.footerButton);
                $('#modalDocumentFormBody').scrollTop(0);
                $('.table-responsive').scrollLeft(0);
                $('.autosize').autosize().trigger('change');
            }
            else if (data.status === 404) {
                $('#modalDocumentFormTitle').html('Detail Form');
                $('.actionHeaderContainer').html('');
                $('#modalDocumentFormBody').html(data.data.form);
                $('#modalDocumentFormFooter').html(data.data.footerButton);
            }
            else {
                $('#modalDocumentFormTitle').html('Detail Form');
                $('.actionHeaderContainer').html('');
                $('#modalDocumentFormBody').html(`<div class="row min-vh-75">
                                                    <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                        <div class="center-container">
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
        }
        else if(form == 'ORDER_FORM_TAB' || form == 'COMPARISON_FORM_TAB') {
            if (data.status === 200) {
                // $('#orderFormContent').html(data.data.form);
                // $('#modalDocumentFormBody').removeClass('pt-0 pb-5 p-0 overflow-hidden').addClass('p-0');
                $('#modalDocumentFormBody').removeClass('pt-0 pb-5 p-0 overflow-y-hidden').addClass('p-0 overflow-y-hidden');
                if(form == 'ORDER_FORM_TAB'){
                    $('#orderFormContent').html(`<object class="w-100 h-100" id="subfile_frame" data="/framePdf?token=${data.data.tokenForm}" type="text/html"><param name="allowfullscreen" value="true"></object>`);
                }
                else {
                    $('#comparisonFormContent').html(`<object class="w-100 h-100" id="subfile_frame" data="/framePdf?token=${data.data.tokenForm}" type="text/html"><param name="allowfullscreen" value="true"></object>`);
                }
            }
            else {
                let formMessage = 'Failed to get data';
                if (response.status === 404) {
                    formMessage = 'Form not available';
                }

                if(form == 'ORDER_FORM_TAB'){
                    $('#orderFormContent').html(`<div class="row min-vh-75">
                        <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                            <div class="center-container">
                                <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                <div class="d-block fs-7 mt-2">${formMessage}</div>
                            </div>
                        </div>
                    </div>`);
                }
                else {
                    $('#comparisonFormContent').html(`<div class="row min-vh-75">
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
    }
    catch (error) {
        if(form == 'ORDER_FORM' || form == 'COMPARISON_FORM' || form == 'APPLICATION_FORM' || form == 'APPLICATION_FORM_COMPLETED') {
            $('#modalDocumentFormTitle').html('Detail Form');
            $('.actionHeaderContainer').html('');
            $('#modalDocumentFormBody').html(`<div class="row min-vh-75">
                                                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                    <div class="center-container">
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
        else if(form == 'ORDER_FORM_TAB') {
            $('#orderFormContent').html(`<div class="row min-vh-75">
                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                    <div class="center-container">
                        <i class="fa-light fa-file-circle-xmark fs-1"></i>
                        <div class="d-block fs-7 mt-2">Failed to get data</div>
                    </div>
                </div>
            </div>`);
        }
        else if(form == 'COMPARISON_FORM_TAB') {
            $('#comparisonFormContent').html(`<div class="row min-vh-75">
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

async function newForm(params) {
    const token = params['token'];
    const form = params['form'];
    const source = params['source'];
    const type = params['type'];
    Snackbar.close();
    asideHide();

    $('#modalDocumentFormTitle').html(`<div class="skeleton mb-0" style="width: 250px; height: 24px"></div>`);
    $('.actionHeaderContainer').html('');
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
        const response = await fetch(`/proc_pur/newForm?type=${type}&form=${form}&token=${token}}`, {
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
            $('.actionHeaderContainer').html('');
            // $('#modalDocumentFormBody').html( data.data.form);
            if(data.data.form) {
                // FORM DOCUMENT
                $('#modalDocumentFormBody').removeClass('pt-0 pb-5 p-0 overflow-x-hidden overflow-y-hidden').addClass('pt-0 pb-5 overflow-x-hidden overflow-y-auto ');
                $('#modalDocumentFormBody').html(data.data.form);
                $('#modalDocumentFormBody').animate({ scrollTop:0 }, 'fast');

                if(type == 'OLD_COMPARISON_REVISE_APPLICATION') {
                    $('#estimateApplication').trigger('change');
                }
                else if(type == 'NEW' && form == 'APPLICATION_FORM') {
                    try {
                        const response = await fetch(`/doc_approval/getDocumentNumber?documentType=2&type=DRAFT_SEQ&companyId=${$('#companyIdEncode').val()}`, {
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
                            // $('#skeletonDocumentNumber').replaceWith(`<input type="text" class="form-control" id="seqNumber" name="seqNumber" value="${data.seqNumber}${data.formatNumber}">`);
                            $('#skeletonDocumentNumber').replaceWith(`<input type="text" class="form-control" id="seqNumber" name="seqNumber" value="${data.documentNumber}" readonly>`);
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
                else if(form == 'COMPARISON_FORM') {
                    // $('#discountType').select2().trigger('change');
                    $('.compareVendor1').trigger('input');
                    $('.compareVendor2').trigger('input');
                    $('.compareVendor3').trigger('input');
                    $('.table-responsive').animate({ scrollLeft:0 }, 'fast');

                    const table = document.getElementById('tableComparison');
                    const rows = table.querySelectorAll('tr');
                    const columnOffsets = [];
                    let cumulativeWidth = 0;

                    rows[0].querySelectorAll('th').forEach((cell, index) => {
                        const width = cell.getBoundingClientRect().width; // Lebar aktual kolom
                        columnOffsets[index] = cumulativeWidth;
                        cumulativeWidth += width; // Tambahkan lebar kolom
                    });

                    // Terapkan sticky pada header (thead)
                    rows[0].querySelectorAll('th').forEach((cell, index) => {
                        if (columnOffsets[index] !== undefined && index < 4) {
                            cell.style.position = 'sticky';
                            cell.style.top = '0px'; // Tetap di atas
                            cell.style.left = `${columnOffsets[index]}px`;
                            cell.style.zIndex = '999';
                        }
                    });

                    // Terapkan sticky pada tbody
                    const bodyRows = table.querySelectorAll('tbody tr');
                    bodyRows.forEach(row => {
                        row.querySelectorAll('td').forEach((cell, index) => {
                            if (index < 5) { // Kolom 1-4 saja
                                cell.style.position = 'sticky';
                                if (index == 4) {
                                    const prevCell = row.querySelectorAll('td')[index - 1];
                                    const prevCellWidth = prevCell.getBoundingClientRect().width;
                                    cell.style.left = `${columnOffsets[3] + prevCellWidth}px`;

                                }
                                else {
                                    cell.style.left = `${columnOffsets[index]}px`;
                                }

                                cell.style.zIndex = '999';
                            }
                        });
                    });
                }
            }
            else {
                // RENDER DOCUMENT
                $('#modalDocumentFormBody').removeClass('pt-0 pb-5 p-0 overflow-y-hidden').addClass('p-0 overflow-y-hidden');
                $('#modalDocumentFormBody').html(`<object class="w-100 h-100" id="subfile_frame" data="/framePdf?token=${data.data.tokenForm}" type="text/html"><param name="allowfullscreen" value="true"></object>`);
            }

            $('#modalDocumentFormFooter').html(data.data.footerButton);
            $('.autosize').autosize().trigger('change');
        }
        else if (data.status === 404) {
            $('#modalDocumentFormTitle').html('Detail Form');
            $('.actionHeaderContainer').html('');
            if(data.data.form) {
                $('#modalDocumentFormBody').html( data.data.form);
            }
            else {
                $('#modalDocumentFormBody').html(`<object class="w-100 h-100" id="subfile_frame" data="/framePdf?token=${data.data.tokenForm}" type="text/html"><param name="allowfullscreen" value="true"></object>`);
            }
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
            $('.actionHeaderContainer').html('');
            $('#modalDocumentFormBody').html(`<div class="row min-vh-75">
                                                <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                    <div class="center-container">
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
        $('.actionHeaderContainer').html('');
        $('#modalDocumentFormBody').html(`<div class="row min-vh-75">
                                            <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                <div class="center-container">
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

$(document).off('click', '.nextNewFormConfirmation').on('click', '.nextNewFormConfirmation', async function (event) {
    let $this = $(this);
    $('.modal').modal('hide');
    $('#modalMessageTitle').html('');
    if($(this).attr('data-form') == 'APPLICATION_FORM') {
        $('#modalMessageBody').html(`<div class="swal2-icon swal2-warning swal2-icon-show d-flex mt-0 mb-3"><div class="swal2-icon-content">!</div></div>
                                    <h6 class="swal2-title text-center" id="swal2-title" style="display: block;">Create Application Form without Comparison Form?</h6>`);
    }

    $('#modalMessageFooter').html(`<div class="container-fluid p-0">
                                        <div class="row justify-content-center w-100 mx-0">
                                            <div class="col-6 d-flex justify-content-center mb-2 px-0">
                                                <button type="button" class="btn btn-default w-100 me-2 modalBackBtn" data-modal-id="modalDocumentForm" title="Back">
                                                    <i class="fa-solid fa-arrow-left"></i> Back
                                                </button>
                                            </div>
                                            <div class="col-6 d-flex justify-content-center mb-2 px-0">
                                                <button type="button" class="btn btn-secondary w-100 nextNewForm" data-type="${$(this).attr('data-type')}" data-form="${$(this).attr('data-form')}" data-token="${$(this).attr('data-token')}" title="Next">
                                                    Next <i class="fa-solid fa-arrow-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>`);

    $('#modalMessageDialog').removeClass('top-20').addClass('top-20');
    $('#modalMessageDialog').removeClass('modal-dialog-scrollable');
    $('#modalMessage').modal('show');
});

$(document).off('click', '.nextNewForm').on('click', '.nextNewForm', async function (event) {
    $('.rowItem').remove();
    $('.countVat').remove();
    $('.rowItemNumber').remove();
    $('.rowItemString').remove();
    $('.subTotalPrice').remove();
    $('#modalDocumentFormTitle').html(`<div class="skeleton mb-0" style="width: 250px; height: 24px"></div>`);
    $('.actionHeaderContainer').html('');
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

    const params = {
        'source': $(this).attr('data-source'),
        'form': $(this).attr('data-form'),
        'token': $(this).attr('data-token'),
        'type': $(this).attr('data-type')
    };

    newForm(params);

    // try {
    //     const response = await fetch(`/proc_pur/newForm?type=${$(this).attr('data-type')}&form=${$(this).attr('data-form')}&token=${$(this).attr('data-token')}`, {
    //         method: 'GET',
    //         headers: {
    //             'Content-Type': 'application/json',
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    //             'Accept': 'application/json',
    //             'Referer': window.location.href
    //         }
    //     });

    //     const data = await response.json();
    //     if (data.status === 200) {
    //         // let selectedFiles = [];
    //         // let selectedFilesProperties = [];
    //         // const maxFileSize = 5 * 1024 * 1024;
    //         $('#modalDocumentFormTitle').html(data.data.title);
    //         $('#modalDocumentFormBody').html( data.data.form);
    //         $('#modalDocumentFormFooter').html(data.data.footerButton);
    //         $('.autosize').autosize().trigger('change');
    //     }
    //     else if (data.status === 404) {
    //         $('#modalDocumentFormTitle').html('Detail Form');
    //         $('#modalDocumentFormBody').html( data.data.form);
    //         $('#modalDocumentFormFooter').html(data.data.footerButton);
    //     }
    //     else if (response.status === 401) {
    //         $('.card-footer-fixed').addClass('border-top-0');
    //         let countdown = 5;
    //         Snackbar.show({
    //             pos: 'bottom-center',
    //             duration: '6000',
    //             text: `<i class="fa-solid fa-circle-exclamation fa-lg fa-fw text-warning"></i> Session expired, please reload your browser or it will automatically reload in <span id="snackbar-countdown">${countdown}</span> seconds.`
    //         });

    //         let countdownInterval = setInterval(() => {
    //             countdown--;
    //             document.getElementById('snackbar-countdown').textContent = countdown;
    //             if (countdown < 0) {
    //                 clearInterval(countdownInterval);
    //                 window.location.href = data.redirect_uri;
    //             }
    //         }, 1000);
    //     }
    //     else {
    //         $('#modalDocumentFormTitle').html('Form');
    //         $('#modalDocumentFormBody').html(`<div class="row min-vh-75">
    //                                             <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
    //                                                 <div class="center-container">
    //                                                     <i class="fa-light fa-file-circle-xmark fs-1"></i>
    //                                                     <div class="d-block fs-7 mt-2">Failed to get data</div>
    //                                                 </div>
    //                                             </div>
    //                                         </div>`);
    //         $('#modalDocumentFormFooter').html(`<div class="container-fluid p-0">
    //                                                 <div class="row justify-content-center w-100 mx-0">
    //                                                     <div class="col-sm-12 col-md-5 d-flex justify-content-center justify-content-md-end mb-2 px-0">
    //                                                         <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
    //                                                             <i class="fas fa-xmark"></i> Close
    //                                                         </button>
    //                                                     </div>
    //                                                 </div>
    //                                             </div>`);
    //     }
    // } catch (error) {
    //     $('#modalDocumentFormTitle').html('Form');
    //     $('#modalDocumentFormBody').html(`<div class="row min-vh-75">
    //                                         <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
    //                                             <div class="center-container">
    //                                                 <i class="fa-light fa-file-circle-xmark fs-1"></i>
    //                                                 <div class="d-block fs-7 mt-2">Failed to get data</div>
    //                                             </div>
    //                                         </div>
    //                                     </div>`);
    //     $('#modalDocumentFormFooter').html(`<div class="container-fluid p-0">
    //                                             <div class="row justify-content-center w-100 mx-0">
    //                                                 <div class="col-sm-12 col-md-5 d-flex justify-content-center justify-content-md-end mb-2 px-0">
    //                                                     <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
    //                                                         <i class="fas fa-xmark"></i> Close
    //                                                     </button>
    //                                                 </div>
    //                                             </div>
    //                                         </div>`);
    // } finally {

    // }
});

$(document).off('click', '.viewFormButton').on('click', '.viewFormButton', async function (event) {
    const params = {
        'token': $(this).attr('data-token'),
        'form': $(this).attr('data-form'),
    };
    viewForm(params);
});

async function actionTable(token, action) {
    if(action == 'VIEW') {
        const params = {
            'token': token,
            'source': 'WEB',
            'form': 'APPLICATION_FORM'
        };
        viewForm(params);
    }
    else if(action == 'RECEIVED') {
        $('#modalMessageTitle').html(`Received Purchase Order`);
        $('#modalMessageBody').html(`<form>
                                        <div class="mb-3">
                                        <label for="newDocumentType" class="form-label">Received Date :</label>
                                        <div class="form-group date-picker" id="receivedDateOrder" data-coreui-date="" data-coreui-name="receivedDateOrder"></div>
                                        </div>
                                    </form>`);

        $('#modalMessageFooter').html(`<div class="d-flex flex-column flex-md-row justify-content-between w-100 gap-2 mb-4">
                        <button type="button" class="btn btn-default flex-grow-1 mb-2" data-coreui-dismiss="modal" title="Close">
                            <i class="fas fa-xmark"></i> Close
                        </button>
                        <button type="button" class="btn btn-info flex-grow-1 mb-2 updatePoFormHistory" data-type="${action}" data-token="${token}">
                            Update <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>`);

        // $('#modalMessageDialog').removeClass('top-20');
        $('#modalMessageDialog').removeClass('modal-dialog-scrollable');
        $('#modalMessage').modal('show');

        var optionsSubmittedDate = {
            locale: 'en-US',
            inputDateFormat: date => dayjs(date).locale('en').format('DD-MM-YYYY'),
            inputDateParse: date => dayjs(date, 'DD-MM-YYYY', 'id').toDate(),
            maxDate: dayjs(new Date()),
            showAdjacementDays: false,
        }

        new coreui.DatePicker(document.getElementById(`receivedDateOrder`), optionsSubmittedDate);
    }
    else if(action == 'INVOICED') {
        $('#modalMessageTitle').html(`Invoiced PO Form`);
        $('#modalMessageBody').html(`<div class="swal2-icon swal2-warning swal2-icon-show d-flex mt-0 mb-3"><div class="swal2-icon-content">!</div></div>
                                    <h6 class="swal2-title text-center" id="swal2-title" style="display: block;">Update Form?</h6>`);

        $('#modalMessageFooter').html(`<div class="d-flex flex-column flex-md-row justify-content-between w-100 gap-2 mb-4">
                        <button type="button" class="btn btn-default flex-grow-1 mb-2" data-coreui-dismiss="modal" title="Close">
                            <i class="fas fa-xmark"></i> Close
                        </button>
                        <button type="button" class="btn btn-info flex-grow-1 mb-2 updatePoFormHistory" data-type="${action}" data-token="${token}">
                            Update <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>`);

        // $('#modalMessageDialog').removeClass('top-20');
        $('#modalMessageDialog').removeClass('modal-dialog-scrollable');
        $('#modalMessage').modal('show');

        // var optionsSubmittedDate = {
        //     locale: 'en-US',
        //     inputDateFormat: date => dayjs(date).locale('en').format('DD-MM-YYYY'),
        //     inputDateParse: date => dayjs(date, 'DD-MM-YYYY', 'id').toDate(),
        //     maxDate: dayjs(new Date()),
        //     showAdjacementDays: false,
        // }

        // new coreui.DatePicker(document.getElementById(`receivedDateOrder`), optionsSubmittedDate);
    }
}

$(document).off('click', '.updatePoFormHistory').on('click', '.updatePoFormHistory', function (event) {
    let $this = $(this);
    Snackbar.close();
    clearValidation();

    let formData = new FormData();

    if($this.attr('data-type') == 'RECEIVED') {
        let requiredDate;
        let requiredDateInstance = coreui.DatePicker.getInstance(document.getElementById(`receivedDateOrder`));
        requiredDate = requiredDateInstance._calendarDate;
        let requiredDateOrderAside = requiredDate.toLocaleDateString('en-GB', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).split('/').reverse().join('-');
        formData.append('receivedDateOrder', requiredDateOrderAside);
    }

    formData.append('tokenForm', $this.attr('data-token'));
    formData.append('type', $this.attr('data-type'));
    formData.append('_method', 'PUT');

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
            $('.asideBtn').prop('disabled', true);
            $this.html('<i class="fas fa-spinner fa-spin"></i> Please wait');
            const response = await fetch('/proc_pur/updatePoFormHistory', {
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
                Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result.message}` });
                $('.modal').modal('hide');
                history_table();
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

$(document).on('mousedown', '.view-file', function (e) {
    if (e.detail > 1) {
        e.preventDefault();
    }
});

$(document).off('click', '.view-file').on('click', '.view-file', async function (event) {
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
        const response = await fetch(`/render?token=${$this.closest('.form-group').find('div.file-details').attr('data-token')}`, {
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

$(document).off('click', '.view-file-card').on('click', '.view-file-card', async function (event) {
    let $this = $(this);
    event.stopImmediatePropagation();
    const selectedText = window.getSelection().toString().trim();
    if (selectedText || $this.hasClass('active')) {
        event.preventDefault();
        return false;
    }

    $('.view-file-card').removeClass('active');
    $this.addClass('active');
    $('.fullscreen-title').html($this.data('filename'));
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

$(document).off('click', '.modifyItem').on('click', '.modifyItem', async function (e) {
    let $this = $(this);
    Snackbar.close();
    clearValidation();
    let dataType = $(this).attr('data-type');
    let dataForm = $(this).attr('data-form');
    let optionItem = '<option></option>';
    $('.rowItem').each(function() {
        let rowItem = this;
        optionItem += `<option value="${rowItem.getAttribute('data-id')}">${rowItem.getAttribute('data-id')} - ${rowItem.getAttribute('data-label')}</option>`;
    });

    let type = '', selectItemLabel = '', selectItem = '', currency = '', content = '';
    let title = '';
    if(dataForm == 'COMPARISON') {
        title = 'Comparison Item';
    }
    else if(dataForm == 'APPLICATION') {
        title = 'Item';
    }

    if(dataType == 'REMOVE') {
        type = 'Remove';
        selectItemLabel = 'Item to Remove';

        selectItem = `<div class="form-group mb-3">
                            <label for="selectItemRow" class="form-label">${selectItemLabel}<span class="required"></span> :</label>
                            <input type="hidden" class="form-control itemMandatory" id="itemRowNo" name="itemRowNo">
                            <select class="select2" name="selectItemRow" id="selectItemRow" data-type="${dataType}">
                                ${optionItem}
                            </select>
                        </div>`;
    }
    else if(dataType == 'MERGE') {
        type = 'Merge';
        selectItemLabel = 'Item to Merge';

        selectItem = `<div class="form-group mb-3">
                            <label for="selectItemRow" class="form-label">${selectItemLabel}<span class="required"></span> :</label>
                            <input type="hidden" class="form-control itemMandatory" id="itemRowNo" name="itemRowNo">
                            <select class="select2" name="selectItemRow" id="selectItemRow" data-type="${dataType}">
                                ${optionItem}
                            </select>
                        </div>`;
    }
    else {
        let unitElement = '';
        if(dataForm == 'COMPARISON') {
            unitElement = `<div class="form-group mb-3">
                                <label for="itemRowUnit" class="form-label">Unit :</label>
                                <div class="skeleton" id="itemRowUnit"></div>
                            </div>`;
        }

        if(dataType == 'EDIT') {
            type = 'Edit';
            selectItemLabel = 'Item to Edit';
            itemNoLabel = 'Item No.';

            selectItem = `<div class="form-group mb-3">
                            <label for="selectItemRow" class="form-label">${selectItemLabel}<span class="required"></span> :</label>
                            <select class="select2" name="selectItemRow" id="selectItemRow" data-form="${dataForm}" data-type="${dataType}">
                                ${optionItem}
                            </select>
                        </div>`;
        }
        else {
            type = 'Add';
            itemNoLabel = 'No.';
            currency = $this.attr('data-currency');

        }

        content = `<div class="form-group mb-3">
                        <label for="itemRowNo" class="form-label">${itemNoLabel}<span class="required"></span> :</label>
                        <input type="text" class="form-control numberValue itemMandatory" id="itemRowNo" name="itemRowNo">
                    </div>
                    <div class="form-group mb-3">
                        <label for="itemRowCriteria" class="form-label">Criteria <span class="required"></span> :</label>
                        <textarea class="form-control singleLine autosize itemMandatory" spellcheck="false" maxlength="255" id="itemRowCriteria" name="itemRowCriteria"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="itemRowQty" class="form-label">Quantity :</label>
                        <input type="text" class="form-control numberValue" id="itemRowQty" name="itemRowQty" maxlength="10">
                    </div>
                    ${unitElement}`;
    }

    $('.aside-title').html(`${type} ${title}`);
    $('.aside-content').html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
                                ${selectItem}
                                ${content}
                            </form>`);

    $('.overlay-aside').addClass('show').trigger('shown');
    $('#globalAside').addClass('show').trigger('shown');
    $('body').addClass('overflow-hidden');
    $('#asideDetailForm').scrollTop(0);
    $('.autosize').autosize({ append: "\n" });
    $('#beforeAfter').select2({dropdownParent: $('#globalAside'), minimumResultsForSearch: Infinity, allowClear: false, placeholder: '-- Select --'});
    $('#selectItemRow').select2({dropdownParent: $('#globalAside'), minimumResultsForSearch: Infinity, allowClear: false, placeholder: '-- Select --'});
    $('.asideFooterBtn').html(`<div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                    <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
                                </div>
                                <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                    <button type="button" class="btn btn-secondary w-100 w-md-auto modifyRowBtn" data-form="${dataForm}" data-type="${dataType}" data-currency="${currency}"></i> ${type} Item</button>
                                </div>`);

    if(dataType != 'REMOVE' && dataForm == 'COMPARISON') {
        let optionUnit = await getUnit();
        $('#itemRowUnit').replaceWith(`<select class="select2" name="itemRowUnit" id="itemRowUnit" data-type=""><option></option></select>`);
        $('#itemRowUnit').select2({
            dropdownParent: $('#globalAside'),
            allowClear: false,
            placeholder: '-- Select --',
            data: optionUnit
        });
    }
});

$(document).off('change', '#selectItemRow').on('change', '#selectItemRow', function (e) {
    let $this = $(this);
    let newNo = '';
    Snackbar.close();
    clearValidation();

    $('#itemRowNo').val('');
    if($this.attr('data-type') == 'EDIT') {
        let parent = $(`#rowItem${$this.val()}`);
        $('#itemRowNo').val(parent.find('input.colNo').val());
        $('#itemRowCriteria').val(parent.find('input.colCriteria').val()).trigger('change');
        $('#itemRowQty').val(parent.find('input.colQty').val());

        if($this.attr('data-form') == 'COMPARISON') {
            $('#itemRowUnit').val(parent.find('input.colUnit').val()).trigger('change');
        }
    }
    else if($this.attr('data-type') == 'REMOVE') {
        $('#itemRowNo').val($this.val());
    }
});

$(document).off('change', '.itemMandatory').on('change', '.itemMandatory', function (e) {
    Snackbar.close();
    clearValidation();
});

$(document).off('click', '.modifyRowBtn').on('click', '.modifyRowBtn', async function (event) {
    Snackbar.close();
    clearValidation();
    event.preventDefault();
    let $this = $(this);
    let isValid = true;
    let dataType = $this.attr('data-type');
    let dataForm = $this.attr('data-form');

    if(dataType == 'ADD') {
        if ($('#itemRowNo').val().trim() === '') {
            $('#itemRowNo').addClass('is-invalid');
            isValid = false;
        }

        if ($('#itemRowCriteria').val().trim() === '') {
            $('#itemRowCriteria').addClass('is-invalid');
            isValid = false;
        }
    }
    else {
        if(dataType == 'REMOVE') {
            let itemLength = $('.rowItemNumber').length;
            if(itemLength <= 1) {
                Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : At least 1 item to order` });
                return false;
            }
        }

        // if ($('#selectItemRow').val() === '') {
        //     $('#selectItemRow').parent().find('span.select2-container').find('span.select2-selection').addClass('is-invalid');
        //     isValid = false;
        // }
    }

    if (isValid) {
        let type = '';
        if(dataType == 'ADD') {
            type = 'Added';
            let itemClass = '', startNewNo = '';
            if (!isNaN(Number($('#itemRowNo').val()))) {
                itemClass = 'rowItemNumber';
                startNewNo = Number($('#itemRowNo').val()) + 1;
            }
            else {
                itemClass = 'rowItemString';
                startNewNo = String.fromCharCode($('#itemRowNo').val().charCodeAt(0) + 1);
            }

            let currency = $this.attr('data-currency');
            let itemHtml = '';
            if(dataForm == 'COMPARISON') {
                itemHtml = `<tr id="rowItem${$('#itemRowNo').val()}" class="rowItem ${itemClass}" data-id="${$('#itemRowNo').val()}" data-label="${$('#itemRowCriteria').val()}">
                                <td class="text-center td-form">
                                    <input type="hidden" class="colNo" name="colNo[]" value="${$('#itemRowNo').val()}">
                                    <div class="d-flex justify-content-between">
                                        <div class="position-relative end-0" style="top: -6px">
                                            <button class="btn btn-sm btn-transparent modifyRowBtn" type="button" title="Remove Item" data-type="REMOVE" data-form="COMPARISON" data-row="${$('#itemRowNo').val()}">
                                                <i class="fa-regular fa-trash-can-xmark fa-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center td-form">
                                    <div class="d-flex justify-content-between">
                                        <span class="colNoLabel mx-1">${$('#itemRowNo').val()}</span>
                                    </div>
                                </td>
                                <td class="td-form">
                                    <input type="hidden" class="colCriteriaId" name="colCriteriaId[]" value="">
                                    <input type="hidden" class="colCriteria" name="colCriteria[]" value="${$('#itemRowCriteria').val()}"><span class="colCriteria">${$('#itemRowCriteria').val()}</span>
                                </td>
                                <td class="text-center td-form" data-item="${$('#itemRowNo').val()}">
                                    <input type="hidden" class="colQty" name="colQty[]" value="${$('#itemRowQty').val()}"><span class="colQty">${$('#itemRowQty').val()}</span>
                                </td>
                                <td class="text-center td-form">
                                    <input type="hidden" class="colUnit" name="colUnit[]" value="${$('#itemRowUnit').val()}"><span class="colUnit">${$('#itemRowUnit').val()}</span>
                                </td>
                                <td class="td-form" style="background-color: #f3fff1;">
                                    <div class="form-group d-flex align-items-center position-relative">
                                        <input type="hidden" class="unitCurrency" name="currency1[]" value="${currency}">
                                        <input type="text" class="form-control text-end d-flex currencyValue countAmount compareVendor1" name="unitPrice1[]" data-vendor="1" data-item="${$('#itemRowNo').val()}" spellcheck="false" autocomplete="off" style="padding-right: 33px;">
                                        <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">${currency}</span>
                                    </div>
                                </td>
                                <td class="td-form" style="background-color: #f3fff1;">
                                    <div class="form-group d-flex align-items-center position-relative">
                                        <input type="text" class="form-control text-end d-flex no-input currencyValue subTotalPrice subTotalPrice1 compareVendor1 subTotalVendor1" id="subTotalVendor1_${$('#itemRowNo').val()}" data-vendor="1" name="subTotalPrice[]" data-item="${$('#itemRowNo').val()}" spellcheck="false" autocomplete="off" style="padding-right: 33px;" readonly="" tabindex="-1">
                                        <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">${currency}</span>
                                    </div>
                                </td>
                                <td class="td-form" style="background-color: #fffbe3;">
                                    <div class="form-group d-flex align-items-center position-relative">
                                        <input type="hidden" class="unitCurrency" name="currency2[]" value="${currency}">
                                        <input type="text" class="form-control text-end d-flex currencyValue countAmount compareVendor2" name="unitPrice2[]" data-vendor="2" data-item="${$('#itemRowNo').val()}" spellcheck="false" autocomplete="off" style="padding-right: 33px;">
                                        <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">${currency}</span>
                                    </div>
                                </td>
                                <td class="td-form" style="background-color: #fffbe3;">
                                    <div class="form-group d-flex align-items-center position-relative">
                                        <input type="text" class="form-control text-end d-flex no-input currencyValue subTotalPrice subTotalPrice2 compareVendor2 subTotalVendor2" id="subTotalVendor2_${$('#itemRowNo').val()}" data-vendor="2" name="subTotalPrice[]" data-item="${$('#itemRowNo').val()}" spellcheck="false" autocomplete="off" style="padding-right: 33px;" readonly="" tabindex="-1">
                                        <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">${currency}</span>
                                    </div>
                                </td>
                                <td class="td-form" style="background-color: #fff0f0">
                                    <div class="form-group d-flex align-items-center position-relative">
                                        <input type="hidden" class="unitCurrency" name="currency3[]" value="${currency}">
                                        <input type="text" class="form-control text-end d-flex currencyValue countAmount compareVendor3" name="unitPrice3[]" data-vendor="3" data-item="${$('#itemRowNo').val()}" spellcheck="false" autocomplete="off" style="padding-right: 33px;">
                                        <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">${currency}</span>
                                    </div>
                                </td>
                                <td class="td-form" style="background-color: #fff0f0">
                                    <div class="form-group d-flex align-items-center position-relative">
                                        <input type="text" class="form-control text-end d-flex no-input currencyValue subTotalPrice subTotalPrice3 compareVendor3 subTotalVendor3" id="subTotalVendor3_${$('#itemRowNo').val()}" data-vendor="3" name="subTotalPrice[]" data-item="${$('#itemRowNo').val()}" spellcheck="false" autocomplete="off" style="padding-right: 33px;" readonly="" tabindex="-1">
                                        <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">${currency}</span>
                                    </div>
                                </td>
                            </tr>`;
            }
            else if(dataForm == 'APPLICATION') {
                itemHtml = `<tr id="rowItem${$('#itemRowNo').val()}" class="rowItem ${itemClass}" data-id="${$('#itemRowNo').val()}" data-label="${$('#itemRowCriteria').val()}">
                                <td class="text-center">
                                    <input type="hidden" class="colNo" name="colNo[]" value="${$('#itemRowNo').val()}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="colNoLabel mx-1">${$('#itemRowNo').val()}</span>
                                        <div class="position-relative end-0">
                                            <button class="btn btn-sm btn-transparent modifyRowBtn" type="button" title="Remove Item" data-type="REMOVE" data-form="APPLICATION" data-row="${$('#itemRowNo').val()}">
                                                <i class="fa-regular fa-trash-can-xmark fa-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input type="hidden" class="colCriteriaId" name="colCriteriaId[]" value="">
                                    <input type="hidden" class="colCriteria" name="colCriteria[]" value="${$('#itemRowCriteria').val()}"><span class="colCriteria">${$('#itemRowCriteria').val()}</span>
                                </td>
                                <td class="text-center" data-item="${$('#itemRowNo').val()}">
                                    <input type="hidden" class="colQty" name="colQty[]" value="${$('#itemRowQty').val()}"><span class="colQty">${$('#itemRowQty').val()}</span>
                                </td>
                                <td>
                                    <div class="form-group d-flex align-items-center position-relative">
                                        <input type="hidden" class="unitCurrency" name="currency1[]" value="${currency}">
                                        <input type="text" class="form-control text-end d-flex currencyValue countAmount compareVendor1" name="unitPrice1[]" data-vendor="1" data-item="${$('#itemRowNo').val()}" spellcheck="false" autocomplete="off" style="padding-right: 33px;">
                                        <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">${currency}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group d-flex align-items-center position-relative">
                                        <input type="text" class="form-control text-end d-flex no-input currencyValue subTotalPrice subTotalPrice1 compareVendor1 subTotalVendor1" id="subTotalVendor1_${$('#itemRowNo').val()}" data-vendor="1" name="subTotalPrice[]" data-item="${$('#itemRowNo').val()}" spellcheck="false" autocomplete="off" style="padding-right: 33px;" readonly="" tabindex="-1">
                                        <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-2">${currency}</span>
                                    </div>
                                </td>
                            </tr>`;
            }

            if($(`#rowItem${$('#itemRowNo').val()}`).length == 0) {
                reorderItem({'type': 'ADD', 'row': null, 'form': dataForm});
                let itemLength = $(`.${itemClass}`).length;
                $(`#rowItem${itemLength}`).after(itemHtml);
            }
            else {
                reorderItem({'type': 'ADD', 'row': null, 'form': dataForm});
                $(`#rowItem${startNewNo}`).before(itemHtml);
            }

            const table = document.getElementById('tableComparison');
            const rows = table.querySelectorAll('tr');
            const columnOffsets = [];
            let cumulativeWidth = 0;

            rows[0].querySelectorAll('th').forEach((cell, index) => {
                const width = cell.getBoundingClientRect().width; // Lebar aktual kolom
                columnOffsets[index] = cumulativeWidth;
                cumulativeWidth += width; // Tambahkan lebar kolom
            });

            // Terapkan sticky pada header (thead)
            rows[0].querySelectorAll('th').forEach((cell, index) => {
                if (columnOffsets[index] !== undefined && index < 4) {
                    cell.style.position = 'sticky';
                    cell.style.top = '0px'; // Tetap di atas
                    cell.style.left = `${columnOffsets[index]}px`;
                    cell.style.zIndex = '999';
                }
            });

            // Terapkan sticky pada tbody
            const bodyRows = table.querySelectorAll('tbody tr');
            bodyRows.forEach(row => {
                row.querySelectorAll('td').forEach((cell, index) => {
                    if (index < 5) { // Kolom 1-4 saja
                        cell.style.position = 'sticky';
                        if (index == 4) {
                            const prevCell = row.querySelectorAll('td')[index - 1];
                            const prevCellWidth = prevCell.getBoundingClientRect().width;
                            cell.style.left = `${columnOffsets[3] + prevCellWidth}px`;

                        }
                        else {
                            cell.style.left = `${columnOffsets[index]}px`;
                        }

                        cell.style.zIndex = '999';
                    }
                });
            });
        }
        else if(dataType == 'EDIT') {
            type = 'Changed';
            $(`#rowItem${$('#selectItemRow').val()}`).attr('data-label', $('#itemRowCriteria').val());
            $(`#colCriteria${$('#selectItemRow').val()}`).val($('#itemRowCriteria').val());
            $(`#colCriteria${$('#selectItemRow').val()}`).next('span').html($('#itemRowCriteria').val());

            if(dataForm == 'COMPARISON') {
                $(`#colQty${$('#selectItemRow').val()}`).val($('#itemRowQty').val());
                $(`#colQty${$('#selectItemRow').val()}`).next('span').html($('#itemRowQty').val());

                $(`#colUnit${$('#selectItemRow').val()}`).val($('#itemRowUnit').val());
                $(`#colUnit${$('#selectItemRow').val()}`).next('span').html($('#itemRowUnit').val());
            }
        }
        else if(dataType == 'REMOVE') {
            type = 'Removed';
            $(`#rowItem${$this.attr('data-row')}`).remove();
            reorderItem({'type': 'REMOVE', 'row': $this.attr('data-row'), 'form': dataForm});
            for(let i = 1; i <= 3; i++) {
                let params = {
                    'dataVendor': i
                };
                countTotal(params);
            }

            $('.countVat').each(function() {
                let $this = $(this);
                let dataVendor = $this.attr('data-vendor');
                let params = {
                    'dataVendor': dataVendor,
                    'vatRate': $this.val(),
                };
                countVat(params);
            });
        }

        asideHide();
        Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> Item ${type}` });
    }
    else {
        Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : Please fill the required form` });
    }
});

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
                    {
                        id: 'ALL',
                        text: '-- ALL DEPARTMENT --',
                        selected: true
                    }
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

async function getLocation(params) {
    if(params == null) {
        return;
    }

    let locationId = params['locationId'];
    try {
        const response = await fetch(`/doc_approval/getLocation?companyId=${params['companyId']}&departmentId=${params['departmentId']}`, {
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
            let options = '';
            if(locationId === 'ALL') {
                options = [
                    {
                        id: 'ALL',
                        text: '-- ALL LOCATION --',
                        selected: true
                    }
                ].concat(
                    result.data.map(row => ({
                        id: row.locationId,
                        text: row.locationName,
                    }))
                );
            }
            else {
                options = result.data.map(row => ({
                                                id: row.locationId,
                                                text: row.locationName,
                                        }));
            }
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
                                                id: row.unit_name,
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

async function getPurchaseTypeComponents() {
    try {
        const response = await fetch('/proc_pur/getPurchaseTypeComponents', {
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
                                                id: row.component_id,
                                                text: row.component_name
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

async function getPurchaseTypeGroup() {
    try {
        const response = await fetch('/proc_pur/getPurchaseTypeGroup', {
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
                                                id: row.purchase_type_group_id,
                                                text: row.purchase_type_group_name
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

// async function getLocation() {
//     try {
//         const response = await fetch('/doc_approval/getLocation', {
//             method: 'GET',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
//                 'Accept': 'application/json',
//                 'Referer': window.location.href
//             },
//         });

//         const data = await response.json();
//         if (response.status === 200) {
//             const options = data.map(row => ({
//                                                 id: row.location_id,
//                                                 text: row.location_name
//                                             }));
//             return options;
//         }
//         else if (response.status === 401) { // Unauthorized
//             let countdown = 5;
//             Snackbar.show({
//                 pos: 'bottom-center',
//                 duration: '6000',
//                 text: `<i class="fa-solid fa-circle-exclamation fa-lg fa-fw text-warning"></i> Session expired, please refresh this page or it will automatically refresh in <span id="snackbar-countdown">${countdown}</span> seconds.`
//             });

//             let countdownInterval = setInterval(() => {
//                 countdown--;
//                 document.getElementById('snackbar-countdown').textContent = countdown;
//                 if (countdown < 0) {
//                     clearInterval(countdownInterval);
//                     window.location.href = data.redirect_uri;
//                 }
//             }, 1000);
//         }
//         else {
//             console.error('HTTP Error:', response.status);
//         }
//     } catch (error) {
//         return;
//     }
// }

function reorderItem(params) {
    let itemRowNo;
    if(params['type'] == 'REMOVE') {
        itemRowNo = params['row'];
    }
    else {
        itemRowNo = $('#itemRowNo').val();
    }

    if (!isNaN(Number(itemRowNo))) {
        let x = (params == 'ADD' || params == 'EDIT') ? Number(itemRowNo) + 1 : Number(itemRowNo);

        $('.rowItemNumber').each(function() {
            let dataId = this.getAttribute('data-id');
            if(dataId >= itemRowNo) {
                this.id = 'rowItem'+x;
                this.setAttribute('data-id', x);
                $(this).find('button.modifyRowBtn').attr('data-row', x);
                $(this).find('input.colNo').val(x);
                $(this).find('span.colNoLabel').html(x);
                x++;
            }
        });
    }
    else {
        let x =  (params == 'ADD' || params == 'EDIT') ? String.fromCharCode(itemRowNo.charCodeAt(0) + 1) : String.fromCharCode(itemRowNo.charCodeAt(0));
        $('.rowItemString').each(function() {
            let dataId = this.getAttribute('data-id');
            if(dataId >= itemRowNo) {
                this.id = 'rowItem'+x;
                this.setAttribute('data-id', x);
                $(this).find('button.modifyRowBtn').attr('data-row', x);
                $(this).find('input.colNo').val(x);
                $(this).find('span.colNoLabel').html(x);
                x = String.fromCharCode(x.charCodeAt(0) + 1);
            }
        });
    }

    if(params['type'] == 'REMOVE' && params['form'] == 'COMPARISON') {
        const table = document.getElementById('tableComparison');
        const rows = table.querySelectorAll('tr');
        const columnOffsets = [];
        let cumulativeWidth = 0;

        rows[0].querySelectorAll('th').forEach((cell, index) => {
            const width = cell.getBoundingClientRect().width; // Lebar aktual kolom
            columnOffsets[index] = cumulativeWidth;
            cumulativeWidth += width; // Tambahkan lebar kolom
        });

        // Terapkan sticky pada header (thead)
        rows[0].querySelectorAll('th').forEach((cell, index) => {
            if (columnOffsets[index] !== undefined && index < 4) {
                cell.style.position = 'sticky';
                cell.style.top = '0px'; // Tetap di atas
                cell.style.left = `${columnOffsets[index]}px`;
                cell.style.zIndex = '4';
            }
        });

        // Terapkan sticky pada tbody
        const bodyRows = table.querySelectorAll('tbody tr');
        bodyRows.forEach(row => {
            row.querySelectorAll('td').forEach((cell, index) => {
                if (index < 5) { // Kolom 1-4 saja
                    cell.style.position = 'sticky';
                    if (index == 4) {
                        const prevCell = row.querySelectorAll('td')[index - 1];
                        const prevCellWidth = prevCell.getBoundingClientRect().width;
                        cell.style.left = `${columnOffsets[3] + prevCellWidth}px`;

                    }
                    else {
                        cell.style.left = `${columnOffsets[index]}px`;
                    }

                    cell.style.zIndex = '3';
                }
            });
        });
    }
}

$(document).off('click', '#vendorRefBtn').on('click', '#vendorRefBtn', function (e) {
    asideHide();
    $('#asideLgTitle').html('Vendor Reference');
    $('.overlay-aside').addClass('show').trigger('shown');
    $('#asideLg').addClass('show').trigger('shown');
    $('body').addClass('overflow-hidden');
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
        if($(this).attr('data-reference') !== '' && $(this).attr('data-reference') !== undefined) {
            $('#globalAside').append(`<div class="aside-sidebar">
                                        <input type="hidden" id="reopenAside" value="VENDOR_REFERENCE">
                                        <input type="hidden" id="dataVendor" value="${$(this).attr('data-vendor')}">
                                        <input type="hidden" id="reloadTable" value="">
                                        <input type="hidden" id="sortBy" value="VENDOR_NAME">
                                        <div><span class="aside-sidebar-item">${$(this).attr('data-reference-title')}</span></div>
                                    </div>`);
        }

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
        $('#globalAside').find('.aside-content').html(`<div class="alert alert-info mb-3 w-100 p-2" id="alertFormAction" role="alert">
                                    1. Nama <span class="fw-semibold">“PT”</span> tidak perlu dituliskan dengan tanda titik (.)<br>
                                    2. Isi <span class="fw-semibold">“Address”</span> dengan dua baris (tekan Enter untuk baris baru)
                                </div>
                                <form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
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
    else if(type == 'REFERENCE' || type == 'REFERENCE_APPLICATION') {
        $('#globalAside').find('.aside-title').html('Vendor Reference');
        $('#globalAside').find('.aside-content').html(`<div class="py-1 h-100 no-scroll">
                                    <div class="row mx-0 mt-2">
                                        <div class="col-2 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-info w-100 w-md-auto me-2 masterVendor" data-type="NEW" data-reference="TRUE" data-reference-title="Vendor Reference" data-vendor="${$(this).attr('data-vendor')}"><i class="fa-regular fa-plus fa-fw"></i> New Vendor</button>
                                        </div>
                                        <div class="col-2 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-default w-100 w-md-auto masterVendor" data-type="EDIT" data-id="" data-reference="TRUE" data-reference-title="Vendor Reference" data-vendor="${$(this).attr('data-vendor')}"></i><i class="fa-regular fa-pen-to-square fa-fw"></i> Edit Selected Vendor</button>
                                        </div>
                                    </div>
                                    <div class="row" id="asideContentFixed">
                                        <div class="col-md-8">
                                            <div class="table-container table-responsive h-100" style="overflow-y: auto;">
                                                <form role="form" class="form-horizontal d-flex justify-content-end align-items-center gap-3 mb-2 formSearchTable" enctype="multipart/form-data">
                                                    <div class="d-flex align-items-center position-relative">
                                                        <input type="hidden" name="sortBy" value="${$(this).attr('data-sortby')}">
                                                        <input type="hidden" name="menu" value="COMPARISON">
                                                    </div>
                                                    <div class="d-flex align-items-center position-relative">
                                                        <div class="form-group d-flex align-items-center position-relative w-100">
                                                            <input type="text" class="form-control d-flex searchInputTable" name="search" data-iscleared="true" spellcheck="false" autocomplete="off" placeholder="Search..." style="padding-right: 66px;">
                                                            <div class="position-absolute end-0 top-0 h-100 d-flex align-items-center px-1">
                                                                <button class="btn btn-sm btn-transparent searchButtonClearTable d-none" type="button" title="Clear Search">
                                                                    <i class="fa-solid fa-times fa-lg"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-transparent searchButtonTable" type="button" title="Search">
                                                                    <i class="fa-solid fa-search fa-lg"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center position-relative ms-1">
                                                            <button class="btn btn-md btn-transparent filterButtonTable" type="button" data-reference="TRUE" data-reference-title="Vendor Reference" title="Filter"><i class="fa-regular fa-filter-list fa-lg"></i></button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <table id="vendorReferenceTable" class="table table-striped table-hover" data-vendor="${$(this).attr('data-vendor')}" style="width:100%">
                                                    <thead class="table-secondary">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Vendor Account</th>
                                                            <th>Vendor Name</th>
                                                            <th>Group</th>
                                                            <th>Currency</th>
                                                            <th>Component</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                    <tfoot></tfoot>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="row-multi-col mb-3 bg-white pb-3">
                                                <div class="row-multi-col-header">
                                                    <span style="display: inline">Vendor Details</span>
                                                </div>
                                                <div class="px-2 pt-2" id="vendorDetailsContainer" style="overflow-y: auto; scrollbar-gutter: stable both-edges; box-sizing: content-box;">
                                                    <div class="card-body d-flex justify-content-center align-items-center">
                                                        <div class="center-container">
                                                            <div class="d-block mt-5">Select record to view details</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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

        // let optionPurchaseType = await getPurchaseTypeGroup();
        // $('#vendorPurchaseTypeGroup').replaceWith(`<select class="select2 w-100" name="vendorPurchaseTypeGroup[]" id="vendorPurchaseTypeGroup"></select>`);
        // $('#vendorPurchaseTypeGroup').select2({
        //     dropdownParent: $('#globalAside'),
        //     allowClear: false,
        //     placeholder: '-- Select --',
        //     data: optionPurchaseType,
        //     multiple: true,
        //     closeOnSelect: false,
        //     allowClear: false,
        //     dropdownAdapter: dropdownAdapter,
        //     minimumResultsForSearch: 0,
        // })
        // .on('select2:opening select2:closing', function (event) {
        //     var searchfield = $(this).parent().find('.select2-search__field');
        //     searchfield.prop('disabled', true);
        // });

        // if(vendorPurchaseType.length > 0) {
        //     vendorPurchaseType.forEach(item => {
        //         const option = new Option(item.purchaseTypeName	, item.purchaseTypeGroupId, true, true);
        //         $('#vendorPurchaseTypeGroup').append(option);
        //     });

        //     $('#vendorPurchaseTypeGroup').trigger('change');
        // }

        // $('#vendorSite').replaceWith(`<select class="select2 w-100" name="vendorSite[]" id="vendorSite"></select>`);
        // $('#vendorSite').select2({
        //     dropdownParent: $('#globalAside'),
        //     placeholder: '-- Select --',
        //     multiple: true,
        //     closeOnSelect: false,
        //     minimumResultsForSearch: 0,
        //     ajax: {
        //         url: '/doc_approval/getLocation?departmentId=ALL&locationId=23',
        //         dataType: 'json',
        //         delay: 250,
        //         processResults: function (data) {
        //             function mergeWithSelected(ajaxData) {
        //                 const ajaxResults = ajaxData.map(row => ({
        //                     id: row.location_id,
        //                     text: row.location_name
        //                 }));

        //                 const selectedIds = selectedSite.map(item => item.id);
        //                 const uniqueAjaxResults = ajaxResults.filter(item => !selectedIds.includes(item.id));
        //                 const mergedAndSorted = [...uniqueAjaxResults, ...selectedSite].sort((a, b) =>
        //                     a.text.localeCompare(b.text)
        //                 );

        //                 return mergedAndSorted;
        //             }

        //             const mergedResults = mergeWithSelected(data);
        //             return {
        //                 results: mergedResults
        //             };
        //         },
        //         cache: true
        //     }
        // }).on('select2:open', function () {
        //     setTimeout(function() {
        //         const dropdown = $('.select2-dropdown');

        //         if ($('#customSearch').length === 0) {
        //             const searchBox = `<span class="select2-search select2-search--dropdown">
        //                 <input class="select2-search__field" id="customSearch" type="search"
        //                 tabindex="0" autocorrect="off" autocapitalize="none" spellcheck="false"
        //                 role="searchbox" aria-autocomplete="list" autocomplete="off"
        //                 aria-label="Search">
        //             </span>`;

        //             dropdown.prepend(searchBox);

        //             $('#customSearch').on('input', function () {
        //                 const term = $(this).val().toLowerCase();
        //                 $('.select2-results__option').each(function () {
        //                     const text = $(this).text().toLowerCase();
        //                     $(this).toggle(text.includes(term));
        //                 });
        //             });
        //         }
        //     }, 100);
        // });

        // if(selectedSite.length > 0) {
        //     selectedSite.forEach(item => {
        //         const optionExists = $(`#vendorSite option[value='${item.id}']`).length > 0;
        //         if (!optionExists) {
        //             const option = new Option(item.text, item.id, true, true);
        //             $('#vendorSite').append(option);
        //         }
        //     });

        //     $('#vendorSite').trigger('change');
        // }
    }
    else if(type == 'REFERENCE' || type == 'REFERENCE_APPLICATION'){
        $('#globalAside').addClass('show aside-xxl').trigger('shown');
        $('#globalAside').find('.asideFooterBtn').html(`<div class="col-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                    <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside asideBtn" title="Close">Close</button>
                                </div>
                                <div class="col-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                    <button type="button" class="btn btn-secondary w-100 w-md-auto asideBtn saveVendor" id="selectVendorReference" data-type="${type}" data-vendor="${$(this).attr('data-vendor')}"></i> Select Vendor</button>
                                </div>`);

        $('body').addClass('overflow-hidden');
        $('#asideDetailForm').scrollTop(0);
        $('.autosize').autosize({ append: "\n" });

        let $container = $('#asideDetailForm');
        let containerHeight = $container.height();
        let headerHeight = 190;
        let footerFixedHeight = 60;
        let scrollHeight = containerHeight - headerHeight;
        var tableId = 'vendorReferenceTable';
        $('#vendorDetailsContainer').css('height', (containerHeight - 150) + 'px');
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
                url: 'purchasingVendorReference',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (d) {
                    let $table = $('#vendorReferenceTable');
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
                $('.rowVendorReference').prop('checked', false).trigger('change');
            },
            columns: [
                {
                    data: 'checkbox',
                    name: 'checkbox',
                    orderable: false,
                    searchable: false
                },
                {data: 'vendor_account', type: 'string', searchable: false, orderable: false, width: '20%'},
                {data: 'vendor_name', type: 'string', searchable: false, orderable: false, width: '35%'},
                {data: 'group', type: 'string', searchable: false, orderable: false, width: '10%'},
                {data: 'currency', type: 'string', searchable: false, orderable: false, width: '10%'},
                {data: 'component_names', type: 'string', searchable: false, orderable: false, width: '20%'},
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

        $(`.masterVendor[data-type="REFERENCE"][data-vendor="${$(this).attr('data-vendor')}"]`).attr('data-sortby', 'VENDOR_NAME');
    }
});

function showSmartPageDropdown(table, $ellipsisParent) {
    var info = table.page.info();
    var currentPage = info.page;
    var totalPages = info.pages;
    var visiblePages = $('.paginate_button:not(.ellipsis):not(.next):not(.previous)').length;

    // Cari range halaman yang tersembunyi
    var firstHidden = visiblePages + 1;
    var lastHidden = totalPages - 1;

    // Siapkan opsi halaman
    var hiddenPages = [];
    for (var i = firstHidden; i < lastHidden; i++) {
        hiddenPages.push(i);
    }

    // Batasi maksimal 5 opsi dengan pola 2 awal + 3 akhir
    var limitedPages = [];
    if (hiddenPages.length > 5) {
        limitedPages = hiddenPages.slice(0, 2).concat(hiddenPages.slice(-3));
    } else {
        limitedPages = hiddenPages;
    }

    // Buat dropdown menu
    var $menu = $('<div class="page-dropup">')
        .css({
            position: 'absolute',
            bottom: '100%',
            left: '0',
            background: 'white',
            border: '1px solid #ddd',
            'z-index': '1000',
            padding: '5px',
            'box-shadow': '0 -2px 5px rgba(0,0,0,0.1)'
        });

    // Tambahkan opsi
    limitedPages.forEach(function(pageIdx) {
        $menu.append(
            $('<a href="#" class="page-link" aria-controls="vendorReferenceTable" data-dt-idx="' + (pageIdx + 1) + '">')
                .text(pageIdx + 1)
                .css({
                    display: 'block',
                    padding: '3px 10px',
                    'white-space': 'nowrap'
                })
                .on('click', function(e) {
                    e.preventDefault();
                    table.page(pageIdx).draw();
                })
        );
    });

    // Tampilkan menu
    $ellipsisParent
        .css('position', 'relative')
        .append($menu);

    // Tutup saat klik di luar
    setTimeout(function() {
        $(document).on('click.pageMenu', function(e) {
            if (!$(e.target).closest('.page-dropup, .ellipsis').length) {
                $menu.remove();
                $(document).off('click.pageMenu');
            }
        });
    }, 10);
}

$(document).off('click contextmenu', '#vendorReferenceTable tbody tr td:not(:first-child)').on('click contextmenu', '#vendorReferenceTable tbody tr td:not(:first-child)', function(e) {
    if (e.which === 3) return; // Ignore right clicks
    const row = $(this).closest('tr');
    const checkbox = row.find('.rowVendorReference');

    if(row.hasClass('active-row')) {
        checkbox.prop('checked', false).trigger('change');
    }
    else {
        checkbox.prop('checked', true).trigger('change');
    }
});

$(document).off('dblclick', '#vendorReferenceTable tbody tr').on('dblclick', '#vendorReferenceTable tbody tr', async function (e) {
    return false;
})

// $(document).off('dblclick', '#vendorReferenceTable tbody tr td:not(:first-child)').on('dblclick', '#vendorReferenceTable tbody tr td:not(:first-child)', async function (e) {
//     const $this = $(this);
//     e.stopPropagation();
//     const row = $this.closest('tr');
//     const checkbox = row.find('.rowVendorReference');
//     const vendorCol = $this.closest('table').attr('data-vendor');
//     $(`#selectVendor${vendorCol}`).val(null).empty().trigger('change');
//     const selectedVendorId = checkbox.val();
//     const selectedVendorName = checkbox.attr('data-name');
//     let newOption = new Option(selectedVendorName, selectedVendorId, false, false);
//     $(`#selectVendor${vendorCol}`).append(newOption).trigger('change');
//     asideHide();
// });

$(document).on('change', '.rowVendorReference', async function(e) {
    e.stopImmediatePropagation();
    $('#vendorReferenceTable tbody tr').removeClass('active-row');
    $('.masterVendor[data-type="EDIT"]').attr('data-id', '');
    const row = $(this).closest('tr');
    if ($(this).prop('checked') === true) {
        $('.rowVendorReference').prop('checked', false);
        $(this).prop('checked', true);
        row.addClass('active-row');

        $('.masterVendor[data-type="EDIT"]').attr('data-id', $(this).val());
        $('#vendorDetailsContainer').html(`

                                            <div class="card card-floating-title mb-4">
                                                <span class="card-title">Vendor Address</span>
                                                <div class="card-body py-2" id="vendorAdrressCardBody">
                                                    <div class="skeleton skeleton-26 my-2"></div>
                                                </div>
                                            </div>
                                            <div class="card card-floating-title mb-4">
                                                <span class="card-title">Vendor PIC</span>
                                                <div class="card-body py-2" id="vendorPicCardBody">
                                                    <div class="skeleton skeleton-26 my-2"></div>
                                                </div>
                                            </div>`);

        const params = {
            'id': $(this).val(),
            'type': 'ROW_DETAILS',
        };
        const getDetails = await getVendorDetails(params);

        const vendorAddress = getDetails.vendorAddress;
        const vendorAdrressCardBody = $('#vendorDetailsContainer').find('#vendorAdrressCardBody');
        if (vendorAddress.length > 0) {
            vendorAdrressCardBody.html('');
            vendorAddress.forEach((item, index) => {
                const div = document.createElement('div');
                div.classList.add('py-2');
                if (index > 0) {
                    div.classList.add('border-top');
                    // if (index === vendorAddress.length - 1) {
                    //     div.classList.add('border-bottom');
                    // }
                }

                div.innerHTML = `<div class="text-muted white-space-pre">${item.addressName}</div>`;
                vendorAdrressCardBody.append(div);
            });
        }
        else {
            vendorAdrressCardBody.html(`<div class="text-muted my-2">No Address data</div>`);
        }

        const vendorPic = getDetails.vendorPic;
        const vendorPicCardBody = $('#vendorDetailsContainer').find('#vendorPicCardBody');
        if (vendorPic.length > 0) {
            vendorPicCardBody.html('');
            vendorPic.forEach((item, index) => {
                const div = document.createElement('div');
                div.classList.add('py-2');
                if (index > 0) {
                    div.classList.add('border-top');
                }

                // let pic = item.picName;
                // if (item.picEmail != null && item.picEmail != '') {
                //     pic += `<span class="d-block">${item.picEmail}</span>`;
                // }

                // if (item.picPhone != null && item.picPhone != '') {
                //     pic += `<span class="d-block">${item.picPhone}</span>`;
                // }

                item.picEmail = (item.picEmail != null && item.picEmail != '') ? item.picEmail : '-';
                item.picPhone = (item.picPhone != null && item.picPhone != '') ? item.picPhone : '-';
                let picName = `<div class="data-row">
                                    <span class="data-label">Name</span>
                                    <span class="data-separator">:</span>
                                    <span class="data-value">${item.picName}</span>
                                </div>`;

                let picEmail = `<div class="data-row">
                                    <span class="data-label">Email</span>
                                    <span class="data-separator">:</span>
                                    <span class="data-value">${item.picEmail}</span>
                                </div>`;

                let picPhone = `<div class="data-row">
                                    <span class="data-label">Phone</span>
                                    <span class="data-separator">:</span>
                                    <span class="data-value">${item.picPhone}</span>
                                </div>`;

                div.innerHTML = `<div class="text-muted data-details">${picName}${picEmail}${picPhone}</div>`;
                vendorPicCardBody.append(div);
            });
        }
        else {
            vendorPicCardBody.html(`<div class="text-muted my-2">No PIC data</div>`);
        }
    }
    else {
        $('.rowVendorReference').prop('checked', false);
        $('#vendorDetailsContainer').html(`<div class="card-body d-flex justify-content-center align-items-center">
                                                <div class="center-container">
                                                    <div class="d-block mt-5">Select record to view details</div>
                                                </div>
                                            </div>`);
    }
});

$(document).off('change', '#vendorCompanyNameEdit').on('change', '#vendorCompanyNameEdit', async function (e) {
    // $('.editVendor').val('').trigger('change');
    $('#detailVendor').html(`<div class="form-group mb-3">
                                <label for="vendorAddress" class="form-label required">Vendor Address :</label>
                                <div class="skeleton textarea"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="vendorPicName" class="form-label required">Vendor PIC Name :</label>
                                <div class="skeleton"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="vendorPicEmail" class="form-label">Vendor PIC Email :</label>
                                <div class="skeleton"></div>
                            </div>`);

    const params = {
        'search': $(this).val(),
        'dataType': 'DETAIL_FORM',
    };

    if($(this).val()){
        try {
            const getVendorData = await getVendor(params);
            if (getVendorData && getVendorData.length > 0) {
                $('#detailVendor').html(`<input type="hidden" name="tokenForm" value="${getVendorData[0].tokenForm}">
                                        <div class="form-group mb-3">
                                            <label for="vendorAddress" class="form-label required">Vendor Address :</label>
                                            <textarea class="form-control autosize editVendor" spellcheck="false" maxlength="255" id="vendorAddress" name="vendorAddress">${getVendorData[0].vendorAddress}</textarea>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="vendorCity" class="form-label">Vendor City :</label>
                                            <input type="text" class="form-control editVendor" id="vendorCity" name="vendorCity">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="vendorPicName" class="form-label">Vendor PIC Name :</label>
                                            <input type="text" class="form-control editVendor" id="vendorPicName" name="vendorPicName" value="${getVendorData[0].vendorPicName}">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="vendorPicEmail" class="form-label">Vendor PIC Email :</label>
                                            <input type="email" class="form-control editVendor" id="vendorPicEmail" name="vendorPicEmail" value="${getVendorData[0].vendorPicEmail}">
                                        </div>`);
                $('.autosize').autosize({ append: "\n" });
            }
        } catch (error) {
            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Error : ${error}` });
        }
    }
    else {
        $('#detailVendor').html(`<div class="form-group mb-3">
                                    <label for="vendorAddress" class="form-label required">Vendor Address :</label>
                                    <textarea class="form-control autosize" spellcheck="false" maxlength="255" id="vendorAddress" name="vendorAddress"></textarea>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="vendorCity" class="form-label">Vendor City :</label>
                                    <input type="text" class="form-control" id="vendorCity" name="vendorCity">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="vendorPicName" class="form-label">Vendor PIC Name :</label>
                                    <input type="text" class="form-control" id="vendorPicName" name="vendorPicName">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="vendorPicEmail" class="form-label">Vendor PIC Email :</label>
                                    <input type="email" class="form-control" id="vendorPicEmail" name="vendorPicEmail">
                                </div>`);
    }
});

// async function getApprovalMatrix(params) {
//     const documentTypeId = params['documentTypeId'];
//     try {
//         const response = await fetch(`/proc_pur/getApprovalMatrix?documentTypeId=${documentTypeId}`, {
//             method: 'GET',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
//                 'Accept': 'application/json',
//                 'Referer': window.location.href
//             },
//         });

//         const data = await response.json();
//         if (response.status === 200) {
//             let options = [
//                 { id: '', text: '' },
//                 { id: 'ALL', text: '-- ALL SIGNER --' }
//             ].concat(
//                 data
//                 .filter(row => row.employeeId)
//                 .map(row => ({
//                     id: row.employeeId,
//                     text: row.employeeName
//                 }))
//             );
//             return options;
//         }
//         else if (response.status === 401) {
//             let countdown = 5;
//             Snackbar.show({
//                 pos: 'bottom-center',
//                 duration: '6000',
//                 text: `<i class="fa-solid fa-circle-exclamation fa-lg fa-fw text-warning"></i> Session expired, please refresh this page or it will automatically refresh in <span id="snackbar-countdown">${countdown}</span> seconds.`
//             });

//             let countdownInterval = setInterval(() => {
//                 countdown--;
//                 document.getElementById('snackbar-countdown').textContent = countdown;
//                 if (countdown < 0) {
//                     clearInterval(countdownInterval);
//                     window.location.href = data.redirect_uri;
//                 }
//             }, 1000);
//         }
//         else {
//             console.error('HTTP Error:', response.status);
//         }
//     }
//     catch (error) {
//         return;
//     }
// }

async function getPoRuleMatrixControl(params) {
    // Batalkan request sebelumnya jika ada
    if (poRuleMatrixController) {
        poRuleMatrixController.abort();
        poRuleMatrixController = null;
    }

    // Batalkan timeout sebelumnya jika ada
    if (poRuleMatrixTimeout) {
        clearTimeout(poRuleMatrixTimeout);
        poRuleMatrixTimeout = null;
    }

    return new Promise((resolve, reject) => {
        poRuleMatrixTimeout = setTimeout(async () => {
            poRuleMatrixController = new AbortController();
            try {
                const data = await getPoRuleMatrix(params, poRuleMatrixController.signal);
                resolve(data);
            }
            catch (error) {
                reject(error);
            }
            finally {
                poRuleMatrixController = null;
                poRuleMatrixTimeout = null;
            }
        }, 1000);
    });
}

async function getPoRuleMatrix(params, signal = null) {
    const queryString = new URLSearchParams(params).toString();
    try {
        const response = await fetch(`/proc_pur/getRuleFlow?${queryString}`, {
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
            return result.data;
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
            console.error('HTTP Error:', response.status);
        }
    }
    catch (error) {
        if (error.name === 'AbortError') {
            console.log('Permintaan dibatalkan');
        }
        else {
            console.error('Fetch error:', error);
        }
    }
}

async function generatePoRuleMatrix(ruleMatrix) {
    $('#ruleIdInfo').html('');
    $('#ruleId').val('');
    let optionPoReviewer = [], optionPoConfirmer1 = [], optionPoReviewAdmin = [], optionPoConfirmer2 = [], optionAcknowledger = [], optionApprover = [];
    if(Object.keys(ruleMatrix).length > 0) {
        const roleMap = {
            REVIEWER: optionPoReviewer,
            CONFIRMER_1: optionPoConfirmer1,
            REVIEW_ADMIN: optionPoReviewAdmin,
            CONFIRMER_2: optionPoConfirmer2,
            ACKNOWLEDGER: optionAcknowledger,
            APPROVER: optionApprover
        };

        Object.keys(ruleMatrix.signer).forEach(role => {
            if (roleMap.hasOwnProperty(role)) {
                let selected = (ruleMatrix.signer[role].length == 1) ? 'selected' : '';
                ruleMatrix.signer[role].forEach(item => {
                    if (item.employeeId) {
                        roleMap[role].push({
                            id: item.employeeId,
                            text: item.employeeName,
                            selected: selected,
                        });
                    }
                });
            }
        });

        $('#ruleIdInfo').html(`Flow Rule ID : ${ruleMatrix.ruleId}`);
        $('#ruleId').val(Number(ruleMatrix.ruleId));
    }

    if ($('#selectApprover').hasClass('select2-hidden-accessible')) {
        $('#selectApprover').val(null).empty().trigger('change');
        $('#selectApprover').select2('destroy');
    }
    if ($('#selectAcknowledge').hasClass('select2-hidden-accessible')) {
        $('#selectAcknowledge').val(null).empty().trigger('change');
        $('#selectAcknowledge').select2('destroy');
    }
    if ($('#selectConfirmer2').hasClass('select2-hidden-accessible')) {
        $('#selectConfirmer2').val(null).empty().trigger('change');
        $('#selectConfirmer2').select2('destroy');
    }
    if ($('#selectReviewAdmin').hasClass('select2-hidden-accessible')) {
        $('#selectReviewAdmin').val(null).empty().trigger('change');
        $('#selectReviewAdmin').select2('destroy');
    }
    if ($('#selectConfirmer1').hasClass('select2-hidden-accessible')) {
        $('#selectConfirmer1').val(null).empty().trigger('change');
        $('#selectConfirmer1').select2('destroy');
    }
    if ($('#selectReviewer').hasClass('select2-hidden-accessible')) {
        $('#selectReviewer').val(null).empty().trigger('change');
        $('#selectReviewer').select2('destroy');
    }

    $('#selectApprover').replaceWith(`<select class="select2 selectSigner" id="selectApprover" name="approver"><option></option></select>`);
    $('#selectAcknowledge').replaceWith(`<select class="select2 selectSigner" id="selectAcknowledge" name="acknowledger"><option></option></select>`);
    $('#selectConfirmer2').replaceWith(`<select class="select2 selectSigner" id="selectConfirmer2" name="confirmer_2"><option></option></select>`);
    $('#selectReviewAdmin').replaceWith(`<select class="select2 selectSigner" id="selectReviewAdmin" name="review_admin"><option></option></select>`);
    $('#selectConfirmer1').replaceWith(`<select class="select2 selectSigner" id="selectConfirmer1" name="confirmer_1"><option></option></select>`);
    $('#selectReviewer').replaceWith(`<select class="select2 selectSigner" id="selectReviewer" name="reviewer"><option></option></select>`);

    initPoSignerSelect2('#selectReviewer', optionPoReviewer);
    initPoSignerSelect2('#selectConfirmer1', optionPoConfirmer1);
    initPoSignerSelect2('#selectReviewAdmin', optionPoReviewAdmin);
    initPoSignerSelect2('#selectConfirmer2', optionPoConfirmer2);
    initPoSignerSelect2('#selectAcknowledge', optionAcknowledger);
    initPoSignerSelect2('#selectApprover', optionApprover);
}

async function initPoSignerSelect2(selector, data) {
    const $select = $(selector);
    const isEmpty = !data || data.length === 0;

    $select.select2({
        dropdownParent: $('#modalDocumentForm'),
        minimumResultsForSearch: Infinity,
        allowClear: false,
        placeholder: isEmpty ? '-- No Option --' : '-- Select --',
        data: data,
        disabled: isEmpty
    });

    if (isEmpty) {
        $($select).prop('disabled', false).addClass('select2-disabled-fake');
        $($select).on('select2:opening select2:open', function (e) {
          e.preventDefault(); // blok buka dropdown
        });
    }
}

async function getVendor(params) {
    const search = params['search'];
    const dataType = params['dataType'];
    try {
        const response = await fetch(`/proc_pur/getVendor?data=${dataType}&search=${search}`, {
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
            if(dataType == 'SELECT_HEADER') {
                const options = data.map(row => ({
                    value: row.value,
                    label: row.label,
                    description: row.vendorPicName != '' ? row.vendorPicName+'<br>'+row.vendorAddress : row.vendorAddress,
                }));

                return options;
            }
            else if(dataType == 'DETAIL_FORM') {
                return data;
            }
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

async function getVendorDetails(params) {
    try {
        const response = await fetch(`/proc_pur/getVendorDetails?id=${params['id']}&type=${params['type']}`, {
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
            return result.data;
        }
        else {
            console.error('HTTP Error:', response.status);
        }
    } catch (error) {
        return;
    }
}

$(document).off('click', '.vendorDetailButton').on('click', '.vendorDetailButton', function (e) {
    let $this = $(this);
    if($this.attr('data-type') == 'ADD_ADDRESS') {
        $this.closest('div#vendorAddressContent').append(`<div class="row row-multi-col mb-3 pt-2 pb-1 bg-white vendorAddressContainer">
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
        $('.autosize').autosize({ append: "\n" });
        $('#asideDetailForm').animate({
            scrollTop: $('#asideDetailForm')[0].scrollHeight
        }, 500);
    }
    else if($this.attr('data-type') == 'REMOVE_ADDRESS') {
        if($this.closest('div#vendorAddressContent').find('div.vendorAddressContainer').length > 1) {
            $this.closest('div.vendorAddressContainer').remove();
        }
        else {
            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : At least 1 Address must be filled` });
            return false;
        }
    }
    else if($this.attr('data-type') == 'ADD_PIC') {
        $this.closest('div#vendorPicContent').append(`<div class="row row-multi-col mb-3 pt-2 pb-1 bg-white vendorPicContainer">
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
        $('#asideDetailForm').animate({
        scrollTop: $('#asideDetailForm')[0].scrollHeight
        }, 500);
    }
    else if($this.attr('data-type') == 'REMOVE_PIC') {
        if($this.closest('div#vendorPicContent').find('div.vendorPicContainer').length > 1) {
            $this.closest('div.vendorPicContainer').remove();
        }
        else {
            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : At least 1 PIC must be filled` });
            return false;
        }
    }
});

$(document).off('click', '.saveVendor').on('click', '.saveVendor', function (e) {
    let $this = $(this);
    if($this.attr('data-type') == 'REFERENCE') {
        const selectedVendorId = $('#vendorReferenceTable tbody').find('tr.active-row').find('.rowVendorReference').val();
        if(!selectedVendorId) {
            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> No record selected` });
            return false;
        }

        const selectedVendorName = $('#vendorReferenceTable tbody').find('tr.active-row').find('.rowVendorReference').attr('data-name');
        const vendorCol = $this.attr('data-vendor');
        $(`#selectVendor${vendorCol}`).val(null).empty().trigger('change');

        let newOption = new Option(selectedVendorName, selectedVendorId, false, false);
        $(`#selectVendor${vendorCol}`).append(newOption).trigger('change');
        asideHide();
    }
    else if($this.attr('data-type') == 'REFERENCE_APPLICATION') {
        const selectedVendorId = $('#vendorReferenceTable tbody').find('tr.active-row').find('.rowVendorReference').val();
        if(!selectedVendorId) {
            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> No record selected` });
            return false;
        }

        const selectedVendorName = $('#vendorReferenceTable tbody').find('tr.active-row').find('.rowVendorReference').attr('data-name');
        $(`#vendorApplication`).val(null).empty().trigger('change');
        let newOption = new Option(selectedVendorName, selectedVendorId, false, false);
        $(`#vendorApplication`).append(newOption).trigger('change');
        asideHide();
    }
    else {
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

                        if($this.closest('aside.form-aside').find('div.aside-sidebar').length == 1) {
                            let asideSidebar = $this.closest('aside.form-aside').find('div.aside-sidebar');
                            asideSidebar.find('#reloadTable').val('vendorReferenceTable');
                            asideSidebar.find('#sortBy').val('LATEST_CREATED');
                        }
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
    }
});

$(document).off('input', '.countAmount').on('input', '.countAmount', function (e) {
    let $this = $(this);
    let dataVendor = $this.attr('data-vendor');
    let dataItem = $this.attr('data-item');
    $(`#subTotalVendor${dataVendor}_${dataItem}`).val('');

    if($this.val().trim() != '') {
        // COUNT SUB TOTAL
        let unitPrice = $this.val();
        unitPrice = currencyToNumber(unitPrice);
        if (isNaN(unitPrice)) {
            unitPrice = 0;
        }

        let qty = 0;
        if($this.attr('data-qty') === 'false') {
            qty = 1;
            $this.closest('tr').find('input.colQty').val(qty);
        }
        else {
            qty = $this.closest('tr').find('input.colQty').val() ?? 0;
        }

        qty =  parseFloat(qty);
        if (isNaN(qty)) {
            qty = 0;
        }

        let subTotalPriceOrder = qty * unitPrice;
        let fraction = (unitPrice.toString().indexOf('.') !== -1) ? 2 : 0;
        let formattedSubTotal = formatCurrency(subTotalPriceOrder, fraction);

        $(`#subTotalVendor${dataVendor}_${dataItem}`).val(formattedSubTotal);
    }
    else if($this.val() == '' && $this.attr('data-qty') === 'false') {
        $this.closest('tr').find('input.colQty').val('');
    }

    let params = {
        'dataVendor': dataVendor
    };
    countTotal(params);

    params = {
        'dataVendor': dataVendor,
        'dataItem': dataItem,
        'vatRate': $(`#vatRateVendor${dataVendor}`).val(),
    };
    countVat(params);
});

$(document).off('input change', '.discountVendor').on('input change', '.discountVendor', function (e) {
    let $this = $(this);
    let dataVendor = $this.attr('data-vendor');
    let dataItem = $this.attr('data-item');
    $(`#subTotalVendor${dataVendor}_${dataItem}`).val('');

    if($this.val().trim() != '') {
        let totalVendor = 0;
        $(`.subTotalPrice${dataVendor}`).each(function() {
            let subTotal = currencyToNumber($(this).val());
            if (isNaN(subTotal)) {
                subTotal = 0;
            }

            totalVendor += subTotal;
        });

        let unitPrice = $this.val();
        unitPrice = currencyToNumber(unitPrice);
        if (isNaN(unitPrice)) {
            unitPrice = 0;
        }

        let qty = 0;
        if($this.attr('data-qty') === 'false') {
            qty = 1;
            $this.closest('tr').find('input.colQty').val(qty);
        }
        else {
            qty = $this.closest('tr').find('input.colQty').val() ?? 0;
        }

        qty =  parseFloat(qty);
        if (isNaN(qty)) {
            qty = 0;
        }

        let subTotalPriceOrder = 0;
        if($(`#discountType${dataVendor}`).val() == '%') {
            subTotalPriceOrder = totalVendor * unitPrice / 100;
        }
        else {
            subTotalPriceOrder = qty * unitPrice;
        }

        let fraction = (unitPrice.toString().indexOf('.') !== -1) ? 2 : 0;
        let formattedSubTotal = formatCurrency(subTotalPriceOrder, fraction);

        $(`#subTotalVendor${dataVendor}_${dataItem}`).val(`(${formattedSubTotal})`);
    }
    else if($this.val() == '' && $this.attr('data-qty') === 'false') {
        $this.closest('tr').find('input.colQty').val('');
    }

    let params = {
        'dataVendor': dataVendor
    };
    countTotal(params);

    params = {
        'dataVendor': dataVendor,
        'dataItem': dataItem,
        'vatRate': $(`#vatRateVendor${dataVendor}`).val(),
    };
    countVat(params);
});

$(document).off('input change', '.pphVendor').on('input change', '.pphVendor', function (e) {
    let $this = $(this);
    let dataVendor = $this.attr('data-vendor');
    let dataItem = $this.attr('data-item');
    $(`#subTotalVendor${dataVendor}_${dataItem}`).val('');

    if($this.val().trim() != '') {
        let totalVendor = 0;
        $(`.subTotalPrice${dataVendor}`).each(function() {
            let subTotal = currencyToNumber($(this).val());
            if (isNaN(subTotal)) {
                subTotal = 0;
            }

            totalVendor += subTotal;
        });

        let unitPrice = $this.val();
        unitPrice = currencyToNumber(unitPrice);
        if (isNaN(unitPrice)) {
            unitPrice = 0;
        }

        let qty = 0;
        if($this.attr('data-qty') === 'false') {
            qty = 1;
            $this.closest('tr').find('input.colQty').val(qty);
        }
        else {
            qty = $this.closest('tr').find('input.colQty').val() ?? 0;
        }

        qty =  parseFloat(qty);
        if (isNaN(qty)) {
            qty = 0;
        }

        let subTotalPriceOrder = 0;
        if($(`#pphType${dataVendor}`).val() == '%') {
            subTotalPriceOrder = totalVendor * unitPrice / 100;
        }
        else {
            subTotalPriceOrder = qty * unitPrice;
        }

        let fraction = (unitPrice.toString().indexOf('.') !== -1) ? 2 : 0;
        let formattedSubTotal = formatCurrency(subTotalPriceOrder, fraction);

        $(`#subTotalVendor${dataVendor}_${dataItem}`).val(`(${formattedSubTotal})`);
    }
    else if($this.val() == '' && $this.attr('data-qty') === 'false') {
        $this.closest('tr').find('input.colQty').val('');
    }

    // let params = {
    //     'dataVendor': dataVendor
    // };
    // countTotal(params);

    // params = {
    //     'dataVendor': dataVendor,
    //     'dataItem': dataItem,
    //     'vatRate': $(`#vatRateVendor${dataVendor}`).val(),
    // };
    // countVat(params);

    params = {
        'dataVendor': dataVendor
    };
    countGrandTotal(params);
});

function countTotal(params){
    const dataVendor = params['dataVendor'];
    let total = 0;
    let isFraction = false;

    $(`#totalVendor${dataVendor}`).val('');
    $(`.subTotalVendor${dataVendor}`).each(function() {
        let subTotal = currencyToNumber($(this).val());
        if (isNaN(subTotal)) {
            subTotal = 0;
        }

        if($(this).attr('data-type') == 'DISCOUNT') {
            total -= subTotal;
        }
        else {
            total += subTotal;
        }

        if(subTotal.toString().indexOf('.') !== -1 && !isFraction) {
            isFraction = true;
        }
    });

    let fraction = isFraction ? 2 : 0;
    total = total > 0 ? total : 0;
    $(`#totalVendor${dataVendor}`).val(formatCurrency(total, fraction));
}

// $(document).off('input', '.countVat').on('input', '.countVat', function (e) {
//     let $this = $(this);
//     let dataVendor = $this.attr('data-vendor');
//     let params = {
//         'dataVendor': dataVendor,
//         'vatRate': $this.val(),
//     };
//     countVat(params);
// });
$(document).off('change', '.countVat').on('change', '.countVat', function (e) {
    let $this = $(this);
    let dataVendor = $this.attr('data-vendor');
    let params = {
        'dataVendor': dataVendor,
        'vatRate': $this.val(),
    };
    countVat(params);
});

function countVat(params) {
    const dataVendor = params['dataVendor'];
    let vatRate = params['vatRate'];

    $(`#vatVendor${dataVendor}`).val('');
    if(vatRate != '') {
        let deliveryFee = currencyToNumber($(`#deliveryFeeVendor${dataVendor}`).val());
        if (isNaN(deliveryFee)) {
            deliveryFee = 0;
        }

        let total = currencyToNumber($(`#totalVendor${dataVendor}`).val());
        if (isNaN(total)) {
            total = 0;
        }

        if(vatRate == '12x11/12') {
            vatRate = 12*11/12;
        }

        let vat = (total - deliveryFee) * (vatRate / 100);
        let fraction = (total.toString().indexOf('.') !== -1) ? 2 : 0;
        $(`#vatVendor${dataVendor}`).val(formatCurrency(vat, fraction));
    }

    params = {
        'dataVendor': dataVendor
    };
    countGrandTotal(params);
}

function countGrandTotal(params) {
    const dataVendor = params['dataVendor'];
    let total = currencyToNumber($(`#totalVendor${dataVendor}`).val());
    if (isNaN(total)) {
        total = 0;
    }

    let vat = currencyToNumber($(`#vatVendor${dataVendor}`).val());
    if (isNaN(vat)) {
        vat = 0;
    }

    let pph = currencyToNumber($(`#subTotalVendor${dataVendor}_e`).val());
    if (isNaN(pph)) {
        pph = 0;
    }

    let grandTotal = (total + vat) - pph;
    let fraction = (total.toString().indexOf('.') !== -1) ? 2 : 0;
    $(`#grandTotalVendor${dataVendor}`).val(formatCurrency(grandTotal, fraction)).trigger('change');
};

// $(document).off('change', '#discountType').on('change', '#discountType', function (e) {
//     let $this = $(this);
//     $('.discountVendor').val('').trigger('change');
//     $('.discountVendor').attr('maxlength', '');
//     if($this.val() == 'NOMINAL') {
//         $('.discountTypeText').html($('#currency').val());
//     }
//     else {
//         $('.discountTypeText').html('%');
//         $('.discountVendor').attr('maxlength', '2');
//     }
// });

// $(document).off('change', '#pphType').on('change', '#pphType', function (e) {
//     let $this = $(this);
//     $('.pphVendor').val('').trigger('change');
//     $('.pphVendor').attr('maxlength', '');
//     if($this.val() == 'NOMINAL') {
//         $('.pphTypeText').html($('#currency').val());
//     }
//     else {
//         $('.pphTypeText').html('%');
//         $('.pphVendor').attr('maxlength', '2');
//     }
// });

$(document).off('change', '.nominalPercentageSelect').on('change', '.nominalPercentageSelect', function (e) {
    let $this = $(this);
    $this.closest('div.form-group').find('input').val('');
    $this.closest('div.form-group').find('input').focus();
});

$(document).off('input', '.nominalPercentageInput').on('input', '.nominalPercentageInput', function (e) {
    let $this = $(this);
    let valueType = $this.closest('div.form-group').find('.nominalPercentageSelect').val();
    if (valueType === '%') {
        let cleanedValue = $this.val().replace(/[^0-9]/g, '');
        if (parseFloat(cleanedValue) > 100) {
            cleanedValue = '100';
        }

        $this.val(cleanedValue);
    }
    else {
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
    }
});

$(document).off('click', '.submitForm').on('click', '.submitForm', async function (e) {
    let $this = $(this);
    Snackbar.close();
    clearValidation();

    let formData;
    if($this.attr('data-form') == 'INSPECTION') {
        formData = new FormData();
        formData.append('documentType', '4');
        formData.append('tokenForm', $this.attr('data-token'));
        formData.append('actionType', $this.attr('data-type'));
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
        formData.append('actionType', $this.attr('data-type'));
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
        const response = await fetch('/proc_pur/saveForm', {
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
            if($this.attr('data-form') == 'COMPARISON_FORM') {
                const params = {
                    'source': 'WEB',
                    'form': 'APPLICATION_FORM',
                    'token': result.tokenForm,
                    'type':  result.applicationFlow,
                };

                newForm(params);
            }
            else {
                $('.modal').modal('hide');
            }

            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result['message']}` });

            currentPageOrder = 1;
            isLoadingOrder = false;
            hasMoreDataOrder = true;
            loadOrderColumn();

            currentPageAppOngoing = 1;
            isLoadingAppOngoing = false;
            hasMoreDataAppOngoing = true;
            loadAppColumn();

            // if($this.attr('data-form') == 'INSPECTION') {
            //     currentPageAppCompleted = 1;
            //     isLoadingAppCompleted = false;
            //     hasMoreDataAppCompleted = true;
            //     loadAppCompletedColumn();
            // }
            currentPageAppCompleted = 1;
            isLoadingAppCompleted = false;
            hasMoreDataAppCompleted = true;
            loadAppCompletedColumn();
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

$(document).off('change', '#fileUpload').on('change', '#fileUpload', function (event) {
    handleFiles(event.target.files);
});

$(document).off('click', '#selectFileBtn').on('click', '#selectFileBtn', function (event) {
    $('#fileUpload').trigger('click');
});

$(document).off('dragover', '#dropZone').on('dragover', '#dropZone', function (event) {
    event.preventDefault();
    $(this).addClass('dragover');
});

$(document).off('dragleave', '#dropZone').on('dragleave', '#dropZone', function (event) {
    event.preventDefault();
    $(this).removeClass('dragover');
});

$(document).off('drop', '#dropZone').on('drop', '#dropZone', function (event) {
    event.preventDefault();
    Snackbar.close();
    $(this).removeClass('dragover');
    const files = event.originalEvent.dataTransfer.files;
    handleFiles(files);
});

$(document).off('click', '#clearFiles').on('click', '#clearFiles', function (event) {
    var selectedFiles = [];
    var selectedFilesProperties = [];
    renderFileList();
});

$(document).off('click', '.deleteFileItem').on('click', '.deleteFileItem', async function (event) {
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
            asideButton = `<button type="button" class="btn btn-info w-100 w-md-auto asideButton" data-type="${$this.attr('data-type')}" data-token="" title="Print Selected Documents">Print</button>`;
        }
        else {
            asideTitle = 'Download';
            asideButton = `<button type="button" class="btn btn-danger w-100 w-md-auto asideButton" data-type="${$this.attr('data-type')}" data-token="" title="Download Selected">Download</button>`;
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
    else if($this.attr('data-type') == 'NEXT_REVISE_FORM') {
        clearValidation();
        selectedFiles = [];
        selectedFilesProperties = [];
        selectedFilesDocument = [];
        selectedFilesPropertiesDocument = [];
        costCenterOption = [];
        $('#fileList').empty();

        if($this.attr('data-revise-type') != '') {
            if($this.attr('data-revise-type') == 'CANCEL_APPLICATION_PO') {
                asideHide();
                $('.modal').modal('hide');
                $('#modalMessageTitle').html('Cancel Application & PO Form');
                $('#modalMessageBody').html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="formAction">
                                                <input type="hidden" id="tokenFormAction" name="tokenForm" autocomplete="false" value="${$(this).attr('data-token')}">
                                                <input type="hidden" name="type" value="PURCHASING">
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
                                                            <button type="button" class="btn btn-secondary w-100 confirmAction" data-type="${$this.attr('data-revise-type')}" data-form="" data-token="" title="">
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
            else {
                if($this.attr('data-revise-type') == 'REVISE_COMPARISON_REVISE_APPLICATION') {
                    $form = 'COMPARISON_FORM';
                }
                else {
                    $form = 'APPLICATION_FORM';
                }

                const params = {
                    'source': 'WEB',
                    'form': $form,
                    'token': $this.attr('data-token'),
                    'type': $this.attr('data-revise-type'),
                };

                newForm(params);
            }
        }
        else {
            Snackbar.show({pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : Select Revision Type`});
            return false;
        }
    }
});

$(document).on('click', '.actionBtn', async function (event) {
    let $this = $(this);
    if($this.attr('data-type') == 'SEND_BACK') {
        asideHide();
        $('.aside-title').html(`Send Back Form`);
        $('.aside-content').html(`<form role="form" class="form-horizontal mt-3" enctype="multipart/form-data" id="formAction">
                                        <input type="hidden" id="tokenFormAction" name="tokenForm" autocomplete="false" value="${$this.attr('data-token')}">
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

        $('.overlay-aside').addClass('show').trigger('shown');
        $('#globalAside').addClass('show').trigger('shown');
        $('body').addClass('overflow-hidden');
        $('#asideDetailForm').scrollTop(0);
        $('.autosize').autosize({ append: "\n" });

        $('.asideFooterBtn').html(`<div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside asideBtn" title="Close">Close</button>
                                    </div>
                                    <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-secondary w-100 w-md-auto asideBtn confirmAction" data-type="${$this.attr('data-type')}" data-form="" data-token="" title="">Send Back Form</button>
                                    </div>`);

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
            }asideForm
        } catch (error) {
            return;
        }
    }
    else if($this.attr('data-type') == 'CANCEL' || $this.attr('data-type') == 'CANCEL_TO_ARCHIVE') {
        $('.modal').modal('hide');
        $('#modalMessageTitle').html('Cancel Form');
        let type = ($this.attr('data-type') == 'CANCEL_TO_ARCHIVE') ? '<input type="hidden" name="type" value="PURCHASING">' : '';
        $('#modalMessageBody').html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="formAction">
                                        <input type="hidden" id="tokenFormAction" name="tokenForm" autocomplete="false" value="${$(this).attr('data-token')}">
                                        ${type}
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
        if($this.attr('data-form') == 'APPLICATION_FORM') {
            asideHide();
            $('.aside-title').html(`Revise PO No : ${$this.attr('data-no')}`);
            $('.aside-content').html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
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
                <div class="card card-hover mb-3 d-none">
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
                <div class="card card-hover mb-3">
                    <div class="card-body p-2-2 fs-7">
                        <div class="m-2 data-details">
                            <div class="data-row">
                                <label class="text-wrap lh-base" style="display: block; padding-left: 1.5em; text-indent: -1.5em;cursor:pointer">
                                    <input type="checkbox" class="form-check-input checkboxInput reviseApplicationCheckbox" value="CANCEL_APPLICATION_PO">
                                    Cancel Application & PO Form, Move to Archive
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>`);

            $('.overlay-aside').addClass('show').trigger('shown');
            $('#globalAside').addClass('show').trigger('shown');
            $('body').addClass('overflow-hidden');
            $('#asideDetailForm').scrollTop(0);
            $('.asideFooterBtn').html(`<div class="row justify-content-center w-100 mx-0 asideFooterBtn"><div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside" title="Close">Close</button>
                                    </div>
                                    <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-secondary w-100 w-md-auto asideButton" data-type="NEXT_REVISE_FORM" data-form="${$this.attr('data-form')}" data-token="${$this.attr('data-token')}" data-revise-type="" title="Next Revise Form">Next</button>
                                    </div></div>`);
        }
        else {
            selectedFiles = [];
            selectedFilesProperties = [];
            selectedFilesDocument = [];
            selectedFilesPropertiesDocument = [];
            costCenterOption = [];
            $('#fileList').empty();

            const params = {
                'source': 'WEB',
                'form': $this.attr('data-form'),
                'token': $this.attr('data-token'),
                'type': $this.attr('data-type'),
            };

            newForm(params);
        }
    }
    else if($this.attr('data-type') == 'OPEN_ORDER' || $this.attr('data-type') == 'SAVE_OPEN_ORDER' || $this.attr('data-type') == 'GOODS_RECEIVED' || $this.attr('data-type') == 'SAVE_GOODS_RECEIVED' || $this.attr('data-type') == 'INVOICED' || $this.attr('data-type') == 'SAVE_INVOICED') {
        Snackbar.close();
        clearValidation();

        if($this.attr('data-type') == 'OPEN_ORDER') {
            asideHide();
            let applicationData;
            try {
                const response = await fetch(`/proc_pur/viewForm?source=WEB&form=APPLICATION_DATA&token=${$this.attr('data-token')}`, {
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
                else if (data.status === 200) {
                    let items;
                    if(data.data.items.length > 1) {
                        data.data.items.forEach(function(item) {
                            items += `<li>${item.item} - Qty: ${item.quantity}</li>`;
                        });
                    }
                    else {
                        items = `<li>${data.data.items[0].item} - Qty: ${data.data.items[0].quantity}</li>`;
                    }

                    applicationData = `<div class="mb-4 data-details lh-md">
                                            <div class="data-row">
                                                <input type="hidden" name="tokenAttachment" id="tokenAttachment" value="${data.data.tokenAttachment}">
                                                <span class="data-label">PO No.</span>
                                                <span class="data-separator">:</span>
                                                <span class="data-value" id="poNumberToOrder">${data.data.poNumber}</span>
                                            </div>
                                            <div class="data-row">
                                                <span class="data-label">Vendor</span>
                                                <span class="data-separator">:</span>
                                                <span class="data-value">${data.data.vendorName}</span>
                                            </div>
                                            <div class="data-row">
                                                <input type="hidden" name="vendorPicEmail" id="vendorPicEmail" value="${data.data.vendorPicEmail}">
                                                <span class="data-label">PIC Name</span>
                                                <span class="data-separator">:</span>
                                                <span class="data-value">${data.data.vendorPicName}</span>
                                            </div>
                                            <div class="data-row">
                                                <span class="data-label">Grand Total</span>
                                                <span class="data-separator">:</span>
                                                <span class="data-value">${data.data.applicationGrandTotal}</span>
                                            </div>
                                            <div class="data-row">
                                                <span class="data-label">Reason</span>
                                                <span class="data-separator">:</span>
                                                <span class="data-value lh-base">${data.data.applicationReason}</span>
                                            </div>
                                            <div class="data-row">
                                                <span class="data-label">Delivery To</span>
                                                <span class="data-separator">:</span>
                                                <span class="data-value lh-base">${data.data.deliveryToName}</span>
                                            </div>
                                            <div class="data-row">
                                                <span class="data-label">Items</span>
                                                <span class="data-separator">:</span>
                                                <span class="data-value">
                                                    <ul class="data-item-list itemListOrder">
                                                        ${items}
                                                    </ul>
                                                </span>
                                            </div>
                                        </div>`;
                }
            }
            catch (error) {
                applicationData = `<div class="row min-vh-75">
                                        <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                            <div class="center-container">
                                                <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                <div class="d-block fs-7 mt-2">Failed to get data</div>
                                            </div>
                                        </div>
                                    </div>`;
            }

            $('.aside-title').html(`Set to Open Order : ${$this.attr('data-no')}`);
            $('.aside-content').html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
                                        <input type="hidden" name="tokenForm" value="${$this.attr('data-token')}">
                                        ${applicationData}
                                        <div class="card card-hover mb-3">
                                            <div class="card-body p-2-2 fs-8">
                                                <div class="m-2 data-details">
                                                    <div class="data-row">
                                                        <label class="text-wrap lh-base" style="display: block; padding-left: 1.5em; text-indent: -1.5em;cursor:pointer">
                                                            <input type="checkbox" name="poFormDownload" class="form-check-input checkboxInput" id="poFormDownload" value="1">
                                                            Download : PO Form
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card-hover mb-3">
                                            <div class="card-body p-2-2 fs-8">
                                                <div class="m-2 data-details">
                                                    <div class="data-row">
                                                        <label class="text-wrap lh-base" style="display: block; padding-left: 1.5em; text-indent: -1.5em;cursor:pointer">
                                                            <input type="checkbox" name="inspectionFormToApplicant" class="form-check-input checkboxInput" id="inspectionFormToApplicant" value="1" checked="">
                                                            Send Inspection Form to Order Form Applicant
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>`);

            $('.overlay-aside').addClass('show').trigger('shown');
            $('#globalAside').addClass('show').trigger('shown');
            $('body').addClass('overflow-hidden');
            $('#asideDetailForm').scrollTop(0);

            $('.asideFooterBtn').html(`<div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside asideBtn" title="Close">Close</button>
                                        </div>
                                        <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-secondary w-100 w-md-auto asideBtn actionBtn" data-type="SAVE_OPEN_ORDER"></i> Update to Open Order</button>
                                        </div>`);
        }
        // else if($this.attr('data-type') == 'GOODS_RECEIVED') {
        //     asideHide();
        //     $('.aside-title').html('Goods Received');
        //     $('.aside-content').html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
        //                                 <input type="hidden" name="tokenForm" value="${$this.attr('data-token')}">
        //                                 <div class="form-group mb-3">
        //                                     <label for="receivedDateOrder" class="form-label">Received Date <span class="required"></span> :</label>
        //                                     <div class="form-group date-picker" id="receivedDateOrder" data-coreui-date="" data-coreui-name="receivedDateOrder"></div>
        //                                 </div>
        //                             </form>`);

        //     $('.overlay-aside').addClass('show').trigger('shown');
        //     $('#globalAside').addClass('show').trigger('shown');
        //     $('body').addClass('overflow-hidden');
        //     $('#asideDetailForm').scrollTop(0);

        //     $('.asideFooterBtn').html(`<div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
        //                                     <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside asideBtn" title="Close">Close</button>
        //                                 </div>
        //                                 <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
        //                                     <button type="button" class="btn btn-secondary w-100 w-md-auto asideBtn actionBtn" data-type="SAVE_GOODS_RECEIVED"></i> Update to Received</button>
        //                                 </div>`);

        //     var optionsSubmittedDate = {
        //         locale: 'en-US',
        //         inputDateFormat: date => dayjs(date).locale('en').format('DD-MM-YYYY'),
        //         inputDateParse: date => dayjs(date, 'DD-MM-YYYY', 'id').toDate(),
        //         maxDate: dayjs(new Date()),
        //         showAdjacementDays: false,
        //     }

        //     new coreui.DatePicker(document.getElementById(`receivedDateOrder`), optionsSubmittedDate);
        // }
        // else if($this.attr('data-type') == 'INVOICED') {
        //     asideHide();
        //     $('.aside-title').html(`Invoiced Purchase : ${$this.attr('data-no')}`);
        //     $('.aside-content').html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
        //                                 <input type="hidden" name="tokenForm" value="${$this.attr('data-token')}">
        //                                 <div class="form-group mb-3">
        //                                     <label for="invoiceNo" class="form-label">Invoice No.<span class="required"></span> :</label>
        //                                     <input type="text" class="form-control" name="invoiceNo" id="invoiceNo" spellcheck="false" autocomplete="off">
        //                                 </div>
        //                                 <div class="form-group mb-3">
        //                                     <label for="invoicedDateOrder" class="form-label">Invoiced Date<span class="required"></span> :</label>
        //                                     <div class="form-group date-picker" id="invoicedDateOrder" data-coreui-date="" data-coreui-name="invoicedDateOrder"></div>
        //                                 </div>
        //                             </form>`);

        //     $('.overlay-aside').addClass('show').trigger('shown');
        //     $('#globalAside').addClass('show').trigger('shown');
        //     $('body').addClass('overflow-hidden');
        //     $('#asideDetailForm').scrollTop(0);

        //     $('.asideFooterBtn').html(`<div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
        //                                     <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside asideBtn" title="Close">Close</button>
        //                                 </div>
        //                                 <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
        //                                     <button type="button" class="btn btn-secondary w-100 w-md-auto asideBtn actionBtn" data-type="SAVE_INVOICED"></i> Invoiced Purchase</button>
        //                                 </div>`);

        //     var optionsSubmittedDate = {
        //         locale: 'en-US',
        //         inputDateFormat: date => dayjs(date).locale('en').format('DD-MM-YYYY'),
        //         inputDateParse: date => dayjs(date, 'DD-MM-YYYY', 'id').toDate(),
        //         maxDate: dayjs(new Date()),
        //         showAdjacementDays: false,
        //     }

        //     new coreui.DatePicker(document.getElementById(`invoicedDateOrder`), optionsSubmittedDate);
        // }
        else if($this.attr('data-type') == 'GOODS_RECEIVED') {
            asideHide();
            $('.aside-title').html(`Inspection : ${$this.attr('data-no')}`);
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
                                            <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside asideBtn" title="Close">Close</button>
                                        </div>`);

            try {
                const response = await fetch(`/doc_approval/getInspection?token=${$this.attr('data-token')}`, {
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
                    let resultForm = result.data.inspectionFlow;
                    if(result.data.form.length > 0) {
                        result.data.form.forEach((row) => {
                            resultForm += row;
                        });
                    }
                    else {
                        resultForm += `<div class="card-body d-flex justify-content-center align-items-center mt-5">
                                            <div class="center-container">
                                                <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                <div class="d-block fs-7 mt-2">No available Inspection Form</div>
                                            </div>
                                        </div>`;
                    }

                    $('#asideForm').html(resultForm);
                }
                else {
                    console.error('HTTP Error:', response.status);
                }
            } catch (error) {
                return;
            }
        }
        else if($this.attr('data-type') == 'INVOICED') {
            asideHide();
            $('.aside-title').html(`Invoiced Purchase : ${$this.attr('data-no')}`);
            $('.aside-content').html(`<form role="form" class="form-horizontal" enctype="multipart/form-data" id="asideForm">
                                        <input type="hidden" name="tokenForm" value="${$this.attr('data-token')}">
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
                                                        <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-3">${$this.attr('data-ccy')}</span>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <div class="">
                                                        <div id="dropZone" class="form-group border p-4 text-center bg-light">
                                                            <input type="file" class="form-control" name="attachmentFile0">
                                                        </div>
                                                    </div>
                                                    <div id="fileList" class="list-group mt-2">
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-end align-items-center gap-2">
                                                    <button type="button" class="btn btn-default btn-sm fs-8 fw-medium invoiceButton" data-type="REMOVE_INVOICE"><i class="fa-solid fa-trash-can-list fa-fw"></i> Remove</button>
                                                    <button type="button" class="btn btn-secondary btn-sm fs-8 fw-medium invoiceButton" data-type="ADD_INVOICE" data-ccy="${$this.attr('data-ccy')}"><i class="fa-regular fa-plus fa-fw"></i> Add Invoice</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>`);

            $('.overlay-aside').addClass('show').trigger('shown');
            $('#globalAside').addClass('show aside-lg').trigger('shown');
            $('body').addClass('overflow-hidden');
            $('#asideDetailForm').scrollTop(0);

            $('.asideFooterBtn').html(`<div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-default w-100 w-md-auto me-2 closeBtnAside asideBtn" title="Close">Close</button>
                                        </div>
                                        <div class="col-6 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-info w-100 w-md-auto asideBtn actionBtn" data-type="SAVE_INVOICED"></i> Submit Invoice</button>
                                        </div>`);

            new coreui.DatePicker(document.getElementById(`invoicedDateOrder0`), maxDateCurrentOptions);
            new coreui.DatePicker(document.getElementById(`invoiceDueDate0`), maxDateNullOptions);
        }
        else {
            $(this).btnLoading(async function() {
                try {
                    $('.actionBtn').prop('disabled', true);
                    $this.html('<i class="fas fa-spinner fa-spin"></i> Please wait');

                    let formData;
                    if($this.attr('data-type') == 'SAVE_OPEN_ORDER' || $this.attr('data-type') == 'SAVE_GOODS_RECEIVED' || $this.attr('data-type') == 'SAVE_INVOICED') {
                        formData = new FormData($('#asideForm')[0]);
                        formData.append('type','PURCHASING');
                        formData.append('actionType', $this.attr('data-type'));
                    }
                    else {
                        formData = new FormData();
                        formData.append('type','PURCHASING');
                        formData.append('actionType', $this.attr('data-type'));
                        formData.append('tokenForm', $this.attr('data-token'));
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
                        if($this.attr('data-type') == 'SAVE_OPEN_ORDER') {
                            let tokenAttachment = $('#tokenAttachment').val();
                            let poNumberToOrder = $('#poNumberToOrder').html();

                            if($('#poFormDownload').prop('checked') == true) {
                                const downloadUrl = '/doc_approval/getAttachment?token=' + tokenAttachment;
                                const loadingIndicator = document.createElement('div');
                                loadingIndicator.id = 'loading-indicator';
                                loadingIndicator.textContent = 'Downloading file...';
                                loadingIndicator.style.position = 'fixed';
                                loadingIndicator.style.top = '50%';
                                loadingIndicator.style.left = '50%';
                                loadingIndicator.style.transform = 'translate(-50%, -50%)';
                                loadingIndicator.style.padding = '10px 20px';
                                loadingIndicator.style.background = 'rgba(0,0,0,0.7)';
                                loadingIndicator.style.color = 'white';
                                loadingIndicator.style.borderRadius = '5px';
                                loadingIndicator.style.zIndex = '9999';
                                document.body.appendChild(loadingIndicator);

                                fetch(downloadUrl)
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error(`HTTP error! Status: ${response.status}`);
                                        }
                                        return response.blob();
                                    })
                                    .then(blob => {
                                        document.getElementById('loading-indicator').remove();
                                        if (blob.size === 0) {
                                            throw new Error('File kosong atau tidak tersedia');
                                        }

                                        const url = window.URL.createObjectURL(blob);
                                        const a = document.createElement('a');
                                        a.style.display = 'none';
                                        a.href = url;
                                        a.download = `PO ${poNumberToOrder}.pdf`;
                                        document.body.appendChild(a);
                                        a.click();

                                        window.URL.revokeObjectURL(url);
                                        document.body.removeChild(a);

                                        // if (confirm('File berhasil diunduh. Buka Microsoft Outlook sekarang?')) {
                                        //     setTimeout(() => {
                                        //         window.location.href = `mailto:${picEmail}`;
                                        //     }, 100);
                                        // }
                                    })
                                    .catch(error => {
                                        if (document.getElementById('loading-indicator')) {
                                            document.getElementById('loading-indicator').remove();
                                        }

                                        console.error('Error saat mengunduh file:', error);
                                        alert('Failed to download : ' + error.message);
                                    });
                            }

                            asideHide();
                            currentPageAppCompleted = 1;
                            isLoadingAppCompleted = false;
                            hasMoreDataAppCompleted = true;
                            loadAppCompletedColumn();

                            currentPageOpenOrder = 1;
                            isLoadingOpenOrder = false;
                            hasMoreDataOpenOrder = true;
                            loadOpenOrder();
                        }
                        else if($this.attr('data-type') == 'SAVE_GOODS_RECEIVED') {
                            asideHide();
                            currentPageOpenOrder = 1;
                            isLoadingOpenOrder = false;
                            hasMoreDataOpenOrder = true;
                            loadOpenOrder();

                            currentPageGoodsReceived = 1;
                            isLoadingGoodsReceived = false;
                            hasMoreDataGoodsReceived = true;
                            loadGoodsReceived();
                        }
                        else if($this.attr('data-type') == 'SAVE_INVOICED') {
                            asideHide();
                            currentPageGoodsReceived = 1;
                            isLoadingGoodsReceived = false;
                            hasMoreDataGoodsReceived = true;
                            loadGoodsReceived();
                        }

                        Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result['message']}`});
                        $('.modal').modal('hide');
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
                    $('.actionBtn').prop('disabled', false);
                }
            });
        }
    }
    else if($this.attr('data-type') == 'ARCHIVE' ) {
        $(this).btnLoading(async function() {
            try {
                $('.actionBtn').prop('disabled', true);
                $this.html('<i class="fas fa-spinner fa-spin"></i> Please wait');
                const formData = new FormData();
                formData.append('type','PURCHASING');
                formData.append('actionType', $this.attr('data-type'));
                formData.append('tokenForm', $this.attr('data-token'));

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
                    Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result['message']}`});
                    $('.modal').modal('hide');
                    currentPageAppCompleted = 1;
                    isLoadingAppCompleted = false;
                    hasMoreDataAppCompleted = true;
                    loadAppCompletedColumn();
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
                $('.actionBtn').prop('disabled', false);
            }
        });
    }
    else if($this.attr('data-type') == 'VIEW' || $this.attr('data-type') == 'VIEW_ONGOING') {
        $('.card-link-content').removeClass('active');
        Snackbar.close();
        $this.addClass('active');

        if($this.attr('data-form') == 'ORDER_FORM') {
            if($this.attr('data-type') == 'VIEW') {
                Snackbar.show({
                    pos: 'bottom-center',
                    duration: '6000',
                    text: `<i class="fa-regular fa-circle-notch fa-spin fs-6 fa-fw text-info"></i> Processing...`
                });

                const token = $this.attr('data-token');
                const form = $this.attr('data-form');
                const source = $this.attr('data-source');

                try {
                    const response = await fetch(`/proc_pur/getComparison?type=PURCHASING&form=${form}&token=${token}`, {
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
                                    'source': 'WEB',
                                    'form': result.data.form,
                                    'token': result.data.tokenForm,
                                    'type': $this.attr('data-type'),
                                };

                                if($this.attr('data-type') == 'NEW') {
                                    newForm(params);
                                }
                                else{
                                    viewForm(params);
                                }
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
            else if($this.attr('data-type') == 'VIEW_ONGOING') {
                event.stopImmediatePropagation();
                const params = {
                    'form': $this.attr('data-form'),
                    'token': $this.attr('data-token'),
                    'type': $this.attr('data-type'),
                };

                viewForm(params);
            }
        }
        else {
            const params = {
                'source': $this.attr('data-source'),
                'form': $this.attr('data-form'),
                'token': $this.attr('data-token'),
                'type': $this.attr('data-type'),
            };

            if($this.attr('data-type') == 'NEW') {
                newForm(params);
            }
            else{
                viewForm(params);
            }
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

$(document).off('click', '.invoiceButton').on('click', '.invoiceButton', function (e) {
    let $this = $(this);
    Snackbar.close();
    if($this.attr('data-type') == 'ADD_INVOICE') {
        let x = $('#invoiceContainer').find('.invoiceSection').length;
        $this.closest('div#invoiceContainer').append(`<div class="row row-multi-col mb-3 pt-2 pb-3 bg-white invoiceSection">
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
                                                        <span class="currencyText position-absolute end-0 top-0 h-100 d-flex align-items-center px-3">${$this.attr('data-ccy')}</span>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <div class="">
                                                        <div id="dropZone" class="form-group border p-4 text-center bg-light">
                                                            <input type="file" class="form-control" name="attachmentFile${x}">
                                                        </div>
                                                    </div>
                                                    <div id="fileList" class="list-group mt-2">
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-end align-items-center gap-2">
                                                    <button type="button" class="btn btn-default btn-sm fs-8 fw-medium invoiceButton" data-type="REMOVE_INVOICE"><i class="fa-solid fa-trash-can-list fa-fw"></i> Remove</button>
                                                    <button type="button" class="btn btn-secondary btn-sm fs-8 fw-medium invoiceButton" data-type="ADD_INVOICE" data-ccy="${$this.attr('data-ccy')}"><i class="fa-regular fa-plus fa-fw"></i> Add Invoice</button>
                                                </div>
                                            </div>`);
        $('.autosize').autosize({ append: "\n" });
        $('#asideDetailForm').animate({
            scrollTop: $('#asideDetailForm')[0].scrollHeight
        }, 500);

        new coreui.DatePicker(document.getElementById(`invoicedDateOrder${x}`), maxDateCurrentOptions);
        new coreui.DatePicker(document.getElementById(`invoiceDueDate${x}`), maxDateNullOptions);
    }
    else if($this.attr('data-type') == 'REMOVE_INVOICE') {
        if($this.closest('div#invoiceContainer').find('div.invoiceSection').length > 1) {
            $this.closest('div.invoiceSection').remove();
        }
        else {
            Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-triangle-exclamation fa-lg fa-fw text-danger"></i> Failed : At least 1 Invoice must be filled` });
            return false;
        }
    }
});

$(document).on('change', '.reviseApplicationCheckbox', function () {
    $(this).closest('#globalAside').find('div.aside-footer').find('button.asideButton').attr('data-revise-type', '');
    if ($(this).prop('checked')) {
        $(this).closest('#globalAside').find('div.aside-footer').find('button.asideButton').attr('data-revise-type', $(this).val());
        $('.reviseApplicationCheckbox').prop('checked', false);
        $(this).prop('checked', true);
    }
});

$(document).on('change', '.vendorAddressSelected', function () {
    if ($(this).prop('checked')) {
        $('.vendorAddressSelected').prop('checked', false);
        $(this).prop('checked', true);
    }
});

$(document).on('change', '.vendorAddressApplication', function () {
    if ($(this).prop('checked')) {
        $('.vendorAddressApplication').prop('checked', false);
        $(this).prop('checked', true);
    }
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

$(document).on('click', '.closePopper', function () {
    hidePopup();
});

$(document).on('click', function (e) {
    if (!$(e.target).closest('.popper-container, .popper-button, .approveBtn, .rejectBtn, .sendBackBtn').length) {
        hidePopup();
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

$(document).on('click', '.confirmAction', function () {
    let $this = $(this);
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
                asideHide();
                Snackbar.show({ pos: 'bottom-center', text: `<i class="fa-solid fa-circle-check fa-lg fa-fw text-success"></i> ${result['message']}` });
                $('.modal').modal('hide');
                if($this.attr('data-type') == 'SEND_BACK' || $this.attr('data-type') == 'CANCEL' || $this.attr('data-type') == 'CANCEL_TO_ARCHIVE') {
                    currentPageAppOngoing = 1;
                    isLoadingAppOngoing = false;
                    hasMoreDataAppOngoing = true;
                    loadAppColumn();

                    currentPageAppCompleted = 1;
                    isLoadingAppCompleted = false;
                    hasMoreDataAppCompleted = true;
                    loadAppCompletedColumn();
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
            $('.confirmAction').prop('disabled', false);
        }
    });
});

async function getSendBackOptions(params) {
    let option = `<option></option>`;
    try {
        const response = await fetch(`/doc_approval/getSendBackOptions?type=PURCHASING&tokenForm=${params['tokenForm']}`, {
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
                label: row.employee_name+' - ORDER FORM '+row.flow_as,
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