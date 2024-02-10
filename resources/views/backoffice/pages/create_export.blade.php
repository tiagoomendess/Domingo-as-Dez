@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>Expotar dados</title>
@endsection

@section('content')

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    @if(!empty($searchFields))
        <div class="row">
            <div class="col s12">
                <h1>Export {{ $model }}</h1>
            </div>
        </div>
        <div id="search_form" style="border: solid 1px black; background-color: white; padding-top: 20px; margin: 0 10px">
            <form action="{{ route('export.store') }}" method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="search" value="true">
                <input type="hidden" name="model" value="{{ $model }}">
                <div class="row">
                    <div class="col s12 m7">
                        @foreach($searchFields as $searchField)
                            @if($searchField['type'] === 'string')
                                <div class="input-field col s12">
                                    <input id="form_search_{{ $searchField['name'] }}" type="text"
                                           name="{{ $searchField['name'] }}" class="validate"
                                           value="{{ !empty($queryParams[$searchField['name']]) ? $queryParams[$searchField['name']] : '' }}">
                                    <label for="form_search_{{ $searchField['name'] }}">{{ $searchField['trans'] }}</label>
                                </div>
                            @elseif($searchField['type'] === 'integer')
                                <div class="input-field col s12">
                                    <input id="form_search_{{ $searchField['name'] }}" type="number"
                                           name="{{ $searchField['name'] }}" class="validate"
                                           value="{{ !empty($queryParams[$searchField['name']]) ? $queryParams[$searchField['name']] : '' }}">
                                    <label for="form_search_{{ $searchField['name'] }}">{{ $searchField['trans'] }}</label>
                                </div>
                            @elseif($searchField['type'] === 'enum')
                                <div class="col s12">
                                    <label>{{ $searchField['trans'] }}</label>
                                    <select name="{{ $searchField['name'] }}" class="browser-default">
                                        <option value="">Não Filtrar</option>
                                        @if(!empty($searchField['enumItems']))
                                            @foreach($searchField['enumItems'] as $item)
                                                @if(!empty($queryParams[$searchField['name']]) && $queryParams[$searchField['name']] === $item['value'])
                                                    <option selected value="{{ $item['value'] }}">{{ $item['name'] }}</option>
                                                @else
                                                    <option value="{{ $item['value'] }}">{{ $item['name'] }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="col s12 m5">
                        <div class="row">
                            <label>Ordenar por</label>
                            <select name="orderBy" class="browser-default">
                                @foreach($searchFields as $searchField)
                                    @if(!empty($queryParams['orderBy']) && $queryParams['orderBy'] === $searchField['name'])
                                        <option selected
                                                value="{{ $searchField['name'] }}">{{ $searchField['trans'] }}</option>
                                    @else
                                        <option value="{{ $searchField['name'] }}">{{ $searchField['trans'] }}</option>
                                    @endif
                                @endforeach
                            </select>

                            <label>Ordem</label>
                            <select name="order" class="browser-default">
                                <option {{ !empty($queryParams['order']) && $queryParams['order'] === 'descend' ? 'selected' : ''}} value="descend">
                                    Descendente
                                </option>
                                <option {{ !empty($queryParams['order']) && $queryParams['order'] === 'ascend' ? 'selected' : ''}} value="ascend">
                                    Ascendente
                                </option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="input-field col s6">
                                <input id="form_search_created_at_start" type="text"
                                       name="created_at_start" class="datepicker"
                                       value="{{ !empty($queryParams['created_at_start']) ? $queryParams['created_at_start'] : $default_start_date }}">
                                <label for="form_search_created_at_start">Inicio</label>
                            </div>

                            <div class="input-field col s6">
                                <input id="form_search_created_at_end" type="text"
                                       name="created_at_end" class="datepicker"
                                       value="{{ !empty($queryParams['created_at_end']) ? $queryParams['created_at_end'] : $default_end_date }}">
                                <label for="form_search_created_at_end">Fim</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col s12 m8 l6">
                                <div class="switch">
                                    <label>
                                        Mesmo Ficheiro
                                        <input name="same_file" type="hidden" value="false" checked>
                                        <input name="same_file" type="checkbox" value="true">
                                        <span class="lever"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <button class="btn waves-effect waves-light green" type="submit" name="action">Exportar
                                <i class="material-icons right">send</i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endif
@endsection

@section('scripts')
    @include('backoffice.partial.pick_a_date_js')
@endsection
