@push('block-styles')
    @vite(['resources/css/blocks/checkout/style.css'])
@endpush

@push('block-scripts')
    @vite(['resources/js/blocks/checkout/index.js'])
@endpush

<section class="checkout" x-data="checkoutForm()">
    <div class="container">
        <x-breadcrumbs
            class="checkout__breadcrumb"
            :items="[
                ['label' => 'Главная', 'url' => '/'],
                ['label' => 'Корзина', 'url' => '/cart'],
                ['label' => 'Оформление заказа'],
            ]"
        />

        <h1 class="checkout__title">Оформление заказа</h1>
    </div>

    <div class="checkout__body">
        <form class="checkout__form" action="#" method="POST" @submit.prevent="onSubmit">
            @csrf

            <div class="checkout__grid">

                {{-- LEFT: form card --}}
                <div class="checkout__form-card">

                    <div class="checkout__login-link">
                        <span>Уже есть аккаунт?</span>
                        <a href="#">Войти</a>
                    </div>

                    {{-- Section 1: Contact data --}}
                    <section class="checkout__section">
                        <div class="checkout__section-head">
                            <span class="checkout__step">1</span>
                            <h2 class="checkout__section-title">Контактные данные</h2>
                        </div>

                        <div class="checkout__fields">
                            <label class="checkout__field">
                                <span class="checkout__label">Имя и фамилия*</span>
                                <input class="checkout__input" type="text" name="name" placeholder="Введите имя и фамилию" required>
                            </label>

                            <label class="checkout__field">
                                <span class="checkout__label">Телефон*</span>
                                <input class="checkout__input" type="tel" name="phone" placeholder="+7 (_ _ _) _ _ _ - _ _ - _ _" required x-model="phone" @input="formatPhone($event)">
                            </label>

                            <label class="checkout__field">
                                <span class="checkout__label">E-mail</span>
                                <input class="checkout__input" type="email" name="email" placeholder="example@gmail.com">
                            </label>
                        </div>

                        <div class="checkout__subgroup">
                            <p class="checkout__subgroup-title">Вам перезвонить?</p>
                            <div class="checkout__radios-inline">
                                <label class="checkout__radio-inline">
                                    <input type="radio" name="callback" value="yes" x-model="callback">
                                    <span class="checkout__radio-dot"></span>
                                    <span>Да, есть вопросы</span>
                                </label>
                                <label class="checkout__radio-inline">
                                    <input type="radio" name="callback" value="no" x-model="callback" checked>
                                    <span class="checkout__radio-dot"></span>
                                    <span>Нет, звоните по необходимости</span>
                                </label>
                            </div>
                        </div>
                    </section>

                    <div class="checkout__separator"></div>

                    {{-- Section 2: Delivery method --}}
                    <section class="checkout__section">
                        <div class="checkout__section-head">
                            <span class="checkout__step">2</span>
                            <h2 class="checkout__section-title">Способ доставки</h2>
                        </div>

                        <div class="checkout__cards-grid">
                            <label class="checkout__card" :class="{ 'is-selected': delivery === 'branch' }">
                                <input type="radio" name="delivery_method" value="branch" x-model="delivery" checked>
                                <span class="checkout__card-radio"></span>
                                <div class="checkout__card-body">
                                    <p class="checkout__card-title">В отделение</p>
                                    <p class="checkout__card-subtitle">По тарифам компании</p>
                                </div>
                            </label>

                            <label class="checkout__card" :class="{ 'is-selected': delivery === 'pickup' }">
                                <input type="radio" name="delivery_method" value="pickup" x-model="delivery">
                                <span class="checkout__card-radio"></span>
                                <div class="checkout__card-body">
                                    <p class="checkout__card-title">Самовывоз</p>
                                    <p class="checkout__card-subtitle">Из нашего магазина</p>
                                </div>
                            </label>

                            <label class="checkout__card" :class="{ 'is-selected': delivery === 'courier' }">
                                <input type="radio" name="delivery_method" value="courier" x-model="delivery">
                                <span class="checkout__card-radio"></span>
                                <div class="checkout__card-body">
                                    <p class="checkout__card-title">Курьером</p>
                                    <p class="checkout__card-subtitle">До двери</p>
                                </div>
                            </label>
                        </div>

                        <div class="checkout__fields checkout__fields--delivery">
                            <label class="checkout__field">
                                <span class="checkout__label">Город*</span>
                                <input class="checkout__input" type="text" name="city" placeholder="Введите город" required>
                            </label>

                            <label class="checkout__field">
                                <span class="checkout__label">Компания доставки*</span>
                                <select class="checkout__input checkout__input--select" name="delivery_company" required>
                                    <option value="" disabled selected>Выберите компанию</option>
                                    <option value="cdek">СДЭК</option>
                                    <option value="post">Почта России</option>
                                </select>
                            </label>

                            <label class="checkout__field">
                                <span class="checkout__label">Отделение*</span>
                                <select class="checkout__input checkout__input--select" name="branch" required>
                                    <option value="" disabled selected>Выберите отделение</option>
                                </select>
                            </label>
                        </div>
                    </section>

                    <div class="checkout__separator"></div>

                    {{-- Section 3: Payment method --}}
                    <section class="checkout__section">
                        <div class="checkout__section-head">
                            <span class="checkout__step">3</span>
                            <h2 class="checkout__section-title">Способ оплаты</h2>
                        </div>

                        <div class="checkout__cards-grid">
                            <label class="checkout__card" :class="{ 'is-selected': payment === 'online' }">
                                <input type="radio" name="payment_method" value="online" x-model="payment" checked>
                                <span class="checkout__card-radio"></span>
                                <div class="checkout__card-body">
                                    <p class="checkout__card-title">Банковская карта</p>
                                    <p class="checkout__card-subtitle">Онлайн оплата</p>
                                </div>
                            </label>

                            <label class="checkout__card" :class="{ 'is-selected': payment === 'on_receipt' }">
                                <input type="radio" name="payment_method" value="on_receipt" x-model="payment">
                                <span class="checkout__card-radio"></span>
                                <div class="checkout__card-body">
                                    <p class="checkout__card-title">При получении</p>
                                    <p class="checkout__card-subtitle">В пункте выдачи</p>
                                </div>
                            </label>
                        </div>
                    </section>

                    <div class="checkout__separator"></div>

                    {{-- Section 4: Comment --}}
                    <section class="checkout__section">
                        <div class="checkout__section-head">
                            <span class="checkout__step">4</span>
                            <h2 class="checkout__section-title">Комментарий к заказу</h2>
                            <span class="checkout__optional">(необязательно)</span>
                        </div>

                        <textarea class="checkout__textarea" name="comment" placeholder="Укажите дополнительную информацию"></textarea>
                    </section>
                </div>

                {{-- RIGHT: summary sidebar (mirror cart's summary card exactly) --}}
                <aside class="checkout__summary">
                    <h2 class="checkout__summary-title">Ваш заказ</h2>

                    <div class="checkout__summary-row">
                        <span class="checkout__summary-label">Товаров</span>
                        <span class="checkout__summary-value">{{ $itemsCount }}</span>
                    </div>
                    <div class="checkout__summary-row">
                        <span class="checkout__summary-label">Сумма</span>
                        <span class="checkout__summary-value">{{ $subtotalFormatted }}</span>
                    </div>
                    <div class="checkout__summary-row">
                        <span class="checkout__summary-label">Доставка</span>
                        <span class="checkout__summary-value">{{ $deliveryLabel }}</span>
                    </div>

                    <div class="checkout__summary-separator"></div>

                    <div class="checkout__summary-row checkout__summary-row--total">
                        <span class="checkout__summary-label">К оплате</span>
                        <span class="checkout__summary-value">{{ $totalFormatted }}</span>
                    </div>

                    <button type="submit" class="checkout__checkout">Оформить заказ</button>

                    <a href="{{ route('cart') }}" class="checkout__back">Вернуться в корзину</a>
                </aside>

            </div>
        </form>
    </div>
</section>
