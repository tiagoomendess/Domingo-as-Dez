@extends('front.layouts.default-page')

@section('head-content')
    <title>Introduza o PIN</title>
@endsection

@section('content')
    <div class="container">
        <div class="row" style="margin: 100px 0">
            <div class="card col s12 m6 l4 offset-m3 offset-l4">
                <div class="card-content">
                    @if($error)
                        <div class="left">
                            <blockquote>
                                <ul style="color: red;">
                                    <li>Pin introduzido "{{ $error }}" está errado</li>
                                </ul>
                            </blockquote>
                        </div>
                    @else
                        <p class="flow-text center">
                            Introduza o PIN que lhe foi fornecido no email para poder aceder.
                        </p>
                    @endif

                    <form action="{{ route('front.game_comment', [ 'uuid' => $uuid ]) }}" method="GET">
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="pin" name="pin" type="number" class="validate" autocomplete="off" required>
                                <label for="pin">PIN</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field center text-center">
                                <button class="btn waves-effect waves-light green btn-lg" type="submit" name="action">
                                    Entrar
                                    <i class="material-icons right">send</i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
