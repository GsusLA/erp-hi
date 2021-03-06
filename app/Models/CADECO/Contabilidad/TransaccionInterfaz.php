<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 26/12/18
 * Time: 05:33 PM
 */

namespace App\Models\CADECO\Contabilidad;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaccionInterfaz extends Model
{
    use softDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.int_transacciones_interfaz';
    protected $primaryKey = 'id_transaccion_interfaz';

    public function polizas()
    {
        return $this->hasMany(Poliza::class, 'id_tipo_poliza_interfaz');
    }

    public function scopeUsadas($query)
    {
        return $query->has('polizas', '>', 0);
    }
}