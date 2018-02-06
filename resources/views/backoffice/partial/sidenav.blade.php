<ul id="slide-out" class="side-nav fixed">

    <li>
        <div class="user-view">
            <div class="background">
                <img src="https://picsum.photos/300/230?image=1058">
            </div>
            <a href="#"><img class="circle" src="{{ Auth::user()->profile->picture }}"></a>
            <a href="#!name"><span class="white-text name">{{ Auth::user()->name }}</span></a>
            <a href="#!email"><span class="white-text email">{{ Auth::user()->email }}</span></a>
        </div>
    </li>

    @if(Auth::user()->hasPermission('articles'))
        <li><a class="waves-effect" href="{{ route('articles.index') }}">{{ trans('models.articles') }}</a></li>
    @endif

    @if(Auth::user()->hasPermission('competitions'))
        <li><a class="waves-effect" href="{{ route('competitions.index') }}">{{ trans('models.competitions') }}</a></li>
    @endif

    @if(Auth::user()->hasPermission('clubs'))
        <li><a class="waves-effect" href="#">{{ trans('models.clubs') }}</a></li>
    @endif

    @if(Auth::user()->hasPermission('teams'))
        <li><a class="waves-effect" href="#">{{ trans('models.teams') }}</a></li>
    @endif

    @if(Auth::user()->hasPermission('games'))
        <li><a class="waves-effect" href="#">{{ trans('models.games') }}</a></li>
    @endif

    @if(Auth::user()->hasPermission('media'))
        <li><a class="waves-effect" href="{{ route('media.index') }}">{{ trans('models.media') }}</a></li>
    @endif

    @if(Auth::user()->hasPermission('admin'))
        <li><a class="waves-effect" href="{{ route('permissions.index') }}">{{ trans('models.permissions') }}</a></li>
        <li><a class="waves-effect" href="{{ route('users.index') }}">{{ trans('models.users') }}</a></li>
    @endif

</ul>