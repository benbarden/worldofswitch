<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\ReviewSiteService;
use App\Services\FeedItemReviewService;
use App\Services\Feed\Importer;

class RunFeedImporter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RunFeedImporter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the feed importer.';

    /**
     * Create a new command instance.
     *
     * @return void
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
        $reviewSiteService = resolve('Services\ReviewSiteService');
        $feedItemReviewService = resolve('Services\FeedItemReviewService');
        /* @var ReviewSiteService $reviewSiteService */
        /* @var FeedItemReviewService $feedItemReviewService */
        $reviewSites = $reviewSiteService->getFeedUrls();

        if (!$reviewSites) {
            $this->info('No sites found with feed URLs. Aborting.');
            return true;
        }

        foreach ($reviewSites as $reviewSite) {

            $siteId = $reviewSite->id;
            $siteName = $reviewSite->name;
            $feedUrl = $reviewSite->feed_url;
            if (!$feedUrl) continue;

            $this->info(sprintf('Site: %s - Feed URL: %s', $siteName, $feedUrl));

            try {

                // Set up the importer for this site
                $feedImporter = new Importer();
                $feedImporter->loadRemoteFeedData($feedUrl);
                $feedImporter->setSiteId($siteId);
                $feedData = $feedImporter->getFeedData();

                foreach ($feedData['channel']['item'] as $feedItem) {

                    // Generate the model
                    $feedItemReview = $feedImporter->generateModel($feedItem);
                    $itemTitle = $feedItemReview->item_title;
                    $itemUrl = $feedItemReview->item_url;

                    // Check if it's already been imported
                    $dbExistingItem = $feedItemReviewService->getByItemUrl($itemUrl);
                    if ($dbExistingItem) {
                        $this->warn('Already imported: '.$itemUrl);
                        continue;
                    }

                    // Output some details
                    $this->info('Importing item: '.$itemUrl);

                    // All good - add it as a feed item
                    $feedItemReview->load_status = 'Loaded OK';
                    $feedItemReview->save();

                }

            } catch (\Exception $e) {
                $this->error('Got error: '.$e->getMessage().'; skipping');
            }

        }

    }
}