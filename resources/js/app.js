import.meta.glob(['../images/**', '../fonts/**']);

import Alpine from 'alpinejs'

import '../js/blocks/common/header/index.js'

import collapse from '@alpinejs/collapse'
import modal from './alpine/plugins/modal'
import slider from './alpine/plugins/slider'
import filters from './alpine/plugins/filters'
import scrollable from './alpine/plugins/scrollable'
import qty from './alpine/plugins/qty'

Alpine.plugin(collapse)
Alpine.plugin(modal)
Alpine.plugin(slider)
Alpine.plugin(filters)
Alpine.plugin(scrollable)
Alpine.plugin(qty)

window.Alpine = Alpine

document.addEventListener('DOMContentLoaded', () => {
    Alpine.start()
    
    function setArticleBtnHeight() {
        document.querySelectorAll('.article').forEach(article => {
            const btn = article.querySelector('.article__more')
            if (btn) {
                article.style.setProperty('--article-btn-height', `${btn.offsetHeight}px`)
            }
        })
    }
    
    setArticleBtnHeight()
    window.addEventListener('resize', setArticleBtnHeight)
})
