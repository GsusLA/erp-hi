<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 10:08 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\DescuentoFondoGarantia;
use App\Models\CADECO\Subcontrato;

class DescuentoFondoGarantiaObserver
{
    /**
     * @param DescuentoFondoGarantia $descuentoFondoGarantia
     */
    public function creating(DescuentoFondoGarantia $descuentoFondoGarantia)
    {
        $subcontrato = Subcontrato::find($descuentoFondoGarantia->id_antecedente);
        $descuentoFondoGarantia->tipo_transaccion = 53;
        $descuentoFondoGarantia->opciones = 1;
        $descuentoFondoGarantia->estado = 1;
        $descuentoFondoGarantia->id_empresa = $subcontrato->id_empresa;
        $descuentoFondoGarantia->id_moneda = $subcontrato->id_moneda;
    }
}