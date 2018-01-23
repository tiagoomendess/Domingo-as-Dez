<nav>
    <div class="nav-wrapper">
        <a href="#!" class="brand-logo center">
            <img class="navbar-logo" src="{{ config('custom.site_logo') }}">
        </a>
        <ul class="right hide-on-med-and-down">
            @if(Auth::check())

                <ul id="dropdown1" class="dropdown-content">
                    <li><a href="{{ route('logout') }}">{{ trans('auth.logout') }}</a></li>
                </ul>

                <li><a class="dropdown-button" href="#" data-activates="dropdown1">{{ Auth::user()->name }}<i class="material-icons right">arrow_drop_down</i></a></li>

            @else
                <li><a class="waves-effect waves-light btn" href="{{ route('login') }}">{{ trans('auth.login') }}</a></li>
                <li><a class="waves-effect waves-light btn" href="{{ route('register') }}">{{ trans('auth.register') }}</a></li>
            @endif

        </ul>

        <ul class="left hide-on-med-and-down">
            <li><a class="waves-effect waves-light btn">{{ trans('models.articles') }}</a></li>
        </ul>
    </div>
</nav>