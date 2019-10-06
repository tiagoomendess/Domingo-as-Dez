@if(!empty($searchFields))
    <div id="search_form" class="hide" style="border: solid 1px black; background-color: white; padding-top: 20px; margin: 0 10px">
        <form action="{{ route(\Request::route()->getName()) }}" method="GET">
            <input type="hidden" name="search" value="true">
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
                        <button class="btn waves-effect waves-light green" type="submit" name="action">Procurar
                            <i class="material-icons right">send</i>
                        </button>
                        <a href="{{ route(\Request::route()->getName()) }}"
                           class="waves-effect waves-light btn red darken-3"><i class="material-icons right">clear</i>Limpar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endif