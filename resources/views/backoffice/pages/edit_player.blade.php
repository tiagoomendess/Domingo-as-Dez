@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.edit') }} {{ trans('models.player') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.edit') }} {{ trans('models.player') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('players.update', ['player' => $player]) }}" method="POST" enctype="multipart/form-data">

        {{ csrf_field() }}

        {{ method_field('PUT') }}

        <div class="row">

            <div class="input-field col s12 m4 l3">
                <input name="name" id="name" type="text" class="validate" value="{{ old('name', $player->name) }}" required>
                <label for="name">{{ trans('general.name') }}</label>
            </div>

            <div class="input-field col s12 m4 l3">
                <input name="nickname" id="nickname" type="text" class="validate" value="{{ old('nickname', $player->nickname) }}">
                <label for="nickname">{{ trans('general.nickname') }}</label>
            </div>

        </div>

        <div class="row">

            <div class="input-field col s12 m4 l3">
                <input name="association_id" id="association_id" type="text" class="validate" value="{{ old('association_id', $player->association_id) }}">
                <label for="association_id">{{ trans('models.association_id') }}</label>
            </div>

            <div class="col s12 m4 l3">
                <label>{{ trans('models.position') }}</label>
                <select name="position" class="browser-default" required>

                    <option value="{{ $player->position }}" selected>{{ trans('general.' . $player->position) }}</option>

                    <option value="none">{{ trans('general.none') }}</option>
                    <option value="goalkeeper">{{ trans('general.goalkeeper') }}</option>
                    <option value="defender">{{ trans('general.defender') }}</option>
                    <option value="midfielder">{{ trans('general.midfielder') }}</option>
                    <option value="striker">{{ trans('general.striker') }}</option>

                </select>
            </div>

        </div>

        <div class="row">

            <div class="col s6 m4 l3">
                <label>{{ trans('models.club') }}</label>
                <select id="club_id" name="club_id" class="browser-default" disabled>
                    <option disabled value="0" selected>{{ trans('general.choose_option') }}</option>
                    <option value="0">{{ trans('general.none') }}</option>
                </select>
            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('models.team') }}</label>
                <select id="team_id" name="team_id" class="browser-default" disabled>
                    <option value="0" disabled selected>{{ trans('general.choose_first', ['name' => trans('models.club')]) }}</option>
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

            <div class="col s12 m4 l3">

                <div class="col s12">

                    <h4 class="flow-text">Alterar Fotografia</h4>
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
                    <label for="picture_url">{{ trans('general.url') }} {{ trans('models.picture') }}</label>

                </div>

            </div>

        </div>

        <div class="row">

            <div class="input-field col s12 m4 l3">
                <input name="phone" id="phone" type="text" class="validate" value="{{ old('phone', $player->phone) }}">
                <label for="phone">{{ trans('general.phone') }}</label>
            </div>

            <div class="input-field col s12 m4 l3">
                <input name="email" id="email" type="email" class="validate" value="{{ old('email', $player->email) }}">
                <label for="email">{{ trans('general.email') }}</label>
            </div>

        </div>

        <div class="row">
            <div class="input-field col s12 m4 l3">
                <input name="facebook_profile" id="url" type="text" class="validate" value="{{ old('facebook_profile', $player->facebook_profile) }}">
                <label for="facebook_profile">{{ trans('models.facebook_profile') }}</label>
            </div>

            <div class="input-field col s12 m4 l3">
                <input id="birth_date" name="birth_date" type="text" class="datepicker" value="{{ old('birth_date', $player->birth_date ? \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $player->birth_date)->format("Y-m-d") : '') }}">
                <label for="birth_date">{{ trans('general.birth_date') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <textarea id="obs" name="obs" class="materialize-textarea" rows="1">{{ old('obs', $player->obs) }}</textarea>
                <label for="obs">{{ trans('models.obs') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('general.visible') }}
                        <input name="visible" type="hidden" value="false">
                        @if ($player->visible)
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
    @include('backoffice.partial.update_team_list_js')
    @include('backoffice.partial.pick_a_date_js')
@endsection