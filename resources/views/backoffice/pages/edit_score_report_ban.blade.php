@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>Editar Bloqueio</title>
@endsection

@section('content')
    <form method="POST" action="{{route('score_report_bans.update', ['ban' => $ban])}}">

        {{ csrf_field() }}
        {{ method_field('PUT') }}

        <div class="row">
            <div class="col s12">
                <h1>Editar Bloqueio {{ $ban->id }}</h1>
            </div>
        </div>

        @if(count($errors) > 0)
            <div class="row">
                <div class="col s12">
                    @include('backoffice.partial.form_errors')
                </div>
            </div>
        @endif

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input name="uuid" id="uuid" type="text" class="validate" value="{{ $ban->uuid }}" disabled>
                <label for="uuid">UUID</label>
            </div>
        </div>

        <div class="row">
            <div class="col input-field col s8 m6 l4">
                <input name="ip_address" id="ip_address" type="text" class="validate" value="{{ $ban->ip_address }}"
                       disabled>
                <label for="ip_address">Endereço de IP</label>
            </div>
            <div class="col input-field col s4 m2 l2">
                <input name="user_id" id="user_id" type="text" class="validate" value="{{ $ban->user_id ?? '-' }}"
                       disabled>
                <label for="user_id">Utilizador</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input name="user_agent" id="user_agent" type="text" class="validate" value="{{ $ban->user_agent }}"
                       disabled>
                <label for="user_agent">User Agent</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input name="reason" id="reason" type="text" class="validate" value="{{ $ban->reason }}" maxlength="255" required>
                <label for="reason">Razão</label>
            </div>
        </div>

        <div class="row">
            <?php
            $carbon = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $ban->expires_at);
            $user = \Illuminate\Support\Facades\Auth::user();
            $carbon->timezone = $user->profile->timezone;
            ?>

            <div class="input-field col s4 m3 l2">
                <input id="expires_at_date" name="expires_at_date" type="text" class="datepicker" required value="{{$carbon->format("Y-m-d")}}">
                <label for="date">Expira {{ trans('general.day') }}</label>
            </div>

            <div class="input-field col s4 m2 l2">
                <input id="expires_at_time" name="expires_at_time" type="text" class="timepicker" required value="{{ $carbon->format("H:i") }}">
                <label for="expires_at_time">Expira {{ trans('general.hour') }}</label>
            </div>

            <div class="col s4 m3 l2">
                @include('backoffice.partial.select_timezone', ['timezone_name' => $user->profile->timezone, 'timezone_value' => $user->profile->timezone])
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        Bloqueio Silencioso
                        <input name="shadow_ban" type="hidden" value="false">
                        @if($ban->shadow_ban)
                            <input name="shadow_ban" type="checkbox" value="true" checked>
                        @else
                            <input name="shadow_ban" type="checkbox" value="true">
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
                        Bloqueio Global de IP
                        <input name="ip_ban" type="hidden" value="false">
                        @if($ban->ip_ban)
                            <input name="ip_ban" type="checkbox" value="true" checked>
                        @else
                            <input name="ip_ban" type="checkbox" value="true">
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
    @include('backoffice.partial.pick_a_date_js')
    @include('backoffice.partial.pick_a_time_js')
@endsection
