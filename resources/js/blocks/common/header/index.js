document.addEventListener('alpine:init', () => {
    Alpine.data('storeHeader', (searchTypes = []) => ({
        menuOpen: false,
        searchDropdownOpen: false,
        searchType: searchTypes[0]?.value ?? 'name',

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

        toggleMenu() {
            this.menuOpen = !this.menuOpen;
            document.body.style.overflow = this.menuOpen ? 'hidden' : '';
        },

        closeMenu() {
            this.menuOpen = false;
            document.body.style.overflow = '';
        },
    }));
});
