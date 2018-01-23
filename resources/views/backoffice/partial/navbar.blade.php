<nav class="top-nav blue darken-2">
    <div class="nav-wrapper">
        <a href="#" class="brand-logo center hidden-md hidden-sm hidden-xs">{{ config('custom.site_name') }}</a>
        <a href="#" data-activates="slide-out" class="button-collapse"><i class="material-icons">menu</i></a>
        <ul class="right">
            <li><a href="#modal_logout" class="modal-trigger"><i class="fa fa-power-off" aria-hidden="true"></i></a></li>
        </ul>
    </div>
</nav>

<div id="modal_logout" class="modal">
    <div class="modal-content">
        <div class="row">
            <div class=" input-field col s12">
                <a href="{{ route('home') }}" style="width: 100%" class="waves-effect waves-light btn-large green"> @lang('backoffice.navbar_back_to_site')</a>
            </div>

            <div class="input-field col s12">
                <a href="{{ route('logout') }}" style="width: 100%" class="waves-effect waves-light btn-large orange"> @lang('auth.logout')</a>
            </div>

            <div class="input-field col s12">
                <a style="width: 100%" class="waves-effect waves-light btn-large blue modal-close"> @lang('general.cancel')</a>
            </div>
        </div>


    </div>
</div>