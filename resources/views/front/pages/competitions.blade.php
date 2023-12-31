@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.competitions') }}</title>
    <link rel="stylesheet" href="/css/front/competitions-styles.css">

    <meta property="og:title" content="{{ trans('models.competitions') . ' - ' . config('app.name') }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="{{ trans('front.footer_desc') }}"/>

@endsection

@section('content')
    <div class="container">
        <h1 class="hide-on-med-and-down">{{ trans('models.competitions') }}</h1>

        <div class="row">
            <div class="col m12 l8">
                <div class="card">
                    <div class="card-content">
                        <ul class="list-a competitions-list">
                            @foreach($competitions as $competition)
                                <li>
                                    <a href="{{ $competition->getPublicUrl() }}">
                                        <img src="{{ $competition->picture }}" alt="{{$competition->name}}">
                                        <span class="flow-text">{{ $competition->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            @if(!has_permission('disable_ads'))
                <div class="row">
                    <div class="col col-xs-12 s12 m10 l8 offset-m1 offset-l2">
                        <div class="card">
                            <script async
                                    src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3518000096682897"
                                    crossorigin="anonymous"></script>
                            <!-- Competitions List Square -->
                            <ins class="adsbygoogle"
                                 style="display:block"
                                 data-ad-client="ca-pub-3518000096682897"
                                 data-ad-slot="2425038814"
                                 data-ad-format="auto"
                                 data-full-width-responsive="true"></ins>
                            <script>
                                (adsbygoogle = window.adsbygoogle || []).push({});
                            </script>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection

@section('scripts')
@endsection