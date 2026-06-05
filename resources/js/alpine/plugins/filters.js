export default function (Alpine) {
    Alpine.data('filters', () => ({
        submit(form) {
            const params = new URLSearchParams();

            for (const [name, value] of new FormData(form).entries()) {
                const trimmed = String(value).trim();
                if (trimmed === '') continue;
                params.set(name, trimmed);
            }

            const qs = params.toString();
            window.location.href = form.action + (qs ? '?' + qs : '');
        },
    }))
}
