<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 12:38 PM
 */

namespace App\Models\CADECO;


class OrdenCompra extends Transaccion
{
    public const TIPO_ANTECEDENTE = 17;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope('tipo',function ($query) {
            return $query->where('tipo_transaccion', '=', 19)
                ->where('opciones', '=', 1)
                ->where('estado', '!=', -2);
        });
    }

    public function empresa()
    {
        return $this->hasOne(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function pago_anticipado(){
        return $this->hasOne(SolicitudPagoAnticipado::class,'id_antecedente', 'id_transaccion');
    }

    public function scopeSinPagoAnticipado($query)
    {
        return $query->whereDoesntHave('pago_anticipado');
    }

    public function entradas_material(){
        return $this->hasMany(EntradaMaterial::class, 'id_antecedente','id_transaccion');
    }

    public function getNombre(){
        return 'ORDEN DE COMPRA';
    }

    public function getEncabezadoReferencia(){
        if (strlen($this->observaciones) > 100) {
           return utf8_encode(substr($this->observaciones, 0, 100));
        } else {
            return utf8_encode($this->observaciones);
        }
    }
}