@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>Solicitação de Atualização - {{ $updateRequest->player ? $updateRequest->player->name : 'Jogador #' . $updateRequest->player_id }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>Solicitação de Atualização de Jogador</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <!-- Player and Request Info -->
    <div class="row no-margin-bottom">
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <div class="row no-margin-bottom">
                        <!-- Left Column: Player Picture -->
                        <div class="col s12 m3">
                            @if($updateRequest->isCreateRequest())
                                @if($updateRequest->picture_url)
                                    <img src="{{ $updateRequest->picture_url }}" alt="Foto do Novo Jogador" 
                                         style="max-width: 100%; max-height: 200px; border-radius: 4px;" class="responsive-img">
                                @else
                                    <div class="grey lighten-3" style="height: 200px; width: 100%; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                        <i class="material-icons large grey-text">person</i>
                                    </div>
                                @endif
                            @elseif($updateRequest->player && $updateRequest->player->picture)
                                <img src="{{ $updateRequest->player->picture }}" alt="Foto do Jogador" 
                                     style="max-width: 100%; max-height: 200px; border-radius: 4px;" class="responsive-img">
                            @else
                                <div class="grey lighten-3" style="height: 200px; width: 100%; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                    <i class="material-icons large grey-text">person</i>
                                </div>
                            @endif
                        </div>

                        <!-- Middle Column: Player Information -->
                        <div class="col s12 m5">
                            <h5>Informações do Jogador</h5>
                            @if($updateRequest->isCreateRequest())
                                <p style="margin-bottom: 5px;"><strong>Nome:</strong> {{ $updateRequest->name ?? 'Não informado' }}</p>
                                @if($updateRequest->nickname)
                                    <p style="margin-bottom: 5px;"><strong>Alcunha:</strong> {{ $updateRequest->nickname }}</p>
                                @endif
                                @if($updateRequest->club_name)
                                    <p style="margin-bottom: 5px;"><strong>Clube:</strong> {{ $updateRequest->club_name }}</p>
                                @endif
                            @elseif($updateRequest->player)
                                <p style="margin-bottom: 5px;"><strong>Nome:</strong> 
                                    <a href="{{ route('players.show', ['player' => $updateRequest->player]) }}">
                                        {{ $updateRequest->player->name }}
                                    </a>
                                </p>
                                @if($updateRequest->player->nickname)
                                    <p style="margin-bottom: 5px;"><strong>Alcunha:</strong> {{ $updateRequest->player->nickname }}</p>
                                @endif
                                @if($updateRequest->player->getClub())
                                    <p style="margin-bottom: 5px;"><strong>Clube:</strong> {{ $updateRequest->player->getClub()->name }}</p>
                                @endif
                            @else
                                <p style="margin-bottom: 5px;"><strong>Jogador:</strong> #{{ $updateRequest->player_id }} (Não encontrado)</p>
                            @endif
                            @if(!empty($updateRequest->obs))
                                <p style="margin-bottom: 5px;"><strong>Observações:</strong> {{ $updateRequest->obs }}</p>
                            @endif
                        </div>

                        <!-- Right Column: Request Information -->
                        <div class="col s12 m4">
                            <h5>Informações da Solicitação</h5>
                            <p style="margin-bottom: 5px;"><strong>ID:</strong> {{ $updateRequest->id }}</p>
                            <p style="margin-bottom: 5px;"><strong>Tipo:</strong> 
                                @if($updateRequest->isCreateRequest())
                                    <span class="new badge purple" data-badge-caption="">Criação</span>
                                @else
                                    <span class="new badge blue" data-badge-caption="">Atualização</span>
                                @endif
                            </p>
                            <p style="margin-bottom: 5px;"><strong>Estado:</strong>
                                @if($updateRequest->status === 'pending')
                                    <span class="new badge orange" data-badge-caption="">Pendente</span>
                                @elseif($updateRequest->status === 'approved')
                                    <span class="new badge green" data-badge-caption="">Aprovado</span>
                                @elseif($updateRequest->status === 'denied')
                                    <span class="new badge red" data-badge-caption="">Negado</span>
                                @endif
                            </p>
                            <p style="margin-bottom: 5px;"><strong>Criado Por:</strong> {{ $updateRequest->created_by ?? 'Sistema' }}</p>
                            <p style="margin-bottom: 5px;"><strong>Data de Criação:</strong> {{ $updateRequest->created_at->format('d/m/Y H:i:s') }}</p>
                            @if($updateRequest->reviewed_at)
                                <p style="margin-bottom: 5px;"><strong>Revisto em:</strong> {{ $updateRequest->reviewed_at }}</p>
                                @if($updateRequest->reviewedBy)
                                    <p style="margin-bottom: 5px;"><strong>Revisto por:</strong> 
                                        <a href="{{ route('users.show', ['user' => $updateRequest->reviewedBy]) }}">
                                            {{ $updateRequest->reviewedBy->name }}
                                        </a>
                                    </p>
                                @endif
                                @if($updateRequest->review_notes)
                                    <p style="margin-bottom: 5px;"><strong>Notas de Revisão:</strong></p>
                                    <p class="grey-text" style="margin-bottom: 5px;">{{ $updateRequest->review_notes }}</p>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Changes Comparison -->
    @if(count($changes) > 0)
        <div class="row no-margin-bottom">
            <div class="col s12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-title">
                            @if($updateRequest->isCreateRequest())
                                Dados do Novo Jogador
                            @else
                                Alterações Propostas
                            @endif
                        </div>
                        <table class="bordered highlight">
                            <thead>
                                <tr>
                                    <th>Campo</th>
                                    @if($updateRequest->isCreateRequest())
                                        <th class="green-text text-lighten-1">Valor</th>
                                    @else
                                        <th class="red-text text-lighten-1">Valor Atual</th>
                                        <th class="green-text text-lighten-1">Novo Valor</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($changes as $field => $change)
                                    <tr>
                                        <td><strong>
                                            @switch($field)
                                                @case('name')
                                                    Nome
                                                    @break
                                                @case('nickname')
                                                    Alcunha
                                                    @break
                                                @case('club_name')
                                                    Clube
                                                    @break
                                                @case('picture')
                                                    Foto
                                                    @break
                                                @case('association_id')
                                                    Nº Associação
                                                    @break
                                                @case('phone')
                                                    Telefone
                                                    @break
                                                @case('email')
                                                    Email
                                                    @break
                                                @case('facebook_profile')
                                                    Perfil Facebook
                                                    @break
                                                @case('birth_date')
                                                    Data de Nascimento
                                                    @break
                                                @case('position')
                                                    Posição
                                                    @break
                                                @case('obs')
                                                    Observações
                                                    @break
                                                @default
                                                    {{ ucfirst($field) }}
                                            @endswitch
                                        </strong></td>
                                        @if($updateRequest->isCreateRequest())
                                            <td class="green-text text-lighten-2">
                                                @if($field === 'picture')
                                                    @if($change['new'])
                                                        <img src="{{ $change['new'] }}" alt="Foto" style="max-height: 75px;" class="responsive-img">
                                                    @else
                                                        <em>Sem foto</em>
                                                    @endif
                                                @elseif($field === 'position')
                                                    {{ trans('general.' . ($change['new'] ?? 'none')) }}
                                                @else
                                                    {{ $change['new'] ?: '-' }}
                                                @endif
                                            </td>
                                        @else
                                            <td class="red-text text-lighten-2">
                                                @if($field === 'picture')
                                                    @if($change['old'])
                                                        <img src="{{ $change['old'] }}" alt="Foto atual" style="max-height: 75px;" class="responsive-img">
                                                    @else
                                                        <em>Sem foto</em>
                                                    @endif
                                                @elseif($field === 'position')
                                                    {{ trans('general.' . ($change['old'] ?? 'none')) }}
                                                @else
                                                    {{ $change['old'] ?: '-' }}
                                                @endif
                                            </td>
                                            <td class="green-text text-lighten-2">
                                                @if($field === 'picture')
                                                    @if($change['new'])
                                                        <img src="{{ $change['new'] }}" alt="Foto proposta" style="max-height: 75px;" class="responsive-img">
                                                    @else
                                                        <em>Sem foto</em>
                                                    @endif
                                                @elseif($field === 'position')
                                                    {{ trans('general.' . ($change['new'] ?? 'none')) }}
                                                @else
                                                    {{ $change['new'] ?: '-' }}
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col s12">
                <div class="card">
                    <div class="card-content">
                        <p class="flow-text grey-text center">Nenhuma alteração detectada nesta solicitação.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Source Data (if available) -->
    @if($updateRequest->source_data)
        <div class="row">
            <div class="col s12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-title">Dados da Fonte</div>
                        <pre class="grey lighten-5" style="padding: 10px; border-radius: 3px;">{{ json_encode($updateRequest->source_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Action Buttons for Pending Requests -->
    @if($updateRequest->isPending() && Auth::user()->hasPermission('player_update_requests.edit'))
        <div class="row no-margin-bottom">
            <div class="col s12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-title center" style="margin-bottom: 15px;">Ações</div>
                        <div class="row no-margin-bottom">
                            <div class="col s12 m6 right-align">
                                <a class="waves-effect waves-light btn-large green modal-trigger" href="#approve_modal">
                                    <i class="material-icons left">check</i>
                                    @if($updateRequest->isCreateRequest())
                                        Criar Jogador
                                    @else
                                        Aprovar Alterações
                                    @endif
                                </a>
                            </div>
                            <div class="col s12 m6">
                                <a class="waves-effect waves-light btn-large red modal-trigger" href="#deny_modal">
                                    <i class="material-icons left">close</i>Recusar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Standard Model Options -->
    @if(Auth::user()->hasPermission('player_update_requests.create'))
        @include('backoffice.partial.model_options', [
            'delete_route' => route('player_update_requests.destroy', ['player_update_request' => $updateRequest])
        ])
    @endif

    <!-- Modals for Pending Requests -->
    @if($updateRequest->isPending() && Auth::user()->hasPermission('player_update_requests.edit'))
        <!-- Approve Modal -->
        <div id="approve_modal" class="modal">
            <form action="{{ route('player_update_requests.approve', ['id' => $updateRequest->id]) }}" method="POST">
                {{ csrf_field() }}
                <div class="modal-content">
                    <h4 class="center green-text">
                        @if($updateRequest->isCreateRequest())
                            Criar Novo Jogador
                        @else
                            Aprovar Solicitação
                        @endif
                    </h4>
                    <p class="flow-text center">
                        @if($updateRequest->isCreateRequest())
                            Tem certeza que deseja criar este novo jogador com os dados fornecidos?
                        @else
                            Tem certeza que deseja aprovar esta solicitação? As alterações serão aplicadas ao jogador automaticamente.
                        @endif
                    </p>
                    
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="approve_notes" name="review_notes" class="materialize-textarea" placeholder="Notas opcionais sobre a aprovação..."></textarea>
                            <label for="approve_notes">Notas de Revisão (Opcional)</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#!" class="modal-action modal-close waves-effect waves-light btn-flat">Cancelar</a>
                    <button class="btn waves-effect waves-light green modal-action" type="submit">
                        <i class="material-icons left">check</i>
                        @if($updateRequest->isCreateRequest())
                            Criar
                        @else
                            Aprovar
                        @endif
                    </button>
                </div>
            </form>
        </div>

        <!-- Deny Modal -->
        <div id="deny_modal" class="modal">
            <form action="{{ route('player_update_requests.deny', ['id' => $updateRequest->id]) }}" method="POST">
                {{ csrf_field() }}
                <div class="modal-content">
                    <h4 class="center red-text">Recusar Solicitação</h4>
                    <p class="flow-text center">Tem certeza que deseja recusar esta solicitação? Nenhuma alteração será aplicada ao jogador.</p>
                    
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="deny_notes" name="review_notes" class="materialize-textarea" placeholder="Motivo da recusa..." required></textarea>
                            <label for="deny_notes">Motivo da Recusa (Obrigatório)</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#!" class="modal-action modal-close waves-effect waves-light btn-flat">Cancelar</a>
                    <button class="btn waves-effect waves-light red modal-action" type="submit">
                        <i class="material-icons left">close</i>Recusar
                    </button>
                </div>
            </form>
        </div>
    @endif

<script>
    setTimeout(() => {
        $(document).ready(function(){
            $('#approve_modal').modal();
            $('#deny_modal').modal();
        });
    }, 150)
</script>
@endsection
