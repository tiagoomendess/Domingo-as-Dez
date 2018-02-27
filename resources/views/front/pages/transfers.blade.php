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

            <div class="card">
                <div class="card-content">
                    @if($transfers->count() == 0)
                        <p class="flow-text">{{ trans('models.no_transfers') }}</p>
                    @endif


                    <ul class="collection">
                        @foreach($transfers as $transfer)
                            <li class="collection-item">
                                <div class="row">

                                    <div class="col xs4 s4">
                                        {{ $transfer->player->name }}
                                    </div>

                                    <div class="col xs4 s4">

                                    </div>

                                    <div class="col xs4 s4">
                                        @if($transfer->team)
                                            {{ $transfer->team->club->name }} ({{ $transfer->team->name }})
                                        @else
                                            {{ trans('none') }}
                                        @endif

                                    </div>

                                </div>
                            </li>
                        @endforeach
                    </ul>

                </div>
            </div>


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