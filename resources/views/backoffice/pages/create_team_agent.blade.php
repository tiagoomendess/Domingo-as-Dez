@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.add') }} {{ trans('models.team_agent') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.add') }} {{ trans('models.team_agent') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('team_agents.store') }}" method="POST" enctype="multipart/form-data">

        {{ csrf_field() }}

        <div class="row">

            <div class="input-field col s12 m4 l3">
                <input name="name" id="name" type="text" class="validate" value="{{ old('name') }}" required>
                <label for="name">{{ trans('general.name') }}</label>
            </div>

            <div class="input-field col s12 m4 l3">
                <input name="email" id="email" type="email" class="validate" value="{{ old('email') }}">
                <label for="email">{{ trans('general.email') }} ({{ trans('general.optional') }})</label>
            </div>

        </div>

        <div class="row">

            <div class="input-field col s12 m4 l3">
                <input name="phone" id="phone" type="text" class="validate" value="{{ old('phone') }}">
                <label for="phone">{{ trans('general.phone') }} ({{ trans('general.optional') }})</label>
            </div>

            <div class="input-field col s12 m4 l3">
                <input name="external_id" id="external_id" type="text" class="validate" value="{{ old('external_id') }}">
                <label for="external_id">{{ trans('models.external_id') }} ({{ trans('general.optional') }})</label>
            </div>

        </div>

        <div class="row">

            <div class="col s12 m2 l2">
                <label>{{ trans('models.club') }} ({{ trans('general.optional') }})</label>
                <select id="club_id" name="club_id" class="browser-default" onchange="updateTeamList('club_id', 'team_id')">
                    <option disabled value="0" selected>{{ trans('general.choose_option') }}</option>
                    <option value="0">{{ trans('general.none') }}</option>
                    @foreach(App\Club::all()->sortBy('name') as $club)
                        <option value="{{ $club->id }}">{{ $club->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col s12 m2 l2">
                <label>{{ trans('models.team') }} ({{ trans('general.optional') }})</label>
                <select id="team_id" name="team_id" class="browser-default">
                    <option value="" selected>{{ trans('general.none') }}</option>
                </select>
            </div>

            <div class="col s12 m4 l2">
                <label>{{ trans('models.agent_type') }}</label>
                <select name="agent_type" class="browser-default" required>
                    <option disabled value="" selected>{{ trans('general.choose_option') }}</option>
                    <option value="manager" {{ old('agent_type') == 'manager' ? 'selected' : '' }}>{{ trans('agent_types.manager') }}</option>
                    <option value="assistant_manager" {{ old('agent_type') == 'assistant_manager' ? 'selected' : '' }}>{{ trans('agent_types.assistant_manager') }}</option>
                    <option value="goalkeeper_manager" {{ old('agent_type') == 'goalkeeper_manager' ? 'selected' : '' }}>{{ trans('agent_types.goalkeeper_manager') }}</option>
                    <option value="director" {{ old('agent_type') == 'director' ? 'selected' : '' }}>{{ trans('agent_types.director') }}</option>
                </select>
            </div>

        </div>

        <div class="row">

            <div class="input-field col s6 m4 l3">
                <input type="hidden" name="player_id" id="player_id" value="{{ old('player_id') }}">
                <input type="text" id="player_search" class="autocomplete" placeholder="{{ trans('general.type_to_search') }}">
                <label for="player_search">{{ trans('models.player') }} ({{ trans('general.optional') }})</label>
            </div>

            <div class="input-field col s12 m4 l3">
                <input id="birth_date" name="birth_date" type="text" class="datepicker" value="{{ old('birth_date') }}">
                <label for="birth_date">{{ trans('general.birth_date') }} ({{ trans('general.optional') }})</label>
            </div>

        </div>

        <div class="row">
            <div class="file-field input-field col s12 m8 l6">
                <div class="btn">
                    <span>{{ trans('models.picture') }} ({{ trans('general.optional') }})</span>
                    <input name="picture" type="file">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m8 l6">
                <p class="flow-text center">
                    {{ trans('general.or') }}
                </p>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input name="picture_url" id="picture_url" type="text" class="validate" value="{{ old('picture_url') }}">
                <label for="picture_url">{{ trans('general.url') }} {{ trans('models.picture') }} ({{ trans('general.optional') }})</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12">
                @include('backoffice.partial.button', ['color' => 'green', 'icon' => 'send', 'text' => trans('general.create')])
            </div>
        </div>

    </form>
@endsection

@section('scripts')
    @include('backoffice.partial.update_team_list_js')
    @include('backoffice.partial.pick_a_date_js')
    @include('backoffice.partial.player_autocomplete_js')
@endsection
