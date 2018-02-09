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

        <div class="file-field input-field col s12 m8 l6">
            <div class="btn">
                <span>{{ trans('models.picture') }}</span>
                <input name="picture" type="file" disabled>
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
            <input name="picture_url" id="picture_url" type="text" class="validate" value="{{ $player->picture }}" disabled>
            <label for="picture_url">{{ trans('general.url') }} {{ trans('models.picture') }}</label>
        </div>
    </div>

    <div class="row">
        <div class="input-field col s12 m8 l6">
            <input name="facebook_profile" id="url" type="text" class="validate" value="{{ $player->facebook_profile }}">
            <label for="facebook_profile">{{ trans('models.facebook_profile') }}</label>
        </div>
    </div>

    <div class="row">
        <div class="input-field col s12 m8 l6">
            <textarea id="obs" name="obs" class="materialize-textarea" rows="1">{{ $player->obs }}</textarea>
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


@endsection