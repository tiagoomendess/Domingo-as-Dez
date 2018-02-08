@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.seasons') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.seasons') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$seasons || $seasons->count() == 0)
                <p class="flow-text">{{ trans('models.no_seasons') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.id') }}</th>
                        <th>{{ trans('general.name') }}</th>
                        <th>{{ trans('general.created_at') }}</th>
                        <th>{{ trans('general.updated_at') }}</th>
                    </tr>
                    </thead>

                    @foreach($seasons as $season)

                        <tr>
                            <td>{{ $season->id }}</td>
                            <td><a href="{{ route('seasons.show', ['season' => $season]) }}">
                                    {{ $season->start_year }}/{{ $season->end_year }} ({{ $season->competition->name }})
                                </a>
                            </td>

                            <td>{{ $season->created_at }}</td>
                            <td>{{ $season->updated_at }}</td>

                        </tr>

                    @endforeach

                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            {{ $seasons->links() }}
        </div>
    </div>

    @if(Auth::user()->hasPermission('seasons.edit'))
        @include('backoffice.partial.add_model_button', ['route' => route('seasons.create')])
    @endif

@endsection