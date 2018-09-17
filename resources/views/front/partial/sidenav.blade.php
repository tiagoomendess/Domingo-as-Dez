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
            <li><div class="divider"></div></li>
        @endif

    @else
        <li>
            <div class="user-view">
                <div class="background">
                    <img src="{{ config('custom.site_sidenav_image_no_login') }}">
                </div>
                <br>
                <br>
                <a href="{{ route('login') }}"><span class="white-text">{{ trans('auth.login') }}</span></a>
            </div>
        </li>
    @endif

    <li><a class="waves-effect" href="{{ route('homePage') }}">{{ trans('general.home_page') }}</a></li>
    <li><a class="waves-effect" href="{{ route('news.index') }}">{{ trans('general.news') }}</a></li>

    <li>
        <ul class="collapsible collapsible-accordion">
            <li class="bold">
                <a style="padding: 0 32px" class="collapsible-header waves-effect">{{ trans('models.competitions') }} <i class="material-icons" style="float: right">arrow_drop_down</i></a>
                <div class="collapsible-body">
                    <ul>
                        @foreach(\App\Competition::all()->where('visible', true) as $competition)
                            <li><a style="padding: 0 45px" class="waves-effect" href="{{ route('competition', ['slug' => str_slug($competition->name)]) }}"> {{ $competition->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </li>
        </ul>
    </li>

    <li><a class="waves-effect" href="{{ route('transfers') }}">{{ trans('models.transfers') }}</a></li>

    @if(\Illuminate\Support\Facades\Auth::check())
        <li><a href="{{ route('logout') }}">{{ trans('auth.logout') }}</a></li>
    @endif

</ul>