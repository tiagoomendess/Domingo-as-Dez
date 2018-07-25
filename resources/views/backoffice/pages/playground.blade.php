@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.add') }} {{ trans('models.playground') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.playground') }}</h1>
        </div>
    </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input name="name" id="name" type="text" class="validate" value="{{ $playground->name }}" disabled>
                <label for="name">{{ trans('general.name') }}</label>
            </div>
        </div>

        <div class="row">

            <div class="input-field col s4 m3 l2">
                <input type="number" name="width" id="width" class="validate" value="{{ $playground->width }}" disabled>
                <label for="width">{{ trans('models.width') }}</label>
            </div>

            <div class="input-field col s4 m3 l2">
                <input type="number" name="height" id="height" class="validate" value="{{ $playground->height }}" disabled>
                <label for="height">{{ trans('models.height') }}</label>
            </div>

            <div class="input-field col s4 m2 l2">
                <input type="number" name="capacity" id="capacity" class="validate" value="{{ $playground->capacity }}" disabled>
                <label for="capacity">{{ trans('models.capacity') }}</label>
            </div>

        </div>

        <div class="row">

            <div class="input-field col s6 m4 l3">
                <input type="text" name="surface" id="surface" class="validate autocomplete" value="{{ $playground->surface }}" disabled>
                <label for="surface">{{ trans('models.surface') }}</label>
            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('models.club') }}</label>
                <select id="club_id" name="club_id" class="browser-default" disabled>
                    @if($playground->club)
                        <option value="0" selected>{{ $playground->club->name }}</option>
                    @else
                        <option value="0" selected>{{ trans('general.none') }}</option>
                    @endif

                </select>
            </div>

        </div>

        <div class="row">

            <div class="col s12 m4 l3">
                @if($playground->picture)
                    <img class="materialboxed" src="{{ $playground->picture }}" alt="" style="width: 100%">
                @else

                    <div class="center">
                        <p class="small center">{{ trans('models.no_picture') }}</p>
                    </div>
                @endif

            </div>

        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <textarea disabled id="obs" name="obs" class="materialize-textarea" rows="1">{{ $playground->obs }}</textarea>
                <label for="obs">{{ trans('models.obs') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('general.visible') }}
                        <input name="visible" type="hidden" value="false">
                        @if($playground->visible)
                            <input name="visible" type="checkbox" value="true" checked disabled>
                        @else
                            <input name="visible" type="checkbox" value="true" disabled>
                        @endif
                        <span class="lever"></span>
                    </label>
                </div>
            </div>
        </div>

    @if(Auth::user()->haspermission('playgrounds.edit'))
        @include('backoffice.partial.model_options', [
            'delete_route' => route('playgrounds.destroy', ['playground' => $playground]),
            'edit_route' => route('playgrounds.edit', ['playground' => $playground])
        ])
    @endif

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $(document).ready(function(){
                $('.materialboxed').materialbox();
            });
        });
    </script>
@endsection