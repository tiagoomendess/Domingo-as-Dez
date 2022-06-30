@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('front.create_info_report_title') }}</title>

    <meta property="og:title" content="{{ 'Enviar Informação - ' . config('app.name') }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:image" content="{{ url('/images/enviar-info.jpg') }}">
    <meta property="og:image:width" content="1280">
    <meta property="og:image:height" content="720">
    <meta property="og:description" content="Envie uma informação que deseja partilhar com a comunidade, pode escolher manter o anonimato ou não." />

    <meta itemprop="name" content="Enviar Informação">
    <meta itemprop="description" content="Envie uma informação que deseja partilhar com a comunidade, pode escolher manter o anonimato ou não.">
    <meta itemprop="image" content="{{ url('/images/enviar-info.jpg') }}">
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col s12 hide-on-med-and-down">
                <h1>{{ trans('front.create_info_report_title') }}</h1>
            </div>

            <div class="col s12">
                <div class="row">
                    <p class="flow-text col s12">
                        Envie uma informação que deseja partilhar com a comunidade, pode escolher manter o anonimato ou
                        não.
                        Para enviar ficheiros de multimédia como imagens ou videos submeta links para os mesmos.
                    </p>
                    @if($errors)
                        <div class="col xs12 s12 no-margin-bottom">
                            <div class="col xs12 s12 no-margin-bottom">
                                @include('front.partial.form_errors')
                            </div>
                        </div>

                    @endif
                </div>
                <div class="row">
                    <div class="col s12 m12 l8">
                        <div class="card blue-grey lighten-5">
                            <div class="card-content">
                                <form action="{{ route('info.store') }}" method="POST">
                                    {{ csrf_field() }}

                                    <div class="row">
                                        <div class="col s12">
                                            <small class="grey-text">Anónimo:</small>
                                            <div class="switch">
                                                <label>
                                                    OFF
                                                    <input name="anonymous" type="hidden" value="false">
                                                    <input type="checkbox"
                                                           name="anonymous"
                                                           @if(!\Illuminate\Support\Facades\Auth::check()) disabled
                                                           checked @endif value="true">
                                                    <span class="lever"></span>
                                                    ON
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="input-field col s12">
                                            <textarea id="content" name="content" class="materialize-textarea validate"
                                                      data-length="500" autocomplete="off" required>{{ old('content') }}</textarea>
                                            <label for="content">Informação</label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="input-field col s12">
                                            <input id="source" name="source" type="text" class="validate"
                                                   data-length="155" autocomplete="off" value="{{ old('content') }}" required>
                                            <label for="source">Fonte</label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col xs12 s12">
                                            {!! Recaptcha::render() !!}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col s12 right">
                                            <button type="submit" class="btn right green darken-3"><i
                                                        class="material-icons right">send</i>Enviar
                                            </button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col s12 m12 l4">
                        <div class="card grey lighten-4">
                            <div class="card-content">
                                <p class="flow-text">Já enviei uma informação e tenho um código para verificar o
                                    estado.</p>
                                <form action="{{ route('info.show') }}" method="POST">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <input id="code" name="code" type="text" class="validate"
                                                   data-length="9" max="9" style="text-transform: uppercase"
                                                   autocomplete="off"
                                                   required>
                                            <label for="source">Código</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col s12 center">
                                            <button style="" type="submit" class="waves-effect waves-teal btn-flat">
                                                <i class="material-icons right">remove_red_eye</i>Verificar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
