<?php

namespace App\Http\Controllers\User;

use Illuminate\Routing\Controller as Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Auth;

use App\Services\ServiceContainer;

use App\Traits\WosServices;
use App\Traits\SiteRequestData;

class CollectionController extends Controller
{
    use WosServices;
    use SiteRequestData;

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var array
     */
    private $validationRules = [
        'game_id' => 'required',
    ];

    public function landing()
    {
        $serviceCollection = $this->getServiceUserGamesCollection();
        $serviceQuickReview = $this->getServiceQuickReview();

        $bindings = [];

        $bindings['TopTitle'] = 'Collection';
        $bindings['PageTitle'] = 'Collection';

        $userId = Auth::id();
        $bindings['UserId'] = $userId;

        $bindings['CollectionList'] = $serviceCollection->getByUser($userId);
        $bindings['CollectionStats'] = $serviceCollection->getStats($userId);

        $quickReviewGameIdList = $serviceQuickReview->getAllByUserGameIdList($userId);
        $bindings['QuickReviewGameIdList'] = $quickReviewGameIdList;

        return view('user.collection.index', $bindings);
    }

    public function add()
    {
        $serviceContainer = \Request::get('serviceContainer');
        /* @var $serviceContainer ServiceContainer */

        $gameService = $serviceContainer->getGameService();
        $collectionService = $serviceContainer->getUserGamesCollectionService();

        $request = request();

        $userId = Auth::id();
        $userRegionCode = Auth::user()->region;

        if ($request->isMethod('post')) {

            $this->validate($request, $this->validationRules);

            $isStarted  = $request->is_started  == 'on' ? 1 : 0;
            $isOngoing  = $request->is_ongoing  == 'on' ? 1 : 0;
            $isComplete = $request->is_complete == 'on' ? 1 : 0;

            $collectionService->create(
                $userId, $request->game_id, $request->owned_from, $request->owned_type,
                $isStarted, $isOngoing, $isComplete, $request->hours_played
            );

            return redirect(route('user.collection.landing'));

        }

        $bindings = [];

        $bindings['TopTitle'] = 'User - Games collection - Add game';
        $bindings['PageTitle'] = 'Add game';
        $bindings['FormMode'] = 'add';

        $bindings['GamesList'] = $gameService->getAll($userRegionCode);

        $urlGameId = $request->gameId;
        if ($urlGameId) {
            $bindings['UrlGameId'] = $urlGameId;
        }

        return view('user.collection.add', $bindings);
    }

    public function edit($itemId)
    {
        $serviceContainer = \Request::get('serviceContainer');
        /* @var $serviceContainer ServiceContainer */

        $gameService = $serviceContainer->getGameService();
        $collectionService = $serviceContainer->getUserGamesCollectionService();

        $request = request();

        $userId = Auth::id();
        $userRegionCode = Auth::user()->region;

        $collectionData = $collectionService->find($itemId);
        if (!$collectionData) abort(404);

        if ($collectionData->user_id != $userId) abort(403);

        $bindings = [];

        if ($request->isMethod('post')) {

            $bindings['FormMode'] = 'edit-post';

            $this->validate($request, $this->validationRules);

            $isStarted  = $request->is_started  == 'on' ? 1 : 0;
            $isOngoing  = $request->is_ongoing  == 'on' ? 1 : 0;
            $isComplete = $request->is_complete == 'on' ? 1 : 0;

            $collectionService->edit(
                $collectionData, $request->owned_from, $request->owned_type,
                $isStarted, $isOngoing, $isComplete, $request->hours_played
            );

            return redirect(route('user.collection.landing'));

        } else {

            $bindings['FormMode'] = 'edit';

        }

        $bindings['TopTitle'] = 'User - Games collection - Edit game';
        $bindings['PageTitle'] = 'Edit game';

        $bindings['CollectionData'] = $collectionData;
        $bindings['ItemId'] = $itemId;

        $bindings['GamesList'] = $gameService->getAll($userRegionCode);

        return view('user.collection.edit', $bindings);
    }

    public function delete()
    {
        $request = request();

        $collectionItemId = $request->itemId;

        if (!$collectionItemId) {
            return response()->json(['error' => 'Missing data: itemId'], 400);
        }

        $serviceContainer = \Request::get('serviceContainer');
        /* @var $serviceContainer ServiceContainer */

        $collectionService = $serviceContainer->getUserGamesCollectionService();
        $collectionItem = $collectionService->find($collectionItemId);

        if (!$collectionItem) {
            return response()->json(['error' => 'Not found: '.$collectionItemId], 404);
        }

        if ($collectionItem->user_id != Auth::id()) {
            return response()->json(['error' => 'Collection item belongs to another user'], 400);
        }

        // Delete from playlist
        $collectionService->delete($collectionItemId);

        $data = array(
            'status' => 'OK'
        );
        return response()->json($data, 200);
    }
}
