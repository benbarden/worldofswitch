<?php

namespace App\Http\Controllers\Reviewers;

use Illuminate\Routing\Controller as Controller;

use App\Traits\SwitchServices;
use App\Traits\AuthUser;

class UnrankedGamesController extends Controller
{
    use SwitchServices;
    use AuthUser;

    public function landing()
    {
        $partnerId = $this->getValidUser($this->getServiceUser())->partner_id;
        if ($partnerId == 0) {
            abort(403);
        }

        $bindings = [];

        $bindings['TopTitle'] = 'Unranked games';
        $bindings['PageTitle'] = 'Unranked games';

        return view('reviewers.unranked-games.landing', $bindings);
    }

    public function showList($mode, $filter)
    {
        $partnerId = $this->getValidUser($this->getServiceUser())->partner_id;
        if ($partnerId == 0) {
            abort(403);
        }

        $serviceGameReleaseDate = $this->getServiceGameReleaseDate();
        $serviceReviewLink = $this->getServiceReviewLink();

        $gameIdsReviewedBySite = $serviceReviewLink->getAllGameIdsReviewedBySite($partnerId);
        $totalGameIdsReviewedBySite = $serviceReviewLink->countAllGameIdsReviewedBySite($partnerId);

        $bindings = [];

        switch ($mode) {

            case 'by-count':
                if (!in_array($filter, ['0', '1', '2'])) abort(404);
                $gamesList = $serviceGameReleaseDate->getUnrankedByReviewCount($filter, $gameIdsReviewedBySite);
                $tableSort = "[1, 'asc']";
                break;

            case 'by-year':
                if (!in_array($filter, ['2017', '2018', '2019'])) abort(404);
                $gamesList = $serviceGameReleaseDate->getUnrankedByYear($filter, $gameIdsReviewedBySite);
                $tableSort = "[1, 'asc']";
                break;

            case 'by-list':
                if (!in_array($filter, ['aca-neogeo', 'arcade-archives', 'all-others'])) abort(404);
                $gamesList = $serviceGameReleaseDate->getUnrankedByList($filter, $gameIdsReviewedBySite);
                $tableSort = "[1, 'asc']";
                break;

            default:
                abort(404);

        }

        $bindings['GamesList'] = $gamesList;
        $bindings['GamesTableSort'] = $tableSort;

        $bindings['GamesReviewedCount'] = $totalGameIdsReviewedBySite;

        $bindings['PageMode'] = $mode;
        $bindings['PageFilter'] = $filter;

        $bindings['TopTitle'] = 'Unranked games';
        $bindings['PageTitle'] = 'Unranked games';

        return view('reviewers.unranked-games.list', $bindings);
    }
}