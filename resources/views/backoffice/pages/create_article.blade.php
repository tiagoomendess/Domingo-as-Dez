@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.add') }} {{ trans('models.article') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.add') }} {{ trans('models.article') }}</h1>
        </div>
    </div>

    <div class="row">

        <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data">

            <div class="row">
                <div class="input-field col s12">
                    <input id="title" name="title" type="text" class="validate" data-length="155">
                    <label for="title">{{ trans('general.title') }}</label>
                </div>
            </div>

            <div class="row">
                <form class="col s12">
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="description" name="description" class="materialize-textarea" data-length="280"></textarea>
                            <label for="description">{{ trans('general.description') }}</label>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row">
                <div class="col s12">
                    <textarea name="editor1" id="editor1" rows="5">

                    </textarea>
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <input placeholder="{{ trans('general.date') }}" id="date" type="text" class="datepicker">
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

        </form>
    </div>

@endsection

@section('scripts')

    <script type="text/javascript" src="/ckeditor4/ckeditor.js"></script>

    <script>
        $(document).ready(function() {

            CKEDITOR.replace( 'editor1' );

            $('input#title').characterCounter();

            $('.datepicker').pickadate({
                format: 'dd-mm-yyyy',
                selectMonths: true,
                selectYears: 3,
                today: '{{ trans('general.today') }}',
                clear: '{{ trans('general.clear') }}',
                close: '{{ trans('general.ok') }}',
                closeOnSelect: false
            });
        });

    </script>
@endsection