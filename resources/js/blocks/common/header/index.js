document.addEventListener('alpine:init', () => {
    Alpine.data('storeHeader', (searchTypes = []) => ({
        menuOpen: false,
        searchDropdownOpen: false,
        searchType: searchTypes[0]?.value ?? 'name',
        isDesktop: window.innerWidth >= 1200,

        init() {
            window.addEventListener('resize', () => {
                this.isDesktop = window.innerWidth >= 1200;
            });
        },

        get searchTypeLabel() {
            const type = searchTypes.find(t => t.value === this.searchType);
            return type ? type.label : '';
        },

        selectSearchType(type) {
            this.searchType = type;
            this.searchDropdownOpen = false;
            this.$nextTick(() => {
                this.$refs.searchInput.focus();
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
