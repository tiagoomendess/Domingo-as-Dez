@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.games') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.games') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$games || $games->count() == 0)
                <p class="flow-text">{{ trans('models.no_games') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.id') }}</th>
                        <th>{{ trans('models.teams') }}</th>
                        <th>{{ trans('models.season') }}</th>
                        <th>{{ trans('general.created_at') }}</th>
                        <th>{{ trans('general.updated_at') }}</th>
                    </tr>
                    </thead>

                    @foreach($games as $game)

                        <tr>
                            <td>{{ $game->id }}</td>

                            <td>

                                <a href="{{ route('games.show', ['game' => $game]) }}">
                                [{{ $game->round }}] {{ $game->homeTeam->club->name }} vs {{ $game->awayTeam->club->name }} ({{ $game->homeTeam->name }})
                                </a>

                            </td>

                            <td>
                                @if($game->season->start_year != $game->season->end_year)
                                    {{ $game->season->start_year }}/{{ $game->season->end_year }} ({{ $game->season->competition->name }})
                                @else
                                    {{ $game->season->start_year }} ({{ $game->season->competition->name }})
                                @endif

                            </td>

                            <td>{{ $game->created_at }}</td>
                            <td>{{ $game->updated_at }}</td>

                        </tr>

                    @endforeach

                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            {{ $games->links() }}
        </div>
    </div>

    @if(Auth::user()->hasPermission('seasons.edit'))
        @include('backoffice.partial.add_model_button', ['route' => route('games.create')])
    @endif

@endsection