@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.playgrounds') }}</title>
@endsection

@section('content')
    <div class="row no-margin-bottom">
        <div class="col s8">
            <h1>{{ trans('models.playgrounds') }}</h1>
        </div>
        <div class="col s4">
            @include('backoffice.partial.search_box_btn')
        </div>
    </div>

    <div class="row no-margin-bottom">
        @include('backoffice.partial.search_box')
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$playgrounds || $playgrounds->count() == 0)
                <p class="flow-text">{{ trans('models.no_playgrounds') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.id') }}</th>
                        <th>{{ trans('general.name') }}</th>
                        <th>{{ trans('models.club') }}</th>
                        <th>{{ trans('general.created_at') }}</th>
                        <th>{{ trans('general.updated_at') }}</th>
                    </tr>
                    </thead>

                    @foreach($playgrounds as $playground)

                        <tr>
                            <td>{{ $playground->id }}</td>
                            <td>
                                <a href="{{ route('playgrounds.show', ['playground' => $playground]) }}">
                                    {{ $playground->name }}
                                </a>
                            </td>

                            @if($playground->club)
                                <td>
                                    {{ $playground->club->name }}
                                </td>
                            @else
                                <td>
                                    {{ trans('general.none') }}
                                </td>
                            @endif


                            <td>{{ $playground->created_at }}</td>
                            <td>{{ $playground->updated_at }}</td>

                        </tr>

                    @endforeach

                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            {{ $playgrounds->links() }}
        </div>
    </div>

    @if(Auth::user()->hasPermission('playgrounds.edit'))
        @include('backoffice.partial.add_model_button', ['route' => route('playgrounds.create')])
    @endif

@endsection