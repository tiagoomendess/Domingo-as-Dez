@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.transfers') }}</title>
@endsection

@section('content')

    <div class="row hide-on-med-and-down">
        <div class="col xs12 s12 m12 l12">
            <h1>{{ trans('models.transfers') }}</h1>
        </div>
    </div>

    <div class="row">

        <div class="col s12 m12 l8 xl8">

            @if($transfers->count() == 0)
                <div class="card-panel">
                    <p class="flow-text">{{ trans('models.no_transfers') }}</p>
                </div>
            @endif

            @foreach($transfers as $transfer)

                <div class="card">
                    <div class="card-content">
                        <div class="row" style="margin-bottom: 0px;">

                            <div class="col xs2 s4 m4 l3 xl3 valign-wrapper" style="min-height: 200px;">
                                <img style="width: 100%;" class="circle responsive-img" src="{{ $transfer->player->getPicture() }}" alt="">
                            </div>

                            <div class="col xs10 s8 m8 l9 xl9">

                                <div class="row">

                                    <div class="col s12">
                                        <p class="flow-text truncate center">{{ $transfer->player->displayName() }}</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col s5 center">

                                        @if($transfer->getPreviousTransfer())

                                            <a class="tooltipped" data-position="top" data-delay="50" data-tooltip="{{ $transfer->getPreviousTransfer()->getClubName() }}">
                                                <div style="width: 100%">
                                                    <img style="width: 60px" src="{{ $transfer->getPreviousTransfer()->getClubEmblem() }}" alt="">
                                                </div>

                                                <div style="width: 100%">
                                                    <small>{{ $transfer->getPreviousTransfer()->getTeamName() }}</small>
                                                </div>
                                            </a>

                                        @else
                                            <a class="tooltipped" data-position="top" data-delay="50" data-tooltip="{{ trans('general.none') }}">
                                                <div style="width: 100%">
                                                    <img style="width: 60px" src="{{ config('custom.default_emblem') }}" alt="">
                                                </div>

                                                <div style="width: 100%">
                                                    <small>{{ trans('general.none') }}</small>
                                                </div>
                                            </a>
                                        @endif

                                    </div>

                                    <div class="col s2 center">
                                            <i class="material-icons center" style="margin-top: 20px;">arrow_forward</i>
                                    </div>

                                    <div class="col s5 center">

                                        <a class="tooltipped" data-position="top" data-delay="50" data-tooltip="{{ $transfer->getClubName() }}">
                                            <div class="" style="width: 100%">
                                                <img style="width: 60px" src="{{ $transfer->getClubEmblem() }}" alt="">
                                            </div>

                                            <div style="width: 100%">
                                                <small>{{ $transfer->getTeamName() }}</small>
                                            </div>
                                        </a>

                                    </div>
                                </div>

                                <div class="row" style="margin-bottom: 0px;">
                                    <div class="col s12">
                                        <p class="truncate center">{{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $transfer->date)->format("d/m/Y") }}</p>
                                    </div>
                                </div>
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

    <div class="row">
        {{ $transfers->links() }}
    </div>


@endsection