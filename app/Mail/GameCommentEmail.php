<?php

namespace App\Mail;

use App\Game;
use App\GameComment;

use App\Team;
use Carbon\Carbon;
use Illuminate\Mail\Mailable;

class GameCommentEmail extends Mailable
{
    /** @var Game */
    private $game;

    /** @var GameComment */
    private $gameComment;

    /** @var Team */
    private $team;

    public function __construct(Game $game, GameComment $gameComment, Team $team)
    {
        $this->game = $game;
        $this->gameComment = $gameComment;
        $this->team = $team;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Carbon::setLocale('pt_BR');

        $homeClubName = $this->game->home_team->club->name;
        $awayClubName = $this->game->away_team->club->name;
        $recipientName = $this->team->club->name;
        $deadline = Carbon::createFromFormat('Y-m-d H:i:s', $this->gameComment->deadline);
        $gameDate = Carbon::createFromFormat('Y-m-d H:i:s', $this->game->date);
        $day = $gameDate->format('d');
        $month = $gameDate->translatedFormat('F');
        $year = $gameDate->format('Y');
        $action = route('front.game_comment', [
            'uuid' => $this->gameComment->uuid,
        ]);
        $action .= "?pin=" . $this->gameComment->pin;
        $unsubscribe = route('front.manage_notifications', [
            'uuid' => $this->gameComment->uuid,
        ]);
        $unsubscribe .= "?pin=" . $this->gameComment->pin;

        return $this->from(config('custom.site_email'), config('app.name'))
            ->subject("Por favor comente o jogo $homeClubName vs $awayClubName")
            ->view('emails.game_comment')
            ->with([
                'recipientName' => $recipientName,
                'homeClubName' => $homeClubName,
                'awayClubName' => $awayClubName,
                'day' => $day,
                'month' => $month,
                'year' => $year,
                'pin' => $this->gameComment->pin,
                'action' => $action,
                'unsubscribe' => $unsubscribe,
                'deadline' => $deadline->timezone('Europe/Lisbon')->format("d/m/Y H:i"),
            ]);
    }
}
