@extends('front.layouts.default-page')

@section('head-content')
    <title>Técnico {{ $agent->name }}</title>
    <link rel="stylesheet" href="/css/front/player-style.css">

    <meta property="og:title" content="{{ $agent->name . ' - ' . config('app.name') }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:description" content="{{ trans('front.footer_desc') }}"/>
    <meta property="og:image" content="{{ url($agent->getPicture()) }}">
@endsection

@section('content')
    <div class="container">
        <div class="row">

            <div class="col s12 hide-on-med-and-down">
                <h1>{{ $agent->name }}</h1>
            </div>

            <section class="col s12 m12 l4 xl4">
                <h2 class="over-card-title">
                    {{ trans('front.photograph') }}
                </h2>
                <figure>
                    <img src="{{ $agent->getPicture() }}" alt="{{ $agent->name }}">
                </figure>
            </section>

            <div class="col s12 m12 l3 xl4">
                <h2 class="over-card-title">{{ trans('front.data') }}</h2>
                <div class="card">
                    <div class="card-content player-info">

                        <div class="full-info">
                            <span>{{ trans('front.agent_type') }}</span>
                            <span>{{ $agent->getAgentTypeTranslated() }}</span>
                        </div>

                        @if($agent->birth_date)
                        <div class="full-info">
                            <span>{{ trans('front.age') }}</span>
                            <span>{{ \Carbon\Carbon::parse($agent->birth_date)->age }}</span>
                        </div>
                        @endif

                        <div class="full-info">
                            <span>{{ trans('front.current_club') }}</span>
                            <span>
                                @if ($agent->team && $agent->team->club)
                                    <img src="{{ $agent->team->club->getEmblem() }}" alt="">
                                    <a href="{{ $agent->team->club->getPublicURL() }}">{{ $agent->team->club->name }}</a>
                                    <small style="display: block; color: grey;">&nbsp;({{ $agent->team->name }})</small>
                                @else
                                    {{ trans('general.none') }}
                                @endif
                            </span>
                        </div>

                        @if($agent->player)
                        <div class="full-info">
                            <span>{{ trans('models.player') }}</span>
                            <span>
                                <a href="{{ $agent->player->getPublicURL() }}">{{ $agent->player->displayName() }}</a>
                            </span>
                        </div>
                        @endif

                    </div>
                </div>
            </div>

            <div class="col s12 m12 l4 xl4">
                @if(!has_permission('disable_ads') && \Config::get('custom.adsense_enabled'))
                    <script async
                            src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3518000096682897"
                            crossorigin="anonymous"></script>
                    <!-- Team Agent Page -->
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
                <h2 class="over-card-title">{{ trans('general.history') }}</h2>
                <div class="card">
                    <div class="card-content player-clubs">
                        <ul>
                            @if(count($history) > 0)
                                @foreach($history as $record)
                                    <li class="item">
                                        <a href="@if($record->team && $record->team->club){{ $record->team->club->getPublicURL() }}@else#@endif">
                                            <img src="{{ $record->team && $record->team->club ? $record->team->club->getEmblem() : config('custom.default_club_emblem') }}" alt="">

                                            @if ($record->team && $record->team->club)
                                                <div>
                                                    <span class="club">{{ $record->team->club->name }}</span>
                                                    <span class="team">{{ $record->team->name }}</span>
                                                    <small style="display: block; color: grey;">{{ $record->getAgentTypeTranslated() }}</small>
                                                </div>
                                            @else
                                                <span>{{ trans('general.none') }}</span>
                                            @endif

                                            <div class="dates right">
                                                <span class="green-text">
                                                    {{ \Carbon\Carbon::parse($record->started_at)->format("m-Y") }}
                                                </span>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li class="item">
                                    <span>{{ trans('general.no_history_available') }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @if(has_permission('team_agents.edit'))
        <div class="row">
            <div class="container">
                <a href="{{ route('team_agents.show', ['team_agent' => $agent]) }}"
                   class="btn-floating btn-large waves-effect waves-light blue right"><i class="material-icons">edit</i></a>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    <script src="/js/front/player-scripts.js"></script>
@endsection
