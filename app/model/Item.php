<?php

namespace mywishlist\model;

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{

    public $timestamps = false;
    protected $table = 'item';
    protected $primaryKey = 'idItem';

    /**
     * Retourne la liste correspondant a l'item
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function liste() {
        return $this->belongsTo('\mywishlist\model\Liste','idList');
    }

}