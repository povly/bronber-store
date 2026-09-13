document.addEventListener('alpine:init', () => {
    Alpine.data('storeHeader', (searchTypes = [], catalogUrl = '/catalog') => ({
        menuOpen: false,
        searchDropdownOpen: false,
        searchType: searchTypes[0]?.value ?? 'name',
        catalogUrl: catalogUrl,

        get searchTypeLabel() {
            const type = searchTypes.find(t => t.value === this.searchType);
            return type ? type.label : '';
        },

        selectSearchType(type) {
            this.searchType = type;
            this.searchDropdownOpen = false;
            this.$nextTick(() => {
                const desktop = this.$refs.searchInputDesktop;
                const mobile = this.$refs.searchInputMobile;
                if (desktop && getComputedStyle(desktop).display !== 'none') {
                    desktop.focus();
                } else if (mobile) {
                    mobile.focus();
                }
            });
        },

        toggleSearchDropdown() {
            this.searchDropdownOpen = !this.searchDropdownOpen;
        },

        closeSearchDropdown() {
            this.searchDropdownOpen = false;
        },

        submitSearch() {
            var desktop = this.$refs.searchInputDesktop;
            var mobile = this.$refs.searchInputMobile;
            var query = '';

            if (desktop && getComputedStyle(desktop).display !== 'none') {
                query = (desktop.value || '').trim();
            } else if (mobile) {
                query = (mobile.value || '').trim();
            }

            if (!query) {
                return;
            }

            console.debug('[storeHeader] search submitted, type=' + this.searchType);

            window.location.assign(
                this.catalogUrl
                    + '?search=' + encodeURIComponent(query)
                    + '&search_type=' + encodeURIComponent(this.searchType)
            );
        },

        toggleMenu() {
            this.menuOpen = !this.menuOpen;
            document.body.style.overflow = this.menuOpen ? 'hidden' : '';
            console.debug('[storeHeader] menu ' + (this.menuOpen ? 'opened' : 'closed'));
        },

        closeMenu() {
            this.menuOpen = false;
            document.body.style.overflow = '';
            console.debug('[storeHeader] menu closed');
        },
    }));
});
