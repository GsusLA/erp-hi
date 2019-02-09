<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14/01/19
 * Time: 09:09 AM
 */

namespace App\Models\CADECO\SubcontratosFG;

use Illuminate\Database\Eloquent\Model;

class SolicitudMovimientoFondoGarantia extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosFG.solicitudes';
    protected $fillable = ['id_fondo_garantia',
                            'id_tipo_solicitud',
                            'fecha',
                            'referencia',
                            'importe',
                            'observaciones',
                            'usuario_registra'
                            ];
    public $timestamps = false;
    protected static function boot()
    {
        parent::boot();

        self::creating(function ($solicitud_movimiento_fg) {
            $solicitud_movimiento_fg->created_at = date('Y-m-d h:i:s');
            if (!$solicitud_movimiento_fg->validaNoSolicitudesPendientes()) {
                throw New \Exception('Hay una solicitud de movimiento a fondo de garantía pendiente de autorizar, la solicitud actual no puede registrarse');
            }
            if (!$solicitud_movimiento_fg->validaMontoSolicitud()) {
                throw New \Exception('El monto de la solicitud sobrepasa el monto disponible del fondo de garantía.');
            }
        });
        self::created(function($solicitud_movimiento_fg){
            $solicitud_movimiento_fg->generaMovimientoRegistro();
        });
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoSolicitudMovimientoFondoGarantia::class,"id_solicitud");

    }

    public function fondo_garantia()
    {
        return $this->belongsTo(FondoGarantia::class,'id_fondo_garantia');
    }

    public function tipo()
    {
        return $this->belongsTo(CtgTipoSolicitud::class,"id_tipo_solicitud");
    }


    /**
     * No puede haber más de una solicitud de movimiento a fondo de garantía con estado 0 (Generada)
     * @return bool
     */

    private function validaNoSolicitudesPendientes()
    {
       $solicitudes = SolicitudMovimientoFondoGarantia::where("id_fondo_garantia",$this->id_fondo_garantia)->where("estado",0)->get();
       if(count($solicitudes)>0)
       {
           return false;
       }
       return true;
    }

    /**
     * El  monto de la solicitud no puede ser mayor al monto disponible del fondo de garantía
     * @return bool
     */
    private function validaMontoSolicitud()
    {

        $this->refresh();
        $monto_disponible = $this->fondo_garantia->saldo;
        if($monto_disponible < $this->importe){
            return false;
        }
        return true;
    }

    protected function generaMovimientoRegistro()
    {
         MovimientoSolicitudMovimientoFondoGarantia::create([
            'id_solicitud'=>$this->id,
             'id_tipo_movimiento'=>1,
             'usuario_registra'=>$this->usuario_registra
             ]
        );

        $this->refresh();
    }

    /**
     * Mètodo para cancelar solicitud de movimiento
     */
    public function cancelar()
    {

    }

    public function autorizar()
    {
        #se genera movimiento de solicitud
        MovimientoSolicitudMovimientoFondoGarantia::create([
                'id_solicitud'=>$this->id,
                'id_tipo_movimiento'=>2,
                'usuario_registra'=>$this->usuario_registra
            ]
        );
        #se actualiza estado de solicitud
        $this->actualizarEstado();

        #se genera movimiento de fondo de garantia

        #se genera transacción de movimiento a fondo de garantia
    }

    /**
     * Se actualiza el estado de la solicitud de acuerdo a su último movimiento registrado
     */
    private function actualizarEstado()
    {
        $ultimo_movimiento = $this->movimientos()->latest()->first();
        $this->estado = $ultimo_movimiento->tipo->estado_resultante;
        $this->save();
    }

    public function rechazar()
    {

    }

    public function revertirAutorizacion()
    {

    }

}