@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.team_agents') }}</title>
@endsection

@section('content')
    <div class="row no-margin-bottom">
        <div class="col s8">
            <h1>{{ trans('models.team_agents') }}</h1>
        </div>
        <div class="col s4">
            @include('backoffice.partial.search_box_btn')
        </div>
    </div>

    <div class="row no-margin-bottom">
        @include('backoffice.partial.search_box')
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$teamAgents || $teamAgents->count() == 0)
                <p class="flow-text">{{ trans('models.no_team_agents') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.id') }}</th>
                        <th>{{ trans('models.picture') }}</th>
                        <th>{{ trans('general.name') }}</th>
                        <th>{{ trans('general.email') }}</th>
                        <th>{{ trans('models.agent_type') }}</th>
                        <th>{{ trans('models.club') }} ({{ trans('models.team') }})</th>
                    </tr>
                    </thead>

                    @foreach($teamAgents as $teamAgent)

                        <tr>
                            <td>{{ $teamAgent->id }}</td>
                            <td>
                                @if($teamAgent->picture)
                                    <img style="max-height: 30px" src="{{ $teamAgent->picture }}" alt="" class="responsive-img circle"/>
                                @else
                                    <img style="max-height: 30px" src="{{ config('custom.default_profile_pic') }}" alt="" class="responsive-img circle"/>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('team_agents.show', ['team_agent' => $teamAgent]) }}">
                                    {{ $teamAgent->name }}
                                </a>
                            </td>
                            <td>{{ $teamAgent->email }}</td>
                            <td>{{ $teamAgent->getAgentTypeTranslated() }}</td>
                            <td>
                                @if($teamAgent->team && $teamAgent->team->club)
                                    <a href="{{ route('teams.show', ['team' => $teamAgent->team]) }}">
                                        {{ $teamAgent->team->club->name }} ({{ $teamAgent->team->name }})
                                    </a>
                                @elseif($teamAgent->team)
                                    <a href="{{ route('teams.show', ['team' => $teamAgent->team]) }}">
                                        {{ trans('general.unknown') }} ({{ $teamAgent->team->name }})
                                    </a>
                                @else
                                    {{ trans('general.none') }}
                                @endif
                            </td>

                        </tr>

                    @endforeach

                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            {{ $teamAgents->links() }}
        </div>
    </div>

    @if(Auth::user()->hasPermission('team_agents.edit'))
        @include('backoffice.partial.add_model_button', ['route' => route('team_agents.create')])
    @endif

@endsection 