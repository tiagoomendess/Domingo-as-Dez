@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.goals') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.goals') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$goals || $goals->count() == 0)
                <p class="flow-text">{{ trans('models.no_goals') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.id') }}</th>
                        <th>{{ trans('models.player') }}</th>
                        <th>{{ trans('models.game') }}</th>
                        <th>{{ trans('general.created_at') }}</th>
                        <th>{{ trans('general.updated_at') }}</th>
                    </tr>
                    </thead>

                    @foreach($goals as $goal)

                        <tr>
                            <td>{{ $goal->id }}</td>

                            <td>

                                <a href="{{ route('goals.show', ['goal' => $goal]) }}">
                                    {{ $goal->player->name }}
                                </a>

                            </td>

                            <td>
                                <a href="{{ route('games.show', ['game' => $goal->game]) }}">
                                    {{ $goal->game->homeTeam->club->name }} vs {{ $goal->game->awayTeam->club->name }}
                                </a>
                            </td>

                            <td>{{ $goal->created_at }}</td>
                            <td>{{ $goal->updated_at }}</td>

                        </tr>

                    @endforeach

                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            {{ $goals->links() }}
        </div>
    </div>

    @if(Auth::user()->hasPermission('goals.edit'))
        @include('backoffice.partial.add_model_button', ['route' => route('goals.create')])
    @endif

@endsection