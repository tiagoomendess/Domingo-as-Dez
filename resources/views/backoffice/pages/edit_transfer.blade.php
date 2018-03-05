@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.edit') }} {{ trans('models.transfer') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.edit') }} {{ trans('models.transfer') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('transfers.update', ['transfer' => $transfer]) }}" method="POST">

        {{ csrf_field() }}

        {{ method_field('PUT') }}

        <div class="row">

            <div class="input-field col s10 m4 l3">
                @if($transfer->player->nickname)
                    <input name="player_name" type="text" id="autocomplete-input" class="autocomplete"
                           autocomplete="off" value="#{{ $transfer->player->id }} {{ $transfer->player->name }} ({{ $transfer->player->nickname }})">
                @else
                    <input name="player_name" type="text" id="autocomplete-input" class="autocomplete"
                           autocomplete="off" value="#{{ $transfer->player->id }} {{ $transfer->player->name }}">
                @endif
                <label for="autocomplete">{{ trans('models.player') }}</label>
            </div>

            <div class="input-field col s2 m1 l1">
                <input name="player_id" type="number" id="player_id" value="{{ old('player_id', $transfer->player->id ) }}">
                <label for="player_id">{{ trans('models.id') }}</label>
            </div>

            <div class="input-field col s12 m3 l2">
                <input name="date" id="date" type="text" class="datepicker" value="{{ old('date', $transfer->date) }}">
                <label for="date">{{ trans('general.date') }}</label>
            </div>
        </div>

        <div class="row">

            <div class="col s6 m4 l3">
                <label>{{ trans('models.club') }}</label>
                <select onchange="updateTeamList('club_id', 'team_id')" id="club_id" name="club_id" class="browser-default">

                    @if($transfer->team)
                        <option value="{{ $transfer->team->club->id }}" selected>{{ $transfer->team->club->name }}</option>

                        @foreach(App\Club::all() as $club)

                            @if ($club->id != $transfer->team->club->id)
                                <option value="{{ $club->id }}">{{ $club->name }}</option>
                            @endif

                        @endforeach

                        <option value="0">{{ trans('general.none') }}</option>

                    @else
                        <option value="0">{{ trans('general.none') }}</option>

                        @foreach(App\Club::all() as $club)
                            <option value="{{ $club->id }}">{{ $club->name }}</option>
                        @endforeach

                    @endif

                </select>
            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('models.team') }}</label>

                @if($transfer->team)
                    <select id="team_id" name="team_id" class="browser-default">

                        <option value="{{ $transfer->team->id }}" selected>{{ $transfer->team->name }}</option>

                        @foreach($transfer->team->club->teams as $team)
                            @if($team->id != $transfer->team->id)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endif
                        @endforeach

                    </select>
                @else
                    <select id="team_id" name="team_id" class="browser-default" disabled>
                        <option value="0" selected disabled>{{ trans('general.choose_first', ['name' => trans('models.club')])}}</option>
                    </select>
                @endif


            </div>

        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('general.visible') }}
                        <input name="visible" type="hidden" value="false">
                        @if($transfer->visible)
                            <input name="visible" type="checkbox" value="true" checked>
                        @else
                            <input name="visible" type="checkbox" value="true">
                        @endif
                        <span class="lever"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12">
                @include('backoffice.partial.button', ['color' => 'green', 'icon' => 'save', 'text' => trans('general.save')])
            </div>
        </div>

    </form>
@endsection

@section('scripts')

    @include('backoffice.partial.pick_a_date_js')
    @include('backoffice.partial.update_team_list_js')

    <script>
        $(function () {
            $('input.autocomplete').autocomplete({
                data: {

                    @foreach(App\Player::all() as $player)

                            @if ($player->nickname && $player->picture)

                    "#{{ $player->id }} {{ $player->name }} ({{ $player->nickname }})": '{{ $player->picture }}',

                    @elseif (!$player->nickname && $player->picture)

                    "#{{ $player->id }} {{ $player->name }}": '{{ $player->picture }}',

                    @elseif ($player->nickname && !$player->picture)

                    "#{{ $player->id }} {{ $player->name }} ({{ $player->nickname }})": '{{ config('custom.default_profile_pic') }}',

                    @elseif (!$player->nickname && !$player->picture)
                    "#{{ $player->id }} {{ $player->name }}": '{{ config('custom.default_profile_pic') }}',
                    @endif

                    @endforeach
                },
                limit: 20,
                onAutocomplete: function(val) {

                    var str = val.toString();
                    var hash_id = str.match("#[0-9]+");
                    var id = hash_id[0].replace('#', '');

                    var m1 = $("#player_id");
                    m1.attr('value', id);
                    Materialize.updateTextFields();

                    console.log(id);
                },
                minLength: 1
            });
        })
    </script>
@endsection