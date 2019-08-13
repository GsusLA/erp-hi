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
            $sol->generaMovimiento();
        });
    }

    private function validar()
    {
        $cuentaBancaria = CuentaBancariaEmpresa::query()->where('cuenta_clabe', '=', $this->cuenta_clabe)->orWhere('id_empresa', '=', $this->id_empresa)->get()->toArray();
        $solicitud = SolicitudAlta::query()->where('cuenta_clabe', $this->cuenta_clabe)->orWhere('id_empresa', '=', $this->id_empresa)->where('estado','>=',0)->get()->toArray();

        if($cuentaBancaria != []){
            abort(400, 'Ya existe una cuenta bancaria registrada para este beneficiario.');
        }
        if($solicitud != []){
            abort(400, 'Ya existe una solicitud de alta de cuenta bancaria registrada con la cuenta ingresada.');
        }
    }

    /**
     * @return mixed
     */
    public function generaMovimiento()
    {
        return SolicitudMovimiento::create([
                'id_solicitud'=>$this->id,
                'id_tipo_movimiento'=>1,
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
}