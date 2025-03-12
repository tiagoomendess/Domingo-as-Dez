@extends('front.layouts.default-page')

@section('head-content')
    <title>Todos os Clubes</title>
    <style>
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row no-margin-bottom hide-on-med-and-down">
            <div class="col s12">
                <h1>Todos os Clubes</h1>
            </div>
        </div>

        @if(!has_permission('disable_ads') && \Config::get('custom.adsense_enabled'))
            <div class="row no-margin-bottom">
                <div class="vertical-spacer hide-on-med-and-down"></div>
                <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3518000096682897"
                        crossorigin="anonymous"></script>
                <!-- Clubs Page -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-3518000096682897"
                     data-ad-slot="1253054067"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
        @endif

        <div class="row">
            <div class="col s12 m12 l12">
                <div class="card">
                    <div class="card-content">
                        <ul class="list-a">
                            @foreach($clubs as $club)
                                <li>
                                    <a href="{{ $club->getPublicURL() }}" style="padding: 7px 0">
                                        <div class="row no-margin-bottom" style="width: 100%">
                                            <div class="col s12" style="display: flex; align-items: center">
                                                <img style="width: 36px" src="{{ $club->getEmblem() }}" alt="{{ $club->name }}">
                                                <span style="margin-left: 15px">{{ $club->name }}</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            {{ $clubs->onEachSide(1)->links() }}
        </div>
    </div>
@endsection
