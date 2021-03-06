<footer class="page-footer">
<div class="container">
    <div class="row">
        <div class="col l6 s12">
            <h5 class="white-text">{{ config('app.name') }}</h5>
            <p class="grey-text text-lighten-4">{{ trans('front.footer_desc') }}</p>
        </div>
        <div class="col l4 offset-l2 s12">
            <h5 class="white-text">{{ trans('front.important_links') }}</h5>
            <ul>
                <li><a class="grey-text text-lighten-3" href="#">{{ trans('front.contact') }}</a></li>
                <li><a class="grey-text text-lighten-3" href="{{ route('privacy_policy') }}">{{ trans('front.privacy_policy') }}</a></li>
                <li><a class="grey-text text-lighten-3" href="{{ route('terms_and_conditions') }}">{{ trans('front.terms_and_conditions') }}</a></li>
                <li><a class="grey-text text-lighten-3" href="{{ route('rgpd_info') }}">{{ trans('front.rgpd') }}</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="footer-copyright">
    <div class="container">
        <div class="row">
            <div class="col xs12 s12 m8 l8">
                © {{ \Carbon\Carbon::now()->year }} - {{ config('custom.site_name') }}. {{ trans('front.all_rights_reserved') }}
            </div>

            <div class="col xs12 s12 m4 l4">
                <a class="grey-text text-lighten-4 right" href="http://mendes.com.pt">Desenvolvido por Tiago Mendes</a>
            </div>
        </div>
    </div>
</div>
</footer>