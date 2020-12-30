<?php


namespace mywishlist\model;

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    public $timestamps = false;
    protected $table = 'message';
    protected $primaryKey = 'idMessage';

    public function liste() {
        return $this->belongsTo('\mywishlist\model\Liste','idList');
    }
}