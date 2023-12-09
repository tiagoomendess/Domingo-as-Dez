<div>
    @foreach($answers as $index => $answer)
        <div style="width: 100%">
            <div class="row no-margin-bottom">
                <div class="col s9"><span class="flow-text">{{ $answer->answer }}</span></div>
                <div class="col s3">
                    <span class="right flow-text">
                        @if ($totalVotes > 0)
                            {{ round($votes[$index] * 100 / $totalVotes, 2) }}
                        @else
                            0
                        @endif
                        %
                    </span>
                </div>
            </div>
            <div class="progress" style="background-color: #d2f1ff">
                @if ($totalVotes > 0)
                    <div class="determinate" style="background-color: #107db7; width: {{ round($votes[$index] * 100 / $totalVotes, 2) }}%"></div>
                @else
                    <div class="determinate" style="background-color: #107db7; width: 0"></div>
                @endif
            </div>
            <div class="vertical-spacer"></div>
        </div>
    @endforeach

    <div class="row">
        <div class="col s6"><span class="grey-text" style="font-size: 10pt">Total de {{ $totalVotes }} votos</span></div>
        <div class="col s6">
            <span class="grey-text right" style="font-size: 10pt">
                @if($now->timestamp > $closeAfter->timestamp)
                    A votação já terminou
                @else
                    Termina às {{ $closeAfter->format("H:i") }} de {{ $closeAfter->format("d/m/Y") }}
                @endif
            </span>
        </div>
    </div>
</div>