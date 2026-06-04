document.addEventListener('alpine:init', () => {
    Alpine.data('catalogFilters', () => ({
        mobileOpen: false,

        openSections: {
            price: true,
            brand: true,
            availability: false,
            compatibility: true,
        },

        openCategories: {
            brakes: true,
        },
        activeCategory: 'brakes',

        priceMin: 500,
        priceMax: 5000,
        rangeMin: 0,
        rangeMax: 10000,

        brandSearch: '',

        brands: [
            { id: 1, name: 'Bosch', count: 24, checked: true },
            { id: 2, name: 'DeatschWerks', count: 15, checked: false },
            { id: 3, name: 'DeatschWerks', count: 21, checked: false },
            { id: 4, name: 'Bosch', count: 6, checked: false },
            { id: 5, name: 'Bosch', count: 6, checked: false },
        ],

        compatibility: {
            mark: 'BMW',
            model: '5 серия',
            generation: '',
            engine: '',
        },

        // Custom select open state
        selectOpen: {
            mark: false,
            model: false,
            generation: false,
            engine: false,
        },

        // Select options data
        selectOptions: {
            mark: [
                { value: '', label: 'Выберите марку' },
                { value: 'BMW', label: 'BMW' },
                { value: 'Audi', label: 'Audi' },
                { value: 'Mercedes', label: 'Mercedes' },
                { value: 'Volkswagen', label: 'Volkswagen' },
            ],
            model: [
                { value: '', label: 'Выберите модель' },
                { value: '5 серия', label: '5 серия' },
                { value: '3 серия', label: '3 серия' },
                { value: '7 серия', label: '7 серия' },
                { value: 'X5', label: 'X5' },
            ],
            generation: [
                { value: '', label: 'Выберите поколение' },
                { value: 'G30', label: 'G30' },
                { value: 'F10', label: 'F10' },
                { value: 'E60', label: 'E60' },
            ],
            engine: [
                { value: '', label: 'Выберите двигатель' },
                { value: '2.0d', label: '2.0d' },
                { value: '3.0i', label: '3.0i' },
                { value: '530d', label: '530d' },
                { value: '540i', label: '540i' },
            ],
        },

        get leftPercent() {
            const total = this.rangeMax - this.rangeMin;
            return total > 0 ? ((this.priceMin - this.rangeMin) / total) * 100 : 0;
        },

        get rightPercent() {
            const total = this.rangeMax - this.rangeMin;
            return total > 0 ? ((this.priceMax - this.rangeMin) / total) * 100 : 100;
        },

        get filteredBrands() {
            if (!this.brandSearch) return this.brands;
            const q = this.brandSearch.toLowerCase();
            return this.brands.filter((b) => b.name.toLowerCase().includes(q));
        },

        getSelectedLabel(field) {
            const val = this.compatibility[field];
            if (!val) return this.selectOptions[field][0].label;
            const opt = this.selectOptions[field].find(o => o.value === val);
            return opt ? opt.label : this.selectOptions[field][0].label;
        },

        selectOption(field, value) {
            this.compatibility[field] = value;
            this.selectOpen[field] = false;
        },

        toggleSelect(field) {
            const wasOpen = this.selectOpen[field];
            Object.keys(this.selectOpen).forEach(k => this.selectOpen[k] = false);
            this.selectOpen[field] = !wasOpen;
        },

        closeSelect(field) {
            this.selectOpen[field] = false;
        },

        toggleSection(key) {
            this.openSections[key] = !this.openSections[key];
        },

        isSectionOpen(key) {
            return this.openSections[key] === true;
        },

        toggleCategory(key) {
            if (this.openCategories[key]) {
                this.openCategories[key] = false;
            } else {
                this.openCategories[key] = true;
                this.activeCategory = key;
            }
        },

        toggleBrand(brandId) {
            const brand = this.brands.find((b) => b.id === brandId);
            if (brand) {
                brand.checked = !brand.checked;
            }
        },

        openMobile() {
            this.mobileOpen = true;
            document.body.style.overflow = 'hidden';
        },

        closeMobile() {
            this.mobileOpen = false;
            document.body.style.overflow = '';
        },

        onTrackClick(event) {
            const track = this.$refs.track;
            if (!track) return;
            const rect = track.getBoundingClientRect();
            const percent = Math.max(0, Math.min(100, ((event.clientX - rect.left) / rect.width) * 100));
            const value = Math.round(this.rangeMin + (percent / 100) * (this.rangeMax - this.rangeMin));
            const distToLeft = Math.abs(value - this.priceMin);
            const distToRight = Math.abs(value - this.priceMax);
            if (distToLeft <= distToRight) {
                this.priceMin = Math.min(value, this.priceMax);
            } else {
                this.priceMax = Math.max(value, this.priceMin);
            }
        },

        startDrag(side, event) {
            event.preventDefault();
            const track = this.$refs.track;
            if (!track) return;
            const rect = track.getBoundingClientRect();

            const onMove = (e) => {
                e.preventDefault();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const percent = Math.max(0, Math.min(100, ((clientX - rect.left) / rect.width) * 100));
                const value = Math.round(this.rangeMin + (percent / 100) * (this.rangeMax - this.rangeMin));

                if (side === 'left') {
                    const clampedValue = Math.min(value, this.priceMax);
                    const clampedPercent = ((clampedValue - this.rangeMin) / (this.rangeMax - this.rangeMin)) * 100;
                    track.style.setProperty('--range-left', clampedPercent + '%');
                    this._pendingMin = clampedValue;
                } else {
                    const clampedValue = Math.max(value, this.priceMin);
                    const clampedPercent = ((clampedValue - this.rangeMin) / (this.rangeMax - this.rangeMin)) * 100;
                    track.style.setProperty('--range-right', clampedPercent + '%');
                    this._pendingMax = clampedValue;
                }
            };

            const onUp = () => {
                if (side === 'left' && this._pendingMin !== undefined) {
                    this.priceMin = this._pendingMin;
                    delete this._pendingMin;
                }
                if (side === 'right' && this._pendingMax !== undefined) {
                    this.priceMax = this._pendingMax;
                    delete this._pendingMax;
                }
                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup', onUp);
                document.removeEventListener('touchmove', onMove);
                document.removeEventListener('touchend', onUp);
            };

            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onUp);
            document.addEventListener('touchmove', onMove, { passive: false });
            document.addEventListener('touchend', onUp);
        },

        resetFilters() {
            this.priceMin = this.rangeMin;
            this.priceMax = this.rangeMax;
            this.brands.forEach((b) => (b.checked = false));
            this.brandSearch = '';
            this.compatibility = { mark: '', model: '', generation: '', engine: '' };
        },
    }));
});
