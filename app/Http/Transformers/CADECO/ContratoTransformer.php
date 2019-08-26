<?php


namespace App\Http\Transformers\CADECO;
use App\Models\CADECO\Contrato;
use League\Fractal\TransformerAbstract;

class ContratoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    public function transform(Contrato $model)
    {
        return [
              'id_concepto' => $model->concepto,
              'unidad' => $model->unidad,
              'cantidad_original' => $model->cantidad_original,
        ];
    }

}
