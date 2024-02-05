@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.generate_partner_image') }}</title>
    <style>
        #colorPicker {
            width: 200px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col s12">
            <h1>{{ trans('models.generate_partner_image') }} {{ $partner->name }}</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('partners.do_generate_image', ['partner' => $partner]) }}">
        {{ csrf_field() }}
        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input name="text" id="text" type="text" class="validate" value="{{ old('text') }}" required>
                <label for="text">Texto</label>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input type="color" id="colorPicker" name="color" value="#000000">
                <input type="hidden" name="chosen_color" id="chosen_color" value="#000000">
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <div class="input-field inline">
                    <a class="waves-effect waves-light btn modal-trigger" href="#select_media">{{ trans('models.media') }}</a>
                </div>
                <div class="input-field inline">
                    <input id="selected_media_id" name="selected_media_id" type="number" class="validate" value="{{ old('selected_media_id') }}">
                    <label for="selected_media_id">{{ trans('models.media') }} {{ trans('general.id') }}</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m8 l6">
                <button class="btn waves-effect waves-light green darken-3" type="submit" >
                    Gerar Imagem
                    <i class="material-icons right">cloud_download</i>
                </button>
            </div>
        </div>
    </form>

@endsection
@section('scripts')
    @include('backoffice.partial.select_media')
    <script>
        $(document).ready(function () {
            $('.modal').modal();
        })

        let colorPicker = document.getElementById('colorPicker');
        colorPicker.addEventListener('input', function() {
            let selectedColor = colorPicker.value;
            console.log('Selected Color: ', selectedColor);
            $('#chosen_color').val(selectedColor);
        });
    </script>
@endsection
