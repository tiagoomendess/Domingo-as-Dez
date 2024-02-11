<?php

namespace App\Console\Commands;

use App\Game;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GptGames extends BaseExportCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gpt:games';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates an export of the games';

    protected $fileType = null;

    protected $fileTypes = ['csv', 'json', 'txt'];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Welcome to the export command tool for games");
        $this->fileType = $this->getWantedFiletype($this->fileTypes);
        $start_date = $this->getRangeDateFromInput('start');
        $end_date = $this->getRangeDateFromInput('end');
        $now = Carbon::now();
        $filename = 'games_' . $now->format('Y-m-d_H-i-s') . '.' . $this->fileType;

        // Will be JSON Lines file
        if ($this->fileType === "json")
            $filename = $filename ."l";

        $path = "exports/games/$this->fileType/$filename";

        // Create empty file
        Storage::disk('public')->put($path, $this->fileType === "csv" ? $this->getCsvHeader() : "");

        $query = Game::where('visible', true)->where('date', '>=', $start_date)->where('date', '<=', $end_date)->orderBy('date', 'asc');
        foreach ($query->cursor() as $game) {
            echo ".";
            Storage::disk('public')->append($path, $this->getLine($game));
        }

        $this->info("");
        $this->info("Finished generating games export file $filename");

        return;
    }

    private function getCsvHeader(): string {
        return "dia;hora;recinto de jogo;competição;época;equipa visitada;equipa visitante;resultado;";
    }

    private function getLine(Game $game): string {
        switch ($this->fileType) {
            case 'csv':
                return $this->getCsvLine($game);
            case 'json':
                return $this->getJsonLine($game);
                case 'txt':
                return $this->getTxtLine($game);
            default:
                return "invalid file type";
        }
    }

    private function getTxtLine(Game $game): string {
        $dateObj = Carbon::createFromFormat('Y-m-d H:i:s', $game->date);
        $dia = $dateObj->setTimezone("Europe/Lisbon")->format("d/m/Y");
        $hora = $dateObj->setTimezone("Europe/Lisbon")->format("H:i");
        $recinto = !empty($game->playground) ? $game->playground->name : "Recinto Desconhecido";
        $competicao = $game->game_group->name . " da " . $game->game_group->season->competition->name;
        $epoca = $game->game_group->season->getName();
        $equipa_visitada = $game->home_team->club->name;
        $equipa_visitante = $game->away_team->club->name;

        if ($game->started() && $game->finished) {
            $resultado = $game->getHomeScore() . "-" . $game->getAwayScore();

            return "$equipa_visitada jogou contra $equipa_visitante no dia $dia às $hora no $recinto para a competição $competicao, da época $epoca, e o resultado final foi de $resultado";
        }

        if ($game->postponed) {
            return "$equipa_visitada jogaria contra $equipa_visitante no dia $dia às $hora no $recinto para a competição $competicao, da época $epoca, mas o jogo foi adiado";
        }

        return "$equipa_visitada vai jogar contra $equipa_visitante no dia $dia às $hora no $recinto para a competição $competicao da época $epoca";
    }

    private function getCsvLine(Game $game): string {
        $dateObj = Carbon::createFromFormat('Y-m-d H:i:s', $game->date);
        $dia = $dateObj->setTimezone("Europe/Lisbon")->format("d/m/Y");
        $hora = $dateObj->setTimezone("Europe/Lisbon")->format("H:i");
        $recinto = !empty($game->playground) ? $game->playground->name : "Recinto Desconhecido";
        $competicao = $game->game_group->name . " " . $game->game_group->season->competition->name;
        $epoca = $game->game_group->season->getName();
        $equipa_visitada = $game->home_team->club->name;
        $equipa_visitante = $game->away_team->club->name;

        if ($game->started() && $game->finished)
            $result = $game->getHomeScore() . "-" . $game->getAwayScore();
        else
            $result = "Jogo por realizar";

        if ($game->postponed)
            $result = "Jogo adiado";

        return "$dia;$hora;$recinto;$competicao;$epoca;$equipa_visitada;$equipa_visitante;$result;";
    }

    private function getJsonLine(Game $game): string {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $game->date);
        $season_name = $game->game_group->season->getName();

        if ($game->started() && $game->finished)
            $result = $game->getHomeScore() . "-" . $game->getAwayScore();
        else
            $result = "Jogo por realizar";

        if ($game->postponed)
            $result = "Jogo adiado";

        $data = [
            'dia' => $date->setTimezone("Europe/Lisbon")->format("d/m/Y"),
            'hora' => $date->setTimezone("Europe/Lisbon")->format("H:i"),
            'recinto_de_jogo' => !empty($game->playground) ? $game->playground->name : "Recinto Desconhecido",
            'competição' => $game->game_group->name . " " . $game->game_group->season->competition->name,
            'época' => $season_name,
            'equipa_visitada' => $game->home_team->club->name,
            'equipa_visitante' => $game->away_team->club->name,
            'resultado' => $result,
        ];

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
