@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.pages') }}</title>
@endsection

@section('content')
    <div class="row no-margin-bottom">
        <div class="col s8">
            <h1>{{ trans('models.pages') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$pages || $pages->count() == 0)
                <p class="flow-text">{{ trans('models.no_pages') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.title') }}</th>
                        <th>{{ trans('general.created_at') }}</th>
                    </tr>
                    </thead>

                    @foreach($pages as $page)
                        <tr>
                            <td><a href="{{ route('pages.show', ['page' => $page]) }}">{{ $page->title }}</a></td>
                            <td>{{ $page->created_at }}</td>
                        </tr>

                    @endforeach
                </table>

                <div class="row">
                    <div class="col s12">
                        {{ $pages->links() }}
                    </div>
                </div>

            @endif
        </div>
    </div>

    @if(Auth::user()->hasPermission('pages.edit'))
        <div class="fixed-action-btn">
            <a class="btn-floating btn-large green waves-effect" href="{{ route('pages.create') }}">
                <i class="large material-icons">add</i>
            </a>
        </div>
    @endif

@endsection