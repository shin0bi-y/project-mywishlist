<?php


namespace mywishlist\model;

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Eloquent\Model;

class Liste extends Model
{

    public $timestamps = false;
    protected $table = 'liste';
    protected $primaryKey = 'id';

}