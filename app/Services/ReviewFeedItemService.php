<?php


namespace App\Services;

use App\ReviewFeedItem;

use Illuminate\Support\Facades\DB;

class ReviewFeedItemService
{
    private $processOptionsSuccess = [
        'Review created',
    ];

    private $processOptionsFailure = [
        'Bundle',
        'DLC or special edition',
        'Duplicate',
        'Historic review',
        'Multiple reviews',
        'No score',
        'Non-review content',
        'Not a game review',
        'Not in database',
        'Page not found',
        'Review for another platform',
        'Review pre-dates Switch version',
        'Skipping - posted elsewhere',
        'Wrong link',
    ];

    public function edit(
        ReviewFeedItem $reviewFeedItem, $siteId, $gameId, $itemRating, $processStatus
    )
    {
        if ($processStatus) {
            $isProcessed = 1;
        } else {
            $isProcessed = null;
            $processStatus = null;
        }

        $reviewFeedItem->site_id = $siteId;
        $reviewFeedItem->game_id = $gameId;
        $reviewFeedItem->item_rating = $itemRating;
        $reviewFeedItem->processed = $isProcessed;
        $reviewFeedItem->process_status = $processStatus;
        $reviewFeedItem->save();
    }

    public function find($id)
    {
        return ReviewFeedItem::find($id);
    }

    public function getByItemUrl($itemUrl)
    {
        return ReviewFeedItem::where('item_url', $itemUrl)->first();
    }

    public function getItemsToParse($limit = null)
    {
        $limit = (int) $limit;
        if ($limit) {
            $reviewFeedItem = ReviewFeedItem::
                whereNull('parsed')
                ->orderBy('item_date', 'asc')
                ->limit($limit)
                ->get();
        } else {
            $reviewFeedItem = ReviewFeedItem::
                whereNull('parsed')
                ->orderBy('id', 'asc')
                ->get();
        }

        return $reviewFeedItem;
    }

    public function getAll()
    {
        return ReviewFeedItem::orderBy('id', 'desc')->get();
    }

    public function getUnprocessed()
    {
        return ReviewFeedItem::whereNull('processed')->orderBy('id', 'asc')->get();
    }

    public function getProcessed($limit = 25)
    {
        $reviewFeedItem = ReviewFeedItem::where('processed', 1)->orderBy('id', 'desc');

        if ($limit) {
            $reviewFeedItem = $reviewFeedItem->limit($limit);
        }

        $reviewFeedItem = $reviewFeedItem->get();
        return $reviewFeedItem;
    }

    public function getByProcessStatus($status)
    {
        return ReviewFeedItem::where('process_status', $status)->orderBy('id', 'desc')->get();
    }

    public function getByImportId($importId)
    {
        return ReviewFeedItem::where('import_id', $importId)->orderBy('id', 'desc')->get();
    }

    public function getProcessStatusStats()
    {
        return DB::select('
            select process_status, count(*) AS count
            from review_feed_items
            where process_status is not null
            group by process_status
        ');
    }

    public function getProcessOptionsSuccess()
    {
        return $this->processOptionsSuccess;
    }

    public function getProcessOptionsFailure()
    {
        return $this->processOptionsFailure;
    }
}