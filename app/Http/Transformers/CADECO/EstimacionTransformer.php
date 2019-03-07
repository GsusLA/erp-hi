<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 03:22 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Estimacion;
use League\Fractal\TransformerAbstract;

class EstimacionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'subcontrato'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'subcontrato'
    ];

    public function transform(Estimacion $model)
    {
        return [
            'id' => $model->getKey(),
            'numeroFolio' => $model->numero_folio
        ];
    }

    public function includeSubcontrato(Estimacion $model)
    {
        if ($subcontrato = $model->subcontrato) {
            return $this->item($subcontrato, new SubcontratoTransformer);
        }
        return null;
    }
}