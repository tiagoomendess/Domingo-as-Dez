@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.create') }} {{ trans('models.team_agent_history') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.create') }} {{ trans('models.team_agent_history') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('team_agent_history.store') }}" method="POST">

        {{ csrf_field() }}

        <!-- Hidden team agent ID field -->
        <input type="hidden" name="team_agent_id" value="{{ old('team_agent_id', $selectedTeamAgentId) }}">

        <div class="row">

            <div class="input-field col s12 m4 l3">
                <input id="started_at" name="started_at" type="text" class="datepicker" value="{{ old('started_at', now()->format('Y-m-d')) }}" required>
                <label for="started_at">{{ trans('general.started_at') }}</label>
            </div>

            <div class="col s12 m4 l3">
                <label>{{ trans('models.agent_type') }}</label>
                <select name="agent_type" class="browser-default" required>
                    <option disabled value="">{{ trans('general.choose_option') }}</option>
                    <option value="manager" {{ old('agent_type') == 'manager' ? 'selected' : '' }}>{{ trans('agent_types.manager') }}</option>
                    <option value="assistant_manager" {{ old('agent_type') == 'assistant_manager' ? 'selected' : '' }}>{{ trans('agent_types.assistant_manager') }}</option>
                    <option value="goalkeeper_manager" {{ old('agent_type') == 'goalkeeper_manager' ? 'selected' : '' }}>{{ trans('agent_types.goalkeeper_manager') }}</option>
                    <option value="director" {{ old('agent_type') == 'director' ? 'selected' : '' }}>{{ trans('agent_types.director') }}</option>
                </select>
            </div>

        </div>

        <div class="row">

            <div class="col s12 m4 l3">
                <label>{{ trans('models.club') }} ({{ trans('general.optional') }})</label>
                <select id="club_id" name="club_id" class="browser-default" onchange="updateTeamList('club_id', 'team_id')">
                    <option disabled value="">{{ trans('general.choose_option') }}</option>
                    <option value="" {{ old('club_id') == '' ? 'selected' : '' }}>{{ trans('general.none') }}</option>
                    @foreach(App\Club::all()->sortBy('name') as $club)
                        <option value="{{ $club->id }}" {{ old('club_id') == $club->id ? 'selected' : '' }}>
                            {{ $club->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col s12 m4 l3">
                <label>{{ trans('models.team') }} ({{ trans('general.optional') }})</label>
                <select id="team_id" name="team_id" class="browser-default">
                    <option value="" {{ old('team_id') == '' ? 'selected' : '' }}>{{ trans('general.none') }}</option>
                </select>
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
@endsection 