@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.add') }} {{ trans('models.permission') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.add') }} {{ trans('models.article') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            @include('backoffice.partial.form_errors')
        </div>
    </div>

    <div class="row">
        <form action="{{ route('permissions.store') }}" method="POST">

            {{ csrf_field() }}

            <div class="input-field col s12 l4">
                <input required name="name" id="name" type="text" class="validate" value="{{ old('name') }}">
                <label for="name">{{ trans('general.name') }}</label>
            </div>

            <div class="col s12">
                <button class="btn waves-effect waves-light" type="submit" name="action">{{ trans('general.send') }}
                    <i class="material-icons right">send</i>
                </button>
            </div>

        </form>
    </div>
@endsection