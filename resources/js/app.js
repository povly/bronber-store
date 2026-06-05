import.meta.glob(['../images/**', '../fonts/**']);

import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'
import modal from './alpine/plugins/modal'
import slider from './alpine/plugins/slider'

import '../js/blocks/common/header/index.js'
import '../js/blocks/catalog/filters/index.js'
import '../js/blocks/catalog/hero/index.js'

Alpine.plugin(collapse)
Alpine.plugin(modal)
Alpine.plugin(slider)

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
