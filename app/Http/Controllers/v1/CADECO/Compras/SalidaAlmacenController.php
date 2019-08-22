<?php


namespace App\Http\Controllers\v1\CADECO\Compras;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Compras\SalidaAlmacenTransformer;
use App\Services\CADECO\Compras\SalidaAlmacenService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class SalidaAlmacenController extends Controller
{
    use ControllerTrait;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var SalidaAlmacenService
     */
    private $service;

    /**
     * @var SalidaAlmacenTransformer
     */
    private $transformer;

    /**
     * SalidaAlmacenController constructor.
     * @param Manager $fractal
     * @param SalidaAlmacenService $service
     * @param SalidaAlmacenTransformer $transformer
     */

    public function __construct(Manager $fractal, SalidaAlmacenService $service, SalidaAlmacenTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_salida_almacen')->only('paginate');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

}