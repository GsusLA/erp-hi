<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 03:21 PM
 */

namespace App\Services\CADECO\Contratos;


use App\Models\CADECO\ContratoProyectado;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class ContratoProyectadoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * EstimacionService constructor.
     */
    public function __construct(ContratoProyectado $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function find($id)
    {
        return $this->repository->where('id_transaccion', '=', $id);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function paginate()
    {
        return $this->repository->paginate();
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public  function actualiza($data, $id)
    {
        $area =  $data['id_area'];

        $transaccion = $this->repository->show($id);
        $transaccion_area = $transaccion->areas_subcontratantes()->get();
        if(count($transaccion_area) > 0){
            $solicitud = ContratoProyectado\AreasSubcontratantes::find($id);
            $solicitud = $solicitud->actualiza($id, $data['id_area']);
            return $transaccion;

        }else{
            try {
                DB::connection('cadeco')->beginTransaction();
                $datos = [
                    'id_area_subcontratante' => $area,
                    'id_transaccion' => $id,
                ];
                $solicitud = ContratoProyectado\AreasSubcontratantes::query()->create($datos);

                DB::connection('cadeco')->commit();

                return $transaccion ;
            } catch (\Exception $e) {
                DB::connection('cadeco')->rollBack();
                abort(400, $e->getMessage());
                throw $e;
            }
        }
    }

}