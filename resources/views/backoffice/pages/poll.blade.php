@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>Ver Sondagem</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>Ver Sondagem</h1>
        </div>
    </div>

    <form action="" method="POST">

        <div class="row">
            <div class="input-field col s12 m12 l6">
                <input disabled required name="question" id="question" type="text" class="validate"
                       value="{{ $poll->question }}" data-length="144" autocomplete="off">
                <label for="question">Pergunta</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m12 l6">
                <select id="timezone" name="timezone" class="browser-default" required disabled>
                    <option value="#">{{ $user->profile->timezone }}</option>
                </select>
            </div>
        </div>

        <!-- Show results after -->
        <div class="row">
            <div class="col s12">
                <label>Apenas mostrar resultados depois de:</label>
            </div>
            <div class="input-field col s7 m7 l3">
                <input id="show_results_after_date" name="show_results_after_date" type="text" class="datepicker"
                       value="{{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $poll->show_results_after)->setTimezone($user->profile->timezone)->format("Y-m-d") }}"
                       required
                       disabled>
                <label for="show_results_after_date">{{ trans('general.day') }}</label>
            </div>

            <div class="input-field col s5 m5 l3">
                <input id="show_results_after_time" name="show_results_after_time" type="text" class="timepicker"
                       value="{{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $poll->show_results_after)->setTimezone($user->profile->timezone)->format("H:i") }}"
                       required disabled>
                <label for="show_results_after_time">{{ trans('general.hour') }}</label>
            </div>
        </div>

        <!-- Close voting -->
        <div class="row">
            <div class="col s12">
                <label>Fechar votação em:</label>
            </div>
            <div class="input-field col s7 m7 l3">
                <input id="close_after_date" name="close_after_date" type="text" class="datepicker"
                       value="{{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $poll->close_after)->setTimezone($user->profile->timezone)->format("Y-m-d") }}"
                       required disabled>
                <label for="close_after_date">{{ trans('general.day') }}</label>
            </div>

            <div class="input-field col s5 m5 l3">
                <input id="close_after_time" name="close_after_time" type="text" class="timepicker"
                       value="{{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $poll->close_after)->setTimezone($user->profile->timezone)->format("H:i") }}"
                       required disabled>
                <label for="close_after_time">{{ trans('general.hour') }}</label>
            </div>
        </div>

        <!-- Publish date -->
        <div class="row">
            <div class="col s12">
                <label>Agendar publicação para:</label>
            </div>
            <div class="input-field col s7 m7 l3">
                <input id="publish_after_date" name="publish_after_date" type="text" class="datepicker"
                       value="{{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $poll->publish_after)->setTimezone($user->profile->timezone)->format("Y-m-d") }}"
                       required disabled>
                <label for="publish_after_date">{{ trans('general.day') }}</label>
            </div>

            <div class="input-field col s5 m5 l3">
                <input id="publish_after_time" name="publish_after_time" type="text" class="timepicker"
                       value="{{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $poll->publish_after)->setTimezone($user->profile->timezone)->format("H:i") }}"
                       required disabled>
                <label for="publish_after_time">{{ trans('general.hour') }}</label>
            </div>
        </div>

        <div id="answers_wrap">
            @foreach($poll->answers as $id => $answer)
                <div class="row">
                    <div class="input-field col s12 m12 l6">
                        <input required name="answers[]" id="answer_1" type="text" class="validate" data-length="144"
                               autocomplete="off" disabled value="{{ $answer->answer }}">
                        <label for="answer_1">Resposta {{ $id + 1 }}</label>
                    </div>
                </div>
            @endforeach
        </div>

    </form>

    @if(Auth::user()->haspermission('polls.edit'))
        @include('backoffice.partial.model_options', [
            'delete_route' => route('polls.destroy', ['poll' => $poll]),
            'edit_route' => route('polls.edit', ['poll' => $poll])
        ])
    @endif
@endsection

