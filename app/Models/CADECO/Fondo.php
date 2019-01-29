<?php
/**
 * Created by PhpStorm.
 * User: dbenitezc
 * Date: 11/01/19
 * Time: 01:15 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class Fondo extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'fondos';
    protected $primaryKey = 'id_fondo';
    public $timestamps =false;

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query){
            return $query->where('id_obra','=',Context::getIdObra());
        });
    }

}