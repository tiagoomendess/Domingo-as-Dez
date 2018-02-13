@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.transfers') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.transfers') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$transfers || $transfers->count() == 0)
                <p class="flow-text">{{ trans('models.no_transfers') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.id') }}</th>
                        <th>{{ trans('general.name') }}</th>
                        <th>{{ trans('models.team') }}</th>
                        <th>{{ trans('general.created_at') }}</th>
                        <th>{{ trans('general.updated_at') }}</th>
                    </tr>
                    </thead>

                    @foreach($transfers as $transfer)

                        <tr>
                            <td>{{ $transfer->id }}</td>
                            <td>
                                <a href="{{ route('transfers.show', ['transfer' => $transfer]) }}">
                                    {{ $transfer->player->name }}
                                </a>
                            </td>

                            @if($transfer->team)
                                <td>
                                    {{ $transfer->team->name }} {{ $transfer->team->club->name }}
                                </td>
                            @else
                                <td>
                                    {{ trans('general.none') }}
                                </td>
                            @endif


                            <td>{{ $transfer->created_at }}</td>
                            <td>{{ $transfer->updated_at }}</td>

                        </tr>

                    @endforeach

                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            {{ $transfers->links() }}
        </div>
    </div>

    @if(Auth::user()->hasPermission('transfers.edit'))
        @include('backoffice.partial.add_model_button', ['route' => route('transfers.create')])
    @endif

@endsection