@foreach ($articles as $article)
    @include('blocks.blog.card', ['article' => $article])
@endforeach
