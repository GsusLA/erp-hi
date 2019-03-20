<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/03/19
 * Time: 06:13 PM
 */

namespace App\Http\Controllers\v1\CADECO;


use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateObraRequest;
use App\Http\Transformers\CADECO\ObraTransformer;
use App\Services\CADECO\ObraService;
use App\Traits\ControllerTrait;
use League\Fractal\Manager;

class ObraController extends Controller
{
    use ControllerTrait {
        update as protected updateTrait;
    }

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var ObraService
     */
    protected $service;

    /**
     * @var ObraTransformer
     */
    protected $transformer;

    /**
     * ObraController constructor.
     * @param Manager $fractal
     * @param ObraService $service
     * @param ObraTransformer $transformer
     */
    public function __construct(Manager $fractal, ObraService $service, ObraTransformer $transformer)
    {
        $this->middleware('auth');
        $this->middleware('context');

        $this->fractal = $fractal;
        $this->service = $service;
        $this->transformer = $transformer;
    }

    public function update(UpdateObraRequest $request, $id)
    {
        return $this->updateTrait($request, $id);
    }
}