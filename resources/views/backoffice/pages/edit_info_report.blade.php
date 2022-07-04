@extends('backoffice.layouts.default-page')

@section('head-content')
    <title> {{ trans('models.info_report') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.info_report') }}</h1>
        </div>
    </div>

    <form method="POST" action="{{ route('info_reports.update', ['id' => $info->id]) }}">

        {{ csrf_field() }}
        {{ method_field('PUT') }}

        @if(count($errors) > 0)
            <div class="row">
                <div class="col s12">
                    @include('backoffice.partial.form_errors')
                </div>
            </div>
        @endif

        <div class="row">
            <div class="input-field col s6 m4 l3">
                <input style="color: black" id="code" type="text" disabled value="{{ $info->code }}">
                <label for="code">Código</label>
            </div>
            <div class="input-field col s6 m4 l3">
                <input id="date" type="text"
                       style="color: black"
                       disabled
                       value="{{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $info->created_at)->timezone('Europe/Lisbon')->format("d/m/Y H:i") }}">
                <label for="date">Enviada</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <textarea id="content" name="content" class="materialize-textarea validate"
                          style="color: black"
                          data-length="500" autocomplete="off"
                          disabled>{{ $info->content }}</textarea>
                <label for="content">Informação</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input style="color: black" id="source" type="text" disabled value="{{ $info->source }}">
                <label for="source">Fonte</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input style="color: black" id="source" type="text" disabled value="{{ $info->user_id ? $info->user->name . " - " . $info->user->email : 'Anonimo' }}">
                <label for="source">Utilizador</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m4 l3">
                <label>Estado</label>
                <select name="status" class="browser-default" required>

                    <option value="{{ $info->status }}" selected>{{ trans('front.info_report_status_' . $info->status) }}</option>

                    @foreach(\App\InfoReport::ALLOWED_STATUS as $status)
                        @if($status != 'deleted' && $status != $info->status)
                            <option value="{{ $status }}">{{ trans('front.info_report_status_' . $status) }}</option>
                        @endif
                    @endforeach

                </select>
            </div>
        </div>

        <div class="row">

            <div class="file-field input-field col s12 m8 l6">
                <a href="{{ route('info_reports.index') }}" class="waves-effect waves-ripple btn grey"><i class="material-icons left">arrow_left</i>Voltar</a>
                <button class="right green btn waves-effect waves-light" type="submit" name="action">{{ trans('general.save') }}
                    <i class="material-icons right">send</i>
                </button>
            </div>
        </div>
    </form>

@endsection

@section('scripts')

@endsection