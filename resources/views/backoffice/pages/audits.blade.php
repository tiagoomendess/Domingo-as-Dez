@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('models.audit') }}</title>
@endsection

@section('content')
    <div class="row no-margin-bottom">
        <div class="col s8">
            <h1>{{ trans('models.audit') }}</h1>
        </div>
        <div class="col s4">
            @include('backoffice.partial.search_box_btn')
        </div>
    </div>

    <div class="row no-margin-bottom">
        @include('backoffice.partial.search_box')
    </div>

    <div class="row">
        <div class="col s12">
            @if(!$audits || $audits->count() == 0)
                <p class="flow-text">{{ trans('models.no_audits') }}</p>
            @else
                <table class="bordered">
                    <thead>
                    <tr>
                        <th>{{ trans('general.id') }}</th>
                        <th>Utilizador</th>
                        <th>Ação</th>
                        <th>Modelo</th>
                        <th>Diferenças</th>
                        <th>Mais</th>
                        <th class="hide-on-med-and-down">{{ trans('general.date') }}</th>
                    </tr>
                    </thead>
                    @foreach($audits as $audit)
                        <tr>
                            <td>{{ $audit->id }}</td>
                            <td>
                                @if($audit->user_id)
                                    <a href="{{ route('users.show', $audit->user_id) }}">{{ $audit->user_id }}</a>
                                @else()
                                    -
                                @endif()
                            </td>
                            <td>{{ $audit->action }}</td>
                            <td>
                                @if($audit->model_id)
                                    @php
                                        try{
                                            $route = route(strtolower($audit->model) . 's.show', $audit->model_id);
                                        } catch (Exception $e) {
                                            $route = null;
                                        }
                                    @endphp

                                    @if($route)
                                        <a href="{{ $route }}">{{ $audit->model }} {{ $audit->model_id }}</a>
                                    @else
                                        {{ $audit->model }}
                                    @endif


                                @else()
                                    {{ $audit->model }}
                                @endif()
                            </td>
                            <td>@if(!empty($audit->old_values) || !empty($audit->new_values))
                                    <a class="modal-trigger waves-effect waves-ripple btn-flat" href="#dif_modal_{{ $audit->id }}"><i class="large material-icons">open_in_new</i></a>
                                    <div id="dif_modal_{{ $audit->id }}" class="modal" style="width: 95%; max-height: 95%">
                                        <div class="modal-content">
                                            <h4 class="text-center center">Diferenças</h4>
                                            <div class="divider"></div>
                                            <div class="row">
                                                <div class="col s6">
                                                    <h5 class="center red-text">Antes</h5>
                                                </div>
                                                <div class="col s6">
                                                    <h5 class="center green-text">Depois</h5>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col s6" style="border-right: #d0d0d0 solid 1px">
                                                    <pre class="json">{{ $audit->old_values }}</pre>
                                                </div>
                                                <div class="col s6" style="border-left: #d0d0d0 solid 1px">
                                                    <pre class="json">{{ $audit->new_values }}</pre>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="divider"></div>
                                            <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Fechar</a>
                                        </div>
                                    </div>
                                @else() - @endif()</td>
                            <td>
                                <a class="modal-trigger waves-effect waves-ripple btn-flat" href="#more_modal_{{ $audit->id }}"> <i class="large material-icons">open_in_new</i></a>
                                <div id="more_modal_{{ $audit->id }}" class="modal">
                                    <div class="modal-content">
                                        <h4 class="text-center center">Mais Informações</h4>
                                        <div class="row">
                                            <div class="col s12">
                                                <ul>
                                                    <li><b>IP:</b> {{ $audit->ip_address ?? 'N/A' }}</li>
                                                    <li><b>País:</b> {{ $audit->ip_country ?? 'Desconhecido' }}</li>
                                                    <li><b>User Agent:</b> {{ $audit->user_agent }}</li>
                                                    <li><b>Timezone:</b> {{ $audit->timezone ?? 'N/A' }}</li>
                                                    <li><b>Idioma:</b> {{ $audit->language ?? 'N/A' }}</li>
                                                    <li><b>Info Extra:</b> {{ $audit->extra_info }}</li>
                                                    <li><b>Data: </b> {{ $audit->created_at }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Fechar</a>
                                    </div>
                                </div>
                            </td>
                            <td class="hide-on-med-and-down">{{ $audit->created_at }}</td>
                        </tr>
                    @endforeach
                </table>

                <div class="row">
                    <div class="col s12">
                        {{ $audits->links() }}
                    </div>
                </div>

            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.modal').modal({
                dismissible: true,
                opacity: .5,
                inDuration: 150,
                outDuration: 75,
                startingTop: '4%',
                endingTop: '10%',
            });

            $('.json').each(function (i, element) {
                try {
                    let json = JSON.parse(element.innerHTML);
                    element.innerHTML = JSON.stringify(json, null, 2);
                } catch (e) {
                    console.warn("Error parsing JSON: ", e);
                }
            });
        })

    </script>
@endsection