<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/08/2019
 * Time: 10:33 AM
 */

namespace App\Models\CADECO\FinanzasCBE;


class SolicitudAlta extends Solicitud
{
    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        self::addGlobalScope(function ($query) {
            return $query->where('id_tipo_solicitud', '=', '1');
        });
    }
}