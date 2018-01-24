<nav>
    <div class="nav-wrapper blue darken-1">

        <div class="row">
            <div class="col s12 m12 l10 offset-l1">

                <a href="#" data-activates="slide-out" class="button-collapse"><i class="material-icons">menu</i></a>

                <a href="#!" class="brand-logo center">
                    <img class="navbar-logo" src="{{ config('custom.site_logo') }}">
                </a>
                <ul id="nav-mobile" class="right hide-on-med-and-down">

                    @if(Auth::check())

                        <li><a class="dropdown-button" href="#" data-activates="dropdown1">{{ Auth::user()->name }}<i class="material-icons right">arrow_drop_down</i></a></li>

                        <ul id="dropdown1" class="dropdown-content dropdown-content-custom">

                            @if(Auth::user()->hasPermission('dashboard'))
                                <li><a href="{{ route('dashboard') }}">{{ trans('backoffice.dashboard') }}</a></li>
                            @endif

                            <li><a href="{{ route('logout') }}">{{ trans('auth.logout') }}</a></li>

                        </ul>

                    @else
                        <li><a href="{{ route('login') }}">{{ trans('auth.login') }}</a></li>
                        <li><a href="{{ route('register') }}">{{ trans('auth.register') }}</a></li>
                    @endif

                </ul>
            </div>
        </div>


    </div>
</nav>