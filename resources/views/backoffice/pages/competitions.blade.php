@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.competitions') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.competitions') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$competitions || $competitions->count() == 0)
                <p class="flow-text">{{ trans('models.no_competitions') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.id') }}</th>
                        <th>{{ trans('general.name') }}</th>
                        <th>{{ trans('general.type') }}</th>
                        <th>{{ trans('general.created_at') }}</th>
                        <th>{{ trans('general.updated_at') }}</th>
                    </tr>
                    </thead>

                    @foreach($competitions as $competition)

                        <tr>
                            <td>{{ $competition->id }}</td>
                            <td><a href="{{ route('competitions.show', ['competition' => $competition]) }}">{{ $competition->name }}</a></td>
                            <td>{{ trans('models.' . $competition->competition_type ) }}</td>
                            <td>{{ $competition->created_at }}</td>
                            <td>{{ $competition->updated_at }}</td>
                        </tr>

                    @endforeach

                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            {{ $competitions->links() }}
        </div>
    </div>

    @if(Auth::user()->hasPermission('competitions.edit'))
        <div class="fixed-action-btn">
            <a class="btn-floating btn-large green waves-effect" href="{{ route('competitions.create') }}">
                <i class="large material-icons">add</i>
            </a>
        </div>
    @endif

@endsection