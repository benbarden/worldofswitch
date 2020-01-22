<?php

namespace App\Http\Controllers\Staff\Games;

use Illuminate\Routing\Controller as Controller;

use App\Traits\WosServices;
use App\Traits\SiteRequestData;

class GamesDetailController extends Controller
{
    use WosServices;
    use SiteRequestData;

    public function show($gameId)
    {
        $regionCode = $this->getRegionCode();

        $serviceGame = $this->getServiceGame();
        $serviceReviewLink = $this->getServiceReviewLink();
        $serviceGameReleaseDate = $this->getServiceGameReleaseDate();
        $serviceGameGenres = $this->getServiceGameGenre();
        $serviceGameTag = $this->getServiceGameTag();
        $serviceQuickReview = $this->getServiceQuickReview();
        $serviceGameDeveloper = $this->getServiceGameDeveloper();
        $serviceGamePublisher = $this->getServiceGamePublisher();
        $serviceGameTitleHash = $this->getServiceGameTitleHash();

        $game = $serviceGame->find($gameId);
        if (!$game) abort(404);

        $gameTitle = $game->title;

        $bindings = [];

        $bindings['TopTitle'] = $gameTitle.' - Game detail - Admin';
        $bindings['PageTitle'] = $gameTitle;

        $bindings['LastAction'] = $lastAction = \Request::get('lastaction');

        $lastGameId = \Request::get('lastgameid');
        if ($lastGameId) {
            $lastGame = $serviceGame->find($lastGameId);
            if ($lastGame) {
                $bindings['LastGame'] = $lastGame;
            }
        }

        $selectedTabId = \Request::get('tabid');
        $bindings['SelectedTabId'] = $selectedTabId;

        // Get all the data
        $gameReviews = $serviceReviewLink->getByGame($gameId);
        $gameGenres = $serviceGameGenres->getByGame($gameId);
        $gameQuickReviews = $serviceQuickReview->getActiveByGame($gameId);
        $gameDevelopers = $serviceGameDeveloper->getByGame($gameId);
        $gamePublishers = $serviceGamePublisher->getByGame($gameId);
        $gameTags = $serviceGameTag->getByGame($gameId);
        $gameTitleHashes = $serviceGameTitleHash->getByGameId($gameId);

        $bindings['GameId'] = $gameId;
        $bindings['GameData'] = $game;
        $bindings['GameReviews'] = $gameReviews;
        $bindings['GameGenres'] = $gameGenres;
        $bindings['GameQuickReviewList'] = $gameQuickReviews;
        $bindings['GameDevelopers'] = $gameDevelopers;
        $bindings['GamePublishers'] = $gamePublishers;
        $bindings['GameTags'] = $gameTags;
        $bindings['GameTitleHashes'] = $gameTitleHashes;

        $bindings['ReleaseDates'] = $serviceGameReleaseDate->getByGame($gameId);
        $bindings['ReleaseDateInfo'] = $serviceGameReleaseDate->getByGameAndRegion($gameId, $regionCode);

        // Audit data
        //$gameAuditsCore = $game->audits()->orderBy('id', 'desc')->get();
        $gameAudits = $this->getServiceAudit()->getAggregatedGameAudits($gameId, 25);
        $bindings['GameAuditsCore'] = $gameAudits;

        // Import rules
        $bindings['ImportRulesEshop'] = $this->getServiceGameImportRuleEshop()->getByGameId($gameId);
        $bindings['ImportRulesWikipedia'] = $this->getServiceGameImportRuleWikipedia()->getByGameId($gameId);

        return view('staff.games.detail.show', $bindings);
    }

}