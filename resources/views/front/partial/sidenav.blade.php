<ul id="slide-out" class="side-nav">

    @if(Auth::check())
        <li>
            <div class="user-view">
                <div class="background">
                    <img src="{{ config('custom.site_sidenav_image') }}">
                </div>
                <a href="{{ route('front.userprofile.edit') }}"><img class="circle" src="{{ Auth::user()->profile->getPicture() }}"></a>
                <a href="{{ route('front.userprofile.edit') }}"><span class="white-text name">{{ Auth::user()->name }}</span></a>
                <a href="{{ route('front.userprofile.edit') }}"><span class="white-text email">{{ Auth::user()->email }}</span></a>
            </div>
        </li>

        @if(Auth::user()->hasPermission('dashboard'))
            <li><a href="{{ route('dashboard') }}">{{ trans('backoffice.dashboard') }}</a></li>
        @endif

        <li><a href="{{ route('front.userprofile.edit') }}">{{ trans('models.profile') }}</a></li>
        <li><a href="{{ route('logout') }}">{{ trans('auth.logout') }}</a></li>

    @else
        <li><a href="{{ route('login') }}">{{ trans('auth.login') }}</a></li>
        <li><a href="{{ route('register') }}">{{ trans('auth.register') }}</a></li>
    @endif

    <li><div class="divider"></div></li>

    <li><a class="waves-effect" href="{{ route('homePage') }}">{{ trans('general.home_page') }}</a></li>
    <li><a class="waves-effect" href="{{ route('news.index') }}">{{ trans('general.news') }}</a></li>

    <li class="no-padding">
        <ul class="collapsible collapsible-accordion">
            <li class="bold">
                <a class="collapsible-header waves-effect" style="padding-left: 31px;">{{ trans('models.competitions') }}</a>
                <div class="collapsible-body">
                    <ul>
                        @foreach(\App\Competition::all()->where('visible', true) as $competition)
                            <li><a class="waves-effect" href="{{ route('competition', ['slug' => str_slug($competition->name)]) }}"> {{ $competition->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </li>
        </ul>
    </li>

    <li><a class="waves-effect" href="{{ route('transfers') }}">{{ trans('models.transfers') }}</a></li>

</ul>