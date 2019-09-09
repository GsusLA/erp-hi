<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/08/2019
 * Time: 05:07 PM
 */

namespace App\Models\CADECO\FinanzasCBE;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class SolicitudMovimiento extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'FinanzasCBE.solicitud_movimiento';
    public $timestamps = false;

    protected $fillable = [
        'id_solicitud',
        'id_tipo_movimiento',
        'usuario_registra',
        'mac_address',
        'ip',
        'observaciones',
        'fecha_hora',
        'id_movimiento_antecedente'
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud', 'id');
    }

    public function tipoMovimientoSolicitud()
    {
        return $this->belongsTo(CtgTipoMovimientoSolicitud::class, 'id_tipo_movimiento', 'id');
    }

    public function movimientoAntecedente()
    {
        return $this->belongsTo(SolicitudMovimiento::class, 'id_movimiento_antecedente', 'id');
    }

    public function registro()
    {
        return $this->belongsTo(Usuario::class, 'usuario_registra', 'idusuario');
    }

    public function getTipoMovimientoAttribute()
    {
        if($this->id_tipo_movimiento == 1){
            return 'Registro';
        }
        if($this->id_tipo_movimiento == 2){
            return 'Autorización';
        }
        if($this->id_tipo_movimiento == 3){
            return 'Cancelación';
        }
        if($this->id_tipo_movimiento == 4){
            return 'Rechazo';
        }
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha_hora);
        return date_format($date,"d/m/Y H:m");
    }
}