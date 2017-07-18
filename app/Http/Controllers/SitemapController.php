<?php

namespace App\Http\Controllers;

use App\Services\GameService;

class SitemapController extends BaseController
{
    public function show()
    {
        $bindings = array();

        $now = new \DateTime('now');
        $timestamp = $now->format('c');
        $bindings['TimestampNow'] = $timestamp;

        $sitePages = array();
        $sitePages[] = array('url' => route('welcome'), 'lastmod' => $timestamp, 'changefreq' => 'weekly', 'priority' => '1.0');
        $sitePages[] = array('url' => route('games.list.released'), 'lastmod' => $timestamp, 'changefreq' => 'weekly', 'priority' => '0.8');
        $sitePages[] = array('url' => route('games.list.upcoming'), 'lastmod' => $timestamp, 'changefreq' => 'weekly', 'priority' => '0.8');
        $sitePages[] = array('url' => route('games.list.topRated'), 'lastmod' => $timestamp, 'changefreq' => 'weekly', 'priority' => '0.8');
        $sitePages[] = array('url' => route('games.list.reviewsNeeded'), 'lastmod' => $timestamp, 'changefreq' => 'weekly', 'priority' => '0.8');

        $sitePages[] = array('url' => route('charts.landing'), 'lastmod' => $timestamp, 'changefreq' => 'weekly', 'priority' => '0.8');

        $chartsDateService = resolve('Services\ChartsDateService');
        $chartDatesEu = $chartsDateService->getDateList('eu');
        $chartDatesUs = $chartsDateService->getDateList('us');
        foreach ($chartDatesEu as $chartDate) {
            $sitePages[] = array(
                'url' => route('charts.date', ['date' => $chartDate->chart_date]),
                'lastmod' => $timestamp,
                'changefreq' => 'weekly',
                'priority' => '0.8'
            );
        }
        foreach ($chartDatesUs as $chartDate) {
            $sitePages[] = array(
                'url' => route('charts.us.date', ['date' => $chartDate->chart_date]),
                'lastmod' => $timestamp,
                'changefreq' => 'weekly',
                'priority' => '0.8'
            );
        }

        $sitePages[] = array('url' => route('charts.mostAppearances'), 'lastmod' => $timestamp, 'changefreq' => 'weekly', 'priority' => '0.8');
        $sitePages[] = array('url' => route('charts.gamesAtPositionLanding'), 'lastmod' => $timestamp, 'changefreq' => 'weekly', 'priority' => '0.8');
        for ($chartPos = 1; $chartPos <= 15; $chartPos++) {
            $sitePages[] = array('url' => route('charts.gamesAtPosition', ['position' => "$chartPos"]), 'lastmod' => $timestamp, 'changefreq' => 'weekly', 'priority' => '0.8');
        }

        $bindings['SitePages'] = $sitePages;

        $gameService = resolve('Services\GameService');
        /* @var $gameService GameService */
        $gameList = $gameService->getAll();

        $bindings['GameList'] = $gameList;

        return response()->view('sitemap.index', $bindings)->header('Content-Type', 'text/xml');
    }
}