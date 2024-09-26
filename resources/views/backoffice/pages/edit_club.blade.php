@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.edit') }} {{ trans('models.club') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.edit') }} {{ trans('models.club') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('clubs.update', ['club' => $club]) }}" method="POST" enctype="multipart/form-data">

        {{ csrf_field() }}

        {{ method_field('PUT') }}

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input name="name" id="name" type="text" class="validate" value="{{ old('name', $club->name) }}" required>
                <label for="name">{{ trans('general.name') }}</label>
            </div>
        </div>

        <div class="row">

            <div class="file-field input-field col s10 m7 l5">
                <div class="btn">
                    <span>{{ trans('models.emblem') }}</span>
                    <input name="emblem" type="file">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text" value="{{ $club->emblem }}">
                </div>
            </div>

            <div class="col s2 m1 l1">
                @if($club->emblem)
                    <img style="max-height: 60px" src="{{ $club->emblem }}" alt="" class="responsive-img"/>
                @else
                    <img style="max-height: 60px" src="{{ config('custom.default_emblem') }}" alt="" class="responsive-img"/>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input name="website" id="website" type="text" class="validate" value="{{ old('website', $club->website) }}">
                <label for="website">{{ trans('models.website') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m4 l3">
                <input name="contact_email" id="contact_email" type="email" class="validate" value="{{ old('contact_email', $club->contact_email) }}">
                <label for="contact_email">Email de Contacto</label>
            </div>
            <div class="input-field col s12 m4 l3">
                <input name="admin_user_id" id="admin_user_id" type="number" class="validate" value="{{ old('admin_user_id', $club->admin_user_id) }}">
                <label for="admin_user_id">Admin User ID</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m4 l3">
                <input required name="priority" id="priority" type="number" class="validate" value="{{ old('priority', $club->priority) }}">
                <label for="priority">Prioridade</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('general.visible') }}
                        <input name="visible" type="hidden" value="false">
                        @if($club->visible)
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