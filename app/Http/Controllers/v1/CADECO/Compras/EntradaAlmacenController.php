<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 12:55 PM
 */

namespace App\Http\Controllers\v1\CADECO\Compras;


use App\Http\Controllers\Controller;
use App\Http\Transformers\CADECO\Compras\EntradaAlmacenTransformer;
use App\Services\CADECO\Compras\EntradaAlmacenService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class EntradaAlmacenController extends Controller
{
    use ControllerTrait;

    /**
     * @var EntradaAlmacenService
     */
    private $service;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var EntradaAlmacenTransformer
     */
    private $transformer;

    /**
     * EntradaAlmacenController constructor.
     * @param EntradaAlmacenService $service
     * @param Manager $fractal
     * @param EntradaAlmacenTransformer $transformer
     */
    public function __construct(EntradaAlmacenService $service, Manager $fractal, EntradaAlmacenTransformer $transformer)
    {
        $this->middleware('auth:api');
        $this->middleware('context');
        $this->middleware('permiso:consultar_entrada_almacen')->only(['show','paginate','index','find']);

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }
}