@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.clubs') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.clubs') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$clubs || $clubs->count() == 0)
                <p class="flow-text">{{ trans('models.no_clubs') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.id') }}</th>
                        <th>{{ trans('models.emblem')  }}</th>
                        <th>{{ trans('general.name') }}</th>
                        <th>{{ trans('general.created_at') }}</th>
                        <th>{{ trans('general.updated_at') }}</th>
                    </tr>
                    </thead>

                    @foreach($clubs as $club)

                        <tr>
                            <td>{{ $club->id }}</td>
                            <td>
                                @if($club->emblem)
                                    <img style="max-height: 30px" src="{{ $club->emblem }}" alt="" class="responsive-img"/>
                                @else
                                    <img style="max-height: 30px" src="{{ config('custom.default_emblem') }}" alt="" class="responsive-img"/>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('clubs.show', ['club' => $club]) }}">
                                    {{ $club->name }}
                                </a>
                            </td>

                            <td>{{ $club->created_at }}</td>
                            <td>{{ $club->updated_at }}</td>

                        </tr>

                    @endforeach

                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            {{ $clubs->links() }}
        </div>
    </div>

    @if(Auth::user()->hasPermission('clubs.edit'))
        @include('backoffice.partial.add_model_button', ['route' => route('clubs.create')])
    @endif

@endsection