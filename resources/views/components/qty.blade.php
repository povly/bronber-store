@props([
    'dec' => 'dec()',
    'inc' => 'inc()',
    'value' => 'qty',
    'data' => 'qty()',
    'watch' => null,
])

<div class="qty" @if($data) x-data="{{ $data }}" @endif @if($watch) x-init="$watch('qty', v => {{ $watch }})" @endif>
    <button type="button" class="qty__btn" @click="{{ $dec }}" aria-label="Уменьшить количество">
        <span class="qty__icon qty__icon--minus"></span>
    </button>
    <span class="qty__value" x-text="{{ $value }}"></span>
    <button type="button" class="qty__btn" @click="{{ $inc }}" aria-label="Увеличить количество">
        <span class="qty__icon qty__icon--plus"></span>
    </button>
</div>
