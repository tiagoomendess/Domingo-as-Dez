@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.club') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.club') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="input-field col s12 m8 l6">
            <input name="name" id="name" type="text" class="validate" value="{{ $club->name }}" disabled>
            <label for="name">{{ trans('general.name') }}</label>
        </div>
    </div>

    <div class="row">
        <div class="input-field col s12 m4 l3">
            <input name="founding_date" id="founding_date" type="text" class="validate" value="{{ $club->founding_date }}" disabled>
            <label for="founding_date">Data de Fundação</label>
        </div>
    </div>

    <div class="row">
        <div class="file-field input-field col s10 m7 l5">
            <div class="btn">
                <span>{{ trans('models.emblem') }}</span>
                <input name="emblem" type="file" disabled>
            </div>
            <div class="file-path-wrapper">
                <input class="file-path validate" type="text" value="{{ $club->emblem }}">
            </div>
        </div>

        <div class="col s2 m1 l1">
            @if($club->emblem)
                <img style="max-height: 60px" src="{{ $club->emblem }}" alt="" class="responsive-img"/>
            @else
                <img style="max-height: 60px" src="{{ config('custom.default_emblem') }}" alt="" class="responsive-img"/>
            @endif

        </div>
    </div>

    <div class="row">
        <div class="input-field col s12 m8 l6">
            <input name="website" id="website" type="text" class="validate" value="{{ $club->website }}" disabled>
            <label for="website">{{ trans('models.website') }}</label>
        </div>
    </div>

    <div class="row">
        <div class="input-field col s12 m4 l3">
            <input name="contact_email" id="contact_email" type="email" class="validate" value="{{ $club->contact_email }}" disabled>
            <label for="contact_email">Email de Contacto</label>
        </div>
        <div class="input-field col s12 m4 l3">
            <input name="admin_user_id" id="admin_user_id" type="number" class="validate" value="{{ $club->admin_user_id }}" disabled>
            <label for="admin_user_id">Admin User ID</label>
        </div>
    </div>

    <div class="row">
        <div class="input-field col s12 m4 l3">
            <input disabled name="priority" id="priority" type="number" class="validate" value="{{ $club->priority }}">
            <label for="priority">Prioridade</label>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <div class="switch">
                <label>
                    Notificações por email
                    <input name="notifications_enabled" type="hidden" value="false">
                    @if($club->notifications_enabled)
                        <input disabled name="notifications_enabled" type="checkbox" value="true" checked>
                    @else
                        <input disabled name="notifications_enabled" type="checkbox" value="true">
                    @endif
                    <span class="lever"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <div class="switch">
                <label>
                    {{ trans('general.visible') }}
                    <input name="visible" type="hidden" value="false">
                    @if($club->visible)
                        <input disabled name="visible" type="checkbox" value="true" checked>
                    @else
                        <input disabled name="visible" type="checkbox" value="true">
                    @endif
                    <span class="lever"></span>
                </label>
            </div>
        </div>
    </div>

    @if(Auth::user()->haspermission('clubs.edit'))
        @include('backoffice.partial.model_options', [
            'delete_route' => route('clubs.destroy', ['club' => $club]),
            'edit_route' => route('clubs.edit', ['club' => $club])
        ])
    @endif

@endsection