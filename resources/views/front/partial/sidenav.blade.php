<ul id="slide-out" class="side-nav">

    @if(Auth::check())
        <li>
            <div class="user-view">
                <div class="background">
                    <img src="https://picsum.photos/300/230?image=1058&blur">
                </div>
                <a href="#!user"><img class="circle" src="{{ Auth::user()->profile->picture }}"></a>
                <a href="#!name"><span class="white-text name">{{ Auth::user()->name }}</span></a>
                <a href="#!email"><span class="white-text email">{{ Auth::user()->email }}</span></a>
            </div>
        </li>

        <li><a href="{{ route('logout') }}">{{ trans('auth.logout') }}</a></li>

        @if(Auth::user()->hasPermission('dashboard'))
            <li><a href="{{ route('dashboard') }}">{{ trans('backoffice.dashboard') }}</a></li>
        @endif
    @else
        <li><a href="{{ route('login') }}">{{ trans('auth.login') }}</a></li>
        <li><a href="{{ route('logout') }} }}">{{ trans('auth.register') }}</a></li>
    @endif

    <li><div class="divider"></div></li>
    <li><a class="waves-effect" href="#!">Third Link With Waves</a></li>
</ul>