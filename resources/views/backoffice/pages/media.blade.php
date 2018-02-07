@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.media') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.media') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <br>
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

            <p class="flow-text">
                <b>{{ trans('general.id') }} : </b> {{ $media->id }} <br>
                <b>{{ trans('general.url') }} : </b> {{ url($media->url) }} <br>
                <b>{{ trans('models.media_type') }} : </b> {{ $media->media_type }} <br>
                <b>{{ trans('general.tags') }} : </b> {{ $media->tags }} <br>
                <b>{{ trans('general.visible') }} : </b> {{ trans_choice('general.boolean', $media->visible) }} <br>
                <b>{{ trans('general.author') }} : </b> {{ $media->user->name }} <br>
                <b>{{ trans('general.created_at') }} : </b> {{ $media->created_at }} <br>
                <b>{{ trans('general.updated_at') }} : </b> {{ $media->updated_at }} <br>
            </p>

        </div>
    </div>

    @if(Auth::user()->hasPermission('media.edit'))
        @include('backoffice.partial.model_options', ['edit_route' => route('media.edit', ['media' => $media]), 'delete_route' => route('media.destroy', ['media' => $media])])
    @endif

@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $('.materialboxed').materialbox();
        });

    </script>
@endsection