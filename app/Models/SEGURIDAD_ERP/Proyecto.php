<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 10/12/18
 * Time: 06:27 PM
 */

namespace App\Models\SEGURIDAD_ERP;

use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'proyectos';

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('base_datos', '=', Context::getDatabase());
        });
    }
}