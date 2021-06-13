<?php

namespace App\Http\Controllers\Staff\News;

use Illuminate\Routing\Controller as Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Domain\ViewBreadcrumbs\Staff as Breadcrumbs;

use App\Traits\SwitchServices;
use App\Traits\StaffView;

class CategoryController extends Controller
{
    use SwitchServices;
    use StaffView;

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var array
     */
    private $validationRules = [
        'name' => 'required|max:100',
        'link_name' => 'required|max:100',
    ];

    protected $viewBreadcrumbs;

    public function __construct(
        Breadcrumbs $viewBreadcrumbs
    )
    {
        $this->viewBreadcrumbs = $viewBreadcrumbs;
    }

    public function showList()
    {
        $bindings = $this->getBindings('News categories');
        $bindings['crumbNav'] = $this->viewBreadcrumbs->newsSubpage('News categories');

        $bindings['NewsCategoryList'] = $this->getServiceNewsCategory()->getAll();

        return view('staff.news.category.list', $bindings);
    }

    public function add()
    {
        $bindings = $this->getBindings('Add news category');
        $bindings['crumbNav'] = $this->viewBreadcrumbs->newsCategoriesSubpage('Add news category');

        $serviceNewsCategory = $this->getServiceNewsCategory();

        $request = request();

        if ($request->isMethod('post')) {

            $this->validate($request, $this->validationRules);

            $serviceNewsCategory->create($request->name, $request->link_name);

            return redirect(route('staff.news.category.list'));

        }

        $bindings['FormMode'] = 'add';

        return view('staff.news.category.add', $bindings);
    }

    public function edit($newsCategoryId)
    {
        $bindings = $this->getBindings('Edit news category');
        $bindings['crumbNav'] = $this->viewBreadcrumbs->newsCategoriesSubpage('Edit news category');

        $serviceNewsCategory = $this->getServiceNewsCategory();

        $newsCategory = $serviceNewsCategory->find($newsCategoryId);
        if (!$newsCategory) abort(404);

        $request = request();

        if ($request->isMethod('post')) {

            $bindings['FormMode'] = 'edit-post';

            $this->validate($request, $this->validationRules);

            $serviceNewsCategory->edit($newsCategory, $request->name, $request->link_name);

            return redirect(route('staff.news.category.list'));

        } else {

            $bindings['FormMode'] = 'edit';

        }

        $bindings['NewsCategory'] = $newsCategory;
        $bindings['NewsCategoryId'] = $newsCategoryId;

        return view('staff.news.category.edit', $bindings);
    }
}