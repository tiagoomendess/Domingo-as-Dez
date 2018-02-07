@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.article') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.article') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <h3>{{ $article->title }}</h3>


            @if($media)
                @if($media->media_type == 'image')

                    <img style="max-width: 50%;" class="responsive-img materialboxed" src="{{ $media->url }}">

                @elseif($media->media_type == 'video')

                    <video class="responsive-video" controls>
                        <source src="{{ $media->url }}" type="video/mp4">
                    </video>

                @elseif($media->media_type == 'youtube')
                    <iframe width="560" height="315" src="{{ str_replace('watch?v=', 'embed/', $media->url) }}" frameborder="0" allow="encrypted-media" allowfullscreen></iframe>
                @else
                    <a class="flow-text" href="{{ $media->url }}" target="_blank">{{ trans('general.open') }}</a>
                @endif
            @endif

            <p class="flow-text">{{ $article->description }}</p>

            <div>
                {!! $article->text !!}
            </div>

            <p class="flow-text">

                <b>{{ trans('general.id') }} : </b> {{ $article->id }} <br>
                <b>{{ trans('general.tags') }} : </b> {{ $article->tags }} <br>
                <b>{{ trans('general.date') }} : </b> {{ $article->date }} <br>
                <b>{{ trans('general.visible') }} : </b> {{ trans_choice('general.boolean', $article->visible) }} <br>
                <b>{{ trans('general.author') }} : </b> {{ App\User::find($article->user_id)->name }} <br>
                <b>{{ trans('general.created_at') }} : </b> {{ $article->created_at }} <br>
                <b>{{ trans('general.updated_at') }} : </b> {{ $article->updated_at }} <br>
            </p>
        </div>
    </div>

@endsection

@if(Auth::user()->hasPermission('articles.edit'))
    @include('backoffice.partial.model_options', ['edit_route' => route('articles.edit', ['article' => $article]), 'delete_route' => route('articles.destroy', ['article' => $article])])
@endif

@section('scripts')
    <script>
        $(document).ready(function(){
            $('.materialboxed').materialbox();
        });
    </script>
@endsection