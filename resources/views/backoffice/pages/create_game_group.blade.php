@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.add') }} {{ trans('models.game_group') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.add') }} {{ trans('models.game_group') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('seasons.store') }}" method="POST">

        {{ csrf_field() }}

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input required name="name" id="name" type="text" class="validate" value="{{ old('name') }}">
                <label for="name">{{ trans('models.name') }}</label>
            </div>
        </div>

        <div class="row">

            <div class="col s6 m4 l3">
                <label>{{ trans('models.competition') }}</label>
                <select onchange="updateSeasonList('competition_id', 'season_id')" id="competition_id" name="competition_id" class="browser-default" required>
                    <option disabled value="0" selected>{{ trans('general.choose_option') }}</option>
                    @foreach(App\Competition::all() as $competition)
                        <option value="{{ $competition->id }}">{{ $competition->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('models.season') }}</label>
                <select id="season_id" name="season_id" class="browser-default" disabled required>
                    <option value="0" disabled selected>{{ trans('general.choose_first', ['name' => trans('models.competition')]) }}</option>
                </select>
            </div>

        </div>

        <div class="row">
            <div class="col s12 m4 l3">
                <label>{{ trans('models.group_rules') }}</label>
                <select name="competition" class="browser-default" required>
                    <option value="none" disabled selected>{{ trans('general.choose_option') }}</option>

                    @foreach(\App\GroupRules::all() as $group_rules)
                        <option value="{{ $group_rules->id }}">{{ $group_rules->name }}</option>
                    @endforeach

                </select>
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
    @include('backoffice.partial.update_seasons_list_js')
@endsection