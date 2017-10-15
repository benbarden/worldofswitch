<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;

class GamesController extends BaseController
{
    public function listReleased()
    {
        $bindings = array();

        $gamesList = $this->serviceGame->getListReleased();

        $bindings['GamesList'] = $gamesList;

        $bindings['TopTitle'] = 'Nintendo Switch released games';
        $bindings['PageTitle'] = 'Nintendo Switch games available in Europe';

        return view('games.list.releasedGames', $bindings);
    }

    public function listUpcoming()
    {
        $bindings = array();

        $upcoming2017WithDates = $this->serviceGame->getAllUpcomingYearWithDates(2017);
        $upcoming2017WithQuarter = $this->serviceGame->getAllUpcomingYearQuarters(2017);
        $upcoming2017NoInfo = $this->serviceGame->getAllUpcomingYearXs(2017);
        $upcomingTBA = $this->serviceGame->getAllUpcomingTBA();
        $upcomingFuture = $this->serviceGame->getAllUpcomingFuture(2017);

        // Get anything not already displayed
        /* @var $upcomingFuture Collection */
        /*
        $upcomingFuture = $this->serviceGame->getListUpcoming();
        $upcomingFuture = $upcomingFuture->whereNotIn(
            'upcoming_date', ['TBA', '2017-XX', '2017-Q3', '2017-Q3/Q4', '2017-Q4']
        );
        */

        $bindings['Upcoming2017WithDates'] = $upcoming2017WithDates;
        $bindings['Upcoming2017WithQuarter'] = $upcoming2017WithQuarter;
        $bindings['Upcoming2017NoInfo'] = $upcoming2017NoInfo;
        $bindings['UpcomingTBA'] = $upcomingTBA;
        $bindings['UpcomingFuture'] = $upcomingFuture;
        $bindings['UpcomingGamesFullList'] = $this->serviceGame->getAllUpcoming();

        $bindings['TopTitle'] = 'Nintendo Switch upcoming games';
        $bindings['PageTitle'] = 'Upcoming Nintendo Switch games';

        return view('games.list.upcomingGames', $bindings);
    }

    public function listTopRated()
    {
        return redirect(route('reviews.topRatedAllTime'), 301);
    }

    public function listReviewsNeeded()
    {
        return redirect(route('reviews.gamesNeedingReviews'), 301);
    }

    public function genresLanding()
    {
        $bindings = array();

        $serviceGenre = resolve('Services\GenreService');
        $genreList = $serviceGenre->getAll();

        $bindings['GenreList'] = $genreList;

        $bindings['TopTitle'] = 'Nintendo Switch - Game genres';
        $bindings['PageTitle'] = 'Nintendo Switch game genres';

        return view('games.genres.landing', $bindings);
    }

    public function genreByName($linkTitle)
    {
        $bindings = array();

        $serviceGenre = resolve('Services\GenreService');
        $genreData = $serviceGenre->getByLinkTitle($linkTitle);

        if (!$genreData) {
            abort(404);
        }

        $bindings['GenreData'] = $genreData;

        $bindings['GamesList'] = $this->serviceGame->getGamesByGenre($genreData->id);

        $bindings['TopTitle'] = 'Nintendo Switch games in genre: '.$genreData->genre;
        $bindings['PageTitle'] = 'Nintendo Switch games in genre: '.$genreData->genre;

        return view('games.genres.item', $bindings);
    }

    /**
     * @param $id
     * @param $linkTitle
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function show($id, $linkTitle)
    {
        $bindings = array();

        $gameData = \App\Game::where('id', $id)->get();

        if (!$gameData) {
            abort(404);
        }

        $gameData = $gameData->first();

        if (!$gameData) {
            abort(404);
        }

        if ($gameData->link_title != $linkTitle) {
            $redirUrl = sprintf('/games/%s/%s', $id, $gameData->link_title);
            return redirect($redirUrl, 301);
        }

        // Get chart rankings for this game
        $gameRanking = \App\ChartsRanking::where('game_id', $gameData->id)
            ->orderBy('chart_date', 'desc')
            ->get();

        // Get reviews
        $reviewLinkService = resolve('Services\ReviewLinkService');
        $gameReviews = $reviewLinkService->getByGame($gameData->id);

        $bindings['TopTitle'] = $gameData->title;
        $bindings['PageTitle'] = $gameData->title.' - Nintendo Switch game details';
        $bindings['GameData'] = $gameData;
        $bindings['GameRanking'] = $gameRanking;
        $bindings['GameReviews'] = $gameReviews;

        // Total rank count
        $bindings['RankMaximum'] = $this->serviceGame->getListTopRatedCount();

        return view('games.show', $bindings);
    }

    /**
     * This is for redirecting old links. Do not use for new links.
     * @param integer $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function showId($id)
    {
        $gameData = \App\Game::where('id', $id)->get();

        if (!$gameData) {
            abort(404);
        }

        $gameData = $gameData->first();

        $redirUrl = sprintf('/games/%s/%s', $id, $gameData->link_title);
        return redirect($redirUrl, 301);
    }

}
