@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.players') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.players') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$players || $players->count() == 0)
                <p class="flow-text">{{ trans('models.no_players') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.id') }}</th>
                        <th>{{ trans('models.picture')  }}</th>
                        <th>{{ trans('general.name') }}</th>
                        <th>{{ trans('general.created_at') }}</th>
                        <th>{{ trans('general.updated_at') }}</th>
                    </tr>
                    </thead>

                    @foreach($players as $player)

                        <tr>
                            <td>{{ $player->id }}</td>
                            <td>
                                @if($player->picture)
                                    <img style="max-height: 30px" src="{{ $player->picture }}" alt="" class="responsive-img circle"/>
                                @else
                                    <img style="max-height: 30px" src="{{ config('custom.default_profile_pic') }}" alt="" class="responsive-img circle"/>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('players.show', ['player' => $player]) }}">
                                    {{ $player->name }}
                                </a>
                            </td>

                            <td>{{ $player->created_at }}</td>
                            <td>{{ $player->updated_at }}</td>

                        </tr>

                    @endforeach

                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            {{ $players->links() }}
        </div>
    </div>

    @if(Auth::user()->hasPermission('players.edit'))
        @include('backoffice.partial.add_model_button', ['route' => route('players.create')])
    @endif

@endsection