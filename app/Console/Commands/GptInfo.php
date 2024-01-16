<?php

namespace App\Console\Commands;

use App\Competition;
use App\Game;
use App\GameGroup;
use App\Season;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GptInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gpt:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates info for Chat Gpt';

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
        /*
        A competition has several seasons and each season can have one or more game_groups.
        A game group as several games
        */
        $competition = $this->getCompetition();
        if (empty($competition))
            return;

        $season = $this->getSeason($competition->id);
        if (empty($season))
            return;

        $game_group = $this->getGameGroup($season->id);
        if (empty($game_group))
            return;

        $games = $this->getGames($game_group->id);
        if (empty($games))
            return;

        $this->info("Total de " .count($games) . " jogos selecionados\n");

        $this->info("A Gerar Informações\n");
        $competition_promotes = $game_group->group_rules->promotes;
        $competition_relegates = $game_group->group_rules->relegates;
        $competition_type_en = $game_group->group_rules->type;
        switch ($competition_type_en) {
            case 'points':
                $competition_type = "Por Pontos";
                break;
            case 'elimination':
                $competition_type = "Por Eliminação ";
                break;
            default:
                $competition_type = "Amigável";
        }

        $table = $this->buildStandingsTable($game_group);

        $game_infos = [];
        foreach ($games as $key => $game) {
            $this->info("A Gerar informações para o jogo " . $game->home_team->club->name . " vs " . $game->away_team->club->name);

            $game_info = [
                'fixture' => $game->home_team->club->name . " vs " . $game->away_team->club->name,
                'competition' => $game->game_group->season->competition->name,
                'date' => $game->date,
                'venue' => $game->playground->name,
                'round' => $game->round,
                'home_club' => $game->home_team->club->name,
                'away_club' => $game->away_team->club->name,
                'home_team_form' => $this->getTeamForm($game->home_team_id, $game->date),
                'away_team_form' => $this->getTeamForm($game->away_team_id, $game->date),
                'last_games_between' => $this->getLastGamesBetween($game->home_team_id, $game->away_team_id, $game->date),
                'playing_surface_type' => $game->playground->surface,
            ];

            if (!empty($game->playground->location)) {
                $game_info['location'] = $game->playground->getLatitude() . "," . $game->playground->getLongitude();
            }

            $game_infos[$key] = $game_info;
        }

        $small_facts = [
            "Esta competição é $competition_type, promove $competition_promotes equipas e despromove $competition_relegates equipas",
        ];

        $custom_keys = [
            "Hoje é dia" => Carbon::now("Europe/Lisbon")->format("d/m/Y"),
        ];

        $this->printInfo($game_infos, $table, $custom_keys, $small_facts);

        return;
    }

    private function printInfo(array $game_infos, array $table, array $custom_keys = null, array $small_facts = null)
    {
        // Barcelos: 41.53100 -8.61997
        $oneOrMore = count($game_infos) > 1 ? "alguns jogos de futebol" : "um jogo de futebol";
        $this->info("=== Informações Geradas ======================\n");
        $this->info("Imagina que és um jornalista de um jornal online tens de escrever um artigo a fazer a antevisão de $oneOrMore. Para escrever esse artigo tens ao teu dispôr as seguintes informações:");

        foreach ($custom_keys as $key => $custom_info) {
            $this->line("$key: $custom_info");
        }

        if (!empty($small_facts)) {
            $str = implode("; ", $small_facts);
            $this->line("Curiosidades: " . $str);
        }

        $this->printStandingsTable($table);
        foreach ($game_infos as $key => $game_info) {
            $data = Carbon::parse($game_info['date'])->timezone("Europe/Lisbon")->format("d/m/Y \à\s H:i");
            $this->info("\nJogo entre " . $game_info['fixture'] . " da jornada " . $game_info['round'] . " da competição " . $game_info['competition'] . ":");
            $this->line("Data: $data");
            $this->line("Recinto de jogo: " . $game_info['venue']);
            $this->line("Equipa visitada: " . $game_info['home_club']);
            $this->line("Últimos 5 resultados de " . $game_info['home_club'] . ": " . $game_info['home_team_form']);
            $this->line("Equipa visitante: " . $game_info['away_club']);
            $this->line("Últimos 5 resultados de " . $game_info['away_club'] .": " . $game_info['away_team_form']);
            $this->line("Últimos jogos entre as duas equipas em todas as competições: " . $game_info['last_games_between']);
        }

        $this->info("Escreve um artigo interessante com factos relevantes que cativem o leitor. Obrigado!");
    }

    private function getTeamForm($team_id, $date): string {
        $home_team_last_games = Game::where('visible', true)
            ->where('finished', true)
            ->where('date', '<', $date)
            ->whereRaw("(home_team_id = ? or away_team_id = ?)", [$team_id, $team_id])
            ->orderByDesc('date')
            ->limit(5)
            ->get();

        $form = [];
        foreach ($home_team_last_games as $game) {
            if ($game->isDraw()) {
                $form[] = "Empate a " . $game->getHomeScore() . "-" . $game->getAwayScore();
            } else if ($game->winner()->id == $team_id) {
                $form[] = "Vitória por " . $game->getHomeScore() . "-" . $game->getAwayScore();
            } else {
                $form[] = "Derrota por " . $game->getHomeScore() . "-" . $game->getAwayScore();
            }
        }

        return implode($form, ", ");
    }

    private function getLastGamesBetween($home_team_id, $away_team_id, $date): string {
        $past_games = Game::where('visible', true)
            ->where('finished', true)
            ->where('date', '<', $date)
            ->whereRaw(
                "((home_team_id = ? and away_team_id = ?) or (home_team_id = ? and away_team_id = ?))",
                [ $home_team_id, $away_team_id, $away_team_id, $home_team_id]
            )
            ->orderByDesc('date')
            ->get();

        if (count($past_games) == 0)
            return "Nunca se defrontaram antes";

        $items = [];
        foreach ($past_games as $key => $past_game) {
            $homeScore = $past_game->getHomeScore();
            $awayScore = $past_game->getAwayScore();
            $homeName = $past_game->home_team->club->name;
            $awayName = $past_game->away_team->club->name;
            $year = Carbon::parse($past_game->date)->format("Y");
            $competition = $past_game->game_group->season->competition->name;

            $items[] = "$homeName $homeScore-$awayScore $awayName em $year para $competition";
        }

        return implode($items, ", ");
    }

    private function getGames(int $game_group_id)
    {
        $games = Game::where('visible', true)
            ->where('game_group_id', $game_group_id)
            ->orderBy('id', 'asc')
            ->get();

        // Group By Round Array
        $games_grouped_by_round = [];
        $highest_round = 0;
        $lowest_round = 1000;
        foreach ($games as $game) {
            $games_grouped_by_round[$game->round][] = $game;

            if ($game->round > $highest_round)
                $highest_round = $game->round;

            if ($game->round < $lowest_round)
                $lowest_round = $game->round;
        }

        $answer = (int) $this->ask("Escolhe uma jornada entre $lowest_round e $highest_round");
        if ($answer < $lowest_round || $answer > $highest_round)
            return null;

        $this->info("Jornada '$answer' escolhida\n");
        $this->info("=== Jogos da Jornada $answer ======================\n");
        $filtered_games = $games_grouped_by_round[$answer];
        foreach ($filtered_games as $key => $game) {
            $this->info($key + 1 . ") " . $game->home_team->club->name ." vs " . $game->away_team->club->name);
        }
        $this->info(count($filtered_games) + 1 . ") Todos os jogos desta jornada");

        $answer = (int) $this->ask('Qual Jogo?');
        if ($answer == 0)
            return null;

        if ($answer >= count($filtered_games) + 1) {
            $this->info("Todos os jogos da jornada $answer selecionados\n");
            return $filtered_games;
        } else {
            $game = $filtered_games[$answer - 1];
            $this->info("Jogo '" . $game->home_team->club->name . " vs " . $game->away_team->club->name . "' selecionado\n");
            return collect([$game]);
        }
    }

    private function getGameGroup(int $season_id)
    {
        $game_groups = GameGroup::where('season_id', $season_id)
            ->orderBy('id', 'desc')
            ->get();

        $this->info("=== Grupos de Jogos Disponíveis ======================\n");
        foreach ($game_groups as $key => $game_group) {
            $this->info($key + 1 . ") $game_group->name");
        }
        $answer = (int) $this->ask('Qual Grupo de Jogos?');
        if ($answer == 0)
            return null;

        $game_group = $game_groups->values()->get($answer - 1);
        $this->info("Grupo de Jogos '$game_group->name' selecionado\n");

        return $game_group;
    }

    private function getSeason(int $competition_id)
    {
        $seasons = Season::where('visible', true)
            ->where('competition_id', $competition_id)
            ->orderBy('start_year', 'desc')
            ->get();

        $this->info("=== Temporadas Disponíveis ======================\n");
        foreach ($seasons as $key => $season) {
            $this->info($key + 1 . ") $season->start_year/$season->end_year");
        }
        $answer = (int) $this->ask('Qual Temporada?');
        if ($answer == 0)
            return null;

        $season = $seasons->values()->get($answer - 1);
        $this->info("Temporada '$season->start_year/$season->end_year' selecionada\n");

        return $season;
    }

    private function getCompetition()
    {
        $competitions = Competition::where('visible', true)->get();
        $this->info("=== Competições Disponíveis ======================\n");

        foreach ($competitions as $key => $competition) {
            $this->info($key + 1 . ") $competition->name");
        }

        $answer = (int) $this->ask('Qual Competição?');
        if ($answer == 0)
            return null;

        $competition = $competitions->values()->get($answer - 1);
        $this->info("Competição '$competition->name' selecionada\n");

        return $competition;
    }

    private function printStandingsTable(array $table)
    {
        $positionHeader = $this->txtWidth("Posição", 7, "center");
        $teamHeader = $this->txtWidth("Equipa", 20);
        $gamesHeader = $this->txtWidth("Jogos", 7, "center");
        $winsHeader = $this->txtWidth("Vitórias", 8, "center");
        $drawsHeader = $this->txtWidth("Empates", 7, "center");
        $lossesHeader = $this->txtWidth("Derrotas", 8, "center");
        $goalsScoredHeader = $this->txtWidth("Marcados", 8, "center");
        $goalsConcededHeader = $this->txtWidth("Sofridos", 8, "center");
        $pointsHeader = $this->txtWidth("Pontos", 6, "center");
        $this->info("Tabela Classificativa atualmente:");
        $this->info("+---------------------------------------------------------------------------------------------------------+");
        $this->info("| $positionHeader | $teamHeader | $gamesHeader | $winsHeader | $drawsHeader | $lossesHeader | $goalsScoredHeader | $goalsConcededHeader | $pointsHeader |");
        $this->info("+---------+----------------------+---------+----------+---------+----------+----------+----------+--------+");
        $position = 1;
        foreach ($table as $equipa => $row) {

            $positionStr = $this->txtWidth($position, 7, "right");
            $teamStr = $this->txtWidth($equipa, 20, "left");
            $gamesStr = $this->txtWidth($row['jogos'], 7, "center");
            $winsStr = $this->txtWidth($row['vitorias'], 8, "center");
            $drawsStr = $this->txtWidth($row['empates'], 7, "center");
            $lossesStr = $this->txtWidth($row['derrotas'], 8, "center");
            $goalsScoredStr = $this->txtWidth($row['Golos Marcados'], 8, "center");
            $goalsConcededStr = $this->txtWidth($row['Golos Sofridos'], 8, "center");
            $pointsStr = $this->txtWidth($row['pontos'], 6, "right");

            $this->info("| $positionStr | $teamStr | $gamesStr | $winsStr | $drawsStr | $lossesStr | $goalsScoredStr | $goalsConcededStr | $pointsStr |");
            $position++;
        }
        $this->info("+---------+----------------------+---------+----------+---------+----------+----------+----------+--------+");
    }

    private function buildStandingsTable(GameGroup $game_group) : array
    {
        $all_games = $game_group->games;

        // initialize table
        $table = [];
        foreach ($all_games as $game) {
            if (!isset($table[$game->home_team->club->name]))
                $table[$game->home_team->club->name] = [
                    'jogos' => 0,
                    'vitorias' => 0,
                    'empates' => 0,
                    'derrotas' => 0,
                    'Golos Marcados' => 0,
                    'Golos Sofridos' => 0,
                    'pontos' => 0,
                ];

            //same for away team
            if (!isset($table[$game->away_team->club->name]))
                $table[$game->away_team->club->name] = [
                    'jogos' => 0,
                    'vitorias' => 0,
                    'empates' => 0,
                    'derrotas' => 0,
                    'Golos Marcados' => 0,
                    'Golos Sofridos' => 0,
                    'pontos' => 0,
                ];
        }

        foreach ($all_games as $game) {
            if (!$game->finished)
                continue;

            $table[$game->home_team->club->name]['jogos']++;
            $table[$game->away_team->club->name]['jogos']++;
            $table[$game->home_team->club->name]['Golos Marcados'] += $game->getHomeScore();
            $table[$game->away_team->club->name]['Golos Marcados'] += $game->getAwayScore();
            $table[$game->home_team->club->name]['Golos Sofridos'] += $game->getAwayScore();
            $table[$game->away_team->club->name]['Golos Sofridos'] += $game->getHomeScore();

            if ($game->isDraw()) {
                $table[$game->home_team->club->name]['empates']++;
                $table[$game->away_team->club->name]['empates']++;
                $table[$game->home_team->club->name]['pontos']++;
                $table[$game->away_team->club->name]['pontos']++;
            } else if ($game->winner()->id == $game->home_team_id) {
                $table[$game->home_team->club->name]['vitorias']++;
                $table[$game->away_team->club->name]['derrotas']++;
                $table[$game->home_team->club->name]['pontos'] += 3;
            } else {
                $table[$game->home_team->club->name]['derrotas']++;
                $table[$game->away_team->club->name]['vitorias']++;
                $table[$game->away_team->club->name]['pontos'] += 3;
            }
        }

        uasort($table, function ($a, $b) {
            /*
            Para estabelecimento da classificação geral final nas provas por pontos, observar-se-ão os seguintes critérios de desempate:
                a) Número de pontos alcançados pelos clubes nos jogos disputados entre si;
                b) Maior diferença entre o número de golos marcados e sofridos nos jogos disputados entre os clubes empatados;
                c) Maior diferença entre os golos marcados e sofridos, durante toda a competição;
                d) Maior número de vitórias na competição;
                e) Maior número de golos marcados na competição;
                f) Menor número de golos sofridos na competição;
             * */
            if ($a['pontos'] == $b['pontos']) {
                if ($a['Golos Marcados'] - $a['Golos Sofridos'] < $b['Golos Marcados'] - $b['Golos Sofridos'])
                    return 1;
                else if ($a['Golos Marcados'] - $a['Golos Sofridos'] > $b['Golos Marcados'] - $b['Golos Sofridos'])
                    return -1;

                if ($a['Golos Marcados'] < $b['Golos Marcados'])
                    return 1;
                else if ($a['Golos Marcados'] > $b['Golos Marcados'])
                    return -1;

                if ($a['Golos Sofridos'] > $b['Golos Sofridos'])
                    return 1;
                else if ($a['Golos Sofridos'] < $b['Golos Sofridos'])
                    return -1;

                if ($a['vitorias'] < $b['vitorias'])
                    return 1;
                else if ($a['vitorias'] > $b['vitorias'])
                    return -1;

                if ($a['Golos Marcados'] < $b['Golos Marcados'])
                    return 1;
                else if ($a['Golos Marcados'] > $b['Golos Marcados'])
                    return -1;

                if ($a['Golos Sofridos'] > $b['Golos Sofridos'])
                    return 1;
                else if ($a['Golos Sofridos'] < $b['Golos Sofridos'])
                    return -1;
            }

            return ($a['pontos'] < $b['pontos']) ? 1 : -1;
        });

        return $table;
    }

    private function txtWidth($txt, $width, $align = "left") {

        $txt = Str::ascii($txt, 'pt');
        switch ($align) {
            case "right":
                $pad_type = STR_PAD_LEFT;
                break;
            case "center":
                $pad_type = STR_PAD_BOTH;
                break;
            default:
                $pad_type = STR_PAD_RIGHT;
        }

        $txt = str_pad($txt, $width, " ", $pad_type);
        return $txt;
    }
}
