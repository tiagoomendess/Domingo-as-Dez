@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.articles') }}</title>
@endsection

@section('content')
    <h1>{{ trans('models.articles') }}</h1>

    @if(!$articles || $articles->count() == 0)
        <p class="flow-text">{{ trans('models.no_articles') }}</p>
    @else
        <table class="responsive-table bordered">
            <thead>
            <tr>
                <th>{{ trans('general.title') }}</th>
                <th>{{ trans('general.created_at') }}</th>
                <th>{{ trans('general.author') }}</th>
            </tr>
            </thead>

            @foreach($articles as $article)
                <td>{{ $article->title }}</td>
                <td>{{ $article->created_at }}</td>
                <td>{{ $article->user->name }}</td>
            @endforeach

            {{ $articles->links() }}
        </table>
    @endif

@endsection