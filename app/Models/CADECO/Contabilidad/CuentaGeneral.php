<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 16/01/2019
 * Time: 01:12 PM
 */

namespace App\Models\CADECO\Contabilidad;


class CuentaGeneral extends CuentaContable
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereHas('tipo', function ($query) {
                return $query->where('tipo', '=', 1);
            });
        });

        self::creating(function ($model) {
            $model->estatus = 1;
        });
    }

    protected $fillable = [
        'cuenta_contable',
        'id_int_tipo_cuenta_contable'
    ];
}