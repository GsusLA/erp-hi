<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 07:20 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\PolizaMovimiento;
use League\Fractal\TransformerAbstract;

class PolizaMovimientoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'tipo',
        'tipoCuentaContable'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'tipo',
        'tipoCuentaContable'
    ];

    public function transform(PolizaMovimiento $model) {
        return [
            'id' => $model->getKey(),
            'cuenta_contable' => $model->cuenta_contable,
            'referencia' => $model->referencia,
            'importe' => $model->importe,
            'concepto' => $model->concepto
        ];
    }

    /**
     * Include TipoMovimiento
     *
     * @param PolizaMovimiento $model
     * @return \League\Fractal\Resource\Item
     */
    public function IncludeTipo(PolizaMovimiento $model)
    {
        $tipo = $model->tipo;

        return $this->item($tipo, new TipoMovimientoTransformer);
    }

    /**
     * Include TipoCuentaContable
     *
     * @param PolizaMovimiento $model
     * @return \League\Fractal\Resource\Item
     */
    public function IncludeTipoCuentaContable(PolizaMovimiento $model)
    {
        $tipo = $model->tipoCuentaContable;

        return $this->item($tipo, new TipoCuentaContableTransformer);
    }
}

