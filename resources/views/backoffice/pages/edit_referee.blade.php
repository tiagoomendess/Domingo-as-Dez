@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.edit') }} {{ trans('models.referee') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.edit') }} {{ trans('models.referee') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('referees.update', ['referee' => $referee]) }}" method="POST" enctype="multipart/form-data">

        {{ csrf_field() }}
        {{ method_field('PUT') }}

        <div class="row">

            <div class="input-field col s12 m4 l3">
                <input name="name" id="name" type="text" class="validate" value="{{ old('name', $referee->name) }}" required>
                <label for="name">{{ trans('general.name') }}</label>
            </div>

            <div class="input-field col s12 m4 l3">
                <input name="association" id="association" type="text" class="validate" value="{{ old('association', $referee->association) }}" required>
                <label for="association">{{ trans('general.association') }}</label>
            </div>

        </div>

        <div class="row">

            <div class="col s12 m4 l3">
                @if($referee->picture)
                    <img width="100%" class="materialboxed" src="{{ $referee->picture }}">
                @else
                    <img width="100%" class="materialboxed"  src="{{ config('custom.default_profile_pic') }}">
                @endif
            </div>

            <div class="col s12 m4 l3">
                <div class="col s12">

                    <h4 class="flow-text">Alterar Fotografia</h4>
                    <div class="divider"></div>

                </div>

                <div class="file-field input-field col s12">
                    <div class="btn">
                        <span>{{ trans('general.upload') }}</span>
                        <input name="picture" type="file">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text">
                    </div>

                </div>

                <div class="col s12">
                    <p class="center">
                        {{ trans('general.or') }}
                    </p>
                </div>

                <div class="input-field col s12">
                    <input name="picture_url" id="picture_url" type="text" class="validate" value="{{ old('picture_url') }}">
                    <label for="picture_url">{{ trans('general.url') }} {{ trans('models.picture') }}</label>

                </div>
            </div>

        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <textarea id="obs" name="obs" class="materialize-textarea" rows="1">{{ old('obs', $referee->obs) }}</textarea>
                <label for="obs">{{ trans('models.obs') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('general.visible') }}
                        <input name="visible" type="hidden" value="false">
                        @if($referee->visible)
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
            <div class="input-field col s12">
                @include('backoffice.partial.button', ['color' => 'green', 'icon' => 'save', 'text' => trans('general.save')])
            </div>
        </div>

    </form>


@endsection