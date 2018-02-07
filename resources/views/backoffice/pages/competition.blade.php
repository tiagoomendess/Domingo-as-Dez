@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.competition') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.competition') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">

            <img class="materialboxed" width="50%" src="{{ $competition->picture }}">

            <p class="flow-text">

                <b>{{ trans('general.id') }} : </b> {{ $competition->id }} <br>
                <b>{{ trans('general.name') }} : </b> {{ $competition->name }} <br>
                <b>{{ trans('models.competition_type') }} : </b> {{ trans('models.' . $competition->competition_type ) }} <br>
                <b>{{ trans('general.visible') }} : </b> {{ trans_choice('general.boolean', $competition->visible) }} <br>
                <b>{{ trans('general.created_at') }} : </b> {{ $competition->created_at }} <br>
                <b>{{ trans('general.updated_at') }} : </b> {{ $competition->updated_at }} <br>
            </p>
        </div>
    </div>

@endsection

@if(Auth::user()->hasPermission('competitions.edit'))

    @include('backoffice.partial.model_options', [
        'edit_route' => route('competitions.edit', ['competition' => $competition]),
        'delete_route' => route('competitions.destroy', ['competition' => $competition])
    ])

@endif

@section('scripts')
    <script>
        $(document).ready(function(){
            $('.materialboxed').materialbox();
        });
    </script>
@endsection