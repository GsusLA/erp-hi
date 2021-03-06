<?php
/**
 * Created by PhpStorm.
 * User: dbenitezc
 * Date: 11/01/19
 * Time: 01:15 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Contabilidad\CuentaFondo;
use Illuminate\Database\Eloquent\Model;

class Fondo extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'fondos';
    protected $primaryKey = 'id_fondo';

    public $timestamps = false;
    public $searchable = [
        'descripcion',
        'saldo',
        'cuentaFondo.cuenta'
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    public function cuentaFondo()
    {
        return $this->hasOne(CuentaFondo::class, 'id_fondo', 'id_fondo')
            ->where('Contabilidad.cuentas_fondos.estatus', '=', 1);
    }

    public function scopeSinCuenta($query)
    {
        return $query->doesntHave('cuentaFondo');
    }

    public function scopeConCuenta($query)
    {
        return $query->has('cuentaFondo');
    }

}