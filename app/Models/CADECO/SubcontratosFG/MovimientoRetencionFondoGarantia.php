<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14/01/19
 * Time: 09:09 AM
 */

namespace App\Models\CADECO\SubcontratosFG;

use Illuminate\Database\Eloquent\Model;

class MovimientoRetencionFondoGarantia extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosFG.ret_movimientos';
    protected $fillable = [ 'id_retencion',
                            'id_movimiento_antecedente',
                            'id_tipo_movimiento',
                            'usuario_registra',
                            ];
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        self::creating(function($movimiento_retencion)
        {
            $movimiento_retencion->created_at = date('Y-m-d h:i:s');
        });

        self::created(function($movimiento_retencion)
        {

            $movimiento_retencion->retencion->estimacion->subcontrato->fondo_garantia->generaMovimientoRetencion($movimiento_retencion);

        });

    }

    public function retencion()
    {
        return $this->belongsTo(RetencionFondoGarantia::class,'id_retencion');
    }

    public function tipo()
    {
        return $this->belongsTo(CtgTipoMovimientoRetencion::class,"id_tipo_movimiento");
    }

}