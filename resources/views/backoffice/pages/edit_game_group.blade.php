@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.edit') }} {{ trans('models.game_group') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.edit') }} {{ trans('models.game_group') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('gamegroups.update', ['game_group' => $game_group]) }}" method="POST">

        {{ csrf_field() }}

        {{ method_field('PUT') }}

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input required name="name" id="name" type="text" class="validate" value="{{ old('name', $game_group->name) }}">
                <label for="name">{{ trans('models.name') }}</label>
            </div>
        </div>

        <div class="row">

            <div class="col s6 m4 l3">
                <label>{{ trans('models.competition') }}</label>
                <select onchange="updateSeasonList('competition_id', 'season_id')" id="competition_id" name="competition_id" class="browser-default" required>
                    @foreach(App\Competition::all() as $competition)
                        @if($competition->id != $game_group->season->competition->id)
                            <option value="{{ $competition->id }}">{{ $competition->name }}</option>
                        @endif
                    @endforeach
                    <option selected value="{{ $game_group->season->competition->id }}">{{ $game_group->season->competition->name }}</option>
                </select>
            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('models.season') }}</label>
                <select id="season_id" name="season_id" class="browser-default" required>
                    @foreach($game_group->season->competition->seasons as $season)
                        @if ($season->id != $game_group->season->id)
                            <option value="{{ $season->id }}">{{ $season->getName() }}</option>
                        @endif

                    @endforeach

                    <option value="{{ $game_group->season->id }}" selected>{{ $game_group->season->getName() }}</option>
                </select>
            </div>

        </div>

        <div class="row">
            <div class="col s12 m4 l3">
                <label>{{ trans('models.group_rules') }}</label>
                <select name="group_rules_id" class="browser-default" required>
                    <option value="{{ $game_group->group_rules->id }}" selected>{{ $game_group->group_rules->name }}</option>

                    @foreach(\App\GroupRules::all() as $group_rules)
                        @if($group_rules->id != $game_group->group_rules->id)
                            <option value="{{ $group_rules->id }}">{{ $group_rules->name }}</option>
                        @endif
                    @endforeach

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
    @include('backoffice.partial.update_seasons_list_js')
@endsection