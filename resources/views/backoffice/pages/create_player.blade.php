@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.add') }} {{ trans('models.player') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.add') }} {{ trans('models.player') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('players.store') }}" method="POST" enctype="multipart/form-data">

        {{ csrf_field() }}

        <div class="row">

            <div class="input-field col s12 m4 l3">
                <input name="name" id="name" type="text" class="validate" value="{{ old('name') }}" required>
                <label for="name">{{ trans('general.name') }}</label>
            </div>

            <div class="input-field col s12 m4 l3">
                <input name="nickname" id="nickname" type="text" class="validate" value="{{ old('nickname') }}">
                <label for="nickname">{{ trans('general.nickname') }}</label>
            </div>

        </div>

        <div class="row">

            <div class="input-field col s12 m4 l3">
                <input name="association_id" id="association_id" type="text" class="validate" value="{{ old('association_id') }}">
                <label for="association_id">{{ trans('models.association_id') }}</label>
            </div>

            <div class="col s12 m4 l3">
                <label>{{ trans('models.position') }}</label>
                <select name="position" class="browser-default" required>

                    <option value="none" disabled selected>{{ trans('general.choose_option') }}</option>

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
                <select id="club_id" name="club_id" class="browser-default">
                    <option disabled value="0" selected>{{ trans('general.choose_option') }}</option>
                    <option onclick="updateTeamList(0)" value="0">{{ trans('general.none') }}</option>
                    @foreach(App\Club::all() as $club)
                        <option onclick="updateTeamList({{ $club->id }})" value="{{ $club->id }}">{{ $club->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('models.team') }}</label>
                <select id="team_id" name="team_id" class="browser-default" disabled>
                    <option value="" disabled selected>{{ trans('general.choose_first', ['name' => trans('models.club')]) }}</option>
                </select>
            </div>

        </div>

        <div class="row">
            <div class="file-field input-field col s12 m8 l6">
                <div class="btn">
                    <span>{{ trans('models.picture') }}</span>
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
                <label for="picture_url">{{ trans('general.url') }} {{ trans('models.picture') }}</label>

            </div>
        </div>

        <div class="row">

            <div class="input-field col s12 m4 l3">
                <input name="phone" id="phone" type="text" class="validate" value="{{ old('phone') }}">
                <label for="phone">{{ trans('general.phone') }}</label>
            </div>

            <div class="input-field col s12 m4 l3">
                <input name="email" id="email" type="email" class="validate" value="{{ old('email') }}">
                <label for="email">{{ trans('general.email') }}</label>
            </div>

        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input name="facebook_profile" id="url" type="text" class="validate" value="{{ old('facebook_profile') }}">
                <label for="facebook_profile">{{ trans('models.facebook_profile') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <textarea id="obs" name="obs" class="materialize-textarea" rows="1">{{ old('obs') }}</textarea>
                <label for="obs">{{ trans('models.obs') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('general.visible') }}
                        <input name="visible" type="hidden" value="false">
                        <input name="visible" type="checkbox" value="true" checked>
                        <span class="lever"></span>
                    </label>
                </div>
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
@endsection