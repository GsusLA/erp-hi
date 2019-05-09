<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 12:47 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Finanzas\TransaccionRubro;

class SolicitudPagoAnticipado extends Transaccion
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 72)
                ->where('opciones', '=', 327681)
                ->where('estado', '!=', -2);
        });

        self::creating(function ($solicitud) { // agregar los nuevos campos
            $solicitud->tipo_transaccion = 72;
            $solicitud->opciones = 327681;
        });
    }

    public function transaccion_rubro(){
        return $this->hasOne(TransaccionRubro::class, 'id_transaccion', 'id_transaccion');
    }
}