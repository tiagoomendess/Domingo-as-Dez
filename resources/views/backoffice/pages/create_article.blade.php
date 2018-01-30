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
                            <textarea id="description" name="description" class="materialize-textarea" data-length="280" rows="1"></textarea>
                            <label for="description">{{ trans('general.description') }}</label>
                        </div>
                    </div>
                </form>


                <div class="row">

                    <div class="input-field inline">
                        <a class="waves-effect waves-light btn modal-trigger" href="#select_media">Imagem</a>
                    </div>

                    <div class="input-field inline">
                        <input id="selected_media_id" name="selected_media_id" type="number" class="validate" value="">
                        <label for="selected_media_id">{{ trans('general.id') }}</label>
                    </div>

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

    @include('backoffice.partial.select_media')

    <script type="text/javascript" src="/ckeditor4/ckeditor.js"></script>

    <script>
        $(document).ready(function() {

            CKEDITOR.replace( 'editor1' );

            $('#select_media').modal();

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