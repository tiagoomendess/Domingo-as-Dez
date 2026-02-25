@if($withCard ?? true)
<div class="card">
    <div class="card-content center-align">
@else
    <div class="center-align" style="padding: 20px 0;">
@endif
        <i class="material-icons large grey-text">lock</i>
        <p class="flow-text">{{ trans('front.login_to_see_more') }}</p>
        <p class="grey-text">{{ $customMessage ?? trans('front.login_to_see_more_desc') }}</p>
        <div style="margin-top: 20px;">
            <a href="{{ route('login') }}" class="btn waves-effect waves-light green darken-2">
                {{ trans('auth.login') }}
            </a>
            <a href="{{ route('register') }}" class="btn-flat waves-effect">
                {{ trans('auth.register') }}
            </a>
        </div>
    </div>
@if($withCard ?? true)
</div>
@endif
