@push('block-styles')
    @vite(['resources/css/blocks/profile/orders/style.css'])
@endpush

@if(($mode ?? 'simple') === 'full')

<section class="profile-orders profile-orders--{{$mode}}">
    <div class="container">
        <div class="profile-orders__header">
            <h2 class="profile-orders__title section__title">{{ __('profile.orders_title') }}</h2>
        </div>

        <div class="profile-orders__nav">
            <p class="profile-orders__subtitle">{{ __('profile.orders_subtitle') }}</p>
    
            <div class="profile-orders__sort" x-data="{ sortOpen: false }">
                <span class="profile-orders__sort-label">{{ __('profile.orders_sort_label') }}</span>
                <div class="profile-orders__sort-select" @click.away="sortOpen = false">
                    <button type="button" class="profile-orders__sort-trigger" @click="sortOpen = !sortOpen">
                        <span>{{ __('profile.orders_sort_all') }}</span>
                        <svg class="profile-orders__sort-chevron" :class="{ 'is-open': sortOpen }" width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.99 6.05a.75.75 0 0 0 1.06 0l4.78-4.78a.75.75 0 0 0-1.06-1.06L5.52 4.46 1.28.21a.75.75 0 0 0-1.06 1.06L4.99 6.05Z" fill="#BFBFBF"/>
                        </svg>
                    </button>
                    <div class="profile-orders__sort-dropdown" x-show="sortOpen" x-transition style="display:none;">
                        <button type="button" class="profile-orders__sort-option is-active" @click="sortOpen = false">
                            <span>{{ __('profile.orders_sort_all') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-orders__list">
            @foreach ($orders as $order)
                <x-order-card
                    :image="$order['image']"
                    :status="$order['status']"
                    :order-number="$order['number']"
                    :date="$order['date']"
                    :total="$order['total']"
                    href="/profile/orders/1"
                />
            @endforeach
        </div>

        <x-btn variant="primary" class="profile-orders__more" :text="__('profile.orders_show_more')" />
    
    </div>
</section>

@else

<section class="profile-orders">
    <div class="container">
        <h2 class="profile-orders__title profile-orders__title--simple section__title">{{ __('profile.recent_orders_title') }}</h2>
        <div class="profile-orders__list">
            @foreach ($orders as $order)
                <x-order-card
                    :image="$order['image']"
                    :status="$order['status']"
                    :order-number="$order['number']"
                    :date="$order['date']"
                    :total="$order['total']"
                    href="/profile/orders/1"
                />
            @endforeach
        </div>
    </div>
</section>

@endif
