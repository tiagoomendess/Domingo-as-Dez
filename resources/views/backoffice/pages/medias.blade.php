@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.media') }}</title>
@endsection

@section('content')

    <div class="row no-margin-bottom">
        <div class="col s8">
            <h1>{{ trans('models.media') }}</h1>
        </div>
        <div class="col s4">
            @include('backoffice.partial.search_box_btn')
        </div>
    </div>

    <div class="row no-margin-bottom">
        @include('backoffice.partial.search_box')
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$medias || $medias->count() == 0)
                <p class="flow-text">{{ trans('models.no_media') }}</p>
            @else

                <div class="row">
                    @foreach($medias as $media)
                        <div class="col xs12 s12 m6 l3">
                            <div class="card small">
                                <div class="card-image">
                                    <img src="{{ $media->thumbnail_url ? $media->thumbnail_url : 'http://placehold.it/350x200' }}">
                                </div>
                                <div class="card-content">
                                    <p>{{ $media->tags }}</p>
                                </div>
                                <div class="card-action">
                                    <a class="right" href="{{ route('media.show', ['media' => $media]) }}">{{ trans('general.details') }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{ $medias->links() }}
            @endif

            @if(Auth::user()->hasPermission('media.edit'))
                <div class="fixed-action-btn">
                    <a class="btn-floating btn-large green waves-effect" href="{{ route('media.create') }}">
                        <i class="large material-icons">add</i>
                    </a>
                </div>
            @endif
        </div>
    </div>



@endsection