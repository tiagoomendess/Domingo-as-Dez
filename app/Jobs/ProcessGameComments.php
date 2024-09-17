<?php

namespace App\Jobs;

use App\Article;
use App\GameComment;
use App\Media;
use Carbon\Carbon;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProcessGameComments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Starting ProcessGameComments Job");
        $startTime = new DateTime();

        $processed = $this->run();

        $endTime = new DateTime();
        $diff = $endTime->diff($startTime);
        $delta = $diff->format('%s seconds %F microseconds');
        Log::info("A total of $processed game comments were processed in $delta");
    }

    private function run(): int
    {
        $totalCount = 0;
        $gameComments = $this->getUnprocessedGameComments();
        $gamesByGroup = $this->getGamesByGameGroup($gameComments);

        Log::info("Found " . $gameComments->count() . " unprocessed game comments");

        foreach ($gamesByGroup as $games) {
            $gameGroupName = $games->first()->game_group->name;
            $competitionName = $games->first()->game_group->season->competition->name;
            $now = Carbon::now();
            $articleText = '';

            foreach ($games as $game) {
                $homeClubName = $game->home_team->club->name;
                $awayClubName = $game->away_team->club->name;
                $goals_home = empty($game->goals_home) ? '0' : $game->goals_home;
                $goals_away = empty($game->goals_away) ? '0' : $game->goals_away;
                $score = "$goals_home-$goals_away";
                if ($game->decidedByPenalties()) {
                    $score .= ' (' . $game->penalties_home . '-' . $game->penalties_away . ' g.p.)';
                }

                $intro = $this->buildSectionIntro($game, $homeClubName, $awayClubName);
                $gameText = "<h3><strong>$homeClubName $score $awayClubName</strong></h3><p>$intro</p>";

                // find game comment for home team
                $homeTeamComment = $gameComments->where('team_id', $game->home_team_id)->first();
                $gameText .= "<p><strong>Comentário de $homeClubName:</strong><br/>";
                if ($homeTeamComment && !empty($homeTeamComment->content)) {
                    $gameText .= "$homeTeamComment->content</p>";
                } else {
                    $gameText .= "$homeClubName não enviou um comentário para este jogo.</p>";
                }

                // find game comment for away team
                $awayTeamComment = $gameComments->where('team_id', $game->away_team_id)->first();
                $gameText .= "<p><strong>Comentário de $awayClubName:</strong><br/>";
                if ($awayTeamComment && !empty($awayTeamComment->content)) {
                    $gameText .= "$awayTeamComment->content</p>";
                } else {
                    $gameText .= "$awayClubName não enviou um comentário para este jogo.</p>";
                }

                $articleText .= $gameText;

                if ($homeTeamComment) {
                    $totalCount++;
                    $homeTeamComment->used = true;
                    $homeTeamComment->save();
                }

                if ($awayTeamComment) {
                    $totalCount++;
                    $awayTeamComment->used = true;
                    $awayTeamComment->save();
                }
            }

            $articleText .= "<p>Atenção! Este artigo foi gerado automaticamente e precisa de ser alterado antes de ser publicado!</p>";

            $mediaId = $this->selectRandomMediaId($games);

            Carbon::setLocale('pt_BR');
            $gameDate = Carbon::createFromFormat('Y-m-d H:i:s', $games->first()->date);
            $dayOfWeek = $gameDate->translatedFormat('l');

            Article::create([
                'title' => "$gameGroupName da $competitionName de $dayOfWeek",
                'media_id' => $mediaId,
                'description' => 'Esta descrição tem de ser alterada antes de publicar o artigo.',
                'text' => $articleText,
                'user_id' => 2,
                'date' => $now->format('Y-m-d'),
                'tags' => "$gameGroupName, $competitionName, crónica",
                'visible' => false,
            ]);

            Log::info("Created article for game group $gameGroupName of $competitionName");
        }

        return $totalCount;
    }

    private function getUnprocessedGameComments(): Collection
    {
        $now = Carbon::now();

        return GameComment::where('used', false)
            ->where('deadline', '<', $now->format('Y-m-d H:i:s'))
            ->get();
    }

    /**
     * Get all the games in a hash map seperated by game group
     *
     * @param Collection $gameComments
     * @return Collection
     */
    private function getGamesByGameGroup(Collection $gameComments): Collection
    {
        $games = collect();
        $gameIds = [];

        foreach ($gameComments as $gameComment) {
            $game = $gameComment->game;

            if (in_array($game->id, $gameIds)) {
                continue;
            }

            if ($games->has($game->game_group_id)) {
                $games->get($game->game_group_id)->push($game);
            } else {
                $games->put($game->game_group_id, collect([$game]));
            }

            $gameIds[] = $game->id;
        }

        return $games;
    }

    private function selectRandomMediaId(Collection $games)
    {
        $game = $games->first();

        $homeClubName = $game->home_team->club->name;

        $media = Media::where('tags', 'like', "%$homeClubName%")
            ->inRandomOrder()
            ->first();

        if (empty($media)) {
            return null;
        }

        return $media->id;
    }

    private function buildSectionIntro($game, $homeClubName, $awayClubName)
    {
        Carbon::setLocale('pt_BR');
        $gameDate = Carbon::createFromFormat('Y-m-d H:i:s', $game->date);
        $intro = $this->getRandomIntroTemplate();
        $finish = $this->getRandomFinish();
        $totalGoals = $game->goals_home + $game->goals_away;
        $wasDraw = $game->isDraw();
        $decidedByPenalties = $game->decidedByPenalties();

        if ($wasDraw) {
            $outcome = $decidedByPenalties
                ? 'foi preciso recorrer a grandes penalidades para desfazer o empate'
                : 'ambas as equipas não foram além de um empate';
        } else {
            $outcome = $game->winner()->id == $game->home_team_id
                ? 'a equipa da casa levou a melhor'
                : 'a equipa visitante levou a melhor';
        }

        // Replace keys
        $intro = str_replace('%home_club%', $homeClubName, $intro);
        $intro = str_replace('%away_club%', $awayClubName, $intro);
        $intro = str_replace('%venue%', $game->playground ? $game->playground->name : 'Campo Desconhecido', $intro);
        $intro = str_replace('%day%', $gameDate->format('d'), $intro);
        $intro = str_replace('%day_of_week%', $gameDate->translatedFormat('l'), $intro);
        $intro = str_replace('%month%', $gameDate->translatedFormat('F'), $intro);
        $intro = str_replace('%total_goals%', $totalGoals, $intro);
        $intro = str_replace('%outcome%', $outcome, $intro);

        return str_replace('%finish%', $finish, $intro);
    }

    private function getRandomIntroTemplate(): string
    {
        $templates = [
            "O %home_club% recebeu o %away_club% no %venue% no dia %day% de %month% numa partida em que foram marcados %total_goals% golos e que %outcome%. %finish%",
            "O %away_club% deslocou-se ao terreno do %home_club%, o %venue%, no dia %day% de %month% para disputar uma partida em que %outcome%. %finish%",
            "O %home_club% e o %away_club% defrontaram-se no %venue% no dia %day% de %month% numa partida em que foram marcados %total_goals% golos e que %outcome%. %finish%",
            "%day_of_week%, %day% de %month%, o %home_club% recebeu o %away_club% no %venue% numa partida em que foram marcados %total_goals% golos onde %outcome%. %finish%",
            "%venue% foi o palco do encontro entre %home_club% e %away_club% no dia %day% de %month% num jogo em que foram marcados %total_goals% golos e que %outcome%. %finish%",
        ];

        return $templates[array_rand($templates)];
    }

    private function getRandomFinish(): string
    {
        $templates = [
            "Vamos ver o que ambas as equipas têm a dizer sobre o encontro.",
            "Vamos agora ouvir as declarações dos treinadores.",
            "O que têm para dizer as duas equipas sobre o jogo?",
            "Qual será a leitura do jogo de ambas as equipas?",
            "Vamos ler as reações de ambas as equipas ao jogo.",
            "Será que ambos os clubes concordam na leitura da partida?",
            "O que têm a dizer os clubes sobre o jogo?",
            "Vamos ler as opiniões dos intervenientes.",
        ];

        return $templates[array_rand($templates)];
    }
}
