@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.teams') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.teams') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$teams || $teams->count() == 0)
                <p class="flow-text">{{ trans('models.no_teams') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.id') }}</th>
                        <th>{{ trans('general.name') }}</th>
                        <th>{{ trans('general.created_at') }}</th>
                        <th>{{ trans('general.updated_at') }}</th>
                    </tr>
                    </thead>

                    @foreach($teams as $team)

                        <tr>
                            <td>{{ $team->id }}</td>
                            <td><a href="{{ route('teams.show', ['team' => $team]) }}">
                                    {{ $team->name }} ({{ $team->club->name }})
                                </a>
                            </td>

                            <td>{{ $team->created_at }}</td>
                            <td>{{ $team->updated_at }}</td>

                        </tr>

                    @endforeach

                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            {{ $teams->links() }}
        </div>
    </div>

    @if(Auth::user()->hasPermission('teams.edit'))
        @include('backoffice.partial.add_model_button', ['route' => route('teams.create')])
    @endif

@endsection