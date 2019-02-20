<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2019
 * Time: 08:50 PM
 */

namespace App\Services\CADECO\Contratos\FondoGarantia;


use App\Models\CADECO\SubcontratosFG\FondoGarantia;
use App\Repositories\CADECO\SubcontratosFG\FondoGarantia\Repository;

class FondoGarantiaService
{
    /**
     * @var Repository
     */
    protected $repository;
    private $id_usuario;
    private $usuario;
    private $id_obra;

    public function __construct(FondoGarantia $model)
    {
        $this->repository = new Repository($model);
        /*$this->id_usuario = auth()->id();
        $this->usuario = auth()->user()->usuario;
        $this->id_obra = Context::getIdObra();*/
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function create($data)
    {
        $data['id_usuario'] = $this->id_usuario;
        $data['usuario'] = $this->usuario;
        $data['id_obra'] = $this->id_obra;
        return $this->repository->create($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }
}