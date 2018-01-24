@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.media') }}</title>
@endsection

@section('content')
    <h1>{{ trans('models.media') }}</h1>

    <div class="row">
        <form class="col s12">


            <div class="row">
                <div class="input-field col s12">
                    <input placeholder="{{ trans('general.tags_help') }}" id="tags" type="text" class="validate">
                    <label for="first_name">{{ trans('general.tags') }}</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <select>
                        <option value="none" disabled selected>{{ trans('general.choose_option') }}</option>
                        <option value="image">{{ trans('general.image') }}</option>
                        <option value="video">{{ trans('general.video') }}</option>
                        <option value="youtube">Youtube</option>
                        <option value="other">{{ trans('general.other') }}</option>
                    </select>
                    <label>Materialize Select</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <div class="switch">
                        <label>
                            {{ trans('general.visible') }}
                            <input type="checkbox">
                            <span class="lever"></span>
                        </label>
                    </div>
                </div>
            </div>

        </form>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('select').material_select();
        });
    </script>
@endsection