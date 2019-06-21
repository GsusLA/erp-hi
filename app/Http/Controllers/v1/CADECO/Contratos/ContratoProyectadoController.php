<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 02:15 PM
 */

namespace App\Http\Controllers\v1\CADECO\Contratos;


use App\Http\Controllers\Controller;
use App\Http\Requests\AprobarEstimacionRequest;
use App\Http\Transformers\CADECO\Contrato\ContratoProyectadoTransformer;
use App\Services\CADECO\Contratos\ContratoProyectadoService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class ContratoProyectadoController extends Controller
{
    use ControllerTrait {
        store as protected traitStore;
    }

    /**
     * @var ContratoProyectadoService
     */
    protected $service;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ContratoProyectadoTransformer
     */
    protected $transformer;

    /**
     * EstimacionController constructor.
     * @param ContratoProyectadoService $service
     * @param Manager $fractal
     * @param ContratoProyectadoTransformer $transformer
     */
    public function __construct(ContratoProyectadoService $service, Manager $fractal, ContratoProyectadoTransformer $transformer)
    {
        $this->middleware('auth')->only('pdfOrdenPago');
        $this->middleware('auth:api');

        $this->middleware('context');

        $this->service = $service;
        $this->fractal = $fractal;
        $this->transformer = $transformer;
    }

    /**
     * @param AprobarEstimacionRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function aprobar(AprobarEstimacionRequest $request, $id)
    {
        $estimacion = $this->service->aprobar($id);
        return $this->respondWithItem($estimacion);
    }
}