<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 08/08/19
 * Time: 06:00 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Sucursal;
use App\Repositories\Repository;

class SucursalService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SucursalService constructor
     *
     * @param Sucursal $model
     */

    public function __construct(Sucursal $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data){
        return $this->repository->paginate();
    }

    public function show($id){
        return $this->repository->show($id);
    }

    public function store(array $data)
    {
        dd($data);
        if($data['checkCentral']==true){
            $central='S';
        }else{
            $central='N';
        }
        $datos = [
            'id_empresa'=> $data[''],
            'descripcion' => $data['descripcion'],
            'direccion' => $data['direccion'],
            'ciudad' => $data['ciudad'],
            'codigo_postal' => $data['codigo_postal'],
            'estado' => $data['estado'],
            'telefono'=> $data['voz'],
            'fax' => $data['fax'],
            'contacto'=>$data['contacto'],
            'central'=>$central,

        ];
//        $sucursal = Sucursal::query()->create($datos);
//        return $sucursal;
    }



}
