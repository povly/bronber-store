document.addEventListener('alpine:init', () => {
    Alpine.data('product', () => ({
        tab: 'description',
        favorited: false,
    }));
});
