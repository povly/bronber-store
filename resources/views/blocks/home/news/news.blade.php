@push('block-styles')
    @vite(['resources/css/blocks/home/news/style.css'])
@endpush

<section class="home-news">
    <div class="container">
        <div class="home-news__header">
            <h2 class="home-news__title">Новости</h2>
            <a href="/news" class="home-news__all-link">
                Все новости
                <svg class="home-news__all-link-icon" width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </div>

        <div class="home-news__list">
            <article class="home-news__card">
                <div class="home-news__card-image">
                    <img data-src="https://placehold.co/464x300/eaeaea/bfbfbf?text=News" alt="Запуск нового направления BRONBER Auto Service" class="lazy" width="464" height="300">
                </div>
                <div class="home-news__card-body">
                    <div class="home-news__card-meta">
                        <span class="home-news__card-tag">#запуск</span>
                        <span class="home-news__card-date">25/12/25</span>
                    </div>
                    <h3 class="home-news__card-title">Запуск нового направления BRONBER Auto Service</h3>
                    <p class="home-news__card-desc">Открываем новое направление в сфере автосервиса. Современное оборудование, квалифицированные специалисты и широкий спектр услуг для вашего автомобиля.</p>
                    <a href="#" class="home-news__card-link">
                        Подробнее
                        <svg class="home-news__card-link-icon" width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>
            </article>

            <article class="home-news__card">
                <div class="home-news__card-image">
                    <img data-src="https://placehold.co/464x300/eaeaea/bfbfbf?text=News" alt="Открытие нового магазина автозапчастей" class="lazy" width="464" height="300">
                </div>
                <div class="home-news__card-body">
                    <div class="home-news__card-meta">
                        <span class="home-news__card-tag">#событие</span>
                        <span class="home-news__card-date">18/12/25</span>
                    </div>
                    <h3 class="home-news__card-title">Открытие нового магазина автозапчастей</h3>
                    <p class="home-news__card-desc">Рады сообщить об открытии нового магазина. Более 50 000 наименований запчастей в наличии и под заказ с быстрой доставкой.</p>
                    <a href="#" class="home-news__card-link">
                        Подробнее
                        <svg class="home-news__card-link-icon" width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>
            </article>

            <article class="home-news__card">
                <div class="home-news__card-image">
                    <img data-src="https://placehold.co/464x300/eaeaea/bfbfbf?text=News" alt="Новое партнерство с ведущими производителями" class="lazy" width="464" height="300">
                </div>
                <div class="home-news__card-body">
                    <div class="home-news__card-meta">
                        <span class="home-news__card-tag">#партнерство</span>
                        <span class="home-news__card-date">10/12/25</span>
                    </div>
                    <h3 class="home-news__card-title">Новое партнерство с ведущими производителями</h3>
                    <p class="home-news__card-desc">Заключили соглашения с крупнейшими мировыми производителями автозапчастей. Теперь в ассортименте ещё больше оригинальных деталей.</p>
                    <a href="#" class="home-news__card-link">
                        Подробнее
                        <svg class="home-news__card-link-icon" width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>
            </article>
        </div>
    </div>
</section>
