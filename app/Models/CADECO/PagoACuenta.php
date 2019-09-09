<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:12 AM
 */

namespace App\Models\CADECO;


class PagoACuenta extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected $fillable = [
        'id_antecedente',
        'fecha',
        'id_obra',
        'cumplimiento',
        'vencimiento',
        'monto',
        'referencia',
        'observaciones',
        'tipo_transaccion',
        "id_cuenta",
        "id_empresa",
        "id_moneda",
        "saldo",
        "destino"
    ];
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 82)
                ->where('opciones', '=', 327681)
                ->where('estado', '!=', -2);
        });
    }
}
