<?php


namespace mywishlist\model;

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Eloquent\Model;

class Liste extends Model
{

    public $timestamps = false;
    protected $table = 'list';
    protected $primaryKey = 'idList';

    /**
     * Retourne les items de la liste
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items() {
        return $this->hasMany('\mywishlist\model\Item','idList');
    }

    /**
     * Retourne les messages de la liste
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages() {
        return $this->hasMany('\mywishlist\model\Message','idList');
    }
}