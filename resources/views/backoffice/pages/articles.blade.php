@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.articles') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.articles') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$articles || $articles->count() == 0)
                <p class="flow-text">{{ trans('models.no_articles') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.title') }}</th>
                        <th>{{ trans('general.created_at') }}</th>
                        <th>{{ trans('general.author') }}</th>
                    </tr>
                    </thead>

                    @foreach($articles as $article)
                        <tr>
                            <td><a href="{{ route('articles.show', ['article' => $article]) }}">{{ $article->title }}</a></td>
                            <td>{{ $article->created_at }}</td>
                            <td>{{ $article->user->name }}</td>
                        </tr>

                    @endforeach

                    {{ $articles->links() }}
                </table>
            @endif
        </div>
    </div>

    @if(Auth::user()->hasPermission('articles.edit'))
        <div class="fixed-action-btn">
            <a class="btn-floating btn-large green waves-effect" href="{{ route('articles.create') }}">
                <i class="large material-icons">add</i>
            </a>
        </div>
    @endif

@endsection