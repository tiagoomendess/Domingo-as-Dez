@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.media') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.add') }} {{ trans('models.media') }}</h1>
        </div>

    </div>

    @include('backoffice.partial.form_errors')

    <div class="row">
        <form class="" method="POST" action="{{ route('media.store') }}" enctype="multipart/form-data">

            {{ csrf_field() }}

            <div class="row">
                <div class="input-field col s12">
                    <input name="tags" placeholder="{{ trans('general.tags_help') }}" id="tags" type="text" class="validate" required>
                    <label for="first_name">{{ trans('general.tags') }}</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12 l5">
                    <input name="url" id="url" type="text" class="validate">
                    <label for="first_name">{{ trans('general.url') }}</label>
                </div>

                <div class="col s12 l2">
                    <p class="flow-text center">Ou</p>
                </div>

                <div class="file-field input-field col s12 l5">
                    <div class="btn">
                        <span>{{ trans('general.file') }}</span>
                        <input name="file" type="file">
                    </div>

                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text">
                    </div>
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
                <div class="file-field input-field col s5">
                    <button class="btn waves-effect waves-light" type="submit" name="action">Submit
                        <i class="material-icons right">send</i>
                    </button>
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