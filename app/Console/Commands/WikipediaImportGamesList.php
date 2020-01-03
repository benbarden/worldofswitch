<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use App\GameImportRuleWikipedia;
use App\Traits\WosServices;

use App\Services\CrawlerWikipediaGamesListSourceService;
use App\Services\GameService;
use App\Services\GameReleaseDateService;
use App\Services\GameTitleHashService;
use App\Services\FeedItemGameService;
use App\Services\HtmlLoader\Wikipedia\Importer;
use App\Services\HtmlLoader\Wikipedia\Skipper;
use App\Services\HtmlLoader\Wikipedia\DateHandler;

class WikipediaImportGamesList extends Command
{
    use WosServices;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'WikipediaImportGamesList';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports feed items from Wikipedia crawl data, ready to update the games database.';

    /**
     * WikipediaImportGamesList constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $logger = Log::channel('cron');

        $logger->info(' *************** '.$this->signature.' *************** ');

        $logger->info('Loading source data...');

        $crawlerService = resolve('Services\CrawlerWikipediaGamesListSourceService');
        /* @var CrawlerWikipediaGamesListSourceService $crawlerService */
        $gameTitleHashService = resolve('Services\GameTitleHashService');
        /* @var GameTitleHashService $gameTitleHashService */
        $feedItemGameService = resolve('Services\FeedItemGameService');
        /* @var FeedItemGameService $feedItemGameService */
        $gameService = resolve('Services\GameService');
        /* @var GameService $gameService */
        $gameReleaseDateService = resolve('Services\GameReleaseDateService');
        /* @var GameReleaseDateService $gameReleaseDateService */

        $wikiSkipper = new Skipper();
        $dateHandler = new DateHandler();

        $gamesList = $crawlerService->getAll();

        $logger->info('Found '.count($gamesList).' item(s)');

        try {

            $importer = new Importer();

            $i = 0;

            foreach ($gamesList as $crawlerModel) {

                $i++;

                $logItemPrefix = "Item {$i}: {$crawlerModel->title} - ";

                $importer->setCrawlerModel($crawlerModel);
                $feedItemGame = $importer->generateFeedModel();
                $title = $feedItemGame->item_title;

                // Discard games with only TBA/Unreleased dates
                $countTBAOrUnreleased = $wikiSkipper->countDatesTBAOrUnreleased($feedItemGame);
                if ($countTBAOrUnreleased == 3) {
                    //$logger->error($logItemPrefix.'All dates are TBA or Unreleased; skipping');
                    continue;
                }

                // Discard games containing certain text
                $skipText = $wikiSkipper->getSkipText($title);
                if ($skipText != null) {
                    //$logger->error($logItemPrefix.'Found ignore text: '.$skipText.'; skipping');
                    continue;
                }

                // Discard games without any dates
                $countRealDates = $wikiSkipper->countRealDates($feedItemGame, $dateHandler);
                if ($countRealDates == 0) {
                    /*
                    $logger->error(
                        $logItemPrefix.
                        'Found no real dates; skipping ('.
                        'EU: '.$feedItemGame->upcoming_date_eu.'; '.
                        'US: '.$feedItemGame->upcoming_date_us.'; '.
                        'JP: '.$feedItemGame->upcoming_date_jp.
                        ')');
                    */
                    continue;
                } elseif ($countRealDates == 1) {
                    // Check if we only have a Japanese release date
                    if ($dateHandler->getUpcomingDate($feedItemGame->upcoming_date_jp) == null) {
                        // This is the only record - skip for now
                        //$logger->error('Skipping as game only has a Japanese date');
                        continue;
                    }
                }
                
                // See if we can locate the game
                $titleHash = $gameTitleHashService->generateHash($title);
                $gameTitleHash = $gameTitleHashService->getByHash($titleHash);
                if ($gameTitleHash) {
                    $gameId = $gameTitleHash->game_id;
                    $feedItemGame->game_id = $gameId;
                } else {
                    $gameId = null;
                }

                // Check if anything's changed since the last record, if one exists
                if ($gameId) {

                    // Get game import rule
                    $gameImportRule = $this->getServiceGameImportRuleWikipedia()->getByGameId($gameId);
                    if (!$gameImportRule) {
                        $gameImportRule = new GameImportRuleWikipedia;
                    }

                    // Let's start by checking if the game has actually changed
                    $game = $gameService->find($gameId);
                    $gameReleaseDates = $gameReleaseDateService->getByGame($gameId);
                    $modifiedFieldList = $importer->getGameModifiedFields($feedItemGame, $game, $gameReleaseDates, $gameImportRule);

                    if (count($modifiedFieldList) == 0) {

                        // No changes - skip and move right along
                        continue;

                    } else {

                        // Something's changed!
                        $logger->info($logItemPrefix.'Changes found');

                        // What has changed, exactly?
                        $sModifiedFields = serialize($modifiedFieldList);

                        // Now, before we continue... is there an existing record?
                        $activeFeedItem = $feedItemGameService->getActiveByGameId($gameId);
                        if ($activeFeedItem) {
                            // Yes there is... is it the same as the latest changes?
                            if ($activeFeedItem->modified_fields == $sModifiedFields) {
                                $logger->warn($logItemPrefix.'Found a pending entry with the same details; skipping');
                                continue;
                            } else {
                                // We should close off the old one before continuing
                                $logger->warn($logItemPrefix.'Found a pending entry with different details - closing it off.');
                                $activeFeedItem->setStatusSkippedSuperseded();
                                $activeFeedItem->save();
                            }
                        }

                        $feedItemGame->modified_fields = $sModifiedFields;

                    }

                } else {
                    // If we don't know the game id and we haven't dealt with the imported data yet,
                    // duplicates will be added each time the importer is run.
                    // This check will stop those duplicates if an update is pending.
                    $activeFeedItem = $feedItemGameService->getActiveByTitle($title);
                    if ($activeFeedItem) {
                        $logger->warn($logItemPrefix.'Found an active, unprocessed entry; skipping');
                        continue;
                    }
                }

                // If we get this far, all of the necessary checks have passed.
                // Saving the model will create an entry in feed_item_games.

                $feedItemGame->save();

            }

        } catch (\Exception $e) {
            $logger->error($e->getMessage());
        }
    }
}
