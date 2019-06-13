<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:08 AM
 */

namespace App\Models\CADECO;


class ContraRecibo extends Transaccion
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 67)
                ->where('opciones', '=', 0)
                ->where('estado', '!=', -2);
        });
    }
}