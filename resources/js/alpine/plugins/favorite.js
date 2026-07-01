function readCookie() {
    const match = document.cookie.match(/(?:^|; )favorites=([^;]*)/);

    return match ? decodeURIComponent(match[1]) : '[]';
}

function writeCookie(items) {
    document.cookie = 'favorites=' + encodeURIComponent(JSON.stringify(items))
        + '; path=/; max-age=' + (60 * 60 * 24 * 365) + '; SameSite=Lax';
}

export default function (Alpine) {
    Alpine.store('favorite', {
        items: JSON.parse(readCookie()),

        has(id) {
            return this.items.includes(id);
        },

        toggle(id) {
            const i = this.items.indexOf(id);

            if (i === -1) {
                this.items.push(id);
            } else {
                this.items.splice(i, 1);
            }

            writeCookie(this.items);
        },

        get count() {
            return this.items.length;
        },
    });

    Alpine.data('favorite', (id = '') => ({
        get active() {
            return Alpine.store('favorite').has(id);
        },

        toggle() {
            Alpine.store('favorite').toggle(id);
        },
    }));
}
