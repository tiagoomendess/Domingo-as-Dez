@extends('front.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.transfers') }}</title>
@endsection

@section('content')

    <div class="row">
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

                <div class="card horizontal">

                    <div class="card-image" style="height: 200px; width: 200px;">
                        @if($transfer->player->picture)
                            <img style="width: 100%;" src="{{ $transfer->player->picture }}" alt="">
                        @else
                            <img style="width: 100%;" src="{{ config('custom.default_profile_pic') }}" alt="">
                        @endif
                    </div>

                    <div class="card-stacked">

                        <div class="card-content">

                            <div class="row" style="margin-bottom: 10px;">
                                <div class="col xs12 s12 m12 l12 center">
                                    <p class="flow-text">{{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $transfer->date)->format("d/m/Y") }}</p>
                                </div>
                            </div>

                            <div class="row" style="margin-bottom: 8px;">

                                <div class="col xs5 s5">
                                    @if($transfer->getPreviousTransfer() && $transfer->getPreviousTransfer()->team)

                                        <div style="width: 100%" class="center">
                                            <img style="width: 30px;" src="{{ $transfer->getPreviousTransfer()->team->club->emblem }}" alt="">
                                        </div>

                                        <div class="center" style="width: 100%">
                                            <p class="truncate">
                                                {{ $transfer->getPreviousTransfer()->team->club->name }} ({{$transfer->getPreviousTransfer()->team->name}})
                                            </p>
                                        </div>

                                    @else
                                        <div style="width: 100%" class="center">
                                            <img style="width: 30px;" src="{{ config('custom.default_emblem') }}" alt="">
                                        </div>

                                        <div class="center" style="width: 100%">

                                            <p>
                                                {{ trans('general.none')}}
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <div class="col xs2 s2 m2 l2">
                                    <div class="center" style="margin-top: 15px;">
                                        <i class="material-icons">arrow_forward</i>
                                    </div>
                                </div>

                                <div class="col xs5 s5">
                                    @if($transfer->team)

                                        <div style="width: 100%" class="center">
                                            <img style="width: 30px;" src="{{ $transfer->team->club->emblem }}" alt="">
                                        </div>

                                        <div class="center" style="width: 100%">
                                            <p class="truncate">
                                                {{ $transfer->team->club->name }} ({{$transfer->team->name}})
                                            </p>
                                        </div>

                                    @else
                                        <div style="width: 100%" class="center">
                                            <img style="width: 30px;" src="{{ config('custom.default_emblem') }}" alt="">
                                        </div>

                                        <div class="center" style="width: 100%">
                                            <p>
                                                {{ trans('general.none')}}
                                            </p>
                                        </div>
                                    @endif
                                </div>

                            </div>

                            <div class="row" style="margin-bottom: 0px;">
                                <div class="col xs12 s12 m12 l12 center">
                                    <p class="flow-text truncate">
                                        {{ $transfer->player->name }}
                                        @if($transfer->player->nickname)
                                            ({{ $transfer->player->nickname }})
                                        @endif
                                    </p>
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