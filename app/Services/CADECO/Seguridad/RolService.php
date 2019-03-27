<?php
/**
 * Created by PhpStorm.
 * User: Alejandro Garrido
 * Date: 26/03/2019
 * Time: 18:45
 */

namespace App\Services\CADECO\Seguridad;

use App\Models\IGH\Usuario;
use App\Models\CADECO\Seguridad\Rol;
use App\Repositories\Repository;

class RolService
{
    protected $repository;


    /**
     * RolService constructor.
     * @param Rol $model
     */
    public function __construct(Rol $model)
    {
        $this->repository = new Repository($model);
    }
    
    public function index($data)
    {
        return $this->repository->all($data);
    }


}