@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.player') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.player') }}</h1>
        </div>
    </div>


    <div class="row">

        <div class="input-field col s12 m4 l3">
            <input name="name" id="name" type="text" class="validate" value="{{ $player->name }}" disabled>
            <label for="name">{{ trans('general.name') }}</label>
        </div>

        <div class="input-field col s12 m4 l3">
            <input name="nickname" id="nickname" type="text" class="validate" value="{{ $player->nickname }}" disabled>
            <label for="nickname">{{ trans('general.nickname') }}</label>
        </div>

    </div>

    <div class="row">

        <div class="input-field col s12 m4 l3">
            <input name="association_id" id="association_id" type="text" class="validate" value="{{ $player->association_id }}" disabled>
            <label for="association_id">{{ trans('models.association_id') }}</label>
        </div>

        <div class="col s12 m4 l3">
            <label>{{ trans('models.position') }}</label>
            <select name="position" class="browser-default" disabled>

                <option value="none" disabled selected>{{ trans('general.' . $player->position) }}</option>


            </select>
        </div>

    </div>

    <div class="row">

        <div class="col s6 m4 l3">
            <label>{{ trans('models.club') }}</label>
            <select id="club_id" name="club_id" class="browser-default" disabled>
                @if($player->getTeam())
                    <option disabled value="0" selected>{{ $player->getTeam()->club->name }}</option>
                @else
                    <option disabled value="0" selected>{{ trans('general.none') }}</option>
                @endif

            </select>
        </div>

        <div class="col s6 m4 l3">
            <label>{{ trans('models.team') }}</label>
            <select id="team_id" name="team_id" class="browser-default" disabled>
                @if($player->getTeam())
                    <option disabled value="0" selected>{{ $player->getTeam()->name }}</option>
                @else
                    <option disabled value="0" selected>{{ trans('general.none') }}</option>
                @endif
            </select>
        </div>

    </div>

    <div class="row">

        <div class="col s12 m4 l3">
            @if($player->picture)
                <img width="100%" class="materialboxed" src="{{ $player->picture }}">
            @else
                <img width="100%" class="materialboxed"  src="{{ config('custom.default_profile_pic') }}">
            @endif
        </div>

    </div>

    <div class="row">

        <div class="input-field col s12 m4 l3">
            <input name="phone" id="phone" type="text" class="validate" value="{{ $player->phone }}" disabled>
            <label for="phone">{{ trans('general.phone') }}</label>
        </div>

        <div class="input-field col s12 m4 l3">
            <input name="email" id="email" type="email" class="validate" value="{{ $player->email }}" disabled>
            <label for="email">{{ trans('general.email') }}</label>
        </div>

    </div>

    <div class="row">
        <div class="input-field col s12 m8 l6">
            <input disabled name="facebook_profile" id="url" type="text" class="validate" value="{{ $player->facebook_profile }}">
            <label for="facebook_profile">{{ trans('models.facebook_profile') }}</label>
        </div>
    </div>

    <div class="row">
        <div class="input-field col s12 m8 l6">
            <textarea disabled id="obs" name="obs" class="materialize-textarea" rows="1">{{ $player->obs }}</textarea>
            <label for="obs">{{ trans('models.obs') }}</label>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <div class="switch">
                <label>
                    {{ trans('general.visible') }}
                    <input name="visible" type="hidden" value="false">
                    @if($player->visible)
                        <input name="visible" type="checkbox" value="true" checked disabled>
                    @else
                        <input name="visible" type="checkbox" value="true" disabled>
                    @endif
                    <span class="lever"></span>
                </label>
            </div>
        </div>
    </div>

    @if(Auth::user()->haspermission('players.edit'))
        @include('backoffice.partial.model_options', [
            'delete_route' => route('players.destroy', ['player' => $player]),
            'edit_route' => route('players.edit', ['player' => $player])
        ])
    @endif


@endsection