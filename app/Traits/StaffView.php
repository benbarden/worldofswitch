<?php


namespace App\Traits;

use App\DataSource;

trait StaffView
{
    /**
     * @var array
     */
    private $breadcrumbs = [];

    /**
     * @var array
     */
    private $bindings = [];

    /**
     * @var string
     */
    private $pageTitle;

    /**
     * @var string
     */
    private $tableSort;

    public function getBindings($pageTitle, $topTitle = 'Staff')
    {
        $this->bindings = $this->getServiceViewHelperBindings()
            ->setPageTitle($pageTitle)
            ->setTopTitleSuffix($topTitle);

        if ($this->breadcrumbs) {
            $this->bindings = $this->bindings->setBreadcrumbs($this->breadcrumbs);
        }

        if ($this->tableSort) {
            $this->bindings = $this->bindings->setDatatablesSort($this->tableSort);
        } else {
            $this->bindings = $this->bindings->setDatatablesSortDefault();
        }

        return $this->bindings->getBindings();
    }

    public function setTableSort($tableSort)
    {
        $this->tableSort = $tableSort;
    }

    // ***** Games ***** //

    /**
     * @deprecated
     */
    public function getBindingsGamesSubpage($pageTitle, $tableSort = '')
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makeGamesSubPage($pageTitle);
        if ($tableSort) {
            $this->tableSort = $tableSort;
        }
        return $this->getBindings($pageTitle);
    }

    /**
     * @deprecated
     */
    public function getBindingsGamesDetailSubpage($pageTitle, $gameId)
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makeGamesDetailSubPage($pageTitle, $gameId);
        return $this->getBindings($pageTitle);
    }

    /**
     * @deprecated
     */
    public function getBindingsGamesTitleHashesSubpage($pageTitle)
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makeGamesTitleHashesSubPage($pageTitle);
        return $this->getBindings($pageTitle);
    }

    /**
     * @deprecated
     */
    public function getBindingsGamesFeaturedGamesSubpage($pageTitle)
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makeGamesFeaturedGamesSubPage($pageTitle);
        return $this->getBindings($pageTitle);
    }

    // ***** Reviews ***** //

    /**
     * @deprecated
     */
    public function getBindingsReviewsSubpage($pageTitle, $tableSort = '')
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makeReviewsSubPage($pageTitle);
        if ($tableSort) {
            $this->tableSort = $tableSort;
        }
        return $this->getBindings($pageTitle);
    }

    /**
     * @deprecated
     */
    public function getBindingsReviewsLinkListSubpage($pageTitle)
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makeReviewsLinkListSubPage($pageTitle);
        return $this->getBindings($pageTitle);
    }

    /**
     * @deprecated
     */
    public function getBindingsReviewsQuickReviewsSubpage($pageTitle)
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makeReviewsQuickReviewsSubPage($pageTitle);
        return $this->getBindings($pageTitle);
    }

    /**
     * @deprecated
     */
    public function getBindingsReviewsCampaignsSubpage($pageTitle)
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makeReviewsCampaignsSubPage($pageTitle);
        return $this->getBindings($pageTitle);
    }

    /**
     * @deprecated
     */
    public function getBindingsReviewsFeedItemsSubpage($pageTitle)
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makeReviewsFeedItemsSubPage($pageTitle);
        return $this->getBindings($pageTitle);
    }

    /**
     * @deprecated
     */
    public function getBindingsReviewsFeedImportsSubpage($pageTitle)
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makeReviewsFeedImportsSubPage($pageTitle);
        return $this->getBindings($pageTitle);
    }

    /**
     * @deprecated
     */
    public function getBindingsReviewsUnrankedByReleaseYearSubpage($pageTitle, $tableSort = '')
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makeReviewsUnrankedByReleaseYearSubPage($pageTitle);
        if ($tableSort) {
            $this->tableSort = $tableSort;
        }
        return $this->getBindings($pageTitle);
    }

    /**
     * @deprecated
     */
    public function getBindingsReviewsUnrankedByReviewCountSubpage($pageTitle, $tableSort = '')
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makeReviewsUnrankedByReviewCountSubPage($pageTitle);
        if ($tableSort) {
            $this->tableSort = $tableSort;
        }
        return $this->getBindings($pageTitle);
    }

    // ***** Partners ***** //

    /**
     * @deprecated
     */
    public function getBindingsPartnersSubpage($pageTitle, $tableSort = '')
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makePartnersSubPage($pageTitle);
        if ($tableSort) {
            $this->tableSort = $tableSort;
        }
        return $this->getBindings($pageTitle);
    }

    /**
     * @deprecated
     */
    public function getBindingsReviewSitesSubpage($pageTitle)
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makeReviewSitesSubPage($pageTitle);
        return $this->getBindings($pageTitle);
    }

    /**
     * @deprecated
     */
    public function getBindingsFeedLinksSubpage($pageTitle)
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makeFeedLinksSubPage($pageTitle);
        return $this->getBindings($pageTitle);
    }

    /**
     * @deprecated
     */
    public function getBindingsGamesCompaniesSubpage($pageTitle)
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makeGamesCompaniesSubPage($pageTitle);
        return $this->getBindings($pageTitle);
    }

    /**
     * @deprecated
     */
    public function getBindingsPartnersOutreachSubpage($pageTitle)
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makePartnersOutreachSubPage($pageTitle);
        return $this->getBindings($pageTitle);
    }

    // ***** Data sources ***** //

    /**
     * @deprecated
     */
    public function getBindingsDataSourcesSubpage($pageTitle, $tableSort = '')
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makeDataSourcesSubPage($pageTitle);
        if ($tableSort) {
            $this->tableSort = $tableSort;
        }
        return $this->getBindings($pageTitle);
    }

    /**
     * @deprecated
     */
    public function getBindingsDataSourcesListRawSubpage($pageTitle, DataSource $dataSource)
    {
        $this->breadcrumbs = $this->getServiceViewHelperStaffBreadcrumbs()->makeDataSourcesListRawSubPage($pageTitle, $dataSource);
        return $this->getBindings($pageTitle);
    }
}