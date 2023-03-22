<?php

namespace App\Jobs;

use App\Poll;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;

class ProcessPolls
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
        Log::info("Starting Processing Polls Job");

        Log::info("Publishing scheduled polls");
        $now = Carbon::now();
        DB::table('polls')
            ->where('visible', '=', false)
            ->where('publish_after', '<', $now->format("Y-m-d H:i:s"))
            ->where('close_after', '>', $now->format("Y-m-d H:i:s"))
            ->update(['visible' => true]);

        Log::info("Updating images");
        $pollsToUpdate = Poll::where('update_image' , true)->limit(10)->get();
        Log::info("Got " . $pollsToUpdate->count() . " polls to update");
        $success = 0;
        $failed = 0;
        foreach($pollsToUpdate as $poll){
            try {
                Log::info('Generating image for poll ' . $poll->id);
                $base = $this->manager->canvas(self::WIDTH, self::HEIGHT);
                $base->insert(public_path("/images/poll_default.png"), 'center');

                $textToInsert = $poll->question;

                $center_x    = self::WIDTH / 2;
                $center_y    = (self::HEIGHT / 2) + 120;
                $max_len     = 36;
                $font_size   = 60;
                $font_height = 35;

                $lines = explode("\n", wordwrap($textToInsert, $max_len));
                $y = $center_y - ((count($lines) - 1) * $font_height);

                foreach ($lines as $line)
                {
                    $base->text($line, $center_x, $y, function($font) use ($font_size){
                        $font->file(public_path('Roboto-Regular.ttf'));
                        $font->size($font_size);
                        $font->color('#ffffff');
                        $font->align('center');
                        $font->valign('center');
                    });

                    $y += $font_height * 2;
                }

                $base = $base->encode('png');
                $filename = $poll->slug . ".png";
                $base->save(public_path("/storage/poll_images/$filename"));

                // Save data
                $poll->image = "/storage/poll_images/$filename";
                $poll->update_image = false;
                $poll->save();
                $success++;

            } catch (\Exception $e) {
                $failed++;
                Log::error("Error generating image for poll $poll->id: $e");
            }
        }

        $total = $success + $failed;
        Log::info("Finished Processing $total Polls. $success success and $failed fails");
    }
}
