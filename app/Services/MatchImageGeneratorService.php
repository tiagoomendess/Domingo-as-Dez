<?php

namespace App\Services;

use App\Game;
use Carbon\Carbon;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class MatchImageGeneratorService
{
    /** @var ImageManager */
    protected $manager;

    const SQUARE_WIDTH = 900;
    const SQUARE_HEIGHT = 900;
    const STORY_WIDTH = 1080;
    const STORY_HEIGHT = 1920;

    public function __construct(ImageManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Generate a square image for a game
     * 
     * @param Game $game
     * @return \Intervention\Image\Image Encoded JPEG image
     */
    public function generateSquareImage(Game $game)
    {
        $base = $this->manager->canvas(self::SQUARE_WIDTH, self::SQUARE_HEIGHT);

        if ($game->playground) {
            $backgroundImg = public_path(str_replace('16_9_placeholder_', '1_1_placeholder_', $game->playground->getPicture()));
        } else {
            $backgroundImg = public_path(\App\Media::getPlaceholder('1:1', $game->id));
        }

        $gameDate = Carbon::createFromFormat('Y-m-d H:i:s', $game->date);

        $data = [
            'home_club_emblem' => public_path($game->home_team->club->getEmblem()),
            'home_club_name' => mb_strtoupper($game->home_team->club->name),
            'away_club_emblem' => public_path($game->away_team->club->getEmblem()),
            'away_club_name' => mb_strtoupper($game->away_team->club->name),
            'playground_name' => $game->playground ? mb_strtoupper($game->playground->name) : '',
            'day' => $gameDate->timezone('Europe/Lisbon')->format('d'),
            'month' => mb_strtoupper($this->translateMonth($gameDate->month)),
            'time' => $gameDate->timezone('Europe/Lisbon')->format('H\Hi'),
            'competition' => mb_strtoupper($game->game_group->season->competition->name),
            'competition_logo' => public_path($game->game_group->season->competition->picture)
        ];

        $base->insert($backgroundImg, 'center');
        $base->blur(round(self::SQUARE_WIDTH / 100));
        $base->insert(public_path('/images/watermark.png'));

        $this->setCompetitionName($base, $data, 'square');
        $this->setCompetitionLogo($base, $data, 'square');
        $this->setClubEmblems($base, $data, 'square');
        $this->setClubNames($base, $data, 'square');

        if ($game->finished) {
            $this->setFinalScore($base, $game, 'square');
        } else {
            $this->setMatchInfo($base, $data, 'square');
        }

        return $base->encode('jpg');
    }

    /**
     * Generate a story image for a game
     * 
     * @param Game $game
     * @return \Intervention\Image\Image Encoded JPEG image
     */
    public function generateStoryImage(Game $game)
    {
        $base = $this->manager->canvas(self::STORY_WIDTH, self::STORY_HEIGHT);

        if ($game->playground) {
            $backgroundImg = public_path($game->playground->getPicture());
        } else {
            $backgroundImg = public_path(\App\Media::getPlaceholder('16:9', $game->id));
        }

        $gameDate = Carbon::createFromFormat('Y-m-d H:i:s', $game->date);

        $data = [
            'home_club_emblem' => public_path($game->home_team->club->getEmblem()),
            'home_club_name' => mb_strtoupper($game->home_team->club->name),
            'away_club_emblem' => public_path($game->away_team->club->getEmblem()),
            'away_club_name' => mb_strtoupper($game->away_team->club->name),
            'playground_name' => $game->playground ? mb_strtoupper($game->playground->name) : '',
            'day' => $gameDate->timezone('Europe/Lisbon')->format('d'),
            'month' => mb_strtoupper($this->translateMonth($gameDate->month)),
            'time' => $gameDate->timezone('Europe/Lisbon')->format('H\Hi'),
            'competition' => mb_strtoupper($game->game_group->season->competition->name),
            'competition_logo' => public_path($game->game_group->season->competition->picture)
        ];

        // Resize background to cover entire canvas (fit height, crop width)
        $bgImage = $this->manager->make($backgroundImg);
        $bgImage->resize(null, self::STORY_HEIGHT, function ($constraint) {
            $constraint->aspectRatio();
        });
        
        $base->insert($bgImage, 'center');
        $base->blur(round(self::STORY_WIDTH / 100));
        $base->insert(public_path('/images/story_watermark.png'));

        $this->setCompetitionName($base, $data, 'story');
        $this->setCompetitionLogo($base, $data, 'story');
        $this->setClubEmblems($base, $data, 'story');
        $this->setClubNames($base, $data, 'story');

        if ($game->finished) {
            $this->setFinalScore($base, $game, 'story');
        } else {
            $this->setMatchInfo($base, $data, 'story');
        }

        return $base->encode('jpg');
    }

    private function translateMonth($month)
    {
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

    private function setCompetitionLogo(Image &$base, $data, $type = 'square')
    {
        $width = $type === 'story' ? self::STORY_WIDTH : self::SQUARE_WIDTH;
        $height = $type === 'story' ? self::STORY_HEIGHT : self::SQUARE_HEIGHT;

        $logoSize = $type === 'story' ? (int) ($width / 5) : (int) ($width / 10);
        
        $competitionLogo = $this->manager->make($data['competition_logo']);
        $competitionLogo->resize($logoSize, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        
        $yPosition = $type === 'story' ? (int) ($height / 5.5) : (int) ($height / 9);
        $base->insert($competitionLogo, 'top-left', (int) (($width / 2) - ($competitionLogo->width() / 2)), (int) ($yPosition - ($competitionLogo->height() / 2)));
    }

    private function setClubEmblems(Image &$base, $data, $type = 'square')
    {
        $width = $type === 'story' ? self::STORY_WIDTH : self::SQUARE_WIDTH;
        $height = $type === 'story' ? self::STORY_HEIGHT : self::SQUARE_HEIGHT;
        
        try {
            // Check if home emblem exists
            if (!file_exists($data['home_club_emblem'])) {
                throw new \RuntimeException("Home club emblem not found: {$data['home_club_emblem']}");
            }
            
            $homeEmblem = $this->manager->make($data['home_club_emblem']);
            $homeEmblem->resize((int) ($width / 2.5), null, function ($constraint) {
                $constraint->aspectRatio();
            });
        } catch (\Exception $e) {
            \Log::error('Failed to load home club emblem', [
                'path' => $data['home_club_emblem'],
                'exists' => file_exists($data['home_club_emblem']),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        try {
            // Check if away emblem exists
            if (!file_exists($data['away_club_emblem'])) {
                throw new \RuntimeException("Away club emblem not found: {$data['away_club_emblem']}");
            }
            
            $awayEmblem = $this->manager->make($data['away_club_emblem']);
            $awayEmblem->resize((int) ($width / 2.5), null, function ($constraint) {
                $constraint->aspectRatio();
            });
        } catch (\Exception $e) {
            \Log::error('Failed to load away club emblem', [
                'path' => $data['away_club_emblem'],
                'exists' => file_exists($data['away_club_emblem']),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        $base->insert($homeEmblem, 'top-left', (int) ($width / 4 + 30 - $homeEmblem->width() / 2), (int) ($height / 2 - $homeEmblem->height() / 2));
        $base->insert($awayEmblem, 'top-left', (int) (($width / 4) * 3 - 30 - $awayEmblem->width() / 2), (int) ($height / 2 - $awayEmblem->height() / 2));
    }

    private function setClubNames(Image &$base, $data, $type = 'square')
    {
        $width = $type === 'story' ? self::STORY_WIDTH : self::SQUARE_WIDTH;
        $height = $type === 'story' ? self::STORY_HEIGHT : self::SQUARE_HEIGHT;
        $yPosition = $type === 'story' ? (int) ($height / 3.1) : (int) ($height / 4);
        $clubNamesTextSize = $type === 'story' ? 40 : 30;

        //Shadow for club names
        $base->text($data['home_club_name'] . ' vs ' . $data['away_club_name'], (int) ($width / 2) + 2, $yPosition + 12, function($font) use ($clubNamesTextSize) {
            $font->file(public_path('Roboto-Regular.ttf'));
            $font->size($clubNamesTextSize);
            $font->color('#282828');
            $font->align('center');
            $font->valign('center');
        });
        //Text for club names
        $base->text($data['home_club_name'] . ' vs ' . $data['away_club_name'], (int) ($width / 2), $yPosition + 10, function($font) use ($clubNamesTextSize) {
            $font->file(public_path('Roboto-Regular.ttf'));
            $font->size($clubNamesTextSize);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });
    }

    private function setFinalScore(Image &$base, Game $game, $type = 'square')
    {
        $width = $type === 'story' ? self::STORY_WIDTH : self::SQUARE_WIDTH;
        $height = $type === 'story' ? self::STORY_HEIGHT : self::SQUARE_HEIGHT;
        $yPosition = $type === 'story' ? (int) (($height / 5) * 3.5) : (int) (($height / 4) * 3);

        $scoreOffset = $type === 'story' ? 40 : 12;
        $gpOffset = $type === 'story' ? 230 : 120;
        $gpTextSize = $type === 'story' ? 60 : 30;

        $finalScoreTextSize = $type === 'story' ? 55 : 30;
        $scoreTextSize = $type === 'story' ? 210 : 110;
        
        // Final score shadow
        $base->text("RESULTADO FINAL", (int) ($width / 2) + 2, $yPosition - 29, function ($font) use ($finalScoreTextSize) {
            $font->file(public_path('Roboto-Regular.ttf'));
            $font->size($finalScoreTextSize);
            $font->color('#282828');
            $font->align('center');
            $font->valign('top');
        });
        $base->text("RESULTADO FINAL", (int) ($width / 2), $yPosition - 30, function ($font) use ($finalScoreTextSize) {
            $font->file(public_path('Roboto-Regular.ttf'));
            $font->size($finalScoreTextSize);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('top');
        });

        // Score Text
        $base->text($game->getHomeScore() . ' - ' . $game->getAwayScore(), (int) ($width / 2) + 2, $yPosition + $scoreOffset + 2, function ($font) use ($scoreTextSize) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size($scoreTextSize);
            $font->color('#484848');
            $font->align('center');
            $font->valign('top');
        });
        $base->text($game->getHomeScore() . ' - ' . $game->getAwayScore(), (int) ($width / 2), $yPosition + $scoreOffset, function ($font) use ($scoreTextSize) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size($scoreTextSize);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('top');
        });

        if ($game->decidedByPenalties()) {
            $base->text('(' . $game->penalties_home . '-' . $game->penalties_away . ' g.p)', (int) ($width / 2) + 1, $yPosition + $gpOffset + 2, function ($font) use ($gpTextSize) {
                $font->file(public_path('Roboto-Black.ttf'));
                $font->size($gpTextSize);
                $font->color('#282828');
                $font->align('center');
                $font->valign('top');
            });
            $base->text('(' . $game->penalties_home . '-' . $game->penalties_away . ' g.p)', (int) ($width / 2), $yPosition + $gpOffset, function ($font) use ($gpTextSize) {
                $font->file(public_path('Roboto-Black.ttf'));
                $font->size($gpTextSize);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });
        }
    }

    private function setMatchInfo(Image $base, $data, $type = 'square')
    {
        $width = $type === 'story' ? self::STORY_WIDTH : self::SQUARE_WIDTH;
        $height = $type === 'story' ? self::STORY_HEIGHT : self::SQUARE_HEIGHT;
        $yPosition = $type === 'story' ? (int) (($height / 5) * 3.5) : (int) (($height / 4) * 3);

        $dateTextSize = $type === 'story' ? 70 : 40;
        $playgroundTextSize = $type === 'story' ? 50 : 30;
        $playgroundYOffset = $type === 'story' ? 70 : 46;

        $base->text($data['day'] . ' DE ' . $data['month'] . ' | ' . $data['time'], (int) ($width / 2) + 1, $yPosition + 1, function ($font) use ($dateTextSize) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size($dateTextSize);
            $font->color('#484848');
            $font->align('center');
            $font->valign('center');
        });
        $base->text($data['day'] . ' DE ' . $data['month'] . ' | ' . $data['time'], (int) ($width / 2), $yPosition, function ($font) use ($dateTextSize) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size($dateTextSize);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });

        $base->text($data['playground_name'], (int) ($width / 2) + 1, $yPosition + $playgroundYOffset, function ($font) use ($playgroundTextSize) {
            $font->file(public_path('Roboto-Regular.ttf'));
            $font->size($playgroundTextSize);
            $font->color('#484848');
            $font->align('center');
            $font->valign('center');
        });
        $base->text($data['playground_name'], (int) ($width / 2), $yPosition + $playgroundYOffset + 1, function ($font) use ($playgroundTextSize) {
            $font->file(public_path('Roboto-Regular.ttf'));
            $font->size($playgroundTextSize);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });
    }

    private function setCompetitionName(Image $base, $data, $type = 'square')
    {
        $width = $type === 'story' ? self::STORY_WIDTH : self::SQUARE_WIDTH;
        $height = $type === 'story' ? self::STORY_HEIGHT : self::SQUARE_HEIGHT;
        $yPosition = $type === 'story' ? (int) ($height / 3.5) : (int) ($height / 5);

        $competitionTextSize = $type === 'story' ? 70 : 55;

        
        // Competition Shadow
        $base->text($data['competition'], (int) ($width / 2) + 2, $yPosition + 2, function($font) use ($competitionTextSize) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size($competitionTextSize);
            $font->color('#484848');
            $font->align('center');
            $font->valign('center');
        });
        //Competition Text
        $base->text($data['competition'], (int) ($width / 2), $yPosition, function($font) use ($competitionTextSize) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size($competitionTextSize);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });
    }
}
