@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.media') }}</title>
@endsection

@section('content')
    <h1>{{ trans('models.media') }}</h1>

    @if(!$medias || $medias->count() == 0)
        <p class="flow-text">{{ trans('models.no_media') }}</p>
    @else
        <table class="responsive-table bordered">
            <thead>
            <tr>
                <th>{{ trans('general.url') }}</th>
                <th>{{ trans('general.created_at') }}</th>
                <th>{{ trans('general.author') }}</th>
            </tr>
            </thead>

            @foreach($medias as $media)
                <td>{{ $media->title }}</td>
                <td>{{ $media->created_at }}</td>
                <td>{{ $media->user->name }}</td>
            @endforeach

            {{ $medias->links() }}
        </table>
    @endif

@endsection