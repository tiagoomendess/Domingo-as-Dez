@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.team_agent') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.team_agent') }}</h1>
        </div>
    </div>

    <div class="row">

        <div class="input-field col s12 m4 l3">
            <input name="name" id="name" type="text" class="validate" value="{{ $teamAgent->name }}" disabled>
            <label for="name">{{ trans('general.name') }}</label>
        </div>

        <div class="input-field col s12 m4 l3">
            <input name="email" id="email" type="email" class="validate" value="{{ $teamAgent->email }}" disabled>
            <label for="email">{{ trans('general.email') }}</label>
        </div>

    </div>

    <div class="row">

        <div class="input-field col s12 m4 l3">
            <input name="phone" id="phone" type="text" class="validate" value="{{ $teamAgent->phone }}" disabled>
            <label for="phone">{{ trans('general.phone') }}</label>
        </div>

        <div class="input-field col s12 m4 l3">
            <input name="external_id" id="external_id" type="text" class="validate" value="{{ $teamAgent->external_id }}" disabled>
            <label for="external_id">{{ trans('models.external_id') }}</label>
        </div>

    </div>

    <div class="row">

        <div class="col s12 m2 l2">
            <label>{{ trans('models.club') }}</label>
            <select id="club_id" name="club_id" class="browser-default" disabled>
                @if($teamAgent->team && $teamAgent->team->club)
                    <option disabled value="0" selected>{{ $teamAgent->team->club->name }}</option>
                @else
                    <option disabled value="0" selected>{{ trans('general.none') }}</option>
                @endif
            </select>
        </div>

        <div class="col s12 m2 l2">
            <label>{{ trans('models.team') }}</label>
            <select id="team_id" name="team_id" class="browser-default" disabled>
                @if($teamAgent->team)
                    <option disabled value="0" selected>{{ $teamAgent->team->name }}</option>
                @else
                    <option disabled value="0" selected>{{ trans('general.none') }}</option>
                @endif
            </select>
        </div>

        <div class="col s12 m4 l2">
            <label>{{ trans('models.agent_type') }}</label>
            <select id="agent_type" name="agent_type" class="browser-default" disabled>
                <option disabled value="0" selected>{{ $teamAgent->getAgentTypeTranslated() }}</option>
            </select>
        </div>

    </div>

    <div class="row">

        <div class="col s6 m4 l3">
            <label>{{ trans('models.player') }}</label>
            <select id="player_id" name="player_id" class="browser-default" disabled>
                @if($teamAgent->player)
                    <option disabled value="0" selected>{{ $teamAgent->player->name }}</option>
                @else
                    <option disabled value="0" selected>{{ trans('general.none') }}</option>
                @endif
            </select>
        </div>

        <div class="input-field col s12 m4 l3">
            <input disabled id="birth_date" name="birth_date" type="text" class="datepicker" value="{{ $teamAgent->birth_date ? \Carbon\Carbon::parse($teamAgent->birth_date)->format('Y-m-d') : '' }}">
            <label for="birth_date">{{ trans('general.birth_date') }}</label>
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

    </div>

    @if($teamAgent->history && $teamAgent->history->count() > 0)
        <div class="row">
            <div class="col s12">
                <h4>{{ trans('general.history') }}</h4>
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.date') }}</th>
                        <th>{{ trans('models.agent_type') }}</th>
                        <th>{{ trans('models.team') }}</th>
                        <th>{{ trans('general.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($teamAgent->history->sortByDesc('started_at') as $history)
                        <tr>
                            <td>{{ $history->started_at->format('d/m/Y') }}</td>
                            <td>{{ $history->getAgentTypeTranslated() }}</td>
                            <td>
                                @if($history->team)
                                    <a href="{{ route('teams.show', ['team' => $history->team]) }}">
                                        {{ $history->team->club->name }} ({{ $history->team->name }})
                                    </a>
                                @else
                                    {{ trans('general.none') }}
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->hasPermission('team_agents.edit'))
                                    <a href="{{ route('team_agent_history.edit', ['team_agent_history' => $history]) }}" class="btn-floating btn-small blue waves-effect waves-light" title="{{ trans('general.edit') }}">
                                        <i class="material-icons">edit</i>
                                    </a>
                                    <form method="POST" action="{{ route('team_agent_history.destroy', ['team_agent_history' => $history]) }}" style="display: inline-block; margin-left: 5px;" onsubmit="return confirm('{{ trans('general.are_you_sure') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-floating btn-small red waves-effect waves-light" title="{{ trans('general.delete') }}">
                                            <i class="material-icons">delete</i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Add History Button -->
    @if(Auth::user()->hasPermission('team_agents.edit'))
        <div class="row">
            <div class="col s12">
                <div class="center-align">
                    <a href="{{ route('team_agent_history.create', ['team_agent_id' => $teamAgent->id]) }}" 
                       class="btn waves-effect waves-light green">
                        <i class="material-icons left">add</i>
                        {{ trans('general.add') }} {{ trans('general.history') }}
                    </a>
                </div>
            </div>
        </div>
    @endif

    @if(Auth::user()->hasPermission('team_agents.edit'))
        @include('backoffice.partial.model_options', [
            'delete_route' => route('team_agents.destroy', ['team_agent' => $teamAgent]),
            'edit_route' => route('team_agents.edit', ['team_agent' => $teamAgent])
        ])
    @endif

@endsection 