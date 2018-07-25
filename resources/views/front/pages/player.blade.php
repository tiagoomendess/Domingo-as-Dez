@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ $player->name }}</title>
    <link rel="stylesheet" href="/css/front/player-style.css">
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
                    <img src="{{ $player->getPicture() }}" alt="{{$player->displayName()}}">
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

                <h2 class="over-card-title">{{ trans('front.history_club') }}</h2>
                <div class="card">
                    <div class="card-content player-clubs">
                        <ul>
                            @foreach($transfers as $index => $transfer)

                                <li class="item">
                                    <a href="@if($transfer->team){{ $transfer->getClub()->getPublicURL() }}@else#@endif">

                                        <img src="{{ $transfer->getClubEmblem() }}" alt="">

                                        @if ($transfer->team)
                                            <div>
                                                <span class="club">{{ $transfer->team->club->name }}</span>
                                                <span class="team">{{ $transfer->team->name }}</span>
                                            </div>
                                        @else
                                            <span>{{ $transfer->displayTeamAndClub() }}</span>
                                        @endif

                                        @if($index == (count($transfers) - 1))
                                            <div class="dates right">
                                                <span>...</span>
                                                <span class="green-text">
                                                {{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $transfer->date)->format("m-Y") }}
                                            </span>
                                            </div>

                                        @else
                                            <div class="dates right">
                                            <span class="red-text">
                                                {{
                                            \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $transfers[$index + 1]->date)->format("m-Y")
                                            }}
                                            </span>

                                                <span class="green-text">
                                                {{
                                            \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $transfer->date)->format("m-Y")
                                            }}
                                            </span>

                                            </div>
                                        @endif

                                    </a>
                                </li>

                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="/js/front/player-scripts.js"></script>
@endsection