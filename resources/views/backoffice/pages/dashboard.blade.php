@extends('backoffice.layouts.default-page')

@section('content')
<style>
    .dashboard-card {
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
    }

    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .metric-number {
        font-size: 3rem;
        font-weight: 700;
        margin: 10px 0;
        line-height: 1;
    }

    .metric-label {
        font-size: 1rem;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 15px;
    }

    .metric-icon {
        font-size: 3rem;
        opacity: 0.3;
        position: absolute;
        right: 20px;
        top: 20px;
    }

    .card-action-btn {
        width: 100%;
        margin-top: 10px;
    }

    .stat-card-content {
        position: relative;
        padding: 24px;
    }

    .partner-ads-table {
        margin-top: 15px;
    }

    .partner-ad-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .partner-ad-row:last-child {
        border-bottom: none;
    }

    .partner-ad-name {
        font-weight: 500;
    }

    .partner-ad-clicks {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1976d2;
    }

    .dashboard-section-title {
        font-size: 1.8rem;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .static-section {
        margin-top: 60px;
        padding-top: 40px;
        border-top: 2px solid #e0e0e0;
    }
</style>

<div class="row">
    <div class="col s12">
        <h1 class="dashboard-section-title">Dashboard</h1>
    </div>

    {{-- Player Update Requests --}}
    <div class="col s12 m6 l4">
        <div class="card dashboard-card">
            <div class="card-content stat-card-content">
                <i class="material-icons metric-icon">person_add</i>
                <div class="metric-label">Pedidos de Atualização de Jogadores</div>
                <div class="metric-number" style="color: #ff9800;">{{ $playerUpdateRequestsCount }}</div>
                <p>Jogadores a aguardar aprovação</p>
            </div>
            <div class="card-action">
                <a href="/player_update_requests" class="btn waves-effect waves-light orange card-action-btn">
                    <i class="material-icons left">visibility</i>Ver Pedidos
                </a>
            </div>
        </div>
    </div>

    {{-- Blocked Users Currently --}}
    <div class="col s12 m6 l4">
        <div class="card dashboard-card">
            <div class="card-content stat-card-content">
                <i class="material-icons metric-icon">block</i>
                <div class="metric-label">Utilizadores Bloqueados</div>
                <div class="metric-number" style="color: #f44336;">{{ $blockedUsersCount }}</div>
                <p>Utilizadores bloqueados atualmente</p>
            </div>
            <div class="card-action">
                <a href="/score_report_bans" class="btn waves-effect waves-light red card-action-btn">
                    <i class="material-icons left">list</i>Ver Utilizadores
                </a>
            </div>
        </div>
    </div>

    {{-- Unseen Info Reports --}}
    <div class="col s12 m6 l4">
        <div class="card dashboard-card">
            <div class="card-content stat-card-content">
                <i class="material-icons metric-icon">report_problem</i>
                <div class="metric-label">Informações Recebidas</div>
                <div class="metric-number" style="color: #9c27b0;">{{ $infoReportsCount }}</div>
                <p>Informações recebidas que ainda não foram vistas.</p>
            </div>
            <div class="card-action">
                <a href="/info_reports" class="btn waves-effect waves-light purple card-action-btn">
                    <i class="material-icons left">remove_red_eye</i>Ver Informações
                </a>
            </div>
        </div>
    </div>

    {{-- Partner Ad Clicks --}}
    <div class="col s12 m6 l4">
        <div class="card dashboard-card">
            <div class="card-content stat-card-content">
                <i class="material-icons metric-icon">ads_click</i>
                <div class="metric-label">Cliques em Anúncios</div>
                <div class="partner-ads-table">
                    @forelse($partners as $partner)
                        <div class="partner-ad-row">
                            <span class="partner-ad-name">{{ $partner->name }}</span>
                            <span class="partner-ad-clicks">{{ number_format($partner->click_count, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <p>Nenhum parceiro disponível</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>


     {{-- Game MVP Votes Last 24h --}}
     <div class="col s12 m6 l4">
         <div class="card dashboard-card">
             <div class="card-content stat-card-content">
                 <i class="material-icons metric-icon">star</i>
                 <div class="metric-label">Votos Homem do Jogo (24h)</div>
                 <div class="metric-number" style="color: #ffd700;">{{ $mvpVotesCount }}</div>
                 <p>Votos nas últimas 24 horas</p>
             </div>
         </div>
     </div>

     {{-- Score Reports Last 24h --}}
     <div class="col s12 m6 l4">
         <div class="card dashboard-card">
             <div class="card-content stat-card-content">
                 <i class="material-icons metric-icon">assignment</i>
                 <div class="metric-label">Resultados Enviados (24H)</div>
                 <div class="metric-number" style="color: #4caf50;">{{ $resultsSentCount }}</div>
                 <p>Resultados enviados nas últimas 24 horas</p>
             </div>
         </div>
     </div>

     {{-- Flash Interview Comments --}}
     <div class="col s12 m6 l4">
         <div class="card dashboard-card">
             <div class="card-content stat-card-content">
                 <i class="material-icons metric-icon">rate_review</i>
                 <div class="metric-label">Flash Interview</div>
                 <div class="metric-number" style="color: #673ab7;">{{ $gameCommentsCount }}</div>
                 <p>Flash Interviews editadas nas últimas 48H</p>
             </div>
             <div class="card-action">
                 <a href="{{ route('game_comments.index') }}" class="btn waves-effect waves-light deep-purple card-action-btn">
                     <i class="material-icons left">list</i>Ver Comentários
                 </a>
             </div>
         </div>
     </div>

     {{-- Total Registered Users --}}
     <div class="col s12 m6 l4">
         <div class="card dashboard-card">
             <div class="card-content stat-card-content">
                 <i class="material-icons metric-icon">people</i>
                 <div class="metric-label">Total de Utilizadores Registados</div>
                 <div class="metric-number" style="color: #00bcd4;">{{ number_format($registeredUsersCount, 0, ',', '.') }}</div>
                 <p>Utilizadores registados no sistema</p>
             </div>
             <div class="card-action">
                 <a href="/users" class="btn waves-effect waves-light cyan card-action-btn">
                     <i class="material-icons left">group</i>Ver Utilizadores
                 </a>
             </div>
         </div>
     </div>

     {{-- Users With Permissions --}}
     <div class="col s12 m6 l4">
         <div class="card dashboard-card">
             <div class="card-content stat-card-content">
                 <i class="material-icons metric-icon">security</i>
                 <div class="metric-label">Utilizadores com Permissões</div>
                 <div class="partner-ads-table">
                     @forelse($usersWithPermissions->take(4) as $user)
                         <div class="partner-ad-row">
                             <span class="partner-ad-name">{{ $user->name }}</span>
                             <i class="material-icons" style="color: #2196f3;">verified_user</i>
                         </div>
                     @empty
                         <p>Nenhum utilizador com permissões</p>
                     @endforelse
                 </div>
             </div>
             <div class="card-action">
                 <a href="/users?has_permissions=1" class="btn waves-effect waves-light teal card-action-btn">
                     <i class="material-icons left">admin_panel_settings</i>Ver Todos
                 </a>
             </div>
         </div>
     </div>
 </div>

{{-- Static Content Section --}}
<div class="row static-section">
    <div class="col s12">
        <h4>Tutoriais:</h4>
        <p class="flow-text">Isto é uma playlist de vídeos a explicar como realizar algumas tarefas no website.
            Consoante as permissões associadas à sua conta,
            poderá não conseguir visualizar todas as opções que aparecem nos vídeos. Se preferir pode visualizar os
            vídeos diretamente no youtube clicando no link:
            <a href="https://www.youtube.com/watch?v=iuVF45lBRxY&list=PL_GkJl6tG6PckYIYCDM5RtLCc38L63lQk">https://www.youtube.com/watch?v=iuVF45lBRxY&list=PL_GkJl6tG6PckYIYCDM5RtLCc38L63lQk</a>
        </p>
        <iframe width="853" height="480"
            src="https://www.youtube.com/embed/videoseries?list=PL_GkJl6tG6PckYIYCDM5RtLCc38L63lQk"
            frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen></iframe>
    </div>

    @if(has_permission('admin'))
    <div class="col s12 m8 l6">
        <h4>Utilizadores com Permissões:</h4>
        <div class="collection">
            @foreach($usersWithPermissions as $user)
            <a class="collection-item"
                href="{{ route('users.show', ['user' => $user->id]) }}">{{ $user->name }}</a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection