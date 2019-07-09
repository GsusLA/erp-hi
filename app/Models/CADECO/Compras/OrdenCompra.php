<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 12:38 PM
 */

namespace App\Models\CADECO\Compras;


use App\Facades\Context;

use App\Models\CADECO\Compras\OrdenCompraPartida;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Compras\OrdenCompraComplemento;
use App\Models\CADECO\Compras\SolicitudCompra;
use App\Models\CADECO\Solicitud;
use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\CADECO\Transaccion;
use Ghi\Domain\Core\Models\Compras\Cotizacion\CotizacionCompra;
use App\Models\CADECO\Obra;

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
    public function cotizacion(){
        return $this->belongsTo(CotizacionCompra::class, 'id_referente', 'id_transaccion');
    }

    public function pago_anticipado(){
        return $this->hasOne(SolicitudPagoAnticipado::class,'id_antecedente', 'id_transaccion');
    }

    public function scopeSinPagoAnticipado($query)
    {
        return $query->whereDoesntHave('pago_anticipado');
    }
    public function ordenCompraVersiones()
    {
        return $this->hasMany(OrdenCompraVersiones::class, 'id_transaccion', 'id_transaccion');
    }

//    public function requisicion(){
//        return $this->hasOne(SolicitudCompra::class, 'id_transaccion', 'id_antecedente');
//    }

    public function entradas_material(){
        return $this->hasMany(EntradaMaterial::class, 'id_antecedente','id_transaccion');
    }

    public function solicitud(){
        return $this->hasOne(Solicitud::class, 'id_transaccion', 'id_antecedente');
    }

    public function getNombre(){
        return 'ORDEN DE COMPRA';
    }
    public function complemento(){
        return $this->hasOne(OrdenCompraComplemento::class, 'id_transaccion');
    }

    public function partidas(){
        return $this->hasMany(OrdenCompraPartida::class,'id_transaccion','id_transaccion');
    }
    public function obra()
    {
        return $this->hasOne(Obra::class, 'id_obra', 'id_obra');
    }



    public function getEncabezadoReferencia(){
        if (strlen($this->observaciones) > 100) {
            return utf8_encode(substr($this->observaciones, 0, 100));
        } else {
            return utf8_encode($this->observaciones);
        }
    }
}