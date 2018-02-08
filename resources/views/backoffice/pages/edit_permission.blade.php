@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.edit') }} {{ trans('models.permission') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.edit') }} {{ trans('models.permission') }}</h1>
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
        <form action="{{ route('permissions.update', ['permission' => $permission]) }}" method="POST">

            {{ csrf_field() }}
            {{ method_field('put') }}

            <div class="input-field col s12 l4">
                <input required name="name" id="name" type="text" class="validate" value="{{ $permission->name }}">
                <label for="name">{{ trans('general.name') }}</label>
            </div>

            <div class="col s12">
                <button class="btn waves-effect waves-light" type="submit" name="action">{{ trans('general.save') }}
                    <i class="material-icons right">send</i>
                </button>
            </div>

        </form>
    </div>
@endsection