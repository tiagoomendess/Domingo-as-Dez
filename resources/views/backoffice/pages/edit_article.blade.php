@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.edit') }} {{ trans('models.article') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.edit') }} {{ trans('models.article') }}</h1>
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

        <form action="{{ route('articles.update', ['article' => $article]) }}" method="POST">

            {{ csrf_field() }}
            {{ method_field('PUT') }}

            <div class="row">
                <div class="input-field col s12">
                    <input required id="title" name="title" type="text" class="validate" data-length="155" value="{{ $article->title }}">
                    <label for="title">{{ trans('general.title') }}</label>
                </div>
            </div>

            <div class="row">
                <form class="col s12">
                    <div class="row">
                        <div class="input-field col s12">
                            <textarea id="description" name="description" class="materialize-textarea" data-length="280">{{ $article->description }}</textarea>
                            <label for="description">{{ trans('general.description') }}</label>
                        </div>
                    </div>
                </form>

                <div class="row">

                    <div class="col s12">
                        <div class="input-field inline">
                            <a class="waves-effect waves-light btn modal-trigger" href="#select_media">{{ trans('models.media') }}</a>
                        </div>

                        <div class="input-field inline">
                            @if($article->media)
                                <input id="selected_media_id" name="selected_media_id" type="number" class="validate" value="{{ old('selected_media_id', $article->media->id ) }}">
                            @else
                                <input id="selected_media_id" name="selected_media_id" type="number" class="validate" value="{{ old('selected_media_id') }}">
                            @endif

                            <label for="selected_media_id">{{ trans('models.media') }} {{ trans('general.id') }}</label>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col s12">
                    <textarea name="editor1" id="editor1" rows="5">
                        {{ $article->text }}
                    </textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col s12">
                        <input name="date" placeholder="{{ trans('general.date') }}" id="date" type="text" class="datepicker" value="{{\Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $article->date)->format("Y-m-d")}}">
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col s12">
                        <input name="tags" placeholder="{{ trans('general.tags_help') }}" id="tags" type="text" value="{{ $article->tags }}" class="validate" required>
                        <label for="first_name">{{ trans('general.tags') }}</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col s12">
                        <div class="switch">

                            <label>
                                {{ trans('general.visible') }}
                                <input name="visible" type="hidden" value="false">
                                @if($article->visible == 1)
                                    <input name="visible" type="checkbox" value="true" checked>
                                @else
                                    <input name="visible" type="checkbox" value="true">
                                @endif

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

    @include('backoffice.partial.pick_a_date_js')

    @include('backoffice.partial.select_media')

    <script type="text/javascript" src="/ckeditor4/ckeditor.js"></script>

    <script>
        $(document).ready(function() {

            CKEDITOR.replace( 'editor1' );

            $('#select_media').modal();

            $('input#title').characterCounter();

        });

    </script>
@endsection