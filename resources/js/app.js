import.meta.glob(['../images/**', '../fonts/**']);

import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'
import modal from './alpine/plugins/modal'
import slider from './alpine/plugins/slider'
import filters from './alpine/plugins/filters'

import '../js/blocks/common/header/index.js'
import '../js/blocks/catalog/filters/index.js'
import '../js/blocks/catalog/hero/index.js'

Alpine.plugin(collapse)
Alpine.plugin(modal)
Alpine.plugin(slider)
Alpine.plugin(filters)

window.Alpine = Alpine

Alpine.start()
