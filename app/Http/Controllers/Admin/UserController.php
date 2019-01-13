<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller as Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Services\ServiceContainer;

class UserController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var array
     */
    private $validationRules = [
        'display_name' => 'required',
        'email' => 'required',
    ];

    public function showList()
    {
        $serviceContainer = \Request::get('serviceContainer');
        /* @var $serviceContainer ServiceContainer */

        $userService = $serviceContainer->getUserService();

        $bindings = [];

        $bindings['TopTitle'] = 'Admin - Users';
        $bindings['PageTitle'] = 'Users';

        $userList = $userService->getAll();

        $bindings['UserList'] = $userList;

        return view('admin.user.list', $bindings);
    }

    public function showUser($userId)
    {
        $serviceContainer = \Request::get('serviceContainer');
        /* @var $serviceContainer ServiceContainer */

        $userService = $serviceContainer->getUserService();
        $collectionService = $serviceContainer->getUserGamesCollectionService();

        $userData = $userService->find($userId);
        if (!$userData) abort(404);

        $displayName = $userData->display_name;

        $bindings = [];

        $bindings['TopTitle'] = 'Admin - View user - '.$displayName;
        $bindings['PageTitle'] = 'View user - '.$displayName;

        $bindings['UserData'] = $userData;

        $bindings['CollectionList'] = $collectionService->getByUser($userId);
        $bindings['CollectionStats'] = $collectionService->getStats($userId);

        return view('admin.user.view', $bindings);
    }

    public function editUser($userId)
    {
        $serviceContainer = \Request::get('serviceContainer');
        /* @var $serviceContainer ServiceContainer */

        $regionCode = \Request::get('regionCode');

        $serviceUser = $serviceContainer->getUserService();
        $serviceReviewSite = $serviceContainer->getReviewSiteService();
        $serviceDeveloper = $serviceContainer->getDeveloperService();
        $servicePublisher = $serviceContainer->getPublisherService();

        $userData = $serviceUser->find($userId);
        if (!$userData) abort(404);

        $request = request();

        $bindings = [];

        if ($request->isMethod('post')) {

            $bindings['FormMode'] = 'edit-post';

            $this->validate($request, $this->validationRules);

            $displayName = $request->display_name;
            $email = $request->email;
            $siteId = $request->site_id;
            $developerId = $request->developer_id;
            $publisherId = $request->publisher_id;

            $serviceUser->edit($userData, $displayName, $email, $siteId, $developerId, $publisherId);

            return redirect(route('admin.user.list'));

        } else {

            $bindings['FormMode'] = 'edit';

        }

        $bindings['TopTitle'] = 'Admin - Edit user';
        $bindings['PageTitle'] = 'Edit user';
        $bindings['UserData'] = $userData;
        $bindings['UserId'] = $userId;

        $bindings['ReviewSites'] = $serviceReviewSite->getAll();
        $bindings['Developers'] = $serviceDeveloper->getAll();
        $bindings['Publishers'] = $servicePublisher->getAll();

        return view('admin.user.edit', $bindings);
    }
}