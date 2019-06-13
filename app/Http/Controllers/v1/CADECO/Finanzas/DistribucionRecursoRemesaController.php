<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 11:34 AM
 */

namespace App\Http\Controllers\v1\CADECO\Finanzas;


use App\Http\Controllers\Controller;
use App\Http\Requests\Finanzas\StoreDistribucionRecursoRemesaRequest;
use App\Http\Transformers\CADECO\Finanzas\DistribucionRecursoRemesaTransformer;
use App\Services\CADECO\Finanzas\DistribucionRecursoRemesaService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class DistribucionRecursoRemesaController extends Controller
{
    use ControllerTrait{
        store as protected traitStore;
    }

    /**
     * @var DistribucionRecursoRemesaService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var DistribucionRecursoRemesaTransformer
     */
    private $transformer;

    /**
     * DistribucionRecursoRemesaController constructor.
     * @param DistribucionRecursoRemesaService $service
     * @param Manager $fractal
     * @param DistribucionRecursoRemesaTransformer $transformer
     */
    public function __construct(DistribucionRecursoRemesaService $service, Manager $fractal, DistribucionRecursoRemesaTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    public function store(StoreDistribucionRecursoRemesaRequest $request)
    {
        return $this->traitStore($request);
    }
}