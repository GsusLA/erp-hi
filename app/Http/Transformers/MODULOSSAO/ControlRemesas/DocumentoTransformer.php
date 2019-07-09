<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 12:53 PM
 */

namespace App\Http\Transformers\MODULOSSAO\ControlRemesas;


use App\Http\Transformers\CADECO\CambioTransformer;
use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
use League\Fractal\TransformerAbstract;

class DocumentoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'remesa',
        'documentoLiberado',
        'empresa',
        'moneda'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(Documento $model){
        return [
            'id' => $model->getKey(),
            'id_remesa' => $model->IDRemesa,
            'referencia' => $model->Referencia,
            'numero_folio' => $model->NumeroFolio,
            'concepto' => $model->Concepto,
            'monto_total_format' => (string) '$'.number_format(($model->MontoTotal),2,".",","),
            'monto_total' => $model->MontoTotal,
            'saldo' => $model->Saldo,
            'id_moneda' => $model->IDMoneda,
            'moneda_nombre' => $model->Moneda,
            'tipo_cambio' => $model->TipoCambio,
            'saldo_moneda_nacional' => $model->SaldoMonedaNacional,
            'saldo_moneda_nacional_format' => (string) '$'.number_format(($model->SaldoMonedaNacional),2,".",","),
            'monto_total_solicitado' => $model->MontoTotalSolicitado,
            'observaciones' => $model->Observaciones,
            'destinatario' => $model->Destinatario,
            'importe_total' => $model->getImporteTotalAttribute()
        ];
    }

    /**
     * @param Documento $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeRemesa(Documento $model)
    {
        if($remesa = $model->remesa){
            return $this->item($remesa, new RemesaTransformer);
        }
        return null;
    }

    /**
     * @param Documento $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeDocumentoLiberado(Documento $model)
    {
        if($documento = $model->documentoLiberado)
        {
            return $this->item($documento, new DocumentoLiberadoTransformer);
        }
        return null;
    }

    /**
     * @param Documento $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(Documento $model)
    {
        if($empresa = $model->empresa)
        {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    public function includeMoneda(Documento $model)
    {
        if($moneda = $model->moneda) {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }
}
