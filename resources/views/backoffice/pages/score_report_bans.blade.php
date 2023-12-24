@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>Bloqueados de Enviar Resultados</title>
@endsection

@section('content')

    <div class="row no-margin-bottom">
        <div class="col s8">
            <h1>Bloqueios</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$bans || $bans->count() == 0)
                <p class="flow-text">Não existem bloqueios</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Uuid</th>
                        <th>User Id</th>
                        <th>IP</th>
                        <th>Expira</th>
                        <th>Criado</th>
                    </tr>
                    </thead>

                    @foreach($bans as $ban)
                        <tr>
                            <td>
                                <a href="{{ route('score_report_bans.show', ['id' => $ban->id]) }}">
                                    {{ $ban->id }}
                                </a>
                            </td>
                            <td>{{ $ban->uuid }}</td>
                            <td>
                                @if($ban->user_id)
                                    <a href="{{ route('users.show', ['user' => $ban->user_id]) }}">{{ $ban->user_id }}</a>
                                @else()
                                    -
                                @endif
                            </td>
                            <td>{{ $ban->ip_address }}</td>
                            <td>{{ $ban->expires_at }}</td>
                            <td>{{ $ban->created_at }}</td>
                        </tr>
                    @endforeach

                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            {{ $bans->links() }}
        </div>
    </div>

    @if(Auth::user()->hasPermission('score_report_bans.create'))
        @include('backoffice.partial.add_model_button', ['route' => route('score_report_bans.create')])
    @endif

@endsection
