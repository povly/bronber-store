import.meta.glob(['../images/**', '../fonts/**']);

import Alpine from 'alpinejs'
import modal from './alpine/plugins/modal'

Alpine.plugin(modal)

window.Alpine = Alpine
Alpine.start()
