@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.add') }} Regra de Grupo</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.add') }} Regra de Grupo</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('group_rules.store') }}" method="POST">

        {{ csrf_field() }}

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input required name="name" id="name" type="text" class="validate" value="{{ old('name') }}">
                <label for="name">{{ trans('models.name') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m6 l4">
                <label>Tipo</label>
                <select name="type" id="type-select" class="browser-default" required onchange="togglePositionsSection()">
                    <option value="" disabled {{ old('type') == '' ? 'selected' : '' }}>Selecionar tipo...</option>
                    <option value="points" {{ old('type') == 'points' ? 'selected' : '' }}>Pontos</option>
                    <option value="elimination" {{ old('type') == 'elimination' ? 'selected' : '' }}>Eliminatórias</option>
                    <option value="friendly" {{ old('type') == 'friendly' ? 'selected' : '' }}>Amigável</option>
                    <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Outro</option>
                </select>
            </div>
        </div>

        <div id="points-fields" style="display: none;">
            <div class="row">
                <div class="input-field col s12">
                    <textarea name="tie_breaker_script" id="tie_breaker_script" class="materialize-textarea">{{ old('tie_breaker_script') }}</textarea>
                    <label for="tie_breaker_script">Script de Desempate (JavaScript)</label>
                </div>
            </div>
        </div>

        <div id="positions-section" style="display: none;">
            <div class="row">
                <div class="col s12">
                    <h5>Posições Personalizadas</h5>
                    <p>Configure cores e legendas para posições específicas na tabela.</p>
                </div>
            </div>

            <div id="positions-container">
                <!-- Dynamic positions will be added here -->
            </div>

            <div class="row">
                <div class="col s12">
                    <a href="javascript:void(0)" onclick="addPositionRow()" class="waves-effect waves-light btn blue">
                        <i class="material-icons left">add</i>Adicionar Posição
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="input-field col s12">
                @include('backoffice.partial.button', ['color' => 'green', 'icon' => 'send', 'text' => trans('general.create')])
                <a href="{{ route('group_rules.index') }}" class="btn waves-effect waves-light grey" style="margin-left: 10px;">
                    <i class="material-icons right">cancel</i>Cancelar
                </a>
            </div>
        </div>

    </form>

@endsection

@section('scripts')
<script>
let positionIndex = 0;

function togglePositionsSection() {
    const typeSelect = document.getElementById('type-select');
    const positionsSection = document.getElementById('positions-section');
    const pointsFields = document.getElementById('points-fields');
    
    if (typeSelect.value === 'points') {
        positionsSection.style.display = 'block';
        pointsFields.style.display = 'block';
    } else {
        positionsSection.style.display = 'none';
        pointsFields.style.display = 'none';
    }
}

function addPositionRow() {
    const container = document.getElementById('positions-container');
    const rowHtml = `
        <div class="row position-row" data-index="${positionIndex}">
            <div class="input-field col s12 m3 l3">
                <input name="positions[${positionIndex}][positions]" type="text" class="validate" placeholder="1,2,3 ou 1">
                <label>Posições</label>
            </div>
            <div class="input-field col s12 m3 l2">
                <input name="positions[${positionIndex}][color]" type="color" class="validate">
                <label>Cor</label>
            </div>
            <div class="input-field col s12 m4 l5">
                <input name="positions[${positionIndex}][label]" type="text" class="validate" placeholder="Promoção">
                <label>Legenda</label>
            </div>
            <div class="col s12 m2 l2" style="display: flex; align-items: center; justify-content: center; min-height: 48px;">
                <a href="javascript:void(0)" onclick="removePositionRow(${positionIndex})" class="btn-floating btn-small red waves-effect waves-light">
                    <i class="material-icons">delete</i>
                </a>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', rowHtml);
    positionIndex++;
}

function removePositionRow(index) {
    const row = document.querySelector(`[data-index="${index}"]`);
    if (row) {
        row.remove();
    }
}

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    // Check initial state and show positions section if needed
    togglePositionsSection();
    
    // Add one position row by default if type is points
    const typeSelect = document.getElementById('type-select');
    if (typeSelect.value === 'points') {
        addPositionRow();
    }
});
</script>
@endsection
