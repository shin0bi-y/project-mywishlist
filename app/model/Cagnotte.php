<?php

namespace mywishlist\model;

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Eloquent\Model;

class Cagnotte extends Model
{

    public $timestamps = false;
    protected $table = 'cagnotte';
    protected $primaryKey = 'idItem';

    /**
     * Retourne les participants de la cagnotte
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participants() {
        return $this->hasMany('\mywishlist\model\Participant','idItem');
    }

}