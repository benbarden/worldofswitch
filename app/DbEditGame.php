<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DbEditGame extends Model
{
    const DATA_CATEGORY = 'category';

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_DENIED = 2;

    protected $table = 'db_edits_games';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'game_id', 'data_to_update', 'current_data', 'new_data', 'status', 'point_transaction_id'
    ];

    /**
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * @var array
     */
    protected $casts = [
    ];

    public function game()
    {
        return $this->hasOne('App\Game', 'id', 'game_id');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function pointTransaction()
    {
        return $this->hasOne('App\UserPointTransaction', 'id', 'point_transaction_id');
    }

    public function newDataAsCategory()
    {
        return $this->hasOne('App\Category', 'id', 'new_data');
    }

    public function isPending()
    {
        return $this->status == self::STATUS_PENDING;
    }

    public function setApproved()
    {
        $this->status = self::STATUS_APPROVED;
    }

    public function setDenied()
    {
        $this->status = self::STATUS_DENIED;
    }

    public function getStatusDesc()
    {
        $statusDesc = null;

        switch ($this->status) {
            case self::STATUS_PENDING:
                $statusDesc = 'Pending';
                break;
            case self::STATUS_APPROVED:
                $statusDesc = 'Approved';
                break;
            case self::STATUS_DENIED:
                $statusDesc = 'Denied';
                break;
            default:
                $statusDesc = 'Unknown';
                break;
        }

        return $statusDesc;
    }
}
