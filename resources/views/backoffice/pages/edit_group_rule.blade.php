@extends('backoffice.layouts.default-page')

@section('head-content')
    <title>{{ trans('general.edit') }} Regra de Grupo</title>
@endsection

@section('content')

    <div class="row">
        <div class="col s12">
            <h1>{{ trans('general.edit') }} Regra de Grupo</h1>
        </div>
    </div>

    @if(count($errors) > 0)
        <div class="row">
            <div class="col s12">
                @include('backoffice.partial.form_errors')
            </div>
        </div>
    @endif

    <form action="{{ route('group_rules.update', ['group_rule' => $group_rule]) }}" method="POST">

        {{ csrf_field() }}

        {{ method_field('PUT') }}

        <div class="row">
            <div class="input-field col s12 m8 l6">
                <input required name="name" id="name" type="text" class="validate" value="{{ old('name', $group_rule->name) }}">
                <label for="name">{{ trans('models.name') }}</label>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m6 l4">
                <label>Tipo</label>
                <select name="type" id="type-select" class="browser-default" required onchange="togglePositionsSection()">
                    <option value="" disabled {{ old('type', $group_rule->type) == '' ? 'selected' : '' }}>Selecionar tipo...</option>
                    <option value="points" {{ old('type', $group_rule->type) == 'points' ? 'selected' : '' }}>Pontos</option>
                    <option value="elimination" {{ old('type', $group_rule->type) == 'elimination' ? 'selected' : '' }}>Eliminatórias</option>
                    <option value="friendly" {{ old('type', $group_rule->type) == 'friendly' ? 'selected' : '' }}>Amigável</option>
                    <option value="other" {{ old('type', $group_rule->type) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
        </div>

        <div id="points-fields">
            @if($group_rule->promotes > 0 || $group_rule->relegates > 0)
                <div class="row">
                    <div class="input-field col s6 m3 l2">
                        <input name="promotes" id="promotes" type="number" class="validate" value="{{ old('promotes', $group_rule->promotes) }}">
                        <label for="promotes">Promoções (Legado)</label>
                    </div>
                    <div class="input-field col s6 m3 l2">
                        <input name="relegates" id="relegates" type="number" class="validate" value="{{ old('relegates', $group_rule->relegates) }}">
                        <label for="relegates">Despromoções (Legado)</label>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="input-field col s12">
                    <textarea name="tie_breaker_script" id="tie_breaker_script" class="materialize-textarea">{{ old('tie_breaker_script', $group_rule->tie_breaker_script) }}</textarea>
                    <label for="tie_breaker_script">Script de Desempate (JavaScript)</label>
                </div>
            </div>
        </div>

        <div id="positions-section">
            <div class="row">
                <div class="col s12">
                    <h5>Posições Personalizadas</h5>
                    <p>Configure cores e legendas para posições específicas na tabela.</p>
                </div>
            </div>

            <div id="positions-container">
                @if($group_rule->positions && $group_rule->positions->count() > 0)
                    @foreach($group_rule->positions as $index => $position)
                        <div class="row position-row" data-index="{{ $index }}">
                            <div class="input-field col s12 m3 l3">
                                <input name="positions[{{ $index }}][positions]" type="text" class="validate" value="{{ $position->positions }}">
                                <label>Posições</label>
                            </div>
                            <div class="input-field col s12 m3 l2">
                                <input name="positions[{{ $index }}][color]" type="color" class="validate" value="{{ $position->color }}">
                                <label>Cor</label>
                            </div>
                            <div class="input-field col s12 m4 l5">
                                <input name="positions[{{ $index }}][label]" type="text" class="validate" value="{{ $position->label }}">
                                <label>Legenda</label>
                            </div>
                            <div class="col s12 m2 l2" style="display: flex; align-items: center; justify-content: center; min-height: 48px;">
                                <a href="javascript:void(0)" onclick="removePositionRow({{ $index }})" class="btn-floating btn-small red waves-effect waves-light">
                                    <i class="material-icons">delete</i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
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
                @include('backoffice.partial.button', ['color' => 'green', 'icon' => 'save', 'text' => trans('general.save')])
                <a href="{{ route('group_rules.index') }}" class="btn waves-effect waves-light grey" style="margin-left: 10px;">
                    <i class="material-icons right">cancel</i>Cancelar
                </a>
            </div>
        </div>

    </form>

@endsection

@section('scripts')
<script>
let positionIndex = {{ $group_rule->positions ? $group_rule->positions->count() : 0 }};

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
    // Check initial state and show/hide positions section
    togglePositionsSection();
});
</script>
@endsection
