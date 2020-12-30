<?php

namespace mywishlist\model;

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{

    public $timestamps = false;
    protected $table = 'item';
    protected $primaryKey = 'idItem';

    public function liste() {
        return $this->belongsTo('\mywishlist\model\Liste','idList');
    }

}