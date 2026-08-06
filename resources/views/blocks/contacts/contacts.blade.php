@push('block-styles')
    @vite(['resources/css/blocks/contacts/style.css'])
@endpush

<section class="contacts">
    <div class="container">
        <div class="contacts__top">
            <div class="contacts__info">
                <h1 class="contacts__title">Открыты<br>к деловому<br>диалогу</h1>
                <p class="contacts__subtitle">Оставьте свою заявку и наш менеджер свяжется с вами в ближайшее время</p>
                <a href="tel:+79854498000" class="contacts__phone">+7 (985) 449-8000</a>
                <div class="contacts__address">
                    <a href="mailto:info@bronber.ru">info@bronber.ru</a>
                    <span>Москва, Пресненская набережная 12</span>
                </div>
            </div>

            <form class="contacts__form" action="" method="POST">
                @csrf
                <div class="contacts__field">
                    <label class="contacts__label" for="name">Ваше имя*</label>
                    <input class="contacts__input" type="text" id="name" name="name"
                        placeholder="Андрей Андреевич" required>
                </div>
                <div class="contacts__row">
                    <div class="contacts__field">
                        <label class="contacts__label" for="phone">Ваш номер телефона*</label>
                        <input class="contacts__input" type="tel" id="phone" name="phone"
                            placeholder="+7 (___) ___-__-__" required>
                    </div>
                    <div class="contacts__field">
                        <label class="contacts__label" for="email">Ваш e-mail*</label>
                        <input class="contacts__input" type="email" id="email" name="email"
                            placeholder="example@mail.ru" required>
                    </div>
                </div>
                <div class="contacts__field">
                    <label class="contacts__label" for="message">Тема обращения</label>
                    <textarea class="contacts__textarea" id="message" name="message" rows="1"
                        placeholder="Хочу арендовать авто, но нужен ваш совет что выбрать"></textarea>
                </div>
                <div class="contacts__bottom">
                    <button type="submit" class="contacts__submit btn btn--primary">Отправить</button>
                    <p class="contacts__privacy">Нажимая кнопку «Отправить» вы соглашаетесь с условиями обработки данных
                    </p>
                </div>
            </form>
        </div>

        <div class="contacts__map">
            <iframe src="https://yandex.ru/map-widget/v1/?ll=37.539822%2C55.748792&z=15" width="100%" height="100%"
                frameborder="0" loading="lazy"></iframe>
        </div>
    </div>
</section>
