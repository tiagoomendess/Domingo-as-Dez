@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.game_groups') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.game_groups') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$game_groups || $game_groups->count() == 0)
                <p class="flow-text">{{ trans('models.no_game_groups') }}</p>
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

                    @foreach($game_groups as $game_group)

                        <tr>
                            <td>{{ $game_group->id }}</td>
                            <td><a href="{{ route('seasons.show', ['season' => $season]) }}">{{ $game_group->name }} ({{ $game_group->season->competition->name }})</a></td>

                            <td>{{ $season->created_at }}</td>
                            <td>{{ $season->updated_at }}</td>

                        </tr>

                    @endforeach

                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            {{ $game_groups->links() }}
        </div>
    </div>

    @if(Auth::user()->hasPermission('game_groups.edit'))
        @include('backoffice.partial.add_model_button', ['route' => route('gamegroups.create')])
    @endif

@endsection