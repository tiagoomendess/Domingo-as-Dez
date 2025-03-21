@extends('front.layouts.default-page')

@section('head-content')
    <title>Sondagem: {{ $poll->question }}</title>

    <meta property="og:title" content="Sondagem: {{ $poll->question }}" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="Participe na sondage Domingo às Dez" />
    <meta property="og:image" content="{{ url($poll->getImage()) }}">

@endsection

@section('content')
    <div class="container">
        <div class="row">

            <div class="col s12 hide-on-med-and-down">
                <h1>Sondagem</h1>
            </div>

        </div>

        @if(!has_permission('disable_ads') && \Config::get('custom.adsense_enabled'))
        <div class="row no-margin-bottom">
            <div class="col col-xs-12 s12 m10 l8 offset-m1 offset-l2">
                <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3518000096682897"
                        crossorigin="anonymous"></script>
                <!-- Poll -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-3518000096682897"
                     data-ad-slot="9620143996"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col s12 m12 l12">
                @include('front.partial.minimal_poll')
            </div>
        </div>
    </div>

    @if(has_permission('polls.edit'))
        <div class="row">
            <div class="container">
                <a href="{{ route('polls.edit', ['poll' => $poll]) }}" class="btn-floating btn-large waves-effect waves-light blue right"><i class="material-icons">edit</i></a>
            </div>
        </div>
    @endif

@endsection