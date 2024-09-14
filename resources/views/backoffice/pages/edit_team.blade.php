@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.edit') }} {{ trans('models.team') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.edit') }} {{ trans('models.team') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('teams.update', ['team' => $team]) }}" method="POST">

        {{ csrf_field() }}

        {{ method_field('PUT') }}

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input required name="name" id="name" type="text" class="validate" value="{{ old('name', $team->name) }}">
                <label for="name">{{ trans('models.name') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m8 l6">
                <label>{{ trans('models.club') }}</label>
                <select name="club_id" class="browser-default" required>

                    <option value="{{ $team->club_id }}" selected>{{ $team->club->name }}</option>

                    @foreach(\App\Club::all() as $club)

                        @if($club->id != $team->club_id)
                            <option value="{{ $club->id }}">{{ $club->name }}</option>
                        @endif

                    @endforeach

                </select>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input autocomplete="off" id="contact_email" name="contact_email" type="email" value="{{ old('contact_email', $team->contact_email) }}">
                <label for="contact_email">Email de Contacto</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('general.visible') }}
                        <input name="visible" type="hidden" value="false">
                        @if($team->visible)
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