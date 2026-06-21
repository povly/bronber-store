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
                                <span class="checkout__label">Имя и фамилия<span class="checkout__required">*</span></span>
                                <input class="checkout__input" type="text" name="name" placeholder="Введите имя и фамилию" required>
                            </label>

                            <label class="checkout__field">
                                <span class="checkout__label">Телефон<span class="checkout__required">*</span></span>
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
                                <div class="checkout__card-svg">
                                    <svg width="41" height="41" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M23.9141 30.7502V10.2502C23.9141 9.34401 23.5541 8.47496 22.9133 7.83421C22.2726 7.19347 21.4036 6.8335 20.4974 6.8335H6.83073C5.92457 6.8335 5.05553 7.19347 4.41478 7.83421C3.77403 8.47496 3.41406 9.34401 3.41406 10.2502V29.0418C3.41406 29.4949 3.59405 29.9294 3.91442 30.2498C4.2348 30.5702 4.66932 30.7502 5.1224 30.7502H8.53906" stroke="#7212BC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                      <path d="M25.625 30.75H15.375" stroke="#7212BC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                      <path d="M32.4557 30.7498H35.8724C36.3255 30.7498 36.76 30.5699 37.0804 30.2495C37.4007 29.9291 37.5807 29.4946 37.5807 29.0415V22.8061C37.58 22.4184 37.4475 22.0425 37.2049 21.7401L31.2599 14.3088C31.1001 14.1088 30.8974 13.9472 30.6668 13.836C30.4361 13.7248 30.1834 13.6669 29.9274 13.6665H23.9141" stroke="#7212BC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                      <path d="M29.0417 34.1668C30.9286 34.1668 32.4583 32.6371 32.4583 30.7502C32.4583 28.8632 30.9286 27.3335 29.0417 27.3335C27.1547 27.3335 25.625 28.8632 25.625 30.7502C25.625 32.6371 27.1547 34.1668 29.0417 34.1668Z" stroke="#7212BC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                      <path d="M11.9557 34.1668C13.8427 34.1668 15.3724 32.6371 15.3724 30.7502C15.3724 28.8632 13.8427 27.3335 11.9557 27.3335C10.0688 27.3335 8.53906 28.8632 8.53906 30.7502C8.53906 32.6371 10.0688 34.1668 11.9557 34.1668Z" stroke="#7212BC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div class="checkout__card-body">
                                    <p class="checkout__card-title">В отделение</p>
                                    <p class="checkout__card-subtitle">По тарифам компании</p>
                                </div>
                            </label>

                            <label class="checkout__card" :class="{ 'is-selected': delivery === 'pickup' }">
                                <input type="radio" name="delivery_method" value="pickup" x-model="delivery">
                                <span class="checkout__card-radio"></span>
                                <div class="checkout__card-svg">
                                    <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M16.2737 28.1572C18.6762 26.0828 25.8307 19.3661 25.8307 12.9168C25.8307 10.1763 24.742 7.54794 22.8042 5.61006C20.8663 3.67218 18.238 2.5835 15.4974 2.5835C12.7568 2.5835 10.1285 3.67218 8.19063 5.61006C6.25275 7.54794 5.16406 10.1763 5.16406 12.9168C5.16406 19.3661 12.3186 26.0828 14.7211 28.1572C14.9449 28.3255 15.2174 28.4165 15.4974 28.4165C15.7774 28.4165 16.0499 28.3255 16.2737 28.1572Z" stroke="#7212BC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                      <path d="M15.5 16.7917C17.6401 16.7917 19.375 15.0568 19.375 12.9167C19.375 10.7766 17.6401 9.04169 15.5 9.04169C13.3599 9.04169 11.625 10.7766 11.625 12.9167C11.625 15.0568 13.3599 16.7917 15.5 16.7917Z" stroke="#7212BC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div class="checkout__card-body">
                                    <p class="checkout__card-title">Самовывоз</p>
                                    <p class="checkout__card-subtitle">Из нашего магазина</p>
                                </div>
                            </label>

                            <label class="checkout__card" :class="{ 'is-selected': delivery === 'courier' }">
                                <input type="radio" name="delivery_method" value="courier" x-model="delivery">
                                <span class="checkout__card-radio"></span>
                                <div class="checkout__card-svg">
                                    <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M14.2083 28.0678C14.601 28.2945 15.0465 28.4139 15.5 28.4139C15.9535 28.4139 16.399 28.2945 16.7917 28.0678L25.8333 22.9011C26.2257 22.6746 26.5515 22.3489 26.7782 21.9567C27.0049 21.5645 27.1245 21.1196 27.125 20.6666V10.3332C27.1245 9.88021 27.0049 9.43527 26.7782 9.04306C26.5515 8.65085 26.2257 8.32515 25.8333 8.09864L16.7917 2.93198C16.399 2.70524 15.9535 2.58588 15.5 2.58588C15.0465 2.58588 14.601 2.70524 14.2083 2.93198L5.16667 8.09864C4.77434 8.32515 4.44847 8.65085 4.22176 9.04306C3.99505 9.43527 3.87546 9.88021 3.875 10.3332V20.6666C3.87546 21.1196 3.99505 21.5645 4.22176 21.9567C4.44847 22.3489 4.77434 22.6746 5.16667 22.9011L14.2083 28.0678Z" stroke="#7212BC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                      <path d="M15.5 28.4167V15.5" stroke="#7212BC" stroke-width="2.58333" stroke-linecap="round" stroke-linejoin="round" />
                                      <path d="M4.25 9.0415L15.5004 15.4998L26.7508 9.0415" stroke="#7212BC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                      <path d="M9.6875 5.51544L21.3125 12.1675" stroke="#7212BC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div class="checkout__card-body">
                                    <p class="checkout__card-title">Курьером</p>
                                    <p class="checkout__card-subtitle">До двери</p>
                                </div>
                            </label>
                        </div>

                        <div class="checkout__fields checkout__fields--delivery">
                            <label class="checkout__field">
                                <span class="checkout__label">Город<span class="checkout__required">*</span></span>
                                <input class="checkout__input" type="text" name="city" placeholder="Введите город" required>
                            </label>

                            <label class="checkout__field">
                                <span class="checkout__label">Компания доставки<span class="checkout__required">*</span></span>
                                <div class="checkout__select"
                                     x-data="select({ placeholder: 'Выберите компанию' })"
                                     @click.away="close()">
                                    <button type="button" class="checkout__select-trigger" x-ref="trigger" @click="toggle()">
                                        <span class="checkout__select-value" :class="{ 'is-placeholder': !hasValue }" x-text="displayLabel">Выберите компанию</span>
                                        <svg class="checkout__select-chevron" :class="{ 'is-open': open }" width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.27344 5.52289C6.27344 5.10867 5.93765 4.77289 5.52344 4.77289C5.10922 4.77289 4.77344 5.10867 4.77344 5.52289L5.52344 5.52289L6.27344 5.52289ZM4.99311 6.05332C5.286 6.34621 5.76087 6.34621 6.05377 6.05332L10.8267 1.28035C11.1196 0.987454 11.1196 0.51258 10.8267 0.219687C10.5338 -0.073206 10.059 -0.073206 9.76608 0.219687L5.52344 4.46233L1.2808 0.219688C0.987904 -0.0732059 0.51303 -0.0732059 0.220137 0.219688C-0.0727568 0.51258 -0.0727568 0.987454 0.220137 1.28035L4.99311 6.05332ZM5.52344 5.52289L4.77344 5.52289L4.77344 5.52299L5.52344 5.52299L6.27344 5.52299L6.27344 5.52289L5.52344 5.52289Z" fill="#CACACA" />
                                        </svg>
                                    </button>
                                    <div class="checkout__select-dropdown" :class="{ 'is-above': flipped }" x-show="open" x-transition style="display: none;">
                                        <button type="button" class="checkout__select-option" :class="{ 'is-active': value === 'cdek' }" @click="choose('cdek', 'СДЭК')">СДЭК</button>
                                        <button type="button" class="checkout__select-option" :class="{ 'is-active': value === 'post' }" @click="choose('post', 'Почта России')">Почта России</button>
                                    </div>
                                    <input type="hidden" name="delivery_company" :value="value">
                                </div>
                            </label>

                            <label class="checkout__field">
                                <span class="checkout__label">Отделение<span class="checkout__required">*</span></span>
                                <div class="checkout__select"
                                     x-data="select({ placeholder: 'Выберите отделение' })"
                                     @click.away="close()">
                                    <button type="button" class="checkout__select-trigger" x-ref="trigger" @click="toggle()">
                                        <span class="checkout__select-value" :class="{ 'is-placeholder': !hasValue }" x-text="displayLabel">Выберите отделение</span>
                                        <svg class="checkout__select-chevron" :class="{ 'is-open': open }" width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.27344 5.52289C6.27344 5.10867 5.93765 4.77289 5.52344 4.77289C5.10922 4.77289 4.77344 5.10867 4.77344 5.52289L5.52344 5.52289L6.27344 5.52289ZM4.99311 6.05332C5.286 6.34621 5.76087 6.34621 6.05377 6.05332L10.8267 1.28035C11.1196 0.987454 11.1196 0.51258 10.8267 0.219687C10.5338 -0.073206 10.059 -0.073206 9.76608 0.219687L5.52344 4.46233L1.2808 0.219688C0.987904 -0.0732059 0.51303 -0.0732059 0.220137 0.219688C-0.0727568 0.51258 -0.0727568 0.987454 0.220137 1.28035L4.99311 6.05332ZM5.52344 5.52289L4.77344 5.52289L4.77344 5.52299L5.52344 5.52299L6.27344 5.52299L6.27344 5.52289L5.52344 5.52289Z" fill="#CACACA" />
                                        </svg>
                                    </button>
                                    <div class="checkout__select-dropdown" :class="{ 'is-above': flipped }" x-show="open" x-transition style="display: none;">
                                        <button type="button" class="checkout__select-option" :class="{ 'is-active': value === '' }" @click="choose('', 'Выберите отделение')">Выберите отделение</button>
                                    </div>
                                    <input type="hidden" name="branch" :value="value">
                                </div>
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
                                <span class="checkout__card-svg">
                                    <svg width="39" height="39" viewBox="0 0 39 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M32.5 8.125H6.5C4.70507 8.125 3.25 9.58007 3.25 11.375V27.625C3.25 29.4199 4.70507 30.875 6.5 30.875H32.5C34.2949 30.875 35.75 29.4199 35.75 27.625V11.375C35.75 9.58007 34.2949 8.125 32.5 8.125Z" stroke="#7212BC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                      <path d="M3.25 16.25H35.75" stroke="#7212BC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <div class="checkout__card-body">
                                    <p class="checkout__card-title">Банковская карта</p>
                                    <p class="checkout__card-subtitle">Онлайн оплата</p>
                                </div>
                            </label>

                            <label class="checkout__card" :class="{ 'is-selected': payment === 'on_receipt' }">
                                <input type="radio" name="payment_method" value="on_receipt" x-model="payment">
                                <span class="checkout__card-radio"></span>
                                <span class="checkout__card-svg">
                                    <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M11.625 20.667H18.0833" stroke="#7212BC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                      <path d="M11.625 15.4997H18.0833C18.7685 15.4997 19.4256 15.2275 19.91 14.743C20.3945 14.2586 20.6667 13.6015 20.6667 12.9163C20.6667 12.2312 20.3945 11.5741 19.91 11.0896C19.4256 10.6052 18.7685 10.333 18.0833 10.333H14.2083V21.958" stroke="#7212BC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                      <circle cx="15.5003" cy="15.5003" r="13.3519" stroke="#7212BC" stroke-width="2" />
                                    </svg>
                                </span>
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
                            <h2 class="checkout__section-title">Комментарий к заказу
                            <span class="checkout__optional">(необязательно)</span>
                            </h2>
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

                    <button type="submit" class="checkout__checkout btn btn--primary">Оформить заказ</button>

                    <a href="{{ route('cart') }}" class="checkout__back">
                        <svg width="22" height="12" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M20.75 6.27295C21.1642 6.27295 21.5 5.93716 21.5 5.52295C21.5 5.10874 21.1642 4.77295 20.75 4.77295L20.75 5.52295L20.75 6.27295ZM0.219669 4.99262C-0.0732231 5.28551 -0.0732231 5.76038 0.219669 6.05328L4.99264 10.8262C5.28553 11.1191 5.76041 11.1191 6.0533 10.8262C6.34619 10.5334 6.34619 10.0585 6.0533 9.76559L1.81066 5.52295L6.0533 1.28031C6.34619 0.987414 6.34619 0.512541 6.0533 0.219647C5.76041 -0.0732464 5.28553 -0.0732464 4.99264 0.219647L0.219669 4.99262ZM20.75 5.52295L20.75 4.77295L0.75 4.77295L0.75 5.52295L0.75 6.27295L20.75 6.27295L20.75 5.52295Z" fill="#7212BC" />
                        </svg>
                        <span>Вернуться в корзину</span>
                    </a>
                </aside>

            </div>
        </form>
    </div>
</section>
