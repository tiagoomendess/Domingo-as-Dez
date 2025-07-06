@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>Solicitações de Atualização de Jogadores</title>
@endsection

@section('content')
    <div class="row no-margin-bottom">
        <div class="col s8">
            <h1>Solicitações de Atualização de Jogadores</h1>
        </div>
        <div class="col s4">
            @include('backoffice.partial.search_box_btn')
        </div>
    </div>

    <div class="row no-margin-bottom">
        @include('backoffice.partial.search_box')
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$updateRequests || $updateRequests->count() == 0)
                <p class="flow-text">Nenhuma solicitação de atualização encontrada.</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>Estado</th>
                        <th>Jogador</th>
                        <th>Tipo</th>
                        <th class="hide-on-med-and-down">{{ trans('general.created_at') }}</th>
                        <th class="hide-on-med-and-down">Revisto Por</th>
                    </tr>
                    </thead>

                    @foreach($updateRequests as $request)
                        <tr>
                            <td class="left-align">
                                @if($request->status === 'pending')
                                    <span style="margin-left: 0" class="new badge orange left" data-badge-caption="">Pendente</span>
                                @elseif($request->status === 'approved')
                                    <span style="margin-left: 0" class="new badge green left" data-badge-caption="">Aprovado</span>
                                @elseif($request->status === 'denied')
                                    <span style="margin-left: 0" class="new badge red left" data-badge-caption="">Negado</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('player_update_requests.show', ['player_update_request' => $request]) }}">
                                    @if($request->isCreateRequest())
                                        @if($request->name)
                                            {{ $request->name }}
                                        @endif
                                    @elseif($request->player)
                                        {{ $request->player->name }}
                                        @if($request->player->nickname)
                                            ({{ $request->player->nickname }})
                                        @endif
                                    @else
                                        Jogador #{{ $request->player_id }}
                                    @endif
                                </a>
                            </td>
                            <td class="left-align">
                                @if($request->isCreateRequest())
                                    <span style="margin-left: 0" class="new badge purple left" data-badge-caption="">Novo</span>
                                @else
                                    @php
                                        $changes = $request->getChanges();
                                        $changeCount = count($changes);
                                    @endphp
                                    @if($changeCount > 0)
                                        <span style="margin-left: 0" class="new badge blue left" data-badge-caption="">{{ $changeCount }} {{ $changeCount === 1 ? 'Alteração' : 'Alterações' }}</span>
                                    @else
                                        <span style="margin-left: 0" class="grey-text">Nenhuma alteração</span>
                                    @endif
                                @endif
                            </td>
                            <td class="hide-on-med-and-down">{{ $request->created_at->format('d/m/Y H:i') }}</td>
                            <td class="hide-on-med-and-down">
                                @if($request->reviewedBy)
                                    <a href="{{ route('users.show', ['user' => $request->reviewedBy]) }}">{{ $request->reviewedBy->name }}</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach

                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            {{ $updateRequests->links() }}
        </div>
    </div>

@endsection 