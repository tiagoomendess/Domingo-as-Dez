@extends('front.layouts.default-page')

@section('head-content')
    <title>Todos os Jogadores</title>
    <style>
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row no-margin-bottom hide-on-med-and-down">
            <div class="col s12">
                <h1>Todos os Jogadores</h1>
            </div>
        </div>

        @if(!has_permission('disable_ads'))
            <div class="row no-margin-bottom">
                <div class="vertical-spacer hide-on-med-and-down"></div>
                <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3518000096682897"
                        crossorigin="anonymous"></script>
                <!-- All Players -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-3518000096682897"
                     data-ad-slot="2075977303"
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
                            @foreach($players as $player)
                                <li>
                                    <a href="{{ $player->public_url }}" style="padding: 7px 0">
                                        <div class="row no-margin-bottom" style="width: 100%">
                                            <div class="col s12" style="display: flex; align-items: center">
                                                <img style="width: 36px" src="{{ $player->age_safe_picture }}" alt="{{ $player->name }}">
                                                <span style="margin-left: 15px">{{ $player->name }}</span>
                                                @if(!empty($player->nickname))
                                                    <span style="margin-left: 5px">({{ $player->nickname }})</span>
                                                @endif
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
            {{ $players->onEachSide(1)->links() }}
        </div>
    </div>
@endsection
