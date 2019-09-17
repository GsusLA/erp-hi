<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 08:33 PM
 */

namespace App\Models\CADECO;


use Illuminate\Support\Facades\DB;

class AjustePositivo extends Ajuste
{
    protected $fillable = [
        'id_almacen',
        'referencia',
        'observaciones'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('opciones', '=', 0);
        });
    }

    public function partidas()
    {
        return $this->hasMany(AjustePositivoPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function registrar($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->validarPartidas($data['items'],$data['id_almacen']);
            $datos = [
                'id_almacen' => $data['id_almacen'],
                'referencia' => $data['referencia'],
                'observaciones' => $data['observaciones'],
            ];
            $ajusteTransaccion = $this->create($datos);
            $partida = new AjustePositivoPartida();
            $partida->registrar($data['items'], $ajusteTransaccion->id_almacen, $ajusteTransaccion->id_transaccion);

            DB::connection('cadeco')->commit();
            return $this;
        }catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function validarPartidas($partidas, $id)
    {
        foreach ($partidas as  $partida) {
            $inventarios = Inventario::query()->where('id_material', '=', $partida['id_material']['id'])
                ->where('id_almacen', '=', $id)
                ->selectRaw('SUM(cantidad) as cantidad, SUM(saldo) as saldo')->first()->toArray();
            if($inventarios['cantidad'] < $inventarios['saldo'])
            {
                abort(400, "No se puede registrar el ajuste de inentario debido a que los saldos no concuerdan.");
            }
            if($inventarios['cantidad'] < $partida['cantidad'])
            {
                abort(400, "La cantidad solicitada es mayor a lo existente en inventarios.");
            }
            if($inventarios['cantidad'] == $inventarios['saldo'])
            {
                abort(400, "Inventarios completos de este material");
            }
        }
    }
}