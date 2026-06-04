@push('block-styles')
    @vite(['resources/blocks/common/footer/style.css'])
@endpush

<footer class="site-footer">
    <div class="container">
        <div class="site-footer__inner">
            <div class="site-footer__top">
                <a href="/" class="site-footer__logo">
                    <svg width="134" height="21" viewBox="0 0 207 32" fill="none">
                        <text x="0" y="25" fill="white" font-family="Manrope" font-weight="700" font-size="28">BRONBER</text>
                    </svg>
                </a>

                <div class="site-footer__contacts">
                    <a href="tel:+79854498000" class="site-footer__contact">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.79 19.79 0 012.12 4.18 2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>+7 (985) 449-8000</span>
                    </a>
                    <a href="mailto:info@bronber.com" class="site-footer__contact">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M22 6l-10 7L2 6" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>info@bronber.com</span>
                    </a>
                </div>

                <div class="site-footer__social">
                    <span class="site-footer__social-label">Соц.сети</span>
                    <a href="#" class="site-footer__social-link" aria-label="Instagram">
                        <svg width="27" height="27" viewBox="0 0 24 24" fill="none"><rect x="2" y="2" width="20" height="20" rx="5" stroke="white" stroke-width="1.5"/><circle cx="12" cy="12" r="5" stroke="white" stroke-width="1.5"/><circle cx="17.5" cy="6.5" r="1.5" fill="white"/></svg>
                    </a>
                    <a href="#" class="site-footer__social-link" aria-label="YouTube">
                        <svg width="31" height="20" viewBox="0 0 24 24" fill="none"><path d="M23.5 6.5s-.2-1.6-.9-2.3c-.9-.9-1.8-.9-2.3-1C17 2.9 12 3 12 3s-5 0-8.3.2c-.5.1-1.4.1-2.3 1-.7.7-.9 2.3-.9 2.3S.2 8.4.2 10.3v1.8c0 1.9.3 3.8.3 3.8s.2 1.6.9 2.3c.9.9 2 .9 2.5 1 1.8.2 7.6.2 7.6.2s5 0 8.3-.3c.5-.1 1.4-.1 2.3-1 .7-.7.9-2.3.9-2.3s.3-1.9.3-3.8v-1.8c0-1.9-.3-3.8-.3-3.8z" fill="white"/><path d="M9.5 15V8l6.5 3.5-6.5 3.5z" fill="#222"/></svg>
                    </a>
                </div>
            </div>

            <div class="site-footer__columns">
                <div class="site-footer__column" x-data="{ open: false }">
                    <button class="site-footer__column-header" type="button" @click="open = !open">
                        <span>Каталог</span>
                        <svg class="site-footer__chevron" :class="{ 'site-footer__chevron--open': open }" width="17" height="15" viewBox="0 0 17 15" fill="none"><path d="M1 1l7.5 12L16 1" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <ul class="site-footer__column-list" x-show="open" x-collapse>
                        <li><a href="#">Двигатель</a></li>
                        <li><a href="#">Тормозная система</a></li>
                        <li><a href="#">Фильтры</a></li>
                        <li><a href="#">Подвеска</a></li>
                        <li><a href="#">Электрика</a></li>
                        <li><a href="#">Аксессуары</a></li>
                    </ul>
                </div>

                <div class="site-footer__column" x-data="{ open: false }">
                    <button class="site-footer__column-header" type="button" @click="open = !open">
                        <span>Покупателям</span>
                        <svg class="site-footer__chevron" :class="{ 'site-footer__chevron--open': open }" width="17" height="15" viewBox="0 0 17 15" fill="none"><path d="M1 1l7.5 12L16 1" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <ul class="site-footer__column-list" x-show="open" x-collapse>
                        <li><a href="#">Доставка и оплата</a></li>
                        <li><a href="#">Гарантия и возврат</a></li>
                        <li><a href="#">Программа лояльности</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>

                <div class="site-footer__column" x-data="{ open: false }">
                    <button class="site-footer__column-header" type="button" @click="open = !open">
                        <span>Компания</span>
                        <svg class="site-footer__chevron" :class="{ 'site-footer__chevron--open': open }" width="17" height="15" viewBox="0 0 17 15" fill="none"><path d="M1 1l7.5 12L16 1" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <ul class="site-footer__column-list" x-show="open" x-collapse>
                        <li><a href="#">О компании</a></li>
                        <li><a href="#">Блог</a></li>
                        <li><a href="#">Новости</a></li>
                        <li><a href="#">Карьера</a></li>
                    </ul>
                </div>

                <div class="site-footer__column" x-data="{ open: false }">
                    <button class="site-footer__column-header" type="button" @click="open = !open">
                        <span>Контакты</span>
                        <svg class="site-footer__chevron" :class="{ 'site-footer__chevron--open': open }" width="17" height="15" viewBox="0 0 17 15" fill="none"><path d="M1 1l7.5 12L16 1" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <ul class="site-footer__column-list" x-show="open" x-collapse>
                        <li><a href="tel:+79854498000">+7 (985) 449-8000</a></li>
                        <li><a href="mailto:info@bronber.com">info@bronber.com</a></li>
                        <li><a href="#">Обратная связь</a></li>
                    </ul>
                </div>
            </div>

            <div class="site-footer__auth">
                <a href="#" class="site-footer__auth-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Регистрация | Вход</span>
                </a>
            </div>

            <div class="site-footer__payment">
                <span class="site-footer__payment-label">MC</span>
                <span class="site-footer__payment-label">Visa</span>
            </div>

            <div class="site-footer__divider"></div>

            <div class="site-footer__bottom">
                <p class="site-footer__legal"><a href="#">Политика конфиденциальности</a></p>
                <p class="site-footer__legal"><a href="#">Пользовательское соглашение</a></p>
                <p class="site-footer__legal">&copy; 2026. Все права защищены</p>
                <p class="site-footer__legal">Сайт разработан командой Павла Климаш</p>
            </div>
        </div>
    </div>
</footer>
