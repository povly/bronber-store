export default function (Alpine) {
    Alpine.store('modal', {
        stack: [],

        show(id, options = {}) {
            if (id) {
                console.debug('[modal] show: ' + id)
            }
            if (options.replace) {
                this.stack = [id]
            } else if (!this.stack.includes(id)) {
                this.stack = [...this.stack, id]
            }
        },

        hide(id) {
            console.debug('[modal] hide: ' + (id || 'top'))
            if (id) {
                this.stack = this.stack.filter(i => i !== id)
            } else {
                this.stack = this.stack.slice(0, -1)
            }
        },

        hideAll() {
            this.stack = []
        },

        isOpen(id) {
            return this.stack.includes(id)
        },

        isTop(id) {
            return this.stack.length > 0 && this.stack[this.stack.length - 1] === id
        },

        depth(id) {
            const index = this.stack.indexOf(id)
            return index === -1 ? 0 : index + 1
        },
    })

    Alpine.magic('modal', () => ({
        show(id, options) {
            Alpine.store('modal').show(id, options)
        },
        hide(id) {
            Alpine.store('modal').hide(id)
        },
        hideAll() {
            Alpine.store('modal').hideAll()
        },
        isOpen(id) {
            return Alpine.store('modal').isOpen(id)
        },
        isTop(id) {
            return Alpine.store('modal').isTop(id)
        },
        depth(id) {
            return Alpine.store('modal').depth(id)
        },
    }))
}
