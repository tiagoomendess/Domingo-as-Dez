@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>Adicionar Bloqueio</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>Adicionar Bloqueio</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('score_report_bans.store') }}" method="POST">
        {{ csrf_field() }}

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input name="uuid" id="uuid" type="text" class="validate" value="{{ old('uuid') }}" >
                <label for="uuid">UUID</label>
            </div>
        </div>

        <div class="row">
            <div class="col input-field col s8 m6 l4">
                <input name="ip_address" id="ip_address" type="text" class="validate" value="{{ old('ip_address') }}"
                       >
                <label for="ip_address">Endereço de IP</label>
            </div>
            <div class="col input-field col s4 m2 l2">
                <input name="user_id" id="user_id" type="number" class="validate" value="{{ old('user_id') }}"
                       >
                <label for="user_id">Utilizador</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input name="user_agent" id="user_agent" type="text" class="validate" value="{{ old('user_agent') }}">
                <label for="user_agent">User Agent</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input name="reason" id="reason" type="text" class="validate" value="{{ old('reason') }}" maxlength="255" required>
                <label for="reason">Razão*</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m8 l6">
                <label for="ban_days">Bloquear por quantos dias?*</label>
                <p class="range-field">
                    <input type="range" id="ban_days" name="ban_days" min="1" max="60" value="{{ old('ban_days', 3) }}"/>
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        Bloqueio Silencioso
                        <input name="shadow_ban" type="hidden" value="false">
                        <input name="shadow_ban" type="checkbox" value="true">
                        <span class="lever"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        Bloqueio Global de IP
                        <input name="ip_ban" type="hidden" value="false">
                        <input name="ip_ban" type="checkbox" value="true">
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
