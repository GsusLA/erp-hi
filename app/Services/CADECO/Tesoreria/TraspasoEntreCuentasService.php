<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 05:42 PM
 */

namespace App\Services\CADECO\Tesoreria;


use App\Facades\Context;
use App\Models\CADECO\Credito;
use App\Models\CADECO\Debito;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Tesoreria\TraspasoCuentas;
use App\Models\CADECO\Tesoreria\TraspasoTransaccion;
use App\Models\CADECO\Transaccion;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class TraspasoEntreCuentasService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TraspasoEntreCuentasService constructor.
     * @param Repository $repository
     */
    public function __construct(TraspasoCuentas $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function delete($data, $id)
    {
        $this->repository->delete($data, $id);
    }

    public function create($data)
    {
        return $this->repository->create($data);
    }

    /**
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function store($data)
    {
        $obra = Obra::query()->find(Context::getIdObra());
        $id_moneda = $obra->id_moneda;

        try {
            DB::connection('cadeco')->beginTransaction();
            $record = $this->repository->create($data);

            $credito = [
                'tipo_transaccion' => 83,
                'fecha' => $data['fecha'] ? $data['fecha'] : date('Y-m-d'),
                'estado' => 1,
                'id_obra' => $obra->id_obra,
                'id_cuenta' => $data['id_cuenta_destino'],
                'id_moneda' => $id_moneda,
                'cumplimiento' => $data['cumplimiento'] ? $data['cumplimiento'] : date('Y-m-d'),
                'vencimiento' => $data['cumplimiento'] ? $data['cumplimiento'] : date('Y-m-d'),
                'opciones' => 1,
                'monto' => $data['importe'],
                'impuesto' => 0,
                'referencia' => $data['referencia'],
                'comentario' => "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario,
                'observaciones' => $data['observaciones'],
                'FechaHoraRegistro' => date('Y-m-d h:i:s'),
            ];

            $debito = $credito;
            $debito['tipo_transaccion'] = 84;
            $debito['id_cuenta'] = $data['id_cuenta_origen'];
            $debito['monto'] = (float)  ($data['importe'] * -1);

            // Crear transaccion Débito
            $transaccion_debito = Debito::query()->create($debito);

            // Crear transaccion Crédito
            $transaccion_credito = Credito::query()->create($credito);

            // Revisa si la transacción se realizó
            $debito_realizo = Transaccion::query()->where('id_transaccion', $transaccion_debito->id_transaccion)->first();
            $credito_realizo = Transaccion::query()->where('id_transaccion', $transaccion_credito->id_transaccion)->first();

            // Si alguna de las transacciones no se registró, regresa un error
            if (!$debito_realizo || !$credito_realizo)
            {
                throw new \Exception("El traspaso no se pudo concretar", 400);
            }

            // Enlaza las transacciones con su respectivo traspaso. Debito
            TraspasoTransaccion::query()->create([
                'id_traspaso' => $record->id_traspaso,
                'id_transaccion' => $transaccion_debito->id_transaccion,
                'tipo_transaccion' => $debito['tipo_transaccion'],
            ]);

            // Enlaza las transacciones con su respectivo traspaso. Credito
            TraspasoTransaccion::query()->create([
                'id_traspaso' => $record->id_traspaso,
                'id_transaccion' => $transaccion_credito->id_transaccion,
                'tipo_transaccion' => $credito['tipo_transaccion'],
            ]);

            DB::connection('cadeco')->commit();

            return $record;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function update($data, $id)
    {

    }
}