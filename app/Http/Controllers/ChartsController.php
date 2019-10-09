<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as Controller;
use Illuminate\Support\Facades\DB;

use App\Traits\WosServices;

use App\ChartsRankingGlobal;

class ChartsController extends Controller
{
    use WosServices;

    public function landing()
    {
        $bindings = [];

        $chartDatesEu = $this->getServiceChartsDate()->getDateList('eu');
        $chartDatesUs = $this->getServiceChartsDate()->getDateList('us');

        $bindings['TopTitle'] = 'Nintendo Switch eShop Charts';
        $bindings['PageTitle'] = 'Charts';
        $bindings['ChartDatesEu'] = $chartDatesEu;
        $bindings['ChartDatesUs'] = $chartDatesUs;

        return view('charts.landing', $bindings);
    }

    public function show($countryCode, $date)
    {
        $bindings = [];

        $title = '';
        $regionText = '';

        switch ($countryCode) {
            case ChartsRankingGlobal::COUNTRY_US;
                $title = 'Nintendo Switch eShop Charts - US';
                $regionText = 'US';
                break;
            case ChartsRankingGlobal::COUNTRY_EU;
                $title = 'Nintendo Switch eShop Charts - Europe';
                $regionText = 'European';
                break;
            default:
                abort(404);
                break;
        }

        $gamesList = $this->getServiceChartsRankingGlobal()->getByCountryAndDate($countryCode, $date);

        if (count($gamesList) == 0) {
            abort(404);
        }

        $chartDate = new \DateTime($date);
        $chartDateDesc = $chartDate->format('jS M Y');

        $pageTitle = $regionText.' eShop Charts: '.$chartDateDesc;

        $bindings['TopTitle'] = $title.' - '.$chartDateDesc;
        $bindings['PageTitle'] = $pageTitle;
        $bindings['RegionText'] = $regionText;
        $bindings['ChartDate'] = $date;
        $bindings['GamesList'] = $gamesList;
        $bindings['CountryCode'] = $countryCode;

        // Next/Previous links
        $dateNext = $this->getServiceChartsDate()->getNext($countryCode, $date);
        $datePrev = $this->getServiceChartsDate()->getPrevious($countryCode, $date);
        if ($dateNext) {
            $bindings['ChartDateNext'] = $dateNext;
        }
        if ($datePrev) {
            $bindings['ChartDatePrev'] = $datePrev;
        }

        return view('charts.topFifteen', $bindings);
    }

    public function mostAppearances()
    {
        $bindings = [];

        $bindings['GamesListEu'] = DB::select("
            SELECT cr.game_id, g.title, g.link_title, g.game_rank, g.rating_avg, count(*) AS count
            FROM charts_rankings_global cr
            JOIN games g ON cr.game_id = g.id
            WHERE cr.country_code = 'eu'
            GROUP BY cr.game_id
            HAVING count > 2
            ORDER BY count DESC, g.rating_avg DESC
        ");

        $bindings['GamesListUs'] = DB::select("
            SELECT cr.game_id, g.title, g.link_title, g.game_rank, g.rating_avg, count(*) AS count
            FROM charts_rankings_global cr
            JOIN games g ON cr.game_id = g.id
            WHERE cr.country_code = 'us'
            GROUP BY cr.game_id
            HAVING count > 2
            ORDER BY count DESC, g.rating_avg DESC
        ");

        $bindings['TopTitle'] = 'Charts - Most appearances';
        $bindings['PageTitle'] = 'Most appearances in the eShop Charts';

        return view('charts.mostAppearances', $bindings);
    }

    public function gamesAtPositionLanding()
    {
        $bindings = [];

        $bindings['TopTitle'] = 'Charts - Games at position';
        $bindings['PageTitle'] = 'Games at position X in the eShop Charts';

        $fifteenList = array();
        for ($i=1; $i<=30; $i++) {
            $fifteenList[] = $i;
        }

        $bindings['PositionList'] = $fifteenList;

        return view('charts.gamesAtPositionLanding', $bindings);
    }

    public function gamesAtPosition($position)
    {
        $bindings = [];

        $posList = [];
        for ($i=1; $i<=30; $i++) {
            $posList[] = $i;
        }

        if (!in_array($position, $posList)) {
            abort(404);
        }

        $bindings['TopTitle'] = 'Charts - Games at position '.$position;
        $bindings['PageTitle'] = 'Games at No '.$position.' in the eShop Charts';
        $bindings['PositionNo'] = $position;

        $bindings['GamesListEu'] = \DB::select("
            SELECT cr.game_id, g.title, g.link_title, count(*) AS count
            FROM charts_rankings_global cr
            JOIN games g ON cr.game_id = g.id
            WHERE cr.country_code = 'eu'
            AND cr.position = ?
            GROUP BY cr.game_id ORDER BY count(*) DESC
        ", array($position));

        $bindings['GamesListUs'] = \DB::select("
            SELECT cr.game_id, g.title, g.link_title, count(*) AS count
            FROM charts_rankings_global cr
            JOIN games g ON cr.game_id = g.id
            WHERE cr.country_code = 'us'
            AND cr.position = ?
            GROUP BY cr.game_id ORDER BY count(*) DESC
        ", array($position));

        return view('charts.gamesAtPosition', $bindings);
    }

    public function redirectEu($date)
    {
        $redirUrl = sprintf('/charts/eu/%s', $date);
        return redirect($redirUrl, 301);
    }

    public function redirectUs($date)
    {
        $redirUrl = sprintf('/charts/us/%s', $date);
        return redirect($redirUrl, 301);
    }
}
