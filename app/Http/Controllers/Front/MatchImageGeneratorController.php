<?php

namespace App\Http\Controllers\Front;

use App\Audit;
use App\Game;
use App\Http\Controllers\Controller;
use App\Services\MatchImageGeneratorService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MatchImageGeneratorController extends Controller
{
    /** @var MatchImageGeneratorService */
    protected $imageService;

    public function __construct(MatchImageGeneratorService $imageService)
    {
        $this->imageService = $imageService;
        $this->middleware('auth');
    }

    public function generateImage(Request $request, Game $game)
    {
        /** @var User $user */
        $user = Auth::user();
        $imageType = $request->input('image_type', 'square');
        
        Log::info('Generating match ' . $game->id . ' ' . $imageType . ' image for user ' . $user->name);

        if ($imageType === 'story') {
            return $this->generateStoryImageResponse($game, $user);
        }
        
        return $this->generateSquareImageResponse($game, $user);
    }

    private function generateSquareImageResponse(Game $game, User $user)
    {
        // Generate the image using the service
        $encodedImage = $this->imageService->generateSquareImage($game);

        // HTTP response handling
        $name = Str::slug($game->home_team->club->name . '-vs-' . $game->away_team->club->name) . '-square.jpg';
        $headers = [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'attachment; filename=' . $name,
        ];

        // Audit logging
        $audit_extra_info = $game->finished 
            ? "Imagem quadrada de resultado final" 
            : "Imagem quadrada de divulgação ao jogo";
        
        Audit::add(Audit::ACTION_VIEW, "MatchImageSquare", null, $game->toArray(), $audit_extra_info);
        Log::info("Square match image generated for game " . $game->id . " by user " . $user->id);

        return response()->stream(function () use ($encodedImage) {
            echo $encodedImage;
        }, 200, $headers);
    }

    private function generateStoryImageResponse(Game $game, User $user)
    {
        // Generate the image using the service
        $encodedImage = $this->imageService->generateStoryImage($game);

        // HTTP response handling
        $name = Str::slug($game->home_team->club->name . '-vs-' . $game->away_team->club->name) . '-story.jpg';
        $headers = [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'attachment; filename=' . $name,
        ];

        // Audit logging
        $audit_extra_info = $game->finished 
            ? "Imagem story de resultado final" 
            : "Imagem story de divulgação ao jogo";
        
        Audit::add(Audit::ACTION_VIEW, "MatchImageStory", null, $game->toArray(), $audit_extra_info);
        Log::info("Story match image generated for game " . $game->id . " by user " . $user->id);

        return response()->stream(function () use ($encodedImage) {
            echo $encodedImage;
        }, 200, $headers);
    }
}
