<?php

namespace App\Http\Controllers\Staff\Categorisation;

use App\Factories\UserFactory;
use App\Factories\UserPointTransactionDirectorFactory;
use App\UserPointTransaction;
use Illuminate\Routing\Controller as Controller;

use App\Traits\SwitchServices;
use App\Traits\AuthUser;

class GameCategorySuggestionController extends Controller
{
    use SwitchServices;
    use AuthUser;

    public function show()
    {
        $request = request();
        $filterStatus = $request->filterStatus;

        $bindings = [];

        $bindings['TopTitle'] = 'Staff - Categories - Game category suggestions';
        $bindings['PageTitle'] = 'Game category suggestions';

        $jsInitialSort = "[ 0, 'desc']";

        if (!isset($filterStatus)) {
            $bindings['FilterStatus'] = '';
            $dbEditList = $this->getServiceDbEditGame()->getAllCategoryEdits();
        } else {
            $bindings['FilterStatus'] = $filterStatus;
            $dbEditList = $this->getServiceDbEditGame()->getCategoryEditsByStatus($filterStatus);
        }

        $bindings['DbEditList'] = $dbEditList;
        $bindings['StatusList'] = $this->getServiceDbEditGame()->getStatusList();
        $bindings['jsInitialSort'] = $jsInitialSort;

        return view('staff.categorisation.game-category-suggestions.list', $bindings);
    }

    public function approve()
    {
        $serviceUser = $this->getServiceUser();
        $serviceDbEdit = $this->getServiceDbEditGame();

        $userId = $this->getAuthId();

        $user = $serviceUser->find($userId);
        if (!$user) {
            return response()->json(['error' => 'Cannot find user!'], 400);
        }

        $request = request();

        $itemId = $request->itemId;
        if (!$itemId) {
            return response()->json(['error' => 'Missing data: itemId'], 400);
        }

        $dbEditGame = $serviceDbEdit->find($itemId);
        if (!$dbEditGame) {
            return response()->json(['error' => 'Record not found: '.$itemId], 400);
        }

        if (!$dbEditGame->isPending()) {
            return response()->json(['error' => 'Record is not pending'], 400);
        }

        $game = $this->getServiceGame()->find($dbEditGame->game_id);
        if (!$game) {
            return response()->json(['error' => 'Game not found for this record'], 400);
        }

        // Approve item
        $dbEditGame->setApproved();
        $dbEditGame->save();

        // Make the change
        $game->category_id = $dbEditGame->new_data;
        $game->save();

        // Credit points
        $pointsToAdd = UserPointTransaction::POINTS_DB_EDIT;
        UserFactory::addToPointsBalance($user, $pointsToAdd);

        // Store the transaction
        $params = UserPointTransactionDirectorFactory::buildParams(
            $userId,
            UserPointTransaction::ACTION_DB_CATEGORY,
            $dbEditGame->game_id,
            $pointsToAdd,
            null
        );
        UserPointTransactionDirectorFactory::createNew($params);

        $data = array(
            'status' => 'OK'
        );
        return response()->json($data, 200);
    }

    public function deny()
    {
        $serviceUser = $this->getServiceUser();
        $serviceDbEdit = $this->getServiceDbEditGame();

        $userId = $this->getAuthId();

        $user = $serviceUser->find($userId);
        if (!$user) {
            return response()->json(['error' => 'Cannot find user!'], 400);
        }

        $request = request();

        $itemId = $request->itemId;
        if (!$itemId) {
            return response()->json(['error' => 'Missing data: itemId'], 400);
        }

        $dbEditGame = $serviceDbEdit->find($itemId);
        if (!$dbEditGame) {
            return response()->json(['error' => 'Record not found: '.$itemId], 400);
        }

        if (!$dbEditGame->isPending()) {
            return response()->json(['error' => 'Record is not pending'], 400);
        }

        // Deny item
        $dbEditGame->setDenied();
        $dbEditGame->save();

        $data = array(
            'status' => 'OK'
        );
        return response()->json($data, 200);
    }
}
