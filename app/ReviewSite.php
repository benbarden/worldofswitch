<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReviewSite extends Model
{
    const SITE_WOS = 1;
    const SITE_SWITCH_PLAYER = 2;
    const SITE_IGN = 3;
    const SITE_NINTENDO_LIFE = 4;
    const SITE_GAMESPEW = 5;
    const SITE_GAMESPOT = 6;
    const SITE_NINTENDO_WORLD_REPORT = 8;
    const SITE_CUBED3 = 9;
    const SITE_VIDEO_CHUMS = 11;
    const SITE_GOD_IS_A_GEEK = 12;
    const SITE_PURE_NINTENDO = 13;
    const SITE_DIGITALLY_DOWNLOADED = 14;
    const SITE_DESTRUCTOID = 15;
    const SITE_NINTENDO_INSIDER = 17;
    const SITE_MIKETENDO64 = 18;
    const SITE_NINDIE_SPOTLIGHT = 19;
    const SITE_THE_SWITCH_EFFECT = 20;
    const SITE_100_HOUR_REVIEWS = 21; // NB: No feed
    const SITE_THE_NEW_ODYSSEY = 22;
    const SITE_SWITCHWATCH = 23;
    const SITE_NINTENDO_NOMAD = 24;
    const SITE_TWO_BEARD_GAMING = 25;
    const SITE_SWITCH_INDIE_FIX = 26;
    const SITE_SIDE_QUEST_VGM = 27;
    const SITE_NINTENDAD = 28;
    const SITE_RAPID_REVIEWS_UK = 29;

    /**
     * @var string
     */
    protected $table = 'review_sites';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'link_title', 'url', 'feed_url', 'active', 'rating_scale',
        'allow_historic_content', 'title_match_rule_pattern', 'title_match_index',
        'feed_url_prefix'
    ];

    public function allowHistoric()
    {
        return $this->allow_historic_content == 1;
    }

    public function links()
    {
        return $this->hasMany('App\ReviewLink', 'id', 'site_id');
    }

}
