@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.media') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <br>
            @if($media->media_type == 'image')

                <img class="responsive-img" src="{{ $media->url }}">

            @elseif($media->media_type == 'video')

                <video class="responsive-video" controls>
                    <source src="{{ $media->url }}" type="video/mp4">
                </video>

            @elseif($media->media_type == 'youtube')
                <iframe width="560" height="315" src="{{ str_replace('watch?v=', 'embed/', $media->url) }}" frameborder="0" allow="encrypted-media" allowfullscreen></iframe>
            @else
                <a href="{{ $media->url }}" target="_blank">{{ trans('general.open') }}</a>
            @endif

            <p class="flow-text">
                <b>{{ trans('general.id') }} : </b> {{ $media->id }} <br>
                <b>{{ trans('general.url') }} : </b> {{ url($media->url) }} <br>
                <b>{{ trans('models.media_type') }} : </b> {{ $media->media_type }} <br>
                <b>{{ trans('models.tags') }} : </b> {{ $media->tags }} <br>
                <b>{{ trans('general.visible') }} : </b> {{ $media->visible }} <br>
                <b>{{ trans('general.created_at') }} : </b> {{ $media->created_at }} <br>
                <b>{{ trans('general.updated_at') }} : </b> {{ $media->updated_at }} <br>
            </p>

        </div>
    </div>

    @if(Auth::user()->hasPermission('media.edit'))
        <div class="fixed-action-btn horizontal click-to-toggle">
            <a class="btn-floating btn-large yellow darken-3">
                <i class="material-icons">menu</i>
            </a>
            <ul>
                <li><a class="btn-floating blue"><i class="material-icons">edit</i></a></li>
                <li><a class="btn-floating red"><i class="material-icons">delete</i></a></li>
            </ul>
        </div>
    @endif

@endsection