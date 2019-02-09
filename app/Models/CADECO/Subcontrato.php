<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 06/02/2019
 * Time: 03:55 PM
 */

namespace App\Models\CADECO;
use App\Models\CADECO\SubcontratosFG\FondoGarantia;

class Subcontrato extends Transaccion
{
    public const TIPO_ANTECEDENTE = 49;

    protected $fillable = [
        'id_antecedente',
        'fecha',
        'id_obra',
        'id_empresa',
        'id_moneda',
        'anticipo',
        'anticipo_monto',
        'anticipo_saldo',
        'monto',
        'PorcentajeDescuento',
        'impuesto',
        'impuesto_retenido',
        'id_costo',
        'retencion',
        'referencia',
        'observaciones',
    ];
    protected $with = array('fondo_garantia', 'estimacion');
    public $usuario_registra = 777;
    protected static function boot()
    {
        parent::boot();
        self::creating(function ($subcontrato) {
            $subcontrato->tipo_transaccion = 51;
            $subcontrato->opciones = 2;
        });
        self::created(function ($subcontrato) {
            if ($subcontrato->retencion > 0) {
                $subcontrato->generaFondoGarantia();
            }
        });
    }

    public function estimacion(){
        return $this->hasMany(Estimacion::class,'id_antecedente','id_transaccion');
    }

    public function fondo_garantia()
    {
        return $this->hasOne(FondoGarantia::class, 'id_subcontrato', 'id_transaccion');
    }

    public function generaFondoGarantia()
    {
        if(is_null($this->fondo_garantia))
        {
            if ($this->retencion > 0) {
                $fondo_garantia = new FondoGarantia();
                $fondo_garantia->id_subcontrato = $this->id_transaccion;
                $fondo_garantia->usuario_registra = $this->usuario_registra;
                $fondo_garantia->save();
                $this->refresh();
            } else {
                throw New \Exception('El subcontrato no tiene establecido un porcentaje de retención de fondo de garantía, el fondo de garantía no puede generarse');
            }
        }
   }


}