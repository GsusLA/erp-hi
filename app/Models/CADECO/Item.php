<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 11/03/2019
 * Time: 07:15 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'items';
    protected $primaryKey = 'id_item';
    public $timestamps = false;

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'item_antecedente', 'id_concepto');
    }

    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto', 'id_concepto');
    }
}