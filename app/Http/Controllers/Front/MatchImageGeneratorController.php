<?php

namespace App\Http\Controllers\Front;

use App\Game;
use App\Http\Controllers\Controller;
use App\Media;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Image;
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
        $this->middleware('auth');
    }

    public function generateImage(Game $game)
    {
        /** @var User $user */
        $user = Auth::user();
        Log::info('Generating match ' . $game->id . ' image for user ' . $user->name . ' with the email ' . $user->email);
        $base = $this->manager->canvas(self::WIDTH, self::HEIGHT);

        $storageFolder = 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;

        if ($game->playground) {
            $backgroundImg = $game->playground->picture
                ? storage_path($storageFolder . $game->playground->picture)
                : public_path(Media::getPlaceholder('1:1', $game->id));
        } else {
            $backgroundImg = public_path(Media::getPlaceholder('1:1', $game->id));
        }

        $gameDate = Carbon::createFromFormat('Y-m-d H:i:s', $game->date);

        $data = [
            'home_club_emblem' => $game->home_team->club->emblem ? storage_path($storageFolder . $game->home_team->club->emblem) : public_path(config('custom.default_emblem')),
            'home_club_name' => mb_strtoupper($game->home_team->club->name),
            'away_club_emblem' => $game->away_team->club->emblem ? storage_path($storageFolder . $game->away_team->club->emblem) : public_path(config('custom.default_emblem')),
            'away_club_name' => mb_strtoupper($game->away_team->club->name),
            'playground_name' => $game->playground ? mb_strtoupper($game->playground->name) : '',
            'day' => $gameDate->timezone('Europe/Lisbon')->format('d'),
            'month' => mb_strtoupper($this->translateMonth($gameDate->month)),
            'time' => $gameDate->timezone('Europe/Lisbon')->format('H\Hi'),
            'competition' => mb_strtoupper($game->game_group->season->competition->name),
            'competition_logo' => storage_path($storageFolder . $game->game_group->season->competition->picture)
        ];

        $base->insert($backgroundImg, 'center');
        $base->blur(round(self::WIDTH / 100));
        $base->insert(public_path('/images/watermark.png'));

        $this->setCompetitionName($base, $data);
        $this->setCompetitionLogo($base, $data);
        $this->setClubEmblems($base, $data);
        $this->setClubNames($base, $data);

        if ($game->finished) {
            $this->setFinalScore($base, $game);
        } else {
            $this->setMatchInfo($base, $data);
        }

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

    private function setCompetitionLogo(Image &$base, $data)
    {
        $competitionLogo = $this->manager->make($data['competition_logo']);
        $competitionLogo->resize(self::WIDTH / 10, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $base->insert($competitionLogo, 'top-left', round((self::WIDTH / 2) - ($competitionLogo->width() / 2)), round((self::HEIGHT / 9) - ($competitionLogo->height() / 2)));
    }

    private function setClubEmblems(Image &$base, $data)
    {
        $homeEmblem = $this->manager->make($data['home_club_emblem']);
        $homeEmblem->resize(round(self::WIDTH / 2.5), null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $awayEmblem = $this->manager->make($data['away_club_emblem']);
        $awayEmblem->resize(round(self::WIDTH / 2.5), null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $base->insert($homeEmblem, 'top-left', round(self::WIDTH / 4) + 30 - round($homeEmblem->width() / 2), round(self::HEIGHT / 2) - round($homeEmblem->height() / 2));
        $base->insert($awayEmblem, 'top-left', round((self::WIDTH / 4) * 3) - 30 - round($awayEmblem->width() / 2), round(self::HEIGHT / 2) - round($awayEmblem->height() / 2));
    }

    private function setClubNames(Image &$base, $data)
    {
        //Shadow for club names
        $base->text($data['home_club_name'] . ' vs ' . $data['away_club_name'], round(self::WIDTH / 2) +1, round((self::HEIGHT / 4)) + 11, function($font) {
            $font->file(public_path('Roboto-Regular.ttf'));
            $font->size(30);
            $font->color('#282828');
            $font->align('center');
            $font->valign('center');
        });
        //Text for club names
        $base->text($data['home_club_name'] . ' vs ' . $data['away_club_name'], round(self::WIDTH / 2), round((self::HEIGHT / 4)) + 10, function($font) {
            $font->file(public_path('Roboto-Regular.ttf'));
            $font->size(30);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });
    }

    private function setFinalScore(Image &$base, Game $game)
    {
        // Final score shadow
        $base->text("RESULTADO FINAL", round(self::WIDTH / 2) + 1, round((self::HEIGHT / 4) * 3) - 29, function ($font) {
            $font->file(public_path('Roboto-Regular.ttf'));
            $font->size(28);
            $font->color('#282828');
            $font->align('center');
            $font->valign('top');
        });
        $base->text("RESULTADO FINAL", round(self::WIDTH / 2), round((self::HEIGHT / 4) * 3) - 30, function ($font) {
            $font->file(public_path('Roboto-Regular.ttf'));
            $font->size(28);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('top');
        });

        // Score Text
        $base->text($game->getHomeScore() . ' - ' . $game->getAwayScore(), round(self::WIDTH / 2) + 2, round((self::HEIGHT / 4) * 3) + 12, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(120);
            $font->color('#484848');
            $font->align('center');
            $font->valign('top');
        });
        $base->text($game->getHomeScore() . ' - ' . $game->getAwayScore(), round(self::WIDTH / 2), round((self::HEIGHT / 4) * 3) + 10, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(120);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('top');
        });

        if ($game->decidedByPenalties()) {
            $base->text('(' . $game->penalties_home . '-' . $game->penalties_away . ' g.p)', round(self::WIDTH / 2) + 1, round((self::HEIGHT / 4) * 3) + 121, function ($font) {
                $font->file(public_path('Roboto-Black.ttf'));
                $font->size(30);
                $font->color('#282828');
                $font->align('center');
                $font->valign('top');
            });
            $base->text('(' . $game->penalties_home . '-' . $game->penalties_away . ' g.p)', round(self::WIDTH / 2), round((self::HEIGHT / 4) * 3) + 120, function ($font) {
                $font->file(public_path('Roboto-Black.ttf'));
                $font->size(30);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });
        }
    }

    private function setMatchInfo(Image $base, $data)
    {
        $base->text($data['day'] . ' DE ' . $data['month'] . ' | ' . $data['time'], round(self::WIDTH / 2) + 1, round((self::HEIGHT / 4) * 3) + 1, function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(40);
            $font->color('#484848');
            $font->align('center');
            $font->valign('center');
        });
        $base->text($data['day'] . ' DE ' . $data['month'] . ' | ' . $data['time'], round(self::WIDTH / 2), round((self::HEIGHT / 4) * 3), function ($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(40);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });

        $base->text($data['playground_name'], round(self::WIDTH / 2) + 1, round((self::HEIGHT / 4) * 3) + 46, function ($font) {
            $font->file(public_path('Roboto-Regular.ttf'));
            $font->size(30);
            $font->color('#484848');
            $font->align('center');
            $font->valign('center');
        });
        $base->text($data['playground_name'], round(self::WIDTH / 2), round((self::HEIGHT / 4) * 3) + 45, function ($font) {
            $font->file(public_path('Roboto-Regular.ttf'));
            $font->size(30);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });
    }

    private function setCompetitionName(Image $base, $data)
    {
        // Competition Shadow
        $base->text($data['competition'], round(self::WIDTH / 2) + 2, round(self::HEIGHT / 5) + 2, function($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(55);
            $font->color('#484848');
            $font->align('center');
            $font->valign('center');
        });
        //Competition Text
        $base->text($data['competition'], round(self::WIDTH / 2), round(self::HEIGHT / 5), function($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(55);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });
    }
}
