<?php

namespace App\Jobs;

use App\Game;
use App\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class GenerateGameImage
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var ImageManager */
    protected $manager;

    const WIDTH = 1280;
    const HEIGHT = 720;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->manager = new ImageManager();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $startTime = new \DateTime();
        $games = Game::where('generate_image', true)->limit(10)->get();

        // If no games, exit silently
        if (count($games) == 0) {
            return;
        }

        Log::debug("Starting GenerateGameImage Job");
        Log::debug("Got " . count($games) . " game(s) to generate.");

        foreach ($games as $game) {
            try {
                Log::debug('Generating image for game ' . $game->id);
                $base = $this->manager->canvas(self::WIDTH, self::HEIGHT);

                if ($game->playground) {
                    $backgroundImg = public_path(str_replace('16_9_placeholder_', '1_1_placeholder_', $game->playground->getPicture()));
                } else {
                    $backgroundImg = public_path(Media::getPlaceholder('16:9', $game->id));
                }

                $data = [
                    'home_club_emblem' => public_path($game->home_team->club->getEmblem()),
                    'home_club_name' => mb_strtoupper($game->home_team->club->name),
                    'away_club_emblem' => public_path($game->away_team->club->getEmblem()),
                    'away_club_name' => mb_strtoupper($game->away_team->club->name),
                    'competition' => mb_strtoupper($game->game_group->season->competition->name),
                    'competition_logo' => public_path($game->game_group->season->competition->picture)
                ];

                $base->insert($backgroundImg, 'center');
                $base->blur(round(self::WIDTH / 100));
                $base->insert(public_path('/images/game_image_watermark.png'));

                $this->setCompetitionName($base, $data);
                $this->setCompetitionLogo($base, $data);
                $this->setClubEmblems($base, $data);
                $this->setClubNames($base, $data);

                if ($game->postponed) {
                    $base->insert(public_path('/images/game_image_postponed_watermark.png'));
                }

                $name = Str::slug($game->home_team->club->name . '-vs-'
                        . $game->away_team->club->name
                        . '-'
                        . $game->game_group->season->competition->name
                        . '-'
                        . str_replace('/', '-', $game->game_group->season->getName())
                    ) . '.jpg';

                $base = $base->encode('jpg');

                $filename = "game_images/$name";
                $base->save(public_path("/storage/$filename"));

                $game->image = "/storage/$filename";
                $game->generate_image = false;
                $game->save();
                Log::debug("Successfully generated image for game $game->id");
            } catch (\Exception $e) {
                Log::error("Error generating image for game $game->id");
            }
        }

        $endTime = new \DateTime();
        $diff = $endTime->diff($startTime);
        Log::debug("Finished GenerateGameImage Job in " . $diff->format('%s seconds %F microseconds'));
    }

    private function setCompetitionLogo(Image &$base, $data)
    {
        $competitionLogo = $this->manager->make($data['competition_logo']);
        $competitionLogo->resize(self::WIDTH / 15, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $base->insert($competitionLogo, 'top-left', round((self::WIDTH / 2) - ($competitionLogo->width() / 2)), round((self::HEIGHT / 9) - ($competitionLogo->height() / 2)));
    }

    private function setClubEmblems(Image &$base, $data)
    {
        $homeEmblem = $this->manager->make($data['home_club_emblem']);
        $homeEmblem->resize(round(self::WIDTH / 4), null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $awayEmblem = $this->manager->make($data['away_club_emblem']);
        $awayEmblem->resize(round(self::WIDTH / 4), null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $base->insert($homeEmblem, 'top-left', round(self::WIDTH / 4) + 30 - round($homeEmblem->width() / 2), round(self::HEIGHT / 2) - round($homeEmblem->height() / 2));
        $base->insert($awayEmblem, 'top-left', round((self::WIDTH / 4) * 3) - 30 - round($awayEmblem->width() / 2), round(self::HEIGHT / 2) - round($awayEmblem->height() / 2));
    }

    private function setClubNames(Image &$base, $data)
    {
        //Shadow for club names
        $base->text($data['home_club_name'] . ' vs ' . $data['away_club_name'], round(self::WIDTH / 2) + 2, self::HEIGHT - round((self::HEIGHT / 5)) + 2, function($font) {
            $font->file(public_path('Roboto-Regular.ttf'));
            $font->size(40);
            $font->color('#282828');
            $font->align('center');
            $font->valign('center');
        });
        //Text for club names
        $base->text($data['home_club_name'] . ' vs ' . $data['away_club_name'], round(self::WIDTH / 2), self::HEIGHT - round((self::HEIGHT / 5)), function($font) {
            $font->file(public_path('Roboto-Regular.ttf'));
            $font->size(40);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });
    }

    private function setCompetitionName(Image $base, $data)
    {
        // Competition Shadow
        $base->text($data['competition'], round(self::WIDTH / 2) + 2, round(self::HEIGHT / 5) + 22, function($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(55);
            $font->color('#484848');
            $font->align('center');
            $font->valign('center');
        });
        //Competition Text
        $base->text($data['competition'], round(self::WIDTH / 2), round(self::HEIGHT / 5) + 20, function($font) {
            $font->file(public_path('Roboto-Black.ttf'));
            $font->size(55);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('center');
        });
    }
}
