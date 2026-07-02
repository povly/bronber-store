@push('block-styles')
    @vite(['resources/css/blocks/product-reviews/style.css'])
@endpush

@push('block-scripts')
    @vite(['resources/js/blocks/product-reviews/index.js'])
@endpush

@php
    $starPath = 'M10.5268 1.29489C10.5706 1.20635 10.6383 1.13183 10.7223 1.07972C10.8062 1.02761 10.903 1 11.0018 1C11.1006 1 11.1974 1.02761 11.2813 1.07972C11.3653 1.13183 11.433 1.20635 11.4768 1.29489L13.7868 5.97389C13.939 6.28186 14.1636 6.5483 14.4414 6.75035C14.7192 6.95239 15.0419 7.08401 15.3818 7.13389L20.5478 7.88989C20.6457 7.90408 20.7376 7.94537 20.8133 8.00909C20.8889 8.07282 20.9452 8.15644 20.9758 8.2505C21.0064 8.34456 21.0101 8.4453 20.9864 8.54133C20.9627 8.63736 20.9126 8.72485 20.8418 8.79389L17.1058 12.4319C16.8594 12.672 16.6751 12.9684 16.5686 13.2955C16.4622 13.6227 16.4369 13.9708 16.4948 14.3099L17.3768 19.4499C17.3941 19.5477 17.3835 19.6485 17.3463 19.7406C17.3091 19.8327 17.2467 19.9125 17.1663 19.9709C17.086 20.0293 16.9908 20.0639 16.8917 20.0708C16.7926 20.0777 16.6935 20.0566 16.6058 20.0099L11.9878 17.5819C11.6835 17.4221 11.345 17.3386 11.0013 17.3386C10.6576 17.3386 10.3191 17.4221 10.0148 17.5819L5.3978 20.0099C5.31013 20.0563 5.2112 20.0772 5.11225 20.0701C5.0133 20.0631 4.91832 20.0285 4.83809 19.9701C4.75787 19.9118 4.69563 19.8321 4.65846 19.7401C4.62128 19.6481 4.61066 19.5476 4.6278 19.4499L5.5088 14.3109C5.567 13.9716 5.54178 13.6233 5.43534 13.2959C5.32889 12.9686 5.14441 12.672 4.8978 12.4319L1.1618 8.79489C1.09039 8.72593 1.03979 8.63829 1.01576 8.54197C0.991731 8.44565 0.995237 8.34451 1.02588 8.25008C1.05652 8.15566 1.11307 8.07174 1.18908 8.00788C1.26509 7.94402 1.3575 7.90279 1.4558 7.88889L6.6208 7.13389C6.96106 7.08439 7.28419 6.95295 7.56238 6.75088C7.84058 6.54881 8.0655 6.28216 8.2178 5.97389L10.5268 1.29489Z';
@endphp

<section class="reviews" x-data="productReviews()">
    <div class="container">
        <x-breadcrumbs class="reviews__breadcrumbs" :items="[
            ['label' => 'Главная', 'url' => route('home')],
            ['label' => 'Каталог', 'url' => route('catalog')],
            ['label' => 'Топливные насосы', 'url' => route('catalog')],
            ['label' => 'Bosch', 'url' => route('product')],
            ['label' => 'Отзывы покупателей'],
        ]" />

        <h1 class="reviews__title">Отзывы покупателей</h1>

        {{-- Product summary card --}}
        <div class="reviews__product">
            <div class="reviews__product-image">
                <img src="{{ $product['image'] }}" alt="{{ $product['title'] }}" loading="lazy" />
            </div>
            <div class="reviews__product-text">
                <div class="reviews__product-info">
                    <h2 class="reviews__product-name">{{ $product['title'] }}</h2>
                    <p class="reviews__product-article">Артикул: {{ $product['article'] }}</p>
                    <div class="reviews__product-rating">
                        <div class="reviews__stars" aria-hidden="true">
                            @for ($s = 0; $s < 5; $s++)
                                <svg width="24" height="24" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="{{ $starPath }}" fill="#FFA903" stroke="#FFA903" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            @endfor
                        </div>
                        <span class="reviews__rating-count">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M22 17C22 17.5304 21.7893 18.0391 21.4142 18.4142C21.0391 18.7893 20.5304 19 20 19H6.828C6.29761 19.0001 5.78899 19.2109 5.414 19.586L3.212 21.788C3.1127 21.8873 2.9862 21.9549 2.84849 21.9823C2.71077 22.0097 2.56803 21.9956 2.43831 21.9419C2.30858 21.8881 2.1977 21.7971 2.11969 21.6804C2.04167 21.5637 2.00002 21.4264 2 21.286V5C2 4.46957 2.21071 3.96086 2.58579 3.58579C2.96086 3.21071 3.46957 3 4 3H20C20.5304 3 21.0391 3.21071 21.4142 3.58579C21.7893 3.96086 22 4.46957 22 5V17Z" stroke="#797878" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                              <path d="M7 11H17" stroke="#797878" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                              <path d="M7 15H13" stroke="#797878" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                              <path d="M7 7H15" stroke="#797878" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>{{ $product['reviewCount'] }}</span>
                        </span>
                    </div>
                </div>
                <div class="reviews__product-side">
                    <div class="reviews__product-side-text">
                        <div class="reviews__product-prices">
                            <span class="reviews__price">{{ $priceFormatted }}</span>
                            <span class="reviews__price-old">{{ $oldPriceFormatted }}</span>
                        </div>
                        <div class="reviews__product-bonus">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M10.561 2.1043C10.6012 2.02314 10.6633 1.95482 10.7402 1.90706C10.8171 1.8593 10.9059 1.83398 10.9964 1.83398C11.087 1.83398 11.1758 1.8593 11.2527 1.90706C11.3296 1.95482 11.3917 2.02314 11.4319 2.1043L13.5494 6.39339C13.6889 6.67569 13.8948 6.91993 14.1494 7.10514C14.4041 7.29034 14.6999 7.41099 15.0114 7.45672L19.7469 8.14972C19.8367 8.16272 19.921 8.20057 19.9903 8.25899C20.0596 8.3174 20.1113 8.39406 20.1393 8.48028C20.1673 8.5665 20.1707 8.65884 20.149 8.74687C20.1273 8.8349 20.0814 8.91509 20.0164 8.97839L16.5918 12.3132C16.3659 12.5333 16.1969 12.805 16.0994 13.1049C16.0018 13.4048 15.9786 13.7239 16.0317 14.0347L16.8402 18.7464C16.856 18.8361 16.8463 18.9284 16.8122 19.0128C16.7781 19.0973 16.721 19.1704 16.6473 19.224C16.5736 19.2775 16.4863 19.3092 16.3955 19.3155C16.3046 19.3219 16.2138 19.3025 16.1334 19.2597L11.9003 17.0341C11.6213 16.8876 11.311 16.8111 10.996 16.8111C10.6809 16.8111 10.3706 16.8876 10.0917 17.0341L5.85944 19.2597C5.77908 19.3023 5.68839 19.3214 5.59769 19.315C5.50699 19.3085 5.41992 19.2767 5.34638 19.2233C5.27284 19.1698 5.21579 19.0967 5.18171 19.0124C5.14763 18.9281 5.13789 18.8359 5.15361 18.7464L5.96119 14.0356C6.01454 13.7246 5.99143 13.4053 5.89385 13.1053C5.79628 12.8052 5.62717 12.5334 5.40111 12.3132L1.97644 8.9793C1.91099 8.91608 1.8646 8.83575 1.84257 8.74746C1.82055 8.65916 1.82376 8.56645 1.85185 8.4799C1.87994 8.39334 1.93177 8.31641 2.00145 8.25787C2.07112 8.19933 2.15584 8.16154 2.24594 8.1488L6.98052 7.45672C7.29243 7.41134 7.58863 7.29086 7.84364 7.10563C8.09866 6.92039 8.30484 6.67597 8.44444 6.39339L10.561 2.1043Z" stroke="#7212BC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="reviews__product-bonus-title"><span>+{{ $product['bonusPoints'] }}</span> баллов</span>
                            <span class="reviews__product-bonus-right">
                                <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M10.5 19.25C15.3325 19.25 19.25 15.3325 19.25 10.5C19.25 5.66751 15.3325 1.75 10.5 1.75C5.66751 1.75 1.75 5.66751 1.75 10.5C1.75 15.3325 5.66751 19.25 10.5 19.25Z" stroke="#CACACA" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                  <path d="M10.5 7V10.5" stroke="#CACACA" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                  <path d="M10.5 14H10.5088" stroke="#CACACA" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="reviews__product-actions">
                        <button type="button" class="reviews__cart-btn btn btn--primary">
                            <span>В корзину</span>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M8 22C8.55228 22 9 21.5523 9 21C9 20.4477 8.55228 20 8 20C7.44772 20 7 20.4477 7 21C7 21.5523 7.44772 22 8 22Z" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                              <path d="M19 22C19.5523 22 20 21.5523 20 21C20 20.4477 19.5523 20 19 20C18.4477 20 18 20.4477 18 21C18 21.5523 18.4477 22 19 22Z" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                              <path d="M2.04688 2.05078H4.04688L6.70687 14.4708C6.80445 14.9256 7.05755 15.3323 7.42259 15.6206C7.78763 15.909 8.24178 16.0611 8.70687 16.0508H18.4869C18.9421 16.05 19.3834 15.8941 19.7379 15.6086C20.0924 15.3232 20.339 14.9253 20.4369 14.4808L22.0869 7.05078H5.11687" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button" class="reviews__fav-btn" aria-label="В избранное">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M2 9.50053C2.00002 8.38773 2.33759 7.30111 2.96813 6.38419C3.59867 5.46727 4.49252 4.76319 5.53161 4.36493C6.5707 3.96667 7.70616 3.89297 8.78801 4.15357C9.86987 4.41417 10.8472 4.99681 11.591 5.82453C11.6434 5.88054 11.7067 5.9252 11.7771 5.95573C11.8474 5.98626 11.9233 6.00201 12 6.00201C12.0767 6.00201 12.1526 5.98626 12.2229 5.95573C12.2933 5.9252 12.3566 5.88054 12.409 5.82453C13.1504 4.99143 14.128 4.4039 15.2116 4.14013C16.2952 3.87636 17.4335 3.94887 18.4749 4.34801C19.5163 4.74715 20.4114 5.45398 21.0411 6.37443C21.6708 7.29488 22.0053 8.38529 22 9.50053C22 11.7905 20.5 13.5005 19 15.0005L13.508 20.3135C13.3217 20.5275 13.0919 20.6994 12.834 20.8178C12.5762 20.9362 12.296 20.9984 12.0123 21.0002C11.7285 21.002 11.4476 20.9434 11.1883 20.8283C10.9289 20.7131 10.697 20.5442 10.508 20.3325L5 15.0005C3.5 13.5005 2 11.8005 2 9.50053Z" fill="white" stroke="#7212BC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="reviews__filters">
            <label class="reviews__filter">
                <input type="checkbox" x-model="filterPhoto" class="reviews__filter-input" />
                <span class="reviews__filter-square">
                    <svg width="13" height="9" viewBox="0 0 13 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M11.4167 0.75L4.08333 8.08333L0.75 4.75" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <span class="reviews__filter-label">С фото</span>
            </label>
            <label class="reviews__filter">
                <input type="checkbox" x-model="filterRating5" class="reviews__filter-input" />
                <span class="reviews__filter-square">
                    <svg width="13" height="9" viewBox="0 0 13 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M11.4167 0.75L4.08333 8.08333L0.75 4.75" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <span class="reviews__filter-label">Только рейтинг 5</span>
            </label>
            <div class="reviews__sort">
                <span class="reviews__sort-label">Сортировка:</span>
                <button type="button" class="reviews__sort-btn">
                    <span class="reviews__sort-text">По дате</span>
                    <svg width="17" height="13" viewBox="0 0 17 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M7.46967 9.53033C7.76256 9.82322 8.23744 9.82322 8.53033 9.53033L13.3033 4.75736C13.5962 4.46447 13.5962 3.98959 13.3033 3.6967C13.0104 3.40381 12.5355 3.40381 12.2426 3.6967L8 7.93934L3.75736 3.6967C3.46447 3.40381 2.98959 3.40381 2.6967 3.6967C2.40381 3.98959 2.40381 4.46447 2.6967 4.75736L7.46967 9.53033ZM8 8L7.25 8L7.25 9L8 9L8.75 9L8.75 8L8 8Z" fill="#BFBFBF" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Review list --}}
        <div class="reviews__list" x-ref="list">
            @foreach ($reviews as $review)
                <article class="reviews__card" data-review
                         data-photos="{{ ! empty($review['photos']) ? '1' : '0' }}"
                         data-rating="{{ $review['rating'] ?? 5 }}">
                    <div class="reviews__card-head">
                        <div class="reviews__avatar" aria-hidden="true">{{ $review['initial'] }}</div>
                        <div class="reviews__card-meta">
                            <p class="reviews__card-name">{{ $review['name'] }}</p>
                            <p class="reviews__card-car">{{ $review['car'] }}</p>
                        </div>
                        <time class="reviews__card-date reviews__card-date--head">{{ $review['date'] }}</time>
                    </div>
                    <div class="reviews__card-rating">
                        <div class="reviews__card-stars" aria-hidden="true">
                            @for ($s = 0; $s < 5; $s++)
                                <svg width="24" height="24" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="{{ $starPath }}" fill="#FFA903" stroke="#FFA903" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            @endfor
                        </div>
                        <time class="reviews__card-date reviews__card-date--rating">{{ $review['date'] }}</time>
                    </div>
                    <p class="reviews__card-text">{{ $review['text'] }}</p>

                    @if (! empty($review['photos']))
                        @php
                            $totalPhotos = count($review['photos']);
                            $hasMorePhotos = $totalPhotos > 4;
                            $visiblePhotos = $hasMorePhotos ? array_slice($review['photos'], 0, 3) : $review['photos'];
                            $extraCount = $totalPhotos - 4;
                        @endphp
                        <div class="reviews__card-photos">
                            @foreach ($visiblePhotos as $photo)
                                <div class="reviews__card-photo">
                                    <img src="{{ $photo }}" alt="" loading="lazy" />
                                </div>
                            @endforeach
                            @if ($hasMorePhotos)
                                <div class="reviews__card-photo reviews__card-photo--more">
                                    <span class="reviews__card-more">+{{ $extraCount }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </article>
            @endforeach
        </div>

        {{-- Show more --}}
        <div class="reviews__more-wrap" x-show="hasMore" x-cloak>
            <button type="button" class="reviews__more-btn btn btn--primary" @click="showMore()">
                Показать больше
            </button>
        </div>
    </div>
</section>
