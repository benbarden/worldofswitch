<?php

namespace App\Http\Controllers\Staff\Partners;

use Illuminate\Routing\Controller as Controller;

use App\Traits\SiteRequestData;
use App\Traits\WosServices;

class DashboardController extends Controller
{
    use WosServices;
    use SiteRequestData;

    public function show()
    {
        $pageTitle = 'Partners dashboard';

        $serviceGameDeveloper = $this->getServiceGameDeveloper();
        $serviceGamePublisher = $this->getServiceGamePublisher();

        $bindings = [];

        $bindings['TopTitle'] = $pageTitle.' - Admin';
        $bindings['PageTitle'] = $pageTitle;

        // Action lists
        $bindings['DeveloperMissingCount'] = $serviceGameDeveloper->countGamesWithNoDeveloper();
        $bindings['NewDeveloperToSetCount'] = $serviceGameDeveloper->countNewDevelopersToSet();
        $bindings['OldDeveloperToClearCount'] = $serviceGameDeveloper->countOldDevelopersToClear();
        $bindings['PublisherMissingCount'] = $serviceGamePublisher->countGamesWithNoPublisher();
        $bindings['NewPublisherToSetCount'] = $serviceGamePublisher->countNewPublishersToSet();
        $bindings['OldPublisherToClearCount'] = $serviceGamePublisher->countOldPublishersToClear();

        // Stats
        $bindings['GameDeveloperLinks'] = $serviceGameDeveloper->countGameDeveloperLinks();
        $bindings['GamePublisherLinks'] = $serviceGamePublisher->countGamePublisherLinks();

        return view('staff.partners.dashboard', $bindings);
    }
}