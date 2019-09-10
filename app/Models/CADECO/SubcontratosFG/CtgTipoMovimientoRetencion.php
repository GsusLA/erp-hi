<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14/01/19
 * Time: 09:09 AM
 */

namespace App\Models\CADECO\SubcontratosFG;

use Illuminate\Database\Eloquent\Model;

class CtgTipoMovimientoRetencion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosFG.ctg_tipos_mov_ret';
    protected $fillable = ['descripcion'];
    public $timestamps = false;
}