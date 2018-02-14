@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.add') }} {{ trans('models.playground') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.add') }} {{ trans('models.playground') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('playgrounds.store') }}" method="POST" enctype="multipart/form-data">

        {{ csrf_field() }}

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input name="name" id="name" type="text" class="validate" value="{{ old('name') }}" required>
                <label for="name">{{ trans('general.name') }}</label>
            </div>
        </div>

        <div class="row">

            <div class="input-field col s4 m3 l2">
                <input type="number" name="width" id="width" class="validate" value="{{ old('width') }}">
                <label for="width">{{ trans('models.width') }}</label>
            </div>

            <div class="input-field col s4 m3 l2">
                <input type="number" name="height" id="height" class="validate" value="{{ old('height') }}">
                <label for="height">{{ trans('models.height') }}</label>
            </div>

            <div class="input-field col s4 m2 l2">
                <input type="number" name="capacity" id="capacity" class="validate" value="{{ old('capacity') }}">
                <label for="capacity">{{ trans('models.capacity') }}</label>
            </div>

        </div>

        <div class="row">

            <div class="input-field col s6 m4 l3">
                <input autocomplete="off" type="text" name="surface" id="surface" class="validate autocomplete" value="{{ old('surface') }}" required>
                <label for="surface">{{ trans('models.surface') }}</label>
            </div>

            <div class="col s6 m4 l3">
                <label>{{ trans('models.club') }}</label>
                <select id="club_id" name="club_id" class="browser-default">
                    <option value="" selected>{{ trans('general.none') }}</option>
                    @foreach(App\Club::all() as $club)
                        <option value="{{ $club->id }}">{{ $club->name }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="row">
            <div class="file-field input-field col s12 m8 l6">
                <div class="btn">
                    <span>{{ trans('models.picture') }}</span>
                    <input name="picture" type="file">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <textarea id="obs" name="obs" class="materialize-textarea" rows="1">{{ old('obs') }}</textarea>
                <label for="obs">{{ trans('models.obs') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('general.visible') }}
                        <input name="visible" type="hidden" value="false">
                        <input name="visible" type="checkbox" value="true" checked>
                        <span class="lever"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12">
                @include('backoffice.partial.button', ['color' => 'green', 'icon' => 'send', 'text' => trans('general.create')])
            </div>
        </div>

    </form>
@endsection

@section('scripts')
    <script>
        $(function () {

            $('input.autocomplete').autocomplete({
                data: {
                    "Pelado": null,
                    "Relva Natural": null,
                    "Relva Artificial": null,
                    "Relva Hibrida": null,
                    "Taco": null,
                    "Cimento": null,
                    "Alcatrão": null,
                    "Cimento Polido": null,

                },
                limit: 20, // The max amount of results that can be shown at once. Default: Infinity.
                onAutocomplete: function(val) {
                    // Callback function when value is autcompleted.
                },
                minLength: 1, // The minimum length of the input for the autocomplete to start. Default: 1.
            });
        })
    </script>
@endsection