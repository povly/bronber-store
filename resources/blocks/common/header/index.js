document.addEventListener('alpine:init', () => {
    Alpine.data('storeHeader', (searchTypes = []) => ({
        menuOpen: false,
        searchDropdownOpen: false,
        searchType: searchTypes[0]?.value ?? 'name',
        searchTypes: searchTypes,

        get searchTypeLabel() {
            const type = this.searchTypes.find(t => t.value === this.searchType);
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
