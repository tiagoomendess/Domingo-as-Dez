<html>
    <head>
        <title>{{ $article->title }}</title>
    </head>
    <body>
        @if($article->media_id)
            <img style="width: 100%" src="{{ url($article->media->thumbnail_url) }}">
        @endif
        <h1>{{ $article->title }}</h1>
        <p>{{ $article->description }}</p>
        <div>
            {!! $article->text !!}
        </div>
        <small>Publicado por {{ $article->user->name }} a {{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $article->date)->format("d/m/Y") }}</small>
    </body>
</html>
