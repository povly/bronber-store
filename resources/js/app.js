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
Alpine.start()
