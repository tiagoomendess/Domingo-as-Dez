<ul id="slide-out" class="side-nav fixed" style="height: 100%;">

    <li>
        <div class="user-view">
            <div class="background">
                <img src="https://picsum.photos/300/230?image=1058">
            </div>
            <a href="{{ route('users.show', ['user' => Auth::user()]) }}"><img class="circle" src="{{ Auth::user()->profile->picture }}"></a>
            <a href="{{ route('users.show', ['user' => Auth::user()]) }}"><span class="white-text name">{{ Auth::user()->name }}</span></a>
            <a href="{{ route('users.show', ['user' => Auth::user()]) }}"><span class="white-text email">{{ Auth::user()->email }}</span></a>
        </div>
    </li>

    <li><a class="waves-effect" href="{{ route('dashboard') }}">{{ trans('backoffice.dashboard') }}</a></li>

    @if(Auth::user()->hasPermission('articles'))
        <li><a class="waves-effect" href="{{ route('articles.index') }}">{{ trans('models.articles') }}</a></li>
    @endif

    @if(Auth::user()->hasPermission('competitions'))
        <li><a class="waves-effect" href="{{ route('competitions.index') }}">{{ trans('models.competitions') }}</a></li>
    @endif

    @if(Auth::user()->hasPermission('seasons'))
        <li><a class="waves-effect" href="{{ route('seasons.index') }}">{{ trans('models.seasons') }}</a></li>
    @endif

    @if(Auth::user()->hasPermission('clubs'))
        <li><a class="waves-effect" href="{{ route('clubs.index') }}">{{ trans('models.clubs') }}</a></li>
    @endif

    @if(Auth::user()->hasPermission('teams'))
        <li><a class="waves-effect" href="{{ route('teams.index') }}">{{ trans('models.teams') }}</a></li>
    @endif

    @if(Auth::user()->hasPermission('goals'))
        <li><a class="waves-effect" href="{{ route('goals.index') }}">{{ trans('models.goals') }}</a></li>
    @endif

    @if(Auth::user()->hasPermission('players'))
        <li><a class="waves-effect" href="{{ route('players.index') }}">{{ trans('models.players') }}</a></li>
    @endif

    @if(Auth::user()->hasPermission('transfers'))
        <li><a class="waves-effect" href="{{ route('transfers.index') }}">{{ trans('models.transfers') }}</a></li>
    @endif

    @if(Auth::user()->hasPermission('games'))
        <li><a class="waves-effect" href="{{ route('games.index') }}">{{ trans('models.games') }}</a></li>
    @endif

    @if(Auth::user()->hasPermission('media'))
        <li><a class="waves-effect" href="{{ route('media.index') }}">{{ trans('models.media') }}</a></li>
    @endif

    @if(Auth::user()->hasPermission('playgrounds'))
        <li><a class="waves-effect" href="{{ route('playgrounds.index') }}">{{ trans('models.playgrounds') }}</a></li>
    @endif

    @if(Auth::user()->hasPermission('permissions'))
        <li><a class="waves-effect" href="{{ route('permissions.index') }}">{{ trans('models.permissions') }}</a></li>
        <li><a class="waves-effect" href="{{ route('users.index') }}">{{ trans('models.users') }}</a></li>
    @endif

</ul>