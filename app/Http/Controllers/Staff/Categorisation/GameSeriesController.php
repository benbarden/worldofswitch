<?php

namespace App\Http\Controllers\Staff\Categorisation;

use Illuminate\Routing\Controller as Controller;

use Auth;

use App\Traits\SiteRequestData;
use App\Traits\WosServices;

class GameSeriesController extends Controller
{
    use WosServices;
    use SiteRequestData;

    public function showList()
    {
        $serviceGameSeries = $this->getServiceGameSeries();

        $bindings = [];

        $bindings['TopTitle'] = 'Admin - Game series';
        $bindings['PageTitle'] = 'Game series';

        $bindings['GameSeriesList'] = $serviceGameSeries->getAll();

        return view('staff.categorisation.game-series.list', $bindings);
    }

    public function addGameSeries()
    {
        $serviceGameSeries = $this->getServiceGameSeries();
        $serviceUser = $this->getServiceUser();
        $serviceUrl = $this->getServiceUrl();

        $userId = Auth::id();

        $user = $serviceUser->find($userId);
        if (!$user) {
            return response()->json(['error' => 'Cannot find user!'], 400);
        }

        $request = request();

        $seriesName = $request->seriesName;
        if (!$seriesName) {
            return response()->json(['error' => 'Missing data: seriesName'], 400);
        }

        $existingRecord = $serviceGameSeries->getByName($seriesName);
        if ($existingRecord) {
            return response()->json(['error' => 'Game series already exists!'], 400);
        }

        $linkTitle = $serviceUrl->generateLinkText($seriesName);

        $serviceGameSeries->create($seriesName, $linkTitle);

        $data = array(
            'status' => 'OK'
        );
        return response()->json($data, 200);
    }
}