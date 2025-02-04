@extends('front.layouts.default-page')

@section('head-content')
    <title>Gerir Notificações</title>
@endsection

@section('content')
    <div class="container">
        <h1 class="hide-on-med-and-down">Gerir Notificações</h1>
        <p class="flow-text">Está a gerir as definições de notificação do clube  <strong>{{ $club->name }}</strong>.</p>

        @if(count($errors) > 0)
            <div class="row">
                <div class="col s12">
                    @include('front.partial.form_errors')
                </div>
            </div>
        @endif

        <form action="{{ route('front.save_manage_notifications', ['uuid' => $uuid]) }}" method="POST">
            {{ csrf_field() }}

            <div class="divider" style="margin-bottom: 20px"></div>

            <input type="hidden" name="pin" value="{{ $pin }}">

            <div class="row">
                <div class="col s6">
                    <span class="flow-text">Notificações Ativadas?</span>
                </div>
                <div class="col s6">
                    <div class="right">
                        <div class="switch">
                            <label>
                                Não
                                <input name="notifications_enabled" type="checkbox" value="true" @if($club->notifications_enabled)checked="checked"@endif>
                                <span class="lever"></span>
                                Sim
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="divider" style="margin-bottom: 20px"></div>

            <div class="row">
                <div class="col s12">
                    <div class="input-field">
                        <input name="contact_email" id="contact_email" type="email" class="validate" value="{{ old('contact_email', $contact_email) }}" required>
                        <label for="contact_email">Email de Contacto</label>
                    </div>
                </div>
            </div>

            <div class="divider" style="margin-bottom: 20px"></div>

            <div class="row">
                <!-- Submit Button -->
                <div class="col s12">
                    <button class="btn waves-effect waves-light green right" type="submit" name="action">Guardar
                        <i class="material-icons right">send</i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="container">
            <small class="grey-text">
                Poderá sempre voltar a esta página e volta a alterar as definições de notificação. Por favor tenha em conta que não
                serão reenviados emails de notificação para este clube enquanto as notificações estiverem desativadas. Os emails têm
                uma hora para serem enviados, só serão notificatos aqueles que naquele momento tenham as notificações ativadas.
            </small>
        </div>
    </div>
@endsection
