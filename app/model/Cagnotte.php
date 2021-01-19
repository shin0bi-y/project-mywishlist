<?php

namespace mywishlist\model;

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Eloquent\Model;

class Cagnotte extends Model
{

    public $timestamps = false;
    protected $table = 'cagnotte';
    protected $primaryKey = 'idItem';

}