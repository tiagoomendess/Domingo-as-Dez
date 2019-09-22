<?php

namespace App\Http\Controllers\Front;

use App\Game;
use App\Http\Controllers\Controller;
use App\Media;
use Carbon\Carbon;
use Intervention\Image\ImageManager;

class MatchImageGeneratorController extends Controller
{
    /** @var ImageManager */
    protected $manager;

    const WIDTH = 900;
    const HEIGHT = 900;

    public function __construct(ImageManager $manager)
    {
        $this->manager = $manager;
    }

    public function generateImage(Game $game)
    {
        $base = $this->manager->canvas(self::WIDTH, self::HEIGHT);

        if ($game->playground) {
            $backgroundImg = public_path(str_replace('16_9_placeholder_', '1_1_placeholder_', $game->playground->getPicture()));
        } else {
            $backgroundImg = public_path(Media::getPlaceholder('1:1', $game->id));
        }

        $gameDate = Carbon::createFromFormat('Y-m-d H:i:s', $game->date, 'Europe/Lisbon');

        $data = [
            'home_club_emblem' => public_path($game->home_team->club->getEmblem()),
            'home_club_name' => $game->home_team->club->name,
            'away_club_emblem' => public_path($game->away_team->club->getEmblem()),
            'away_club_name' => $game->away_team->club->name,
            'playground_name' => $game->playground ? $game->playground->name : '',
            'day' => $gameDate->format('d'),
            'month' => $this->translateMonth($gameDate->month),
            'time' => $gameDate->format('H\Hi'),
            'competition' => $game->game_group->season->competition->name,
            'competition_logo' => public_path($game->game_group->season->competition->picture)
        ];

        $base->insert($backgroundImg, 'center');
        $base->blur(round(self::WIDTH / 100));
        $base->brightness(-5);

        $base->text($data['competition'], round(self::WIDTH / 2), round(self::HEIGHT / 5), function($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(60);
            $font->color('#fdf6e3');
            $font->align('center');
            $font->valign('center');
        });

        $competitionLogo = $this->manager->make($data['competition_logo']);
        $competitionLogo->resize(self::WIDTH / 10, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $base->insert($competitionLogo, 'top-left', round((self::WIDTH / 2) - ($competitionLogo->width() / 2)), round((self::HEIGHT / 9) - ($competitionLogo->height() / 2)));

        $homeEmblem = $this->manager->make($data['home_club_emblem']);
        $homeEmblem->resize(round(self::WIDTH / 2.5), null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $awayEmblem = $this->manager->make($data['away_club_emblem']);
        $awayEmblem->resize(round(self::WIDTH / 2.5), null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $base->insert($homeEmblem, 'top-left', round(self::WIDTH / 4) - round($homeEmblem->width() / 2), round(self::HEIGHT / 2) - round($homeEmblem->height() / 2));
        $base->insert($awayEmblem, 'top-left', round((self::WIDTH / 4) * 3) - round($awayEmblem->width() / 2), round(self::HEIGHT / 2) - round($awayEmblem->height() / 2));

        $base->text($data['home_club_name'] . ' vs ' . $data['away_club_name'], round(self::WIDTH / 2), round((self::HEIGHT / 4)) + 10, function($font) {
            $font->file(public_path('Roboto-Regular.ttf'));
            $font->size(30);
            $font->color('#fdf6e3');
            $font->align('center');
            $font->valign('center');
        });

        if ($game->finished) {
            $base->text("Resultado Final", round(self::WIDTH / 2), round((self::HEIGHT / 4) * 3) - 40, function ($font) {
                $font->file(public_path('Roboto-Regular.ttf'));
                $font->size(35);
                $font->color('#fdf6e3');
                $font->align('center');
                $font->valign('top');
            });

            $base->text($game->getHomeScore() . ' - ' . $game->getAwayScore(), round(self::WIDTH / 2), round((self::HEIGHT / 4) * 3) + 10, function ($font) {
                $font->file(public_path('Roboto-Black.ttf'));
                $font->size(120);
                $font->color('#fdf6e3');
                $font->align('center');
                $font->valign('top');
            });

            if ($game->decidedByPenalties()) {
                $base->text('(' . $game->penalties_home . '-' . $game->penalties_away . ' g.p)', round(self::WIDTH / 2), round((self::HEIGHT / 4) * 3) + 120, function ($font) {
                    $font->file(public_path('Roboto-Black.ttf'));
                    $font->size(30);
                    $font->color('#fdf6e3');
                    $font->align('center');
                    $font->valign('top');
                });
            }
        } else {
            $base->text($data['day'] . ' de ' . $data['month'] . ' | ' . $data['time'], round(self::WIDTH / 2), round((self::HEIGHT / 4) * 3), function ($font) {
                $font->file(public_path('Roboto-Black.ttf'));
                $font->size(40);
                $font->color('#fdf6e3');
                $font->align('center');
                $font->valign('center');
            });

            $base->text($data['playground_name'], round(self::WIDTH / 2), round((self::HEIGHT / 4) * 3) + 45, function ($font) {
                $font->file(public_path('Roboto-Regular.ttf'));
                $font->size(30);
                $font->color('#fdf6e3');
                $font->align('center');
                $font->valign('center');
            });
        }

        $base->insert(public_path('/images/match_water_mark.png'));

        $name = str_slug($game->home_team->club->name . '-vs-' . $game->away_team->club->name . '-' . $game->game_group->season->competition->name) . '.jpg';
        $base = $base->encode('jpg');
        $headers = [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'attachment; filename=' . $name,
        ];
        return response()->stream(function () use ($base) {
            echo $base;
        }, 200, $headers);
    }

    private function translateMonth($month) {
        $months = [
            '1' => 'Janeiro',
            '2' => 'Fevereiro',
            '3' => 'Março',
            '4' => 'Abril',
            '5' => 'Maio',
            '6' => 'Junho',
            '7' => 'Julho',
            '8' => 'Agosto',
            '9' => 'Setembro',
            '10' => 'Outubro',
            '11' => 'Novembro',
            '12' => 'Dezembro',
        ];

        return $months[$month];
    }
}


