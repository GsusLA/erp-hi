<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/08/2019
 * Time: 10:33 AM
 */

namespace App\Models\CADECO\FinanzasCBE;


use App\Models\CADECO\Finanzas\CuentaBancariaEmpresa;
use function foo\func;
use Illuminate\Support\Facades\DB;

class SolicitudAlta extends Solicitud
{
    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        self::addGlobalScope(function ($query) {
            return $query->where('id_tipo_solicitud', '=', 1);
        });

        self::creating(function ($solicitud) {
            $solicitud->validar();
            $solicitud->numero_folio = $solicitud->folio();
            $solicitud->id_tipo_solicitud = 1;
            $solicitud->fecha = date('Y-m-d H:i:s');
            $solicitud->usuario_registra = auth()->id();
            $solicitud->estado = 1;
        });

        self::created(function ($sol){
            $sol->generaMovimiento(1);
        });
    }

    private function validar()
    {
        if(CuentaBancariaEmpresa::query()->where('cuenta_clabe', '=', $this->cuenta_clabe)->where('estatus','>=',0)->get()->toArray() != []){
            abort(400, 'Ya existe está cuenta bancaria registrada.');
        }

        if(CuentaBancariaEmpresa::query()->where('id_empresa', '=', $this->id_empresa)->where('estatus','>=',0)->get()->toArray() != []){
            abort(400, 'Ya existe una cuenta bancaria registrada para este beneficiario.');
        }

        if(SolicitudAlta::query()->where('cuenta_clabe', $this->cuenta_clabe)->where('estado','>=',0)->get()->toArray() != []){
            abort(400, 'Ya existe una solicitud de alta de cuenta bancaria registrada con la cuenta ingresada.');
        }

        if(SolicitudAlta::query()->where('id_empresa', '=', $this->id_empresa)->where('estado','>=',0)->get()->toArray() != []){
            abort(400, 'Ya existe una solicitud de alta de cuenta bancaria registrada con el beneficiario seleccionado.');
        }
    }

    /**
     * @return mixed
     */
    public function generaMovimiento($tipo_movimiento)
    {
        return SolicitudMovimiento::create([
                'id_solicitud'=>$this->id,
                'id_tipo_movimiento'=>$tipo_movimiento,
                'observaciones'=>$this->observaciones
            ]
        );
    }

    /**
     * @return int
     */
    public function folio()
    {
        return $count = SolicitudAlta::query()->count('id') + 1;
    }

    public function autorizar(){
        DB::connection('cadeco')->transaction(function() {
            $cuenta = CuentaBancariaEmpresa::query()->create( [
                'id_empresa' => $this->id_empresa,
                'id_banco' => $this->id_banco,
                'cuenta_clabe' => $this->cuenta_clabe,
                'sucursal' => $this->sucursal,
                'tipo_cuenta' => $this->tipo_cuenta,
                'id_solicitud_origen_alta' => $this->id,
                'id_plaza' => $this->id_plaza,
                'id_moneda' => $this->id_moneda
            ] );

            $movimiento = SolicitudMovimiento::query()->where( 'id_solicitud', '=', $this->id )->first();
            $id = $movimiento->id;
            $movs = SolicitudMovimiento::query()->create( [
                'id_solicitud' => $this->id,
                'id_movimiento_antecedente' => $id,
                'id_tipo_movimiento' => 2,
                'observaciones' => $this->observaciones,
            ] );
            $this->update( [
                'estado' => 2
            ] );
        });
        return $this;
    }
}