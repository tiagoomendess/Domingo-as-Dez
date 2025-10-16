<ul id="slide-out" class="side-nav fixed" style="height: 100%;">

    <li>
        <div class="user-view">
            <div class="background">
                <img src="{{ config('custom.dashboard_sidenav_image') }}">
            </div>
            <a href="{{ route('users.show', ['user' => Auth::user()]) }}"><img class="circle"
                                                                               src="{{ Auth::user()->profile->picture }}"></a>
            <a href="{{ route('users.show', ['user' => Auth::user()]) }}"><span
                        class="white-text name">{{ Auth::user()->name }}</span></a>
            <a href="{{ route('users.show', ['user' => Auth::user()]) }}"><span
                        class="white-text email">{{ Auth::user()->email }}</span></a>
        </div>
    </li>

    <li><a class="waves-effect" href="{{ route('dashboard') }}">{{ trans('backoffice.dashboard') }}</a></li>


    <li>
        <div class="divider"></div>
    </li>

    <li class="no-padding">
        <ul class="collapsible collapsible-accordion">
            <li>
                <a class="collapsible-header">Conteúdo<i class="material-icons right">arrow_drop_down</i></a>
                <div class="collapsible-body">
                    <ul>
                        @if(Auth::user()->hasPermission('articles'))
                            <li><a class="waves-effect"
                                   href="{{ route('articles.index') }}">{{ trans('models.articles') }}</a></li>
                        @endif
                        @if(Auth::user()->hasPermission('pages'))
                            <li><a class="waves-effect"
                                   href="{{ route('pages.index') }}">{{ trans('models.pages') }}</a></li>
                        @endif
                        @if(Auth::user()->hasPermission('media'))
                            <li><a class="waves-effect"
                                   href="{{ route('media.index') }}">{{ trans('models.media') }}</a></li>
                        @endif
                        @if(Auth::user()->hasPermission('polls'))
                            <li><a class="waves-effect"
                                   href="{{ route('polls.index') }}">Sondagens</a></li>
                        @endif
                    </ul>
                </div>
            </li>
        </ul>
    </li>

    <li class="no-padding">
        <ul class="collapsible collapsible-accordion">
            <li>
                <a class="collapsible-header">Pessoas<i class="material-icons right">arrow_drop_down</i></a>
                <div class="collapsible-body">
                    <ul>
                        @if(Auth::user()->hasPermission('players'))
                            <li><a class="waves-effect"
                                   href="{{ route('players.index') }}">{{ trans('models.players') }}</a></li>
                        @endif
                        @if(Auth::user()->hasPermission('player_update_requests'))
                            <li><a class="waves-effect"
                                   href="{{ route('player_update_requests.index') }}">Atualizar Jogadores</a></li>
                        @endif
                        @if(Auth::user()->hasPermission('team_agents'))
                            <li><a class="waves-effect"
                                   href="{{ route('team_agents.index') }}">{{ trans('models.team_agents') }}</a></li>
                        @endif
                        @if(Auth::user()->hasPermission('referees'))
                            <li><a class="waves-effect"
                                   href="{{ route('referees.index') }}">{{ trans('models.referees') }}</a></li>
                        @endif
                        @if(Auth::user()->hasPermission('users'))
                            <li><a class="waves-effect"
                                   href="{{ route('users.index') }}">{{ trans('models.users') }}</a></li>
                        @endif
                        @if(Auth::user()->hasPermission('score-report-bans'))
                            <li><a class="waves-effect"
                                   href="{{ route('score_report_bans.index') }}">{{ trans('models.score_report_bans') }}</a></li>
                        @endif
                    </ul>
                </div>
            </li>
        </ul>
    </li>

    <li class="no-padding">
        <ul class="collapsible collapsible-accordion">
            <li>
                <a class="collapsible-header">Competições<i class="material-icons right">arrow_drop_down</i></a>
                <div class="collapsible-body">
                    <ul>
                        @if(Auth::user()->hasPermission('competitions'))
                            <li><a class="waves-effect"
                                   href="{{ route('competitions.index') }}">{{ trans('models.competitions') }}</a></li>
                        @endif
                        @if(Auth::user()->hasPermission('seasons'))
                            <li><a class="waves-effect"
                                   href="{{ route('seasons.index') }}">{{ trans('models.seasons') }}</a></li>
                        @endif
                        @if(Auth::user()->hasPermission('game_groups'))
                            <li><a class="waves-effect"
                                   href="{{ route('gamegroups.index') }}">{{ trans('models.game_groups') }}</a></li>
                        @endif
                        @if(Auth::user()->hasPermission('group_rules'))
                            <li><a class="waves-effect"
                                   href="{{ route('group_rules.index') }}">Regras</a></li>
                        @endif
                    </ul>
                </div>
            </li>
        </ul>
    </li>

    <li class="no-padding">
        <ul class="collapsible collapsible-accordion">
            <li>
                <a class="collapsible-header">Jogos<i class="material-icons right">arrow_drop_down</i></a>
                <div class="collapsible-body">
                    <ul>
                        @if(Auth::user()->hasPermission('games'))
                            <li><a class="waves-effect"
                                   href="{{ route('games.index') }}">{{ trans('models.games') }}</a></li>
                        @endif
                        @if(Auth::user()->hasPermission('games'))
                            <li><a class="waves-effect"
                                   href="{{ route('game_comments.index') }}">Flash Interview</a></li>
                        @endif
                        @if(Auth::user()->hasPermission('goals'))
                            <li><a class="waves-effect"
                                   href="{{ route('goals.index') }}">{{ trans('models.goals') }}</a></li>
                        @endif
                    </ul>
                </div>
            </li>
        </ul>
    </li>

    <li class="no-padding">
        <ul class="collapsible collapsible-accordion">
            <li>
                <a class="collapsible-header">Clubes<i class="material-icons right">arrow_drop_down</i></a>
                <div class="collapsible-body">
                    <ul>
                        @if(Auth::user()->hasPermission('clubs'))
                            <li><a class="waves-effect"
                                   href="{{ route('clubs.index') }}">{{ trans('models.clubs') }}</a></li>
                        @endif
                        @if(Auth::user()->hasPermission('teams'))
                            <li><a class="waves-effect"
                                   href="{{ route('teams.index') }}">{{ trans('models.teams') }}</a></li>
                        @endif
                        @if(Auth::user()->hasPermission('transfers'))
                            <li><a class="waves-effect"
                                   href="{{ route('transfers.index') }}">{{ trans('models.transfers') }}</a></li>
                        @endif
                    </ul>
                </div>
            </li>
        </ul>
    </li>

    <li class="no-padding">
        <ul class="collapsible collapsible-accordion">
            <li>
                <a class="collapsible-header">Outros<i class="material-icons right">arrow_drop_down</i></a>
                <div class="collapsible-body">
                    <ul>
                        @if(Auth::user()->hasPermission('playgrounds'))
                            <li><a class="waves-effect"
                                   href="{{ route('playgrounds.index') }}">{{ trans('models.playgrounds') }}</a></li>
                        @endif
                        @if(Auth::user()->hasPermission('partners'))
                            <li><a class="waves-effect"
                                   href="{{ route('partners.index') }}">{{ trans('models.partners') }}</a></li>
                        @endif
                        @if(Auth::user()->hasPermission('permissions'))
                            <li><a class="waves-effect"
                                   href="{{ route('permissions.index') }}">{{ trans('models.permissions') }}</a></li>
                        @endif
                        @if(Auth::user()->hasPermission('info_reports'))
                            <li><a class="waves-effect"
                                   href="{{ route('info_reports.index') }}">{{ trans('models.info_reports') }}</a></li>
                        @endif
                            @if(Auth::user()->hasPermission('admin'))
                                <li><a class="waves-effect"
                                       href="{{ route('audit.index') }}">{{ trans('models.audit') }}</a></li>
                            @endif
                    </ul>
                </div>
            </li>
        </ul>
    </li>

    <li>
        <div class="divider"></div>
    </li>
</ul>