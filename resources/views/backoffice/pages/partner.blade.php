@extends('backoffice.layouts.default-page')

@section('head-content')
    <title> {{ trans('models.partner') }}</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.partner') }}</h1>
        </div>
    </div>


    <div class="row">

        <form>

            {{ csrf_field() }}

            <div class="row">
                <div class="input-field col s10 m5">
                    <input id="name" name="name" type="text" class="validate" data-length="50"
                           value="{{ $partner->name }}" disabled>
                    <label for="name">{{ trans('general.name') }}</label>
                </div>

                <div class="input-field col s2 m1">
                    <input id="priority" name="priority" type="number" class="validate"
                           value="{{ $partner->priority }}" disabled>
                    <label for="name">Prioridade</label>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12 m6">
                    <input id="url" name="url" type="text" class="validate" data-length="255"
                           value="{{ $partner->url }}" disabled>
                    <label for="url">{{ trans('general.url') }}</label>
                </div>
            </div>

            <div class="row">
                <div class="col s12 m6">
                    <div class="file-field input-field">
                        <div class="btn">
                            <span>{{ trans('models.picture') }}</span>
                            <input type="file" name="picture" disabled>
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" value="{{ $partner->picture }}" disabled>
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
                            @if($partner->visible)
                                <input disabled name="visible" type="checkbox" value="true" checked>
                            @else
                                <input disabled name="visible" type="checkbox" value="true">
                            @endif
                            <span class="lever"></span>
                        </label>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @if(Auth::user()->haspermission('partners.edit'))
        @include('backoffice.partial.model_options', [
            'delete_route' => route('partners.destroy', ['partner' => $partner]),
            'edit_route' => route('partners.edit', ['partner' => $partner])
        ])
    @endif

@endsection

@section('scripts')
@endsection