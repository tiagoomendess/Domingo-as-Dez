<footer class="page-footer">
<div class="container">
    <div class="row">
        <div class="col l6 s12">
            <h5 class="white-text">Footer Content</h5>
            <p class="grey-text text-lighten-4">You can use rows and columns here to organize your footer content.</p>
        </div>
        <div class="col l4 offset-l2 s12">
            <h5 class="white-text">{{ trans('front.important_links') }}</h5>
            <ul>
                <li><a class="grey-text text-lighten-3" href="#!">{{ trans('front.contact') }}</a></li>
                <li><a class="grey-text text-lighten-3" href="#!">{{ trans('front.privacy_policy') }}</a></li>
                <li><a class="grey-text text-lighten-3" href="#!">{{ trans('front.terms_and_conditions') }}</a></li>
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