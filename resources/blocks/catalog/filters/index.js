document.addEventListener('alpine:init', () => {
    Alpine.data('catalogFilters', (data = {}) => ({
        mobileOpen: false,
        _ready: false,

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

        priceMin: data.priceMin ?? 0,
        priceMax: data.priceMax ?? 10000,
        rangeMin: data.rangeMin ?? 0,
        rangeMax: data.rangeMax ?? 10000,

        brandSearch: '',
        brandShowAll: false,
        visibleCount: data.visibleCount ?? 4,

        availability: data.availability || { in_stock: false, to_order: false },

        brands: data.brands || [],

        compatibility: data.compatibility || { mark: '', model: '', generation: '', engine: '' },

        selectOpen: {
            mark: false,
            model: false,
            generation: false,
            engine: false,
        },

        selectFlip: {
            mark: false,
            model: false,
            generation: false,
            engine: false,
        },

        selectOptions: data.selectOptions || {},

        init() {
            this.applyUrlParams();
            this.$nextTick(() => {
                this._ready = true;
                this.emitChips();
            });

            window.addEventListener('chip-remove', (e) => {
                this.removeChipByData(e.detail);
                this.emitChips();
                this.$nextTick(() => this.submitForm());
            });

            window.addEventListener('chip-clear', () => {
                this.clearAll();
                this.emitChips();
                this.$nextTick(() => this.submitForm());
            });
        },

        applyUrlParams() {
            const params = new URLSearchParams(window.location.search);

            if (params.has('price_min')) this.priceMin = Number(params.get('price_min'));
            if (params.has('price_max')) this.priceMax = Number(params.get('price_max'));

            if (params.has('brand')) {
                const activeBrands = params.get('brand').split(',');
                this.brands.forEach(b => {
                    b.checked = activeBrands.includes(b.name);
                });
            }

            if (params.has('availability')) {
                const keys = params.get('availability').split(',');
                this.availability.in_stock = keys.includes('in_stock');
                this.availability.to_order = keys.includes('to_order');
            }

            ['mark', 'model', 'generation', 'engine'].forEach(field => {
                if (params.has(field) && params.get(field) !== '') {
                    this.compatibility[field] = params.get(field);
                }
            });

            if (this.availability.in_stock || this.availability.to_order) {
                this.openSections.availability = true;
            }
            if (Object.values(this.compatibility).some(v => v !== '')) {
                this.openSections.compatibility = true;
            }
        },

        buildChips() {
            const chips = [];

            this.brands.filter(b => b.checked).forEach(b => {
                chips.push({ id: 'brand-' + b.name, label: b.name, type: 'brand', value: b.name });
            });

            if (this.priceMin > this.rangeMin || this.priceMax < this.rangeMax) {
                chips.push({
                    id: 'price-range',
                    label: `От ${this.priceMin} до ${this.priceMax}₽`,
                    type: 'price',
                });
            }

            if (this.availability.in_stock) {
                chips.push({ id: 'avail-in_stock', label: 'В наличии', type: 'availability', value: 'in_stock' });
            }
            if (this.availability.to_order) {
                chips.push({ id: 'avail-to_order', label: 'Под заказ', type: 'availability', value: 'to_order' });
            }

            Object.entries(this.compatibility).forEach(([field, value]) => {
                if (value) {
                    const labels = { mark: 'Марка', model: 'Модель', generation: 'Поколение', engine: 'Двигатель' };
                    chips.push({ id: 'compat-' + field, label: `${labels[field]}: ${value}`, type: 'compatibility', field });
                }
            });

            return chips;
        },

        emitChips() {
            window.dispatchEvent(new CustomEvent('filters-chips', { detail: { chips: this.buildChips() } }));
        },

        removeChipByData(chipData) {
            switch (chipData.type) {
                case 'brand': {
                    const brand = this.brands.find(b => b.name === chipData.value);
                    if (brand) brand.checked = false;
                    break;
                }
                case 'price':
                    this.priceMin = this.rangeMin;
                    this.priceMax = this.rangeMax;
                    break;
                case 'availability':
                    this.availability[chipData.value] = false;
                    break;
                case 'compatibility':
                    this.compatibility[chipData.field] = '';
                    break;
            }
        },

        clearAll() {
            this.brands.forEach(b => b.checked = false);
            this.priceMin = this.rangeMin;
            this.priceMax = this.rangeMax;
            this.availability = { in_stock: false, to_order: false };
            this.compatibility = { mark: '', model: '', generation: '', engine: '' };
        },

        submitForm() {
            if (!this._ready) return;
            const form = this.$el.closest('form');
            if (!form) return;
            form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        },

        get leftPercent() {
            const total = this.rangeMax - this.rangeMin;
            return total > 0 ? ((this.priceMin - this.rangeMin) / total) * 100 : 0;
        },

        get rightPercent() {
            const total = this.rangeMax - this.rangeMin;
            return total > 0 ? ((this.priceMax - this.rangeMin) / total) * 100 : 100;
        },

        onPriceChange(side) {
            if (side === 'min') {
                const val = Number(this.priceMin);
                this.priceMin = isNaN(val)
                    ? this.rangeMin
                    : Math.max(this.rangeMin, Math.min(val, this.priceMax));
            } else {
                const val = Number(this.priceMax);
                this.priceMax = isNaN(val)
                    ? this.rangeMax
                    : Math.min(this.rangeMax, Math.max(val, this.priceMin));
            }
            this.emitChips();
        },

        get filteredBrands() {
            if (!this.brandSearch) return this.brands;
            const q = this.brandSearch.toLowerCase();
            return this.brands.filter((b) => b.name.toLowerCase().includes(q));
        },

        get visibleBrands() {
            const list = this.brandSearch ? this.filteredBrands : this.brands;
            if (this.brandShowAll || this.brandSearch) return list;
            return list.slice(0, this.visibleCount);
        },

        get checkedBrandNames() {
            return this.brands.filter(b => b.checked).map(b => b.name).join(',');
        },

        getBrand(id) {
            return this.brands.find(b => b.id === id);
        },

        get visibleBrandIds() {
            const list = this.brandSearch
                ? this.brands.filter(b => b.name.toLowerCase().includes(this.brandSearch.toLowerCase()))
                : this.brands;
            const sliced = (this.brandShowAll || this.brandSearch) ? list : list.slice(0, this.visibleCount);
            return new Set(sliced.map(b => b.id));
        },

        isBrandVisible(brandId) {
            return this.visibleBrandIds.has(brandId);
        },

        get checkedAvailabilityKeys() {
            return Object.entries(this.availability).filter(([, v]) => v).map(([k]) => k).join(',');
        },

        getSelectedLabel(field) {
            const val = this.compatibility[field];
            if (!val) return (this.selectOptions[field] || [])[0]?.label || '';
            const opt = (this.selectOptions[field] || []).find(o => o.value === val);
            return opt ? opt.label : (this.selectOptions[field] || [])[0]?.label || '';
        },

        selectOption(field, value) {
            this.compatibility[field] = value;
            this.selectOpen[field] = false;
            this.emitChips();
        },

        toggleSelect(field) {
            const wasOpen = this.selectOpen[field];
            Object.keys(this.selectOpen).forEach(k => this.selectOpen[k] = false);
            this.selectOpen[field] = !wasOpen;

            if (!wasOpen) {
                this.$nextTick(() => {
                    const refKey = 'select' + field.charAt(0).toUpperCase() + field.slice(1);
                    const el = this.$refs[refKey];
                    if (!el) return;
                    const rect = el.getBoundingClientRect();
                    const spaceBelow = window.innerHeight - rect.bottom;
                    this.selectFlip[field] = spaceBelow < 200;
                });
            }
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
            this.emitChips();
        },

        toggleAvailability(key) {
            this.availability[key] = !this.availability[key];
            this.emitChips();
        },

        openMobile() {
            this.mobileOpen = true;
            document.body.style.overflow = 'hidden';
        },

        closeMobile() {
            this.mobileOpen = false;
            document.body.style.overflow = '';
        },

        applyFilters() {
            this.closeMobile();
            this.$nextTick(() => this.submitForm());
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
            this.emitChips();
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
                this.emitChips();
            };

            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onUp);
            document.addEventListener('touchmove', onMove, { passive: false });
            document.addEventListener('touchend', onUp);
        },

        resetFilters() {
            this.clearAll();
            this.brandSearch = '';
            this.$nextTick(() => this.submitForm());
        },
    }));
});
