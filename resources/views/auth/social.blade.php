@php
    $socialCount = count($socialProviders);
    $columnClass = $socialCount >= 3 ? 'm4' : ($socialCount === 2 ? 'm6' : 'm12');
@endphp
<div class="row no-margin-bottom">
    <div class="col xs12 s12 m10 l8 xl6 offset-m1 offset-l2 offset-xl3">
        <div class="card social-login-card">
            <div class="card-content">
                <div class="row">
                    @foreach($socialProviders as $socialProvider)
                        <div class="col s12 {{ $columnClass }} center">
                            <a href="{{ route('social.redirect', ['provider' => $socialProvider]) }}" class="social-login-link">
                                @if($socialProvider === 'facebook')
                                    <img class="facebook-btn" src="/images/facebook-logo.svg" alt="">
                                @elseif($socialProvider === 'google')
                                    <img src="/images/google_icon.png" alt="">
                                @else
                                    <img src="/images/apple-logo.svg" alt="">
                                @endif
                                <span>{{ trans('auth.login_with_'.$socialProvider) }}</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
