@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>Sondagens</title>
@endsection

@section('content')
    <div class="row no-margin-bottom">
        <div class="col s8">
            <h1>Sondagens</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$polls || $polls->count() == 0)
                <p class="flow-text">Não existem sondagens</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Pergunta</th>
                    </tr>
                    </thead>

                    @foreach($polls as $poll)
                        <tr>
                            <td>{{ $poll->id }}</td>
                            <td><a href="{{ route('polls.show', ['poll' => $poll->id]) }}">{{ $poll->question }} ({{ $poll->answers->count() }})</a></td>
                            <td>
                                <i data-url="{{ route('polls.front.show', ['slug' => $poll->slug]) }}"
                                   style="cursor: pointer;"
                                   class="material-icons right copy-link-btn">content_copy</i>
                            </td>
                        </tr>
                    @endforeach

                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            {{ $polls->links() }}
        </div>
    </div>

    @if(Auth::user()->hasPermission('polls.create'))
        @include('backoffice.partial.add_model_button', ['route' => route('polls.create')])
    @endif

@endsection

@section('scripts')
    <script>
        $('.copy-link-btn').on('click', (event) => {
            let url = $(event.target).attr('data-url');
            navigator.clipboard.writeText(url);
            Materialize.toast('URL copiado para a área de transferência', 3000) // 4000 is the duration of the toast
        })
    </script>
@endsection