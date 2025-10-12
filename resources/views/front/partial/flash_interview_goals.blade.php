<div class="col s12">
    <p class="flow-text text-bold" style="margin-bottom: 10px">
        {{ $recipientClubName }}
        marcou {{ $amountOfGoals }} {{ $amountOfGoals > 1 ?
        'golos, quem foram os marcadores' : 'golo, quem foi o marcador' }}?
    </p>
</div>

<div class="col s12">
    @for($i = 0; $i < $amountOfGoals; $i++)
        <div class="row no-margin-bottom">
            <div class="col s1">
                <p class="input-field right" style="margin-top: 20px">
                    {{ $i + 1 }}
                </p>
            </div>

            <div class="col s8">
                <div class="input-field text-left">
                    <select @if(!$canEdit) disabled @endif name="players[]">
                        <option value="0" disabled selected>Escolha uma opção</option>
                        <option value="-1" @if(!empty($goals) && isset($goals[$i]) && $goals[$i]->own_goal) selected @endif >Auto Golo do Adversário</option>
                        @foreach($players as $player)
                            <option
                                    data-icon="{{ $player->getPicture() }}"
                                    class="text-left circle"
                                    value="{{ $player->id }}"
                                    @if($goals && !empty($goals[$i]) && $goals[$i]->player_id == $player->id) selected @endif
                            >{{ $player->displayName() }}</option>
                        @endforeach
                        <option value="0" @if(!empty($goals) && isset($goals[$i]) && $goals[$i]->player_id == null && !$goals[$i]->own_goal) selected @endif>Jogador em Falta</option>
                    </select>
                    <label>Escolha um Jogador</label>
                </div>
            </div>

            <div class="col s3">
                <div class="input-field">
                    <input name="minutes[]" @if(!$canEdit) disabled @endif id="minute-{{ $i }}" type="number"
                           class="validate"
                           value="{{ !empty($goals) && !empty($goals[$i]) ? $goals[$i]->minute : '' }}">
                    <label for="minute-{{ $i }}">Minuto</label>
                </div>
            </div>
        </div>
    @endfor
</div>

<script>
    setTimeout(function () {
        $(document).ready(function () {
            $('select').material_select();
        });
    }, 100);
</script>
