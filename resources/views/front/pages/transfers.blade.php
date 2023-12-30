@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.transfers') }}</title>
    <link rel="stylesheet" href="/css/front/transfers-style.css">

    <meta property="og:title" content="{{ trans('models.transfers') . ' - ' . config('app.name') }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="{{ trans('front.footer_desc') }}"/>

@endsection

@section('content')

    <div class="container">
        <div class="row hide-on-med-and-down no-margin-bottom">
            <div class="col xs12 s12 m12 l12">
                <h1>{{ trans('models.transfers') }}</h1>
            </div>
        </div>

        <div class="row no-margin-bottom">
            <div class="col s12 m12 l8 xl8">
                @if($transfers->count() == 0)
                    <div class="card-panel">
                        <p class="flow-text">{{ trans('models.no_transfers') }}</p>
                    </div>
                @endif

                @foreach($transfers as $index => $transfer)
                    @if($index % 3 == 0 && $index != 0)
                        <div class="">
                            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3518000096682897"
                                    crossorigin="anonymous"></script>
                            <!-- Transfers Page Feed -->
                            <ins class="adsbygoogle"
                                 style="display:block"
                                 data-ad-client="ca-pub-3518000096682897"
                                 data-ad-slot="9373590543"
                                 data-ad-format="auto"
                                 data-full-width-responsive="true"></ins>
                            <script>
                                (adsbygoogle = window.adsbygoogle || []).push({});
                            </script>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-content">
                            <div class="transfer">
                                <div class="player-pic">
                                    <figure>
                                        <img src="{{ $transfer->player->getPicture() }}"
                                             alt="{{ $transfer->player->displayName() }}">
                                    </figure>
                                </div>

                                <div class="transfer-info">
                                    <a style="color: #000000" class="player-name"
                                       href="{{ route('front.player.show', ['id' => $transfer->player->id, 'name_slug' => str_slug($transfer->player->name) ]) }}">{{ $transfer->player->displayName() }}</a>
                                    <div class="clubs">
                                        <div class="club">
                                            @if ($transfer->getPreviousTransfer() && !empty($transfer->getPreviousTransfer()->team))
                                                <a href="{{ $transfer->getPreviousTransfer()->team->club->getPublicURL() }}">
                                                    <figure>
                                                        <img src="{{ $transfer->getPreviousTransfer()->getClubEmblem() }}"
                                                             alt="{{ $transfer->getPreviousTransfer()->getClubName() }}">
                                                    </figure>
                                                    <span class="club-name">{{ $transfer->getPreviousTransfer()->displayTeamAndClub() }}</span>
                                                </a>
                                            @else
                                                <figure>
                                                    <img src="{{ config('custom.default_emblem') }}" alt="">
                                                </figure>
                                                <span class="club-name">{{ trans('general.none') }}</span>
                                            @endif
                                        </div>

                                        <div class="separator">
                                            <i class="material-icons">
                                                arrow_forward
                                            </i>
                                        </div>

                                        <div class="club">
                                            @if ($transfer->team)
                                                <a href="{{ $transfer->team->club->getPublicURL() }}">
                                                    <figure>
                                                        <img src="{{ $transfer->getClubEmblem() }}"
                                                             alt="{{ $transfer->getClubName() }}">
                                                    </figure>
                                                    <span class="club-name">{{ $transfer->displayTeamAndClub() }}</span>
                                                </a>
                                            @else

                                                <figure>
                                                    <img src="{{ $transfer->getClubEmblem() }}"
                                                         alt="{{ $transfer->getClubName() }}">
                                                </figure>
                                                <span class="club-name">{{ $transfer->displayTeamAndClub() }}</span>

                                            @endif
                                        </div>
                                    </div>
                                    @if(has_permission('transfers.edit'))
                                        <span class="transfer-date"><a
                                                    href="{{ route('transfers.show', ['id' => $transfer->id]) }}">{{ Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $transfer->date)->format("d/m/Y") }}</a></span>
                                    @else
                                        <span class="transfer-date">{{ Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $transfer->date)->format("d/m/Y") }}</span>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

            <div class="col s12 m12 l4 xl4">

                <div style="margin-top: .5rem">
                    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3518000096682897"
                            crossorigin="anonymous"></script>
                    <!-- Transfers Page -->
                    <ins class="adsbygoogle"
                         style="display:block"
                         data-ad-client="ca-pub-3518000096682897"
                         data-ad-slot="7172832993"
                         data-ad-format="auto"
                         data-full-width-responsive="true"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>

            </div>

        </div>

        <div class="row no-margin-bottom">
            {{ $transfers->links() }}
        </div>

    </div>




@endsection