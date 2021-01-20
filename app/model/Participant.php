<?php

namespace mywishlist\model;

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{

    public $timestamps = false;
    protected $table = 'participantCagnotte';
    protected $primaryKey = 'idParticipation';

}