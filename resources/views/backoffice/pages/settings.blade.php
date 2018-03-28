@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('backoffice.settings') }}</title>
@endsection

@section('content')
    <br>
    <div class="row">
        <div class="col xs12 s12 m8 l6">
            <table class="bordered">

                <tr class="setting setting-popup modal-trigger" id="setting_1" data-target="modal_1">

                    <td>
                        <div class="row">
                            <div class="col s6">
                                <span>{{ trans('settings.custom.author') }}</span>
                                <small>custom.author</small>
                            </div>

                            <div class="col s6">

                            </div>
                        </div>
                    </td>

                    <div id="modal_1" class="modal">
                        <div class="modal-content">
                            <h4 class="center">{{ trans('settings.custom.author') }}</h4>
                            <div class="row" style="border-bottom: 0">
                                <div class="input-field col s12 m8 l6 offset-m2 offset-l3">
                                    <input id="setting_value_1" type="text" class="validate" value="{{ config('custom.author') }}">
                                    <label for="setting_value">{{ trans('settings.custom.author') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat" onclick="">{{ trans('general.ok') }}</a>
                        </div>
                    </div>

                </tr>

                <tr class="setting setting-popup" id="setting_2">
                    <td>
                        <div class="row">
                            <div class="col s6">
                                <span>{{ trans('settings.custom.author_website') }}</span>
                                <small>custom.author_website</small>
                            </div>

                            <div class="col s6">

                            </div>
                        </div>
                    </td>
                </tr>

                <tr class="setting setting-popup" id="setting_3">
                    <td>
                        <div class="row">
                            <div class="col s6">
                                <span>{{ trans('settings.custom.site_name') }}</span>
                                <small>custom.site_name</small>
                            </div>

                            <div class="col s6">

                            </div>
                        </div>
                    </td>
                </tr>

            </table>
        </div>
    </div>


@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $('#modal_1').modal();
        });
    </script>

    <script>

        function openModal(id) {
            $('#' + id).open();
        }
    </script>
@endsection