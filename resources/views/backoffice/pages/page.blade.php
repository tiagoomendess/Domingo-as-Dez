@extends('backoffice.layouts.default-page')

@section('head-content')
    <title> {{ trans('models.page') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.page') }}</h1>
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

        <form action="" method="POST">

            <div class="row">
                <div class="input-field col s12 disabled">
                    <input disabled id="title" name="title" type="text" class="validate" data-length="155"
                           value="{{ $page->title }}">
                    <label for="title">{{ trans('general.title') }}</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12 m6">
                    <input disabled id="name" name="name" type="text" class="validate disabled" data-length="50"
                           value="{{ $page->name }}">
                    <label for="name">{{ trans('general.name') }}</label>
                </div>

                <div class="col s12 m6">
                    <div class="file-field input-field">
                        <div class="btn">
                            <span>{{ trans('models.picture') }}</span>
                            <input disabled type="file">
                        </div>
                        <div class="file-path-wrapper">
                            <input disabled class="file-path validate disabled" type="text" value="{{ $page->picture }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <div style="width: 100%; border-color: #313131; border-style: solid; padding: 10px">
                        {!! $page->body !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <div class="switch">
                        <label>
                            {{ trans('general.visible') }}
                            <input name="visible" type="hidden" value="false">
                            @if($page->visible)
                                <input disabled name="visible" type="checkbox" value="true" checked>
                            @else
                                <input disabled name="visible" type="checkbox" value="true">
                            @endif
                            <span class="lever"></span>
                        </label>
                    </div>
                </div>
            </div>

        </form>
    </div>

    @if(Auth::user()->haspermission('pages.edit'))
        @include('backoffice.partial.model_options', [
            'delete_route' => route('pages.destroy', ['page' => $page]),
            'edit_route' => route('pages.edit', ['page' => $page])
        ])
    @endif

@endsection

@section('scripts')

    <script type="text/javascript" src="/ckeditor4/ckeditor.js"></script>

    <script>
        $(document).ready(function () {

            CKEDITOR.replace('editor1');

            $('#select_media').modal();

            $('input#title').characterCounter();

        });

    </script>
@endsection