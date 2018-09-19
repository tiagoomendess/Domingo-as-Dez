<header>
    <div class="navbar-fixed">
        <nav>
            <div class="nav-wrapper darken-1">
                <div class="row">

                    <a href="#" data-activates="slide-out" class="button-collapse"><i class="material-icons">menu</i></a>

                    <div class="hide-on-large-only">
                            <span id="navbar_title" class="hide truncate">
                                @if(isset($navbar_title))
                                    {{ $navbar_title }}
                                @endif
                            </span>
                    </div>

                    <div class="container">

                        <a href="{{ route('homePage') }}" class="brand-logo hide-on-med-and-down left">
                            <img class="navbar-logo" src="{{ config('custom.site_logo') }}" alt="{{ config('app.name') }}">
                        </a>

                        <ul id="nav-mobile" class="hide-on-med-and-down navbar-items">
                            <li>
                                <a href="{{ route('homePage') }}">{{ trans('general.home_page') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('news.index') }}">{{ trans('general.news') }}</a>
                            </li>
                            <li>
                                <a class="dropdown-button" href="#" data-activates="competitions_dropdown">
                                    {{ trans('models.competitions') }}<i class="material-icons right">arrow_drop_down</i>
                                </a>
                            </li>

                            <ul id="competitions_dropdown" class="dropdown-content navbar-dropdown">
                                @foreach(\App\Competition::all()->where('visible', true) as $competition)
                                    <li><a href="{{ route('competition', ['slug' => str_slug($competition->name)]) }}">{{ $competition->name }}</a></li>
                                @endforeach
                            </ul>

                            <li>
                                <a href="{{ route('transfers') }}">{{ trans('models.transfers') }}</a>
                            </li>

                        </ul>

                        <ul id="nav-mobile" class="right hide-on-med-and-down">
                            @if(Auth::check())
                                <li id="account_action">
                                    <a class="dropdown-button" href="#" data-activates="account_dropdown">
                                        <img class="circle" src="{{ Auth::user()->profile->getPicture() }}" alt="{{ $user->name }}">
                                        {{ str_replace("...", "", str_limit( Auth::user()->name, 13))}}
                                        <i class="material-icons right">arrow_drop_down</i>
                                    </a>
                                </li>

                                <ul id="account_dropdown" class="dropdown-content navbar-dropdown">
                                    @if(Auth::user()->hasPermission('dashboard'))
                                        <li><a href="{{ route('dashboard') }}">{{ trans('backoffice.dashboard') }}</a></li>
                                    @endif
                                    <li>
                                        <a href="{{ route('front.userprofile.edit')}}">{{ trans('models.profile') }}</a>
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
