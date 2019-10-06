@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.referees') }}</title>
@endsection

@section('content')
    <div class="row no-margin-bottom">
        <div class="col s8">
            <h1>{{ trans('models.referees') }}</h1>
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
            @if(!$referees || $referees->count() == 0)
                <p class="flow-text">{{ trans('models.no_referees') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.id') }}</th>
                        <th>{{ trans('models.picture')  }}</th>
                        <th>{{ trans('general.name') }}</th>
                        <th>{{ trans('general.created_at') }}</th>
                        <th>{{ trans('general.updated_at') }}</th>
                    </tr>
                    </thead>

                    @foreach($referees as $referee)

                        <tr>
                            <td>{{ $referee->id }}</td>
                            <td>
                                <img style="max-height: 30px" src="{{ $referee->getPicture() }}" alt="" class="responsive-img circle"/>
                            </td>
                            <td>
                                <a href="{{ route('referees.show', ['referees' => $referee]) }}">
                                    {{ $referee->name }}
                                </a>
                            </td>

                            <td>{{ $referee->created_at }}</td>
                            <td>{{ $referee->updated_at }}</td>

                        </tr>

                    @endforeach

                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            {{ $referees->links() }}
        </div>
    </div>

    @if(Auth::user()->hasPermission('referee.edit'))
        @include('backoffice.partial.add_model_button', ['route' => route('referees.create')])
    @endif

@endsection