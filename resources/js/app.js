import.meta.glob(['../images/**', '../fonts/**']);

import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'
import modal from './alpine/plugins/modal'

// Block Alpine components — must register BEFORE Alpine.start()
import '../blocks/common/header/index.js'
import '../blocks/catalog/filters/index.js'
import '../blocks/catalog/hero/index.js'

Alpine.plugin(collapse)
Alpine.plugin(modal)

window.Alpine = Alpine

window.submitFilters = function (event) {
    event.preventDefault();
    const form = event.target;
    const params = new URLSearchParams();

    for (const [name, value] of new FormData(form).entries()) {
        const trimmed = String(value).trim();
        if (trimmed === '') continue;
        params.set(name, trimmed);
    }

    const qs = params.toString();
    window.location.href = form.action + (qs ? '?' + qs : '');
};

Alpine.start()
