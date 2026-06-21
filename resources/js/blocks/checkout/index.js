document.addEventListener('alpine:init', () => {
    Alpine.data('checkoutForm', () => ({
        callback: 'no',
        delivery: 'branch',
        payment: 'online',
        phone: '',

        formatPhone(e) {
            // Simple Russian phone mask: +7 (XXX) XXX-XX-XX
            let digits = e.target.value.replace(/\D/g, '').replace(/^[78]/, '');

            if (digits.length > 10) {
                digits = digits.slice(0, 10);
            }

            let formatted = '+7';

            if (digits.length > 0) {
                formatted += ' (' + digits.slice(0, 3);
            }

            if (digits.length >= 3) {
                formatted += ') ' + digits.slice(3, 6);
            }

            if (digits.length >= 6) {
                formatted += '-' + digits.slice(6, 8);
            }

            if (digits.length >= 8) {
                formatted += '-' + digits.slice(8, 10);
            }

            this.phone = formatted;
        },

        onSubmit() {
            // Allow real form submission (server-side handler added later)
            this.$el.submit();
        },
    }));
});
