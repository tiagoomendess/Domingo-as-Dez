@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>Criar Sondagem</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>Criar Sondagem</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('polls.store') }}" method="POST">

        {{ csrf_field() }}

        <div class="row">
            <div class="input-field col s12 m12 l6">
                <input required name="question" id="question" type="text" class="validate" value="{{ old('question') }}" data-length="144" autocomplete="off">
                <label for="question">Pergunta</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m12 l6">
                @include('backoffice.partial.select_timezone', ['timezone_name' => $user->profile->timezone, 'timezone_value' => $user->profile->timezone])
            </div>
        </div>

        <!-- Show results after -->
        <div class="row">
            <div class="col s12">
                <label>Apenas mostrar resultados depois de:</label>
            </div>
            <div class="input-field col s7 m7 l3">
                <input id="show_results_after_date" name="show_results_after_date" type="text" class="datepicker" value="{{ $now->format("Y-m-d") }}" required>
                <label for="show_results_after_date">{{ trans('general.day') }}</label>
            </div>

            <div class="input-field col s5 m5 l3">
                <input id="show_results_after_time" name="show_results_after_time" type="text" class="timepicker" value="{{ $now->format("H:i") }}" required>
                <label for="show_results_after_time">{{ trans('general.hour') }}</label>
            </div>
        </div>

        <!-- Close voting -->
        <div class="row">
            <div class="col s12">
                <label>Fechar votação em:</label>
            </div>
            <div class="input-field col s7 m7 l3">
                <input id="close_after_date" name="close_after_date" type="text" class="datepicker" value="{{ $closeBy->format("Y-m-d") }}" required>
                <label for="close_after_date">{{ trans('general.day') }}</label>
            </div>

            <div class="input-field col s5 m5 l3">
                <input id="close_after_time" name="close_after_time" type="text" class="timepicker" value="{{ $closeBy->format("H:i") }}" required>
                <label for="close_after_time">{{ trans('general.hour') }}</label>
            </div>
        </div>

        <!-- Publish date -->
        <div class="row">
            <div class="col s12">
                <label>Agendar publicação para:</label>
            </div>
            <div class="input-field col s7 m7 l3">
                <input id="publish_after_date" name="publish_after_date" type="text" class="datepicker" value="{{ $now->format("Y-m-d") }}" required>
                <label for="publish_after_date">{{ trans('general.day') }}</label>
            </div>

            <div class="input-field col s5 m5 l3">
                <input id="publish_after_time" name="publish_after_time" type="text" class="timepicker" value="{{ $now->format("H:i") }}" required>
                <label for="publish_after_time">{{ trans('general.hour') }}</label>
            </div>
        </div>

        <div id="answers_wrap">
            <div class="row">
                <div class="input-field col s12 m12 l6">
                    <input required name="answers[]" id="answer_1" type="text" class="validate" data-length="144" autocomplete="off">
                    <label for="answer_1">Resposta 1</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12 m12 l6">
                    <input required name="answers[]" id="answer_2" type="text" class="validate" data-length="144" autocomplete="off">
                    <label for="answer_2">Resposta 2</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col s6 m6 l3">
                <a id="remove_btn" onclick="removeAnswer()" style="width: 100%" class="hide waves-effect waves btn-flat grey lighten-2 center"><i class="material-icons">remove</i></a>
            </div>
            <div class="col s6 m6 l3">
                <a onclick="addAnswer()" style="width: 100%" class="waves-effect waves btn-flat grey lighten-2 center"><i class="material-icons">add</i></a>
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
    <script>
        function addAnswer() {

            let allAnswers = $('#answers_wrap');
            let example = $("#answers_wrap > div.row:nth-child(1)")
            let totalAnswers = $("#answers_wrap > div.row").length

            if (totalAnswers >= 40) {
                alert("O número máximo de respostas é de 40")
                return;
            }

            let newAnswer = example.clone()
            newAnswer.find("input").attr('id', `answer_${totalAnswers + 1}`).removeClass('valid')
            newAnswer.find("label").attr('for', `answer_${totalAnswers + 1}`).html(`Resposta ${totalAnswers + 1}`)
            newAnswer.find("input").val("")
            newAnswer.appendTo(allAnswers);

            $('#remove_btn').removeClass('hide')
        }

        function removeAnswer() {
            let totalAnswers = $("#answers_wrap > div.row").length

            if (totalAnswers <= 2) {
                alert('O Número minimo de respostas é 2')
                return
            }

            if(totalAnswers === 3) {
                $('#remove_btn').addClass('hide')
            }

            $('#answers_wrap > div.row:last-child').remove()
        }
    </script>
    @include('backoffice.partial.pick_a_date_js')
    @include('backoffice.partial.pick_a_time_js')
@endsection

