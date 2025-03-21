<div class="row no-margin-bottom">
    <div class="col xs12 s12 m10 l8 xl6 offset-m1 offset-l2 offset-xl3">
        <div class="card social-login-card">
            <div class="card-content">
                <div class="row">

                    <div class="col xs6 s6 center">
                        <a href="{{ route('social.redirect', ['provider' => 'facebook']) }}" class="social-login-link">
                            <img class="facebook-btn" src="/images/facebook-logo.svg" alt="">
                        </a>
                    </div>

                    <div class="col xs6 s6 center">
                        <a href="{{ route('social.redirect', ['provider' => 'google']) }}" class="social-login-link">
                            <img src="/images/google_icon.png" alt="">
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>