<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2019
 * Time: 08:54 PM
 */

namespace App\Http\Transformers\CADECO\SubcontratosFG;


use App\Http\Transformers\CADECO\Subcontratos\SubcontratoTransformer;
use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\SubcontratosFG\FondoGarantia;
use League\Fractal\TransformerAbstract;

class FondoGarantiaTransformer extends TransformerAbstract
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

    /**
     * @param FondoGarantia $model
     * @return array
     */
    public function transform(FondoGarantia $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'fecha' => (string)$model->fecha,
            'saldo_format' => (string)$model->saldo_format,
            'saldo' => (float)$model->saldo,
            'created_at'=>(string)$model->created_at,
        ];
    }

    /**
     * Include Subcontrato
     *
     * @param FondoGarantia $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeSubcontrato(FondoGarantia $model) {
        if ($subcontrato = $model->subcontrato) {
            return $this->item($subcontrato, new SubcontratoTransformer);
        }
        return null;
    }
}