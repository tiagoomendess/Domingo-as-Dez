@extends('front.layouts.default-page')

@section('head-content')
    <title>Jogador {{ $player->name }}</title>
    <link rel="stylesheet" href="/css/front/player-style.css">

    <meta property="og:title" content="{{ $player->name . ' - ' . config('app.name') }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="{{ trans('front.footer_desc') }}"/>
    <meta property="og:image" content="{{ url($player->getAgeSafePicture()) }}">
@endsection

@section('content')
    <div class="container">
        <div class="row">

            <div class="col s12 hide-on-med-and-down">
                <h1>{{ $player->name }}</h1>
            </div>

            <section class="col s12 m12 l4 xl4">
                <h2 class="over-card-title">
                    {{ trans('front.photograph') }}
                </h2>
                <figure>
                    <img src="{{ $player->getAgeSafePicture() }}" alt="{{$player->displayName()}}">
                </figure>
            </section>

            <div class="col s12 m12 l3 xl4">
                <h2 class="over-card-title">{{ trans('front.data') }}</h2>
                <div class="card">
                    <div class="card-content player-info">

                        <div class="full-info">
                            <span>{{ trans('front.age') }}</span>
                            <span>{{ $player->birth_date ? $player->getAge() : trans('general.unknown_female') }}</span>
                        </div>

                        <div class="full-info">
                            <span>{{ trans('general.nickname') }}</span>
                            <span>{{ $player->nickname ? $player->nickname : '-'}}</span>
                        </div>

                        <div class="full-info">
                            <span>{{ trans('models.position') }}</span>
                            <span>
                                {{trans('general.' . $player->position)}}
                            </span>
                        </div>

                        <div class="full-info">
                            <span>{{ trans('front.current_club') }}</span>
                            <span>
                                @if ($player->getClub())
                                    <img src="{{ $player->getClub()->getEmblem() }}" alt="">
                                    <a href="{{ $player->getClub()->getPublicURL() }}">{{ $player->getClub()->name }}</a>
                                @else
                                    {{ trans('general.none') }}
                                @endif
                            </span>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col s12 m12 l4 xl4">
                @if(!has_permission('disable_ads'))
                    <script async
                            src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3518000096682897"
                            crossorigin="anonymous"></script>
                    <!-- Player Page -->
                    <ins class="adsbygoogle"
                         style="display:block"
                         data-ad-client="ca-pub-3518000096682897"
                         data-ad-slot="8786241371"
                         data-ad-format="auto"
                         data-full-width-responsive="true"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                @endif
                <h2 class="over-card-title">{{ trans('front.history_club') }}</h2>
                <div class="card">
                    <div class="card-content player-clubs">
                        <ul>
                            @foreach($transfers as $index => $transfer)

                                <li class="item">
                                    <a href="@if(has_permission('transfers.edit')){{ route('transfers.show',['id' => $transfer->id]) }}@elseif($transfer->team){{ $transfer->getClub()->getPublicURL() }}@else#@endif">

                                        <img src="{{ $transfer->getClubEmblem() }}" alt="">

                                        @if ($transfer->team)
                                            <div>
                                                <span class="club">{{ $transfer->team->club->name }}</span>
                                                <span class="team">{{ $transfer->team->name }}</span>
                                            </div>
                                        @else
                                            <span>{{ $transfer->displayTeamAndClub() }}</span>
                                        @endif

                                        <div class="dates right">
                                            <span class="green-text">
                                                {{
                                                \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $transfer->date)->format("m-Y")
                                                }}
                                            </span>
                                        </div>


                                    </a>
                                </li>

                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @if(has_permission('players.edit'))
        <div class="row">
            <div class="container">
                <a href="{{ route('players.show', ['player' => $player]) }}"
                   class="btn-floating btn-large waves-effect waves-light blue right"><i class="material-icons">edit</i></a>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    <script src="/js/front/player-scripts.js"></script>
@endsection