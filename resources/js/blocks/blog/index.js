document.addEventListener('alpine:init', () => {
    Alpine.data('blog', (options) => ({
        page: 1,
        hasMore: !!(options && options.hasMore),
        loading: false,
        cardsUrl: (options && options.cardsUrl) || '/blog/cards',

        // The next batch comes from the server over AJAX — only the first
        // page (3 cards) is server-rendered. XMLHttpRequest instead of
        // fetch: the project targets IE11, where fetch is unavailable.
        showMore() {
            if (this.loading || !this.hasMore) {
                return;
            }

            var self = this;
            this.loading = true;

            var request = new XMLHttpRequest();
            request.open('GET', this.cardsUrl + '?page=' + (this.page + 1), true);
            request.onreadystatechange = function () {
                if (request.readyState !== 4) {
                    return;
                }

                self.loading = false;

                if (request.status !== 200) {
                    return;
                }

                var data = null;

                try {
                    data = JSON.parse(request.responseText);
                } catch (error) {
                    return;
                }

                if (data === null || !data.html) {
                    return;
                }

                self.$refs.list.insertAdjacentHTML('beforeend', data.html);
                self.page += 1;
                self.hasMore = !!data.has_more;
                self.refreshArticleHeights();
            };
            request.send();
        },

        // Equal-heights recalc for the appended cards. The Event
        // constructor is missing in IE11 — fall back to createEvent.
        refreshArticleHeights() {
            var event;

            if (typeof Event === 'function') {
                event = new Event('article-heights:refresh');
            } else {
                event = document.createEvent('Event');
                event.initEvent('article-heights:refresh', true, true);
            }

            window.dispatchEvent(event);
        },
    }));
});
