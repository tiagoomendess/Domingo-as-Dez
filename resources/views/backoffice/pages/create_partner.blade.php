@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.add') }} {{ trans('models.partner') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.add') }} {{ trans('models.partner') }}</h1>
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

        <form action="{{ route('partners.store') }}" method="POST" enctype="multipart/form-data">

            {{ csrf_field() }}

            <div class="row">
                <div class="input-field col s10 m5">
                    <input id="name" name="name" type="text" class="validate" data-length="50"
                           value="{{ old('name') }}" required>
                    <label for="name">{{ trans('general.name') }}</label>
                </div>

                <div class="input-field col s2 m1">
                    <input id="priority" name="priority" type="number" class="validate"
                           value="{{ old('priority') }}" required>
                    <label for="name">Prioridade</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12 m6">
                    <input id="url" name="url" type="text" class="validate" data-length="255"
                           value="{{ old('url') }}" required>
                    <label for="url">{{ trans('general.url') }}</label>
                </div>
            </div>

            <div class="row">
                <div class="col s12 m6">
                    <div class="file-field input-field">
                        <div class="btn">
                            <span>{{ trans('models.picture') }}</span>
                            <input type="file" name="picture" value="{{ old('picture') }}">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" value="{{ old('picture') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

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

@endsection