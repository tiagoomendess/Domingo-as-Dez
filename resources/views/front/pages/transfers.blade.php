@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.transfers') }}</title>
    <link rel="stylesheet" href="/css/front/transfers-style.css">
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

                @foreach($transfers as $transfer)
                    <div class="card">
                        <div class="card-content">
                            <div class="transfer">
                                <div class="player-pic">
                                    <figure>
                                        <img src="{{ $transfer->player->getPicture() }}" alt="{{ $transfer->player->displayName() }}">
                                    </figure>
                                </div>

                                <div class="transfer-info">
                                    <span class="player-name">{{ $transfer->player->displayName() }}</span>
                                    <div class="clubs">
                                        <div class="club">
                                            @if ($transfer->getPreviousTransfer())
                                                <a href="{{ $transfer->getPreviousTransfer()->team->club->getPublicURL() }}">
                                                    <figure>
                                                        <img src="{{ $transfer->getPreviousTransfer()->getClubEmblem() }}" alt="{{ $transfer->getPreviousTransfer()->getClubName() }}">
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
                                                        <img src="{{ $transfer->getClubEmblem() }}" alt="{{ $transfer->getClubName() }}">
                                                    </figure>
                                                    <span class="club-name">{{ $transfer->displayTeamAndClub() }}</span>
                                                </a>
                                            @else

                                                <figure>
                                                    <img src="{{ $transfer->getClubEmblem() }}" alt="{{ $transfer->getClubName() }}">
                                                </figure>
                                                <span class="club-name">{{ $transfer->displayTeamAndClub() }}</span>

                                            @endif
                                        </div>
                                    </div>
                                    <span class="transfer-date">{{ Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $transfer->date)->format("d/m/Y") }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

            <div class="col s12 m12 l4 xl4">
                <div class="card">
                    <div class="card-image">
                        <img src="http://placehold.it/500x500">
                    </div>
                </div>
            </div>
        </div>

        <div class="row no-margin-bottom">
            {{ $transfers->links() }}
        </div>

    </div>




@endsection