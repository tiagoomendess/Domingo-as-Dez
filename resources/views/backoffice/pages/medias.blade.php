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
            @if(!$medias || $medias->count() == 0)
                <p class="flow-text">{{ trans('models.no_media') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.url') }}</th>
                        <th>{{ trans('general.created_at') }}</th>
                        <th>{{ trans('general.author') }}</th>
                    </tr>
                    </thead>

                    @foreach($medias as $media)
                        <tr>
                            <td><a href="{{ route('media.show', ['media' => $media->id]) }}">{{ $media->tags }}</a></td>
                            <td>{{ $media->created_at }}</td>
                            <td>{{ $media->user->name }}</td>
                        </tr>
                    @endforeach

                    {{ $medias->links() }}
                </table>
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