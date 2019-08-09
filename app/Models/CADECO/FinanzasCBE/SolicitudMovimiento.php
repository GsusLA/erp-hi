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

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        self::creating(function ($solicitud) {
            $solicitud->validar();
            $solicitud->fecha_hora = date('Y-m-d H:i:s');
            $solicitud->usuario_registra = auth()->id();
        });
    }

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

    public function validar()
    {
        if($this->id_tipo_movimiento != 1)// validar otros movimientos
        {

        }
    }
}