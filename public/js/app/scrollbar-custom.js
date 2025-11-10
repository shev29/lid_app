class ScrollbarCustom {
    constructor(columnSelector, options = {}) {
        this.column = document.querySelector(columnSelector);
        this.scrollbarV = document.createElement('div'); // Vertical scrollbar
        this.scrollbarThumbV = document.createElement('div'); // Vertical thumb
        this.scrollbarH = document.createElement('div'); // Horizontal scrollbar
        this.scrollbarThumbH = document.createElement('div'); // Horizontal thumb
        this.isScrolling = false;
        this.isDraggingV = false;
        this.isDraggingH = false;
        this.startY = 0;
        this.startX = 0;
        this.startThumbTopV = 0;
        this.startThumbLeftH = 0;
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
        // Hide native scrollbars
        this.hideNativeScrollbars();

        // Setup vertical scrollbar
        if (this.overflowY !== 'none') {
            this.scrollbarV.classList.add('custom-scrollbar', 'custom-scrollbar-vertical');
            this.scrollbarThumbV.classList.add('custom-scrollbar-thumb', 'custom-scrollbar-thumb-vertical');
            this.scrollbarV.appendChild(this.scrollbarThumbV);
            this.column.parentElement.appendChild(this.scrollbarV);
            this.setupVerticalScrollbar();
        }

        // Setup horizontal scrollbar
        if (this.overflowX !== 'none') {
            this.scrollbarH.classList.add('custom-scrollbar', 'custom-scrollbar-horizontal');
            this.scrollbarThumbH.classList.add('custom-scrollbar-thumb', 'custom-scrollbar-thumb-horizontal');
            this.scrollbarH.appendChild(this.scrollbarThumbH);
            this.column.parentElement.appendChild(this.scrollbarH);
            this.setupHorizontalScrollbar();
        }

        this.updateScrollbarVisibility();
        this.updateScrollbarPosition();
        this.updateScrollbarThumb();
        if ('ResizeObserver' in window) {
            this.resizeObserver = new ResizeObserver(() => {
                this.updateScrollbarThumb();
                this.updateScrollbarVisibility();
            });
            this.resizeObserver.observe(this.column);
        }

        // Column scroll event
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

        // Mouse events for visibility (only for auto mode)
        this.column.addEventListener('mouseenter', () => this.updateScrollbarVisibility());
        this.column.addEventListener('mouseleave', () => this.updateScrollbarVisibility());

        if (this.overflowY !== 'none') {
            this.scrollbarV.addEventListener('mouseenter', () => this.updateScrollbarVisibility());
            this.scrollbarV.addEventListener('mouseleave', () => this.updateScrollbarVisibility());
        }

        if (this.overflowX !== 'none') {
            this.scrollbarH.addEventListener('mouseenter', () => this.updateScrollbarVisibility());
            this.scrollbarH.addEventListener('mouseleave', () => this.updateScrollbarVisibility());
        }

        this.updateScrollbarVisibility();
    }

    setupVerticalScrollbar() {
        // Click on scrollbar track
        this.scrollbarV.addEventListener('mousedown', (e) => {
            if (e.target === this.scrollbarV) {
                this.setScrollPositionV(e.clientY);
                this.updateScrollbarThumb();
            }
        });

        // Wheel event on vertical scrollbar
        this.scrollbarV.addEventListener('wheel', (e) => this.handleWheelV(e));

        // Thumb drag events
        this.scrollbarThumbV.addEventListener('mousedown', (e) => {
            this.isDraggingV = true;
            this.scrollbarThumbV.classList.add('custom-scrollbar-thumb-dragging');
            this.startY = e.clientY;
            this.startThumbTopV = this.scrollbarThumbV.offsetTop;

            document.addEventListener('mousemove', this.onMouseMoveV.bind(this));
            document.addEventListener('mouseup', this.onMouseUpV.bind(this));

            // Nonaktifkan scrollbar horizontal termasuk bawaan browser
            // if (this.scrollbarH) this.scrollbarH.style.pointerEvents = 'none';
            // this.column.dataset.prevOverflowX = this.column.style.overflowX;
            // this.column.style.overflowX = 'hidden';
            // document.body.classList.add('no-scroll');
            if(document.getElementById('body-container')) {
                document.getElementById('body-container').classList.add('no-scroll');
            }
            e.preventDefault();
        });
    }

    setupHorizontalScrollbar() {
        // Click on scrollbar track
        this.scrollbarH.addEventListener('mousedown', (e) => {
            if (e.target === this.scrollbarH) {
                this.setScrollPositionH(e.clientX);
                this.updateScrollbarThumb();
            }
        });

        // Wheel event on horizontal scrollbar
        this.scrollbarH.addEventListener('wheel', (e) => this.handleWheelH(e));

        // Thumb drag events
        this.scrollbarThumbH.addEventListener('mousedown', (e) => {
            this.isDraggingH = true;
            this.scrollbarThumbH.classList.add('custom-scrollbar-thumb-dragging');
            this.startX = e.clientX;
            this.startThumbLeftH = this.scrollbarThumbH.offsetLeft;

            document.addEventListener('mousemove', this.onMouseMoveH.bind(this));
            document.addEventListener('mouseup', this.onMouseUpH.bind(this));
            e.preventDefault();
        });
    }

    hideScrollbar() {
        if (this.scrollbarV.parentElement) {
            this.scrollbarV.parentElement.removeChild(this.scrollbarV);
        }
        if (this.scrollbarH.parentElement) {
            this.scrollbarH.parentElement.removeChild(this.scrollbarH);
        }
        // Restore native scrollbars on mobile
        this.showNativeScrollbars();
    }

    hideNativeScrollbars() {
        // Hide native scrollbars using CSS
        const style = document.createElement('style');
        style.id = `scrollbar-hide-${this.column.className.replace(/\s+/g, '-')}`;
        style.innerHTML = `
            .${this.column.className.split(' ').join('.')} {
                scrollbar-width: none; /* Firefox */
                -ms-overflow-style: none; /* IE and Edge */
            }
            .${this.column.className.split(' ').join('.')}::-webkit-scrollbar {
                display: none; /* Chrome, Safari, Opera */
            }
        `;

        // Remove existing style if present
        const existingStyle = document.getElementById(style.id);
        if (existingStyle) {
            existingStyle.remove();
        }

        document.head.appendChild(style);
    }

    showNativeScrollbars() {
        // Remove the hide scrollbar style
        const styleId = `scrollbar-hide-${this.column.className.replace(/\s+/g, '-')}`;
        const existingStyle = document.getElementById(styleId);
        if (existingStyle) {
            existingStyle.remove();
        }
    }

    scrollTo(position, direction = 'vertical') {
        if (direction === 'vertical') {
            if (typeof position === 'number') {
                this.column.scrollTop = position;
            } else if (position === 'top') {
                this.column.scrollTop = 0;
            } else if (position === 'bottom') {
                this.column.scrollTop = this.column.scrollHeight;
            } else if (position === 'middle') {
                this.column.scrollTop = (this.column.scrollHeight - this.column.clientHeight) / 2;
            }
        } else if (direction === 'horizontal') {
            if (typeof position === 'number') {
                this.column.scrollLeft = position;
            } else if (position === 'left') {
                this.column.scrollLeft = 0;
            } else if (position === 'right') {
                this.column.scrollLeft = this.column.scrollWidth;
            } else if (position === 'center') {
                this.column.scrollLeft = (this.column.scrollWidth - this.column.clientWidth) / 2;
            }
        }

        this.updateScrollbarThumb();
    }

    updateScrollbarVisibility() {
        const isScrollableY = this.column.scrollHeight > this.column.clientHeight;
        const isScrollableX = this.column.scrollWidth > this.column.clientWidth;

        // === VERTICAL SCROLLBAR ===
        if (this.overflowY === 'scroll') {
            if (isScrollableY) {
                this.scrollbarV.style.display = 'block';
                requestAnimationFrame(() => {
                    this.scrollbarV.style.opacity = '1';
                    this.scrollbarThumbV.style.opacity = '1';
                });
            }
            else {
                this.scrollbarV.style.opacity = '0';
                this.scrollbarThumbV.style.opacity = '0';
                setTimeout(() => {
                    this.scrollbarV.style.display = 'none';
                }, 0);
            }
        }
        else if (this.overflowY === 'auto') {
            const isHovered = this.column.matches(':hover') || this.scrollbarV.matches(':hover') || this.isScrolling || this.isDraggingV;
            if (isScrollableY) {
                this.scrollbarV.style.display = 'block';
                requestAnimationFrame(() => {
                    this.scrollbarV.style.opacity = isHovered ? '1' : '0';
                    this.scrollbarThumbV.style.opacity = isHovered ? '1' : '0';
                });
            } else {
                this.scrollbarV.style.opacity = '0';
                this.scrollbarThumbV.style.opacity = '0';
                setTimeout(() => {
                    this.scrollbarV.style.display = 'none';
                }, 0);
            }
        }
        else if (this.overflowY === 'hidden') {
            this.scrollbarV.style.opacity = '0';
            this.scrollbarThumbV.style.opacity = '0';
            this.scrollbarV.style.display = 'none';
            this.column.style.overflowY = 'auto';
        }
        else if (this.overflowY === 'none') {
            this.scrollbarV.style.opacity = '0';
            this.scrollbarThumbV.style.opacity = '0';
            this.scrollbarV.style.display = 'none';
            this.column.style.overflowY = 'hidden';
        }

        // === HORIZONTAL SCROLLBAR ===
        if (this.overflowX === 'scroll') {
            if (isScrollableX) {
                this.scrollbarH.style.display = 'block';
                requestAnimationFrame(() => {
                    this.scrollbarH.style.opacity = '1';
                    this.scrollbarThumbH.style.opacity = '1';
                });
            } else {
                this.scrollbarH.style.opacity = '0';
                this.scrollbarThumbH.style.opacity = '0';
                setTimeout(() => {
                    this.scrollbarH.style.display = 'none';
                }, 0);
            }
        }
        else if (this.overflowX === 'auto') {
            const isHovered = this.column.matches(':hover') || this.scrollbarH.matches(':hover') || this.isScrolling || this.isDraggingH;
            if (isScrollableX) {
                this.scrollbarH.style.display = 'block';
                requestAnimationFrame(() => {
                    this.scrollbarH.style.opacity = isHovered ? '1' : '0';
                    this.scrollbarThumbH.style.opacity = isHovered ? '1' : '0';
                });
            } else {
                this.scrollbarH.style.opacity = '0';
                this.scrollbarThumbH.style.opacity = '0';
                setTimeout(() => {
                    this.scrollbarH.style.display = 'none';
                }, 0);
            }
        }
        else if (this.overflowX === 'hidden') {
            this.scrollbarH.style.opacity = '0';
            this.scrollbarThumbH.style.opacity = '0';
            this.scrollbarH.style.display = 'none';
            this.column.style.overflowX = 'auto';
        }
        else if (this.overflowX === 'none') {
            this.scrollbarH.style.opacity = '0';
            this.scrollbarThumbH.style.opacity = '0';
            this.scrollbarH.style.display = 'none';
            this.column.style.overflowX = 'hidden';
        }
    }

    updateScrollbarPosition() {
        const columnWidth = this.column.clientWidth;
        const columnHeight = this.column.clientHeight;
        const columnTop = this.column.offsetTop;
        const columnLeft = this.column.offsetLeft;

        // Position vertical scrollbar
        if (this.overflowY !== 'none') {
            if (this.options.top !== undefined && this.options.top !== null) {
                this.scrollbarV.style.top = `${this.options.top}px`;
            } else {
                this.scrollbarV.style.top = `${columnTop}px`;
            }

            this.scrollbarV.style.height = `${columnHeight}px`;

            if (this.options.right !== undefined && this.options.right !== null) {
                this.scrollbarV.style.right = `${this.options.right}px`;
                this.scrollbarV.style.left = 'auto';
            } else if (this.options.left !== undefined && this.options.left !== null) {
                this.scrollbarV.style.left = `${this.options.left}px`;
                this.scrollbarV.style.right = 'auto';
            } else {
                this.scrollbarV.style.left = `${columnLeft + columnWidth}px`;
                this.scrollbarV.style.right = 'auto';
            }
        }

        // Position horizontal scrollbar
        if (this.overflowX !== 'none') {
            if (this.options.bottom !== undefined && this.options.bottom !== null) {
                this.scrollbarH.style.bottom = `${this.options.bottom}px`;
                this.scrollbarH.style.top = 'auto';
            } else if (this.options.top !== undefined && this.options.top !== null) {
                this.scrollbarH.style.top = `${this.options.top + columnHeight}px`;
                this.scrollbarH.style.bottom = 'auto';
            } else {
                this.scrollbarH.style.top = `${columnTop + columnHeight}px`;
                this.scrollbarH.style.bottom = 'auto';
            }

            this.scrollbarH.style.width = `${columnWidth}px`;
            this.scrollbarH.style.left = `${columnLeft}px`;
        }
    }

    updateScrollbarThumb() {
        // Update vertical thumb
        if (this.overflowY !== 'none' && this.column.scrollHeight > this.column.clientHeight) {
            const scrollPercentageY = (this.column.scrollTop / (this.column.scrollHeight - this.column.clientHeight));
            const thumbHeight = Math.max((this.column.clientHeight / this.column.scrollHeight) * this.scrollbarV.clientHeight, 30);
            this.scrollbarThumbV.style.height = `${thumbHeight}px`;
            this.scrollbarThumbV.style.top = `${scrollPercentageY * (this.scrollbarV.clientHeight - thumbHeight)}px`;
        }

        // Update horizontal thumb
        if (this.overflowX !== 'none' && this.column.scrollWidth > this.column.clientWidth) {
            const scrollPercentageX = (this.column.scrollLeft / (this.column.scrollWidth - this.column.clientWidth));
            const thumbWidth = Math.max((this.column.clientWidth / this.column.scrollWidth) * this.scrollbarH.clientWidth, 30);
            this.scrollbarThumbH.style.width = `${thumbWidth}px`;
            this.scrollbarThumbH.style.left = `${scrollPercentageX * (this.scrollbarH.clientWidth - thumbWidth)}px`;
        }
    }

    handleWheelV(e) {
        e.preventDefault();
        const scrollAmount = e.deltaY;
        this.column.scrollTop += scrollAmount;
        this.updateScrollbarThumb();
    }

    handleWheelH(e) {
        e.preventDefault();
        const scrollAmount = e.deltaX || e.deltaY;
        this.column.scrollLeft += scrollAmount;
        this.updateScrollbarThumb();
    }

    saveScrollPosition() {
        this.column.dataset.scrollTop = this.column.scrollTop;
        this.column.dataset.scrollLeft = this.column.scrollLeft;
    }

    loadScrollPosition() {
        const savedScrollTop = this.column.dataset.scrollTop;
        const savedScrollLeft = this.column.dataset.scrollLeft;

        if (savedScrollTop !== undefined) {
            this.column.scrollTop = parseFloat(savedScrollTop);
        }
        if (savedScrollLeft !== undefined) {
            this.column.scrollLeft = parseFloat(savedScrollLeft);
        }

        this.updateScrollbarThumb();
    }

    setScrollPositionV(clientY) {
        const scrollbarRect = this.scrollbarV.getBoundingClientRect();
        const thumbHeight = this.scrollbarThumbV.offsetHeight;
        const scrollableHeight = this.scrollbarV.clientHeight - thumbHeight;
        const scrollPercentage = (clientY - scrollbarRect.top - thumbHeight / 2) / scrollableHeight;
        this.column.scrollTop = scrollPercentage * (this.column.scrollHeight - this.column.clientHeight);
    }

    setScrollPositionH(clientX) {
        const scrollbarRect = this.scrollbarH.getBoundingClientRect();
        const thumbWidth = this.scrollbarThumbH.offsetWidth;
        const scrollableWidth = this.scrollbarH.clientWidth - thumbWidth;
        const scrollPercentage = (clientX - scrollbarRect.left - thumbWidth / 2) / scrollableWidth;
        this.column.scrollLeft = scrollPercentage * (this.column.scrollWidth - this.column.clientWidth);
    }

    onMouseMoveV(e) {
        if (this.isDraggingV) {
            const deltaY = e.clientY - this.startY;
            const newThumbTop = this.startThumbTopV + deltaY;
            const scrollbarHeight = this.scrollbarV.clientHeight;
            const thumbHeight = this.scrollbarThumbV.offsetHeight;

            // Lepaskan jika kursor keluar >30px dari thumb
            const thumbRect = this.scrollbarThumbV.getBoundingClientRect();
            if (
                e.clientX < thumbRect.left - 30 ||
                e.clientX > thumbRect.right + 30 ||
                e.clientY < thumbRect.top - 30 ||
                e.clientY > thumbRect.bottom + 30
            ) {
                this.onMouseUpV();
                return;
            }

            const maxTop = scrollbarHeight - thumbHeight;
            const constrainedTop = Math.max(0, Math.min(newThumbTop, maxTop));
            const scrollPercentage = constrainedTop / maxTop;

            this.column.scrollTop = scrollPercentage * (this.column.scrollHeight - this.column.clientHeight);
            this.updateScrollbarThumb();
        }
    }

    onMouseMoveH(e) {
        if (this.isDraggingH) {
            const deltaX = e.clientX - this.startX;
            const newThumbLeft = this.startThumbLeftH + deltaX;
            const scrollbarWidth = this.scrollbarH.clientWidth;
            const thumbWidth = this.scrollbarThumbH.offsetWidth;

            // Lepaskan jika kursor keluar >30px dari thumb
            const thumbRect = this.scrollbarThumbH.getBoundingClientRect();
            if (
                e.clientX < thumbRect.left - 100 ||
                e.clientX > thumbRect.right + 100 ||
                e.clientY < thumbRect.top - 100 ||
                e.clientY > thumbRect.bottom + 100
            ) {
                this.onMouseUpH(); // lepas drag
                return;
            }

            const maxLeft = scrollbarWidth - thumbWidth;
            const constrainedLeft = Math.max(0, Math.min(newThumbLeft, maxLeft));
            const scrollPercentage = constrainedLeft / maxLeft;

            this.column.scrollLeft = scrollPercentage * (this.column.scrollWidth - this.column.clientWidth);
            this.updateScrollbarThumb();
        }
    }

    onMouseUpV() {
        this.isDraggingV = false;
        this.scrollbarThumbV.classList.remove('custom-scrollbar-thumb-dragging');
        document.removeEventListener('mousemove', this.onMouseMoveV.bind(this));
        document.removeEventListener('mouseup', this.onMouseUpV.bind(this));
        this.updateScrollbarVisibility();
        // this.scrollbarH.style.pointerEvents = 'auto';
        // document.body.classList.remove('no-scroll');
        if(document.getElementById('body-container')) {
            document.getElementById('body-container').classList.remove('no-scroll');
        }
    }

    onMouseUpH() {
        this.isDraggingH = false;
        this.scrollbarThumbH.classList.remove('custom-scrollbar-thumb-dragging');
        document.removeEventListener('mousemove', this.onMouseMoveH.bind(this));
        document.removeEventListener('mouseup', this.onMouseUpH.bind(this));
        this.updateScrollbarVisibility();
        // this.scrollbarV.style.pointerEvents = 'auto';
    }

    forceUpdate() {
        this.updateScrollbarVisibility();
        this.updateScrollbarPosition();
        this.updateScrollbarThumb();
    }

    refresh() {
        requestAnimationFrame(() => {
            this.updateScrollbarVisibility();
            this.updateScrollbarPosition();
            this.updateScrollbarThumb();
        });
    }
}