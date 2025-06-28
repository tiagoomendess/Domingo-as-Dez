@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.edit') }} {{ trans('models.team_agent') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.edit') }} {{ trans('models.team_agent') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('team_agents.update', ['team_agent' => $teamAgent]) }}" method="POST" enctype="multipart/form-data">

        {{ csrf_field() }}

        {{ method_field('PUT') }}

        <div class="row">

            <div class="input-field col s12 m4 l3">
                <input name="name" id="name" type="text" class="validate" value="{{ old('name', $teamAgent->name) }}" required>
                <label for="name">{{ trans('general.name') }}</label>
            </div>

            <div class="input-field col s12 m4 l3">
                <input name="email" id="email" type="email" class="validate" value="{{ old('email', $teamAgent->email) }}">
                <label for="email">{{ trans('general.email') }} ({{ trans('general.optional') }})</label>
            </div>

        </div>

        <div class="row">

            <div class="input-field col s12 m4 l3">
                <input name="phone" id="phone" type="text" class="validate" value="{{ old('phone', $teamAgent->phone) }}">
                <label for="phone">{{ trans('general.phone') }} ({{ trans('general.optional') }})</label>
            </div>

            <div class="input-field col s12 m4 l3">
                <input name="external_id" id="external_id" type="text" class="validate" value="{{ old('external_id', $teamAgent->external_id) }}">
                <label for="external_id">{{ trans('models.external_id') }} ({{ trans('general.optional') }})</label>
            </div>

        </div>

        <div class="row">

            <div class="col s12 m2 l2">
                <label>{{ trans('models.club') }} ({{ trans('general.optional') }})</label>
                <select id="club_id" name="club_id" class="browser-default" onchange="updateTeamList('club_id', 'team_id')">
                    <option disabled value="0" {{ !$teamAgent->team || !$teamAgent->team->club ? 'selected' : '' }}>{{ trans('general.choose_option') }}</option>
                    <option value="0" {{ !$teamAgent->team || !$teamAgent->team->club ? 'selected' : '' }}>{{ trans('general.none') }}</option>
                    @foreach(App\Club::all()->sortBy('name') as $club)
                        <option value="{{ $club->id }}" {{ old('club_id', $teamAgent->team ? $teamAgent->team->club_id : '') == $club->id ? 'selected' : '' }}>
                            {{ $club->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col s12 m2 l2">
                <label>{{ trans('models.team') }} ({{ trans('general.optional') }})</label>
                <select id="team_id" name="team_id" class="browser-default">
                    <option value="" {{ !$teamAgent->team ? 'selected' : '' }}>{{ trans('general.none') }}</option>
                    @if($teamAgent->team)
                        <option value="{{ $teamAgent->team->id }}" selected>{{ $teamAgent->team->name }}</option>
                    @endif
                </select>
            </div>

            <div class="col s12 m4 l2">
                <label>{{ trans('models.agent_type') }}</label>
                <select name="agent_type" class="browser-default" required>
                    <option disabled value="" selected>{{ trans('general.choose_option') }}</option>
                    <option value="manager" {{ old('agent_type', $teamAgent->agent_type) == 'manager' ? 'selected' : '' }}>{{ trans('agent_types.manager') }}</option>
                    <option value="assistant_manager" {{ old('agent_type', $teamAgent->agent_type) == 'assistant_manager' ? 'selected' : '' }}>{{ trans('agent_types.assistant_manager') }}</option>
                    <option value="goalkeeper_manager" {{ old('agent_type', $teamAgent->agent_type) == 'goalkeeper_manager' ? 'selected' : '' }}>{{ trans('agent_types.goalkeeper_manager') }}</option>
                    <option value="director" {{ old('agent_type', $teamAgent->agent_type) == 'director' ? 'selected' : '' }}>{{ trans('agent_types.director') }}</option>
                </select>
            </div>

        </div>

        <div class="row">

            <div class="input-field col s6 m4 l3">
                <input type="hidden" name="player_id" id="player_id" value="{{ old('player_id', $teamAgent->player_id) }}">
                <input type="text" id="player_search" class="autocomplete" placeholder="{{ trans('general.type_to_search') }}" value="{{ old('player_id', $teamAgent->player_id) ? ($teamAgent->player ? $teamAgent->player->name : '') : '' }}">
                <label for="player_search">{{ trans('models.player') }} ({{ trans('general.optional') }})</label>
            </div>

            <div class="input-field col s12 m4 l3">
                <input id="birth_date" name="birth_date" type="text" class="datepicker" value="{{ old('birth_date', $teamAgent->birth_date ? \Carbon\Carbon::parse($teamAgent->birth_date)->format('Y-m-d') : '') }}">
                <label for="birth_date">{{ trans('general.birth_date') }} ({{ trans('general.optional') }})</label>
            </div>

        </div>

        <div class="row">

            <div class="col s12 m4 l3">
                @if($teamAgent->picture)
                    <img width="100%" class="materialboxed" src="{{ $teamAgent->picture }}">
                @else
                    <img width="100%" class="materialboxed" src="{{ config('custom.default_profile_pic') }}">
                @endif
            </div>

            <div class="col s12 m4 l3">

                <div class="col s12">

                    <h4 class="flow-text">{{ trans('general.change_picture') }} ({{ trans('general.optional') }})</h4>
                    <div class="divider"></div>

                </div>

                <div class="file-field input-field col s12">
                    <div class="btn">
                        <span>{{ trans('general.upload') }}</span>
                        <input name="picture" type="file">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text">
                    </div>

                </div>

                <div class="col s12">
                    <p class="center">
                        {{ trans('general.or') }}
                    </p>
                </div>

                <div class="input-field col s12">
                    <input name="picture_url" id="picture_url" type="text" class="validate" value="{{ old('picture_url') }}">
                    <label for="picture_url">{{ trans('general.url') }} {{ trans('models.picture') }} ({{ trans('general.optional') }})</label>

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
    @include('backoffice.partial.update_team_list_js')
    @include('backoffice.partial.pick_a_date_js')
    @include('backoffice.partial.player_autocomplete_js')
@endsection
