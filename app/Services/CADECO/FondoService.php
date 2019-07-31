<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 14/02/19
 * Time: 12:59 PM
 */

namespace App\Services\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Finanzas\CtgTipoFondo;
use App\Models\CADECO\Fondo;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FondoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * FondoService constructor.
     *
     * @param Fondo $model
     */
    public function __construct(Fondo $model)
    {
        $this->repository = new Repository($model);
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function index()
    {
        return $this->repository->all();
    }

    public function store(array $data){

    if(!empty($data['empresa_manual'])){
        $data_empresa =[
            'tipo_empresa'=>32,
            'razon_social'=>$data['empresa_manual'],
//            'UsuarioRegistro'=>auth()->id(),
                    ];
        try {
            DB::connection('cadeco')->beginTransaction();
            $empresa_arr= Empresa::query()->create($data_empresa);
            $razon_social=$empresa_arr['razon_social'];
            $id_responsable=$empresa_arr['id_empresa'];
            DB::connection('cadeco')->commit();
        }catch(\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }


    }else{
        $empresa_array = Empresa::query()->where('id_empresa','=',$data['id_responsable'])->get();
        $razon_social=$empresa_array[0]['razon_social'];
        $id_responsable=$data['id_responsable'];
    }


        $tipos_array= CtgTipoFondo::query()->where('id',$data['id_tipo'])->get();
        $tipo_desc_corta=$tipos_array[0]['descripcion_corta'];

        $id_obra= Context::getIdObra();

        $descripcion= $tipo_desc_corta .' '.$razon_social;




        try{
            DB::connection('cadeco')->beginTransaction();

            $datos = [
                'id_obra' => $id_obra,
                'id_tipo' => $data['id_tipo'],
                'id_responsable' => $id_responsable,
                'descripcion' => $descripcion,
                'nombre' =>$razon_social,
                'fecha'=>Carbon::now()->format('Y-m-d'),
                'fondo_obra'=>$data['fondo_obra'],
                'id_costo' => $data['id_costo']
            ];

            $fondo = Fondo::query()->create($datos);

            DB::connection('cadeco')->commit();

            return $fondo;
        }catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }

    }


}