class ScrollbarCustom {
    constructor(columnSelector, options = {}) {
        this.column = document.querySelector(columnSelector);
        this.scrollbar = document.createElement('div');
        this.scrollbarThumb = document.createElement('div');
        this.isScrolling = false;
        this.isDragging = false;
        this.startY = 0;
        this.startThumbTop = 0;
        this.options = options;
        this.isMobile = window.innerWidth <= 767.98;
        this.overflowX = options.overflowX || 'none';
        this.overflowY = options.overflowY || 'auto';
        this.init();

        window.addEventListener('resize', () => this.handleResize());
        this.loadScrollPosition();
    }

    init() {
        if (this.isMobile) {
            this.hideScrollbar();
        } else {
            this.showScrollbar();
        }
    }

    handleResize() {
        this.isMobile = window.innerWidth <= 767.98;
        if (this.isMobile) {
            this.hideScrollbar();
        } else {
            this.showScrollbar();
        }
    }

    showScrollbar() {
        this.scrollbar.classList.add('custom-scrollbar');
        this.scrollbarThumb.classList.add('custom-scrollbar-thumb');
        this.scrollbar.appendChild(this.scrollbarThumb);
        this.column.parentElement.appendChild(this.scrollbar);

        this.updateScrollbarVisibility();
        this.updateScrollbarPosition();
        this.updateScrollbarThumb();

        this.column.addEventListener('scroll', () => {
            this.isScrolling = true;
            this.updateScrollbarThumb();
            this.updateScrollbarVisibility();
            clearTimeout(this.column.scrollTimeout);
            this.column.scrollTimeout = setTimeout(() => {
                this.isScrolling = false;
                this.updateScrollbarVisibility();
            }, 1000);

            this.saveScrollPosition();
        });

        this.column.addEventListener('mouseenter', () => this.updateScrollbarVisibility());
        this.column.addEventListener('mouseleave', () => this.updateScrollbarVisibility());
        this.scrollbar.addEventListener('mouseenter', () => this.updateScrollbarVisibility());
        this.scrollbar.addEventListener('mouseleave', () => this.updateScrollbarVisibility());

        this.scrollbar.addEventListener('mousedown', (e) => {
            if (e.target === this.scrollbar) {
                this.setScrollPosition(e.clientY);
                this.updateScrollbarThumb();
            }
        });

        this.scrollbar.addEventListener('wheel', (e) => this.handleWheel(e));

        this.scrollbarThumb.addEventListener('mousedown', (e) => {
            this.isDragging = true;
            this.scrollbarThumb.classList.add('custom-scrollbar-thumb-dragging');
            this.startY = e.clientY;
            this.startThumbTop = this.scrollbarThumb.offsetTop;
            document.addEventListener('mousemove', this.onMouseMove.bind(this));
            document.addEventListener('mouseup', this.onMouseUp.bind(this));
            e.preventDefault();
        });

        this.updateScrollbarVisibility();
    }

    hideScrollbar() {
        if (this.scrollbar.parentElement) {
            this.scrollbar.parentElement.removeChild(this.scrollbar);
        }
        this.column.style.overflowY = 'auto';
    }

    scrollTo(position) {
        if (typeof position === 'number') {
            this.column.scrollTop = position;
        } else if (position === 'top') {
            this.column.scrollTop = 0;
        } else if (position === 'bottom') {
            this.column.scrollTop = this.column.scrollHeight;
        } else if (position === 'middle') {
            this.column.scrollTop = (this.column.scrollHeight - this.column.clientHeight) / 2;
        }

        this.updateScrollbarThumb();
    }

    updateScrollbarVisibility() {
        const isScrollableY = this.column.scrollHeight > this.column.clientHeight;
        const isScrollableX = this.column.scrollWidth > this.column.clientWidth;

        let scrollbarVisibleY = false;
        let scrollbarVisibleX = false;

        if (this.overflowY === 'auto') {
            scrollbarVisibleY = this.isScrolling || this.column.matches(':hover') || this.isDragging;
        } else if (this.overflowY === 'scroll') {
            scrollbarVisibleY = isScrollableY;
        } else if (this.overflowY === 'hidden') {
            scrollbarVisibleY = false;
        } else if (this.overflowY === 'none') {
            scrollbarVisibleY = false;
            this.column.style.overflowY = 'hidden';
        }

        if (this.overflowX === 'auto') {
            scrollbarVisibleX = this.isScrolling || this.column.matches(':hover') || this.isDragging;
        } else if (this.overflowX === 'scroll') {
            scrollbarVisibleX = isScrollableX;
        } else if (this.overflowX === 'hidden') {
            scrollbarVisibleX = false;
        } else if (this.overflowX === 'none') {
            scrollbarVisibleX = false;
            this.column.style.overflowX = 'hidden';
        }

        this.scrollbar.style.opacity = scrollbarVisibleY || scrollbarVisibleX ? '1' : '0';
        this.scrollbar.style.display = (isScrollableY || isScrollableX) ? 'block' : 'none';
    }

    updateScrollbarPosition() {
        const columnWidth = this.column.clientWidth;
        const columnTop = this.column.offsetTop;

        if (this.options.top !== undefined && this.options.top !== null) {
            this.scrollbar.style.top = `${this.options.top}px`;
        } else {
            this.scrollbar.style.top = `${columnTop}px`;
        }

        if (this.options.right !== undefined && this.options.right !== null) {
            this.scrollbar.style.right = `${this.options.right}px`;
            this.scrollbar.style.left = 'auto';
        }
        else if (this.options.left !== undefined && this.options.left !== null) {
            this.scrollbar.style.left = `${this.options.left}px`;
            this.scrollbar.style.right = 'auto';
        }
        else {
            this.scrollbar.style.left = `${columnWidth}px`;
            this.scrollbar.style.right = 'auto';
        }
    }

    updateScrollbarThumb() {
        const scrollPercentage = (this.column.scrollTop / (this.column.scrollHeight - this.column.clientHeight));
        const thumbHeight = Math.max((this.column.clientHeight / this.column.scrollHeight) * this.scrollbar.clientHeight, 70);
        this.scrollbarThumb.style.height = `${thumbHeight}px`;
        this.scrollbarThumb.style.top = `${scrollPercentage * (this.scrollbar.clientHeight - thumbHeight)}px`;
    }

    handleWheel(e) {
        // Menangani scroll wheel pada area scrollbar
        e.preventDefault();
        const scrollAmount = e.deltaY; // Jumlah scroll dari wheel
        this.column.scrollTop += scrollAmount;

        this.updateScrollbarThumb();
    }

    saveScrollPosition() {
        this.column.dataset.scrollTop = this.column.scrollTop;
    }

    loadScrollPosition() {
        const savedScrollTop = this.column.dataset.scrollTop;
        if (savedScrollTop !== undefined) {
            this.column.scrollTop = parseFloat(savedScrollTop);
            this.updateScrollbarThumb();
        }
    }

    setScrollPosition(clientY) {
        const scrollbarRect = this.scrollbar.getBoundingClientRect();
        const thumbHeight = this.scrollbarThumb.offsetHeight;
        const scrollableHeight = this.scrollbar.clientHeight - thumbHeight;
        const scrollPercentage = (clientY - scrollbarRect.top - thumbHeight / 2) / scrollableHeight;
        this.column.scrollTop = scrollPercentage * (this.column.scrollHeight - this.column.clientHeight);
    }

    onMouseMove(e) {
        if (this.isDragging) {
            const deltaY = e.clientY - this.startY;
            const newThumbTop = this.startThumbTop + deltaY;
            const scrollbarHeight = this.scrollbar.clientHeight;
            const thumbHeight = this.scrollbarThumb.offsetHeight;

            const maxTop = scrollbarHeight - thumbHeight;
            const constrainedTop = Math.max(0, Math.min(newThumbTop, maxTop));

            const scrollPercentage = constrainedTop / maxTop;
            this.column.scrollTop = scrollPercentage * (this.column.scrollHeight - this.column.clientHeight);

            this.updateScrollbarThumb();
        }
    }

    onMouseUp() {
        this.isDragging = false;
        this.scrollbarThumb.classList.remove('custom-scrollbar-thumb-dragging');
        document.removeEventListener('mousemove', this.onMouseMove.bind(this));
        document.removeEventListener('mouseup', this.onMouseUp.bind(this));
        this.updateScrollbarVisibility();
    }
}
