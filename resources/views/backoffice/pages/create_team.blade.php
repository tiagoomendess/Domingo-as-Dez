@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.add') }} {{ trans('models.team') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.add') }} {{ trans('models.team') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('teams.store') }}" method="POST">

        {{ csrf_field() }}

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input required name="name" id="name" type="text" class="validate" value="{{ old('name') }}">
                <label for="name">{{ trans('models.name') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m8 l6">
                <label>{{ trans('models.club') }}</label>
                <select name="club_id" class="browser-default" required>
                    <option value="none" disabled selected>{{ trans('general.choose_option') }}</option>

                    @foreach(\App\Club::all() as $club)
                        <option value="{{ $club->id }}">{{ $club->name }}</option>
                    @endforeach

                </select>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input autocomplete="off" id="contact_email" name="contact_email" type="email" value="{{ old('contact_email') }}">
                <label for="contact_email">Email de Contacto</label>
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
            <div class="input-field col s12">
                @include('backoffice.partial.button', ['color' => 'green', 'icon' => 'send', 'text' => trans('general.create')])
            </div>
        </div>

    </form>
@endsection