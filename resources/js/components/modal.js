document.addEventListener('alpine:init', () => {
    Alpine.store('modal', {
        active: null,

        show(id) {
            this.active = id
            document.body.style.overflow = 'hidden'
            document.dispatchEvent(new CustomEvent('modalOpen', { detail: { id } }))
        },

        hide() {
            const prev = this.active
            this.active = null
            document.body.style.overflow = ''
            document.dispatchEvent(new CustomEvent('modalClose', { detail: { id: prev } }))
        },

        isOpen(id) {
            return this.active === id
        },
    })
})

window.Modal = {
    showModal(el) {
        if (!el) return
        const id = [...el.classList].find(c => c.startsWith('modal_'))?.replace('modal_', '')
        if (id) Alpine.store('modal').show(id)
    },
    hideModal(el) {
        if (!el) return
        Alpine.store('modal').hide()
    },
}

document.addEventListener('click', (e) => {
    const trigger = e.target.closest('.modal__show')
    if (trigger) {
        e.preventDefault()
        const id = trigger.getAttribute('data-modal')
        if (id) Alpine.store('modal').show(id)
        return
    }

    const closeBtn = e.target.closest('.modal__close')
    if (closeBtn) {
        Alpine.store('modal').hide()
        return
    }

    const modalLink = e.target.closest('.modal_menu a')
    if (modalLink) {
        Alpine.store('modal').hide()
        return
    }

    const backBtn = e.target.closest('.modal__back')
    if (backBtn) {
        Alpine.store('modal').hide()
        return
    }

    const overlay = e.target.closest('.modal__ceil')
    if (overlay && e.target === overlay) {
        Alpine.store('modal').hide()
        return
    }
})