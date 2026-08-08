@push('block-styles')
    @vite(['resources/css/blocks/profile/orders/style.css'])
@endpush

<section class="profile-orders">
    <div class="container">
        <h2 class="profile-orders__title section__title">{{ __('profile.orders_title') }}</h2>
        <div class="profile-orders__list">
            @foreach ($orders as $order)
                <x-order-card
                    :image="$order['image']"
                    :status="$order['status']"
                    :order-number="$order['number']"
                    :date="$order['date']"
                    :total="$order['total']"
                    href="#"
                />
            @endforeach
        </div>
    </div>
</section>
