@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.add') }} {{ trans('models.page') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.add') }} {{ trans('models.page') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <div class="row">

        <form action="{{ route('pages.store') }}" method="POST" enctype="multipart/form-data">

            {{ csrf_field() }}

            <div class="row">
                <div class="input-field col s12">
                    <input id="title" name="title" type="text" class="validate" data-length="155"
                           value="{{ old('title') }}">
                    <label for="title">{{ trans('general.title') }}</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12 m6">
                    <input id="name" name="name" type="text" class="validate" data-length="50"
                           value="{{ old('name') }}">
                    <label for="name">{{ trans('general.name') }}</label>
                </div>

                <div class="col s12 m6">
                    <div class="file-field input-field">
                        <div class="btn">
                            <span>{{ trans('models.picture') }}</span>
                            <input type="file" name="picture">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <textarea name="body" id="editor1" rows="5">
                        {{ old('body') }}
                    </textarea>
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
                    <button class="btn waves-effect waves-light" type="submit" name="action">{{ trans('general.send') }}
                        <i class="material-icons right">send</i>
                    </button>
                </div>
            </div>

        </form>
    </div>

@endsection

@section('scripts')

    <script type="text/javascript" src="/ckeditor4/ckeditor.js"></script>

    <script>
        $(document).ready(function () {

            CKEDITOR.replace('editor1', {
                filebrowserUploadUrl: '{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}',
                filebrowserUploadMethod: 'form',
                disableNativeSpellChecker: false
            });

            $('#select_media').modal();

            $('input#title').characterCounter();

        });

    </script>
@endsection