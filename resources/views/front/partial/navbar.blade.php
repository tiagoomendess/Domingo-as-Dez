<header>
    <div class="navbar-fixed">
        <nav>
            <div class="nav-wrapper blue darken-1">

                <div class="row">
                    <div class="container">

                        <a href="#" data-activates="slide-out" class="button-collapse"><i class="material-icons">menu</i></a>

                        <ul id="nav-mobile" class="left hide-on-med-and-down">

                            <li><a href="{{ route('homePage') }}">{{ trans('general.home_page') }}</a></li>
                            <li><a href="{{ route('publicNews') }}">{{ trans('general.news') }}</a></li>
                            <li><a class="dropdown-button" href="#" data-activates="competitions_dropdown">{{ trans('models.competitions') }}<i class="material-icons right">arrow_drop_down</i></a></li>

                            <ul id="competitions_dropdown" class="dropdown-content dropdown-content-custom">
                                @foreach(\App\Competition::all()->where('visible', true) as $competition)
                                    <li><a href="{{ route('competition', ['slug' => str_slug($competition->name)]) }}">{{ $competition->name }}</a></li>
                                @endforeach
                            </ul>

                        </ul>

                        <a href="{{ route('homePage') }}" class="brand-logo center hide-on-small-only">
                            <img class="navbar-logo" src="{{ config('custom.site_logo') }}">
                        </a>

                        <ul id="nav-mobile" class="right hide-on-med-and-down">

                            <li><a href="{{ route('transfers') }}">{{ trans('models.transfers') }}</a></li>

                            @if(Auth::check())

                                <li><a class="dropdown-button" href="#" data-activates="dropdown1">

                                        {{ Auth::user()->name }}
                                        <i class="material-icons right">arrow_drop_down</i></a></li>

                                <ul id="dropdown1" class="dropdown-content dropdown-content-custom">

                                    @if(Auth::user()->hasPermission('dashboard'))
                                        <li><a href="{{ route('dashboard') }}">{{ trans('backoffice.dashboard') }}</a></li>
                                    @endif

                                    <li>
                                        <a href="#">{{ trans('models.profile') }}</a>
                                    </li>

                                    <li>
                                        <a href="{{ route('logout') }}">{{ trans('auth.logout') }}</a>
                                    </li>

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
    </div>

</header>
