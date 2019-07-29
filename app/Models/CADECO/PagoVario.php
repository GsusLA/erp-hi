<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:11 AM
 */

namespace App\Models\CADECO;


class PagoVario extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected $fillable = [
        'id_antecedente',
        'id_referente',
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
        "estado",
        "destino"
    ];
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 82)
                ->where('opciones', '=', 1)
                ->where('estado', '!=', -2);
        });
        self::creating(function ($model) {
            $model->tipo_transaccion = 82;
            $model->opciones = 1;
            $model->fecha = date('Y-m-d');
            $model->cumplimiento =  date('Y-m-d');
            $model->vencimiento = date('Y-m-d');
        });
    }
}
