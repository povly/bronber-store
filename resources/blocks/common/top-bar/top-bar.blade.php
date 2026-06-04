@push('block-styles')
    @vite(['resources/blocks/common/top-bar/style.css'])
@endpush

<div class="top-bar">
    <div class="container">
        <div class="top-bar__inner">
            <a href="tel:+79854498000" class="top-bar__phone">
                <svg class="top-bar__phone-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                +7 (985) 449-8000
            </a>

            <div class="top-bar__separator"></div>

            <nav class="top-bar__links">
                <a href="#" class="top-bar__link">Доставка и оплата</a>
                <a href="#" class="top-bar__link">Гарантия и возврат</a>
                <a href="#" class="top-bar__link">Карьера</a>
                <a href="#" class="top-bar__link">FAQ</a>
                <a href="#" class="top-bar__link">Контакты</a>
            </nav>

            <div class="top-bar__lang">
                <button class="top-bar__lang-btn is-active">RU</button>
                <span class="top-bar__lang-divider">|</span>
                <button class="top-bar__lang-btn">EN</button>
            </div>
        </div>
    </div>
</div>
