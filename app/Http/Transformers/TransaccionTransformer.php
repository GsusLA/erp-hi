<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 7/01/19
 * Time: 07:40 PM
 */

namespace App\Http\Transformers;


use App\Http\Transformers\CADECO\TipoTransaccionTransformer;
use App\Models\CADECO\Transaccion;
use League\Fractal\TransformerAbstract;

class TransaccionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'tipo'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'tipo'
    ];

    public function transform(Transaccion $model) {
        return $model->toArray();
    }

    /**
     * Include TipoTransaccion
     *
     * @param Transaccion $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeTipo(Transaccion $model) {
        if ($tipo = $model->tipo) {
            return $this->item($tipo, new TipoTransaccionTransformer);
        }
        return null;
    }
}