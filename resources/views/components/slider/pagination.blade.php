@props([])

<div class="slider__pagination {{ $class }}" x-show="pagination">
    <template x-for="page in totalPages">
        <button type="button"
                class="slider__pagination-dot"
                :class="{ 'slider__pagination-dot--active': currentPage === page - 1 }"
                @click="goToPage(page - 1)"></button>
    </template>
</div>
