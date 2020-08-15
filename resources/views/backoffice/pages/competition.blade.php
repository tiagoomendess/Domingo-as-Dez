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
        <div class="input-field col s12 m8 l6">
            <input required disabled name="name" id="name" type="text" class="validate" value="{{ $competition->name }}">
            <label for="name">{{ trans('general.name') }}</label>
        </div>
    </div>

    <div class="row">
        <div class="file-field input-field col s10 m6 l5">
            <div class="btn">
                <span>{{ trans('general.file') }}</span>
                <input name="file" type="file" disabled>
            </div>
            <div class="file-path-wrapper">
                <input class="file-path validate" type="text" value="{{ $competition->getPicture() }}" disabled="">
            </div>
        </div>

        <div class="col s2 m2 l1">
            <img src="{{ $competition->getPicture() }}" alt="" style="max-height: 60px;">
        </div>
    </div>

    <div class="row">
        <div class="col col s12 m8 l6">
            <div class="switch">
                <label>
                    {{ trans('general.visible') }}
                    <input name="visible" type="hidden" value="false">
                    @if($competition->visible)
                        <input name="visible" type="checkbox" value="true" checked disabled>
                    @else
                        <input name="visible" type="checkbox" value="true" disabled>
                    @endif

                    <span class="lever"></span>
                </label>
            </div>
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