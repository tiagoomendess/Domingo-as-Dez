@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.edit') }} {{ trans('models.season') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.edit') }} {{ trans('models.season') }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('seasons.update', ['season' => $season]) }}" method="POST" enctype="multipart/form-data">

        {{ csrf_field() }}

        {{ method_field('PUT') }}

        <div class="row">
            <div class="input-field col s6 m4 l3">
                <input required name="start_year" id="start_year" type="number" class="validate" value="{{ old('start_year', $season->start_year) }}">
                <label for="start_year">{{ trans('models.start_year') }}</label>
            </div>

            <div class="input-field col s6 m4 l3">
                <input required name="end_year" id="end_year" type="number" class="validate" value="{{ old('end_year', $season->end_year) }}">
                <label for="end_year">{{ trans('models.end_year') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m4 l3">
                <label>{{ trans('models.competition') }}</label>
                <select name="competition" class="browser-default" required>
                    <option value="{{ $season->competition_id }}" selected>{{ \App\Competition::find($season->competition_id)->name }}</option>

                    @foreach(\App\Competition::all() as $competition)

                        @if($competition->id != $season->competition_id)
                            <option value="{{ $competition->id }}">{{ $competition->name }}</option>
                        @endif

                    @endforeach

                </select>
            </div>

        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input name="name" id="name" type="text" class="validate" value="{{ old('name', $season->name) }}" maxlength="155">
                <label for="name">{{ trans('models.season_display_name') }}</label>
                <span class="helper-text">{{ trans('models.season_display_name_hint') }}</span>
            </div>
        </div>

        <div class="row">
            <div class="file-field input-field col s12 m8 l6">
                <div class="btn">
                    <span>{{ trans('general.file') }}</span>
                    <input name="file" type="file" accept="image/jpeg,image/png,image/jpg">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text" value="{{ $season->picture }}" placeholder="{{ trans('models.season_display_picture') }}">
                </div>
                <span class="helper-text">{{ trans('models.season_display_picture_hint') }}</span>
            </div>
        </div>

        @if($season->picture)
            <div class="row">
                <div class="col s12 m8 l6">
                    <img src="{{ $season->picture }}" alt="" style="max-height: 60px; margin-bottom: 10px;">
                    <div class="switch">
                        <label>
                            Remover logótipo da época
                            <input name="clear_picture" type="hidden" value="false">
                            <input name="clear_picture" type="checkbox" value="true">
                            <span class="lever"></span>
                        </label>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <textarea id="obs" name="obs" class="materialize-textarea" rows="1">{{ old('obs', $season->obs) }}</textarea>
                <label for="obs">{{ trans('models.obs') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="switch">
                    <label>
                        {{ trans('general.visible') }}
                        <input name="visible" type="hidden" value="false">
                        @if($season->visible)
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
