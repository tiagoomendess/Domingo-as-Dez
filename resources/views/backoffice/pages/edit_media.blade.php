@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.edit') }} {{ trans('models.media') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.edit') }} {{ trans('models.media') }}</h1>
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



        <form class="" method="POST" action="{{ route('media.update', ['media' => $media]) }}">

            {{ method_field('PUT') }}
            {{ csrf_field() }}

            <div class="row">
                <div class="input-field col s12">
                    <input name="tags" placeholder="{{ trans('general.tags_help') }}" id="tags" type="text" value="{{ $media->tags }}" class="validate" required>
                    <label for="first_name">{{ trans('general.tags') }}</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <input disabled name="url" id="url" type="text" class="disabled" value="{{ $media->url }}">
                    <label for="url">{{ trans('general.url') }}</label>
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <div class="switch">

                        <label>
                            {{ trans('general.visible') }}
                            <input name="visible" type="hidden" value="false">
                            @if($media->visible == 1)
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
                    <button class="btn waves-effect waves-light" type="submit" name="action">{{ trans('general.save') }}
                        <i class="material-icons right">send</i>
                    </button>
                </div>
            </div>

        </form>

    </div>
@endsection