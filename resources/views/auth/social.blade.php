<div class="row no-margin-bottom">
    <div class="col xs12 s12 m8 l6 xl4 offset-m2 offset-l3 offset-xl4">
        <div class="card">
            <div class="card-content">
                <div class="row no-margin-bottom">

                    <div class="col xs4 s4 center">
                        <a href="{{ route('social.redirect', ['provider' => 'facebook']) }}" class="social-login-link">
                            <img class="facebook-btn" src="/images/facebook-logo.svg" alt="">
                        </a>
                    </div>

                    <div class="col xs4 s4 center">
                        <a href="{{ route('social.redirect', ['provider' => 'twitter']) }}" class="social-login-link">
                            <img src="/images/twitter-logo.svg" alt="">
                        </a>
                    </div>

                    <div class="col xs4 s4 center">
                        <a href="{{ route('social.redirect', ['provider' => 'google']) }}" class="social-login-link">
                            <img src="/images/google-plus-logo.svg" alt="">
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>