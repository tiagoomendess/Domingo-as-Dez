@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.transfer') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.transfer') }}</h1>
        </div>
    </div>


        <div class="row">

            <div class="input-field col s10 m4 l3">
                @if($transfer->player->nickname)
                    <input disabled name="player_name" type="text" id="autocomplete-input" class="autocomplete"
                           autocomplete="off" value="#{{ $transfer->player->id }} {{ $transfer->player->name }} ({{ $transfer->player->nickname }})">
                @else
                    <input disabled name="player_name" type="text" id="autocomplete-input" class="autocomplete"
                           autocomplete="off" value="#{{ $transfer->player->id }} {{ $transfer->player->name }}">
                @endif

                <label for="autocomplete">{{ trans('models.player') }}</label>
            </div>

            <div class="input-field col s2 m1 l1">
                <input disabled name="player_id" type="number" id="player_id" value="{{ $transfer->player->id }}">
                <label for="player_id">{{ trans('general.id') }}</label>
            </div>

            <div class="input-field col s12 m3 l2">
                <input disabled name="date" id="date" type="text" class="datepicker" value="{{ $transfer->date }}">
                <label for="date">{{ trans('general.date') }}</label>
            </div>
        </div>

        <div class="row">

            <div class="col s6 m4 l3">
                <label>{{ trans('models.club') }}</label>
                <select disabled id="club_id" name="club_id" class="browser-default">
                    @if($transfer->team)
                        <option disabled value="0" selected>{{ $transfer->team->club->name }}</option>
                    @else
                        <option disabled value="0" selected>{{ trans('general.none') }}</option>
                    @endif

                </select>
            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('models.team') }}</label>
                <select disabled id="team_id" name="team_id" class="browser-default">
                    @if($transfer->team)
                        <option disabled selected>{{ $transfer->team->name }}</option>
                    @else
                        <option disabled value="0" selected>{{ trans('general.none') }}</option>
                    @endif

                </select>
            </div>

        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('general.visible') }}
                        <input name="visible" type="hidden" value="false">
                        @if($transfer->visible)
                            <input name="visible" type="checkbox" value="true" checked disabled>
                        @else
                            <input name="visible" type="checkbox" value="true" disabled>
                        @endif
                        <span class="lever"></span>
                    </label>
                </div>
            </div>
        </div>

    @if(Auth::user()->haspermission('transfers.edit'))
        @include('backoffice.partial.model_options', [
            'delete_route' => route('transfers.destroy', ['transfer' => $transfer]),
            'edit_route' => route('transfers.edit', ['transfer' => $transfer])
        ])
    @endif


@endsection