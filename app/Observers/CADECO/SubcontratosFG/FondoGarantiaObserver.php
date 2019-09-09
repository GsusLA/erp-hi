<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 08:59 PM
 */

namespace App\Observers\CADECO\SubcontratosFG;


use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\SubcontratosFG\FondoGarantia;

class FondoGarantiaObserver
{
    /**
     * @param FondoGarantia $fondoGarantia
     * @throws \Exception
     */
    public function creating(FondoGarantia $fondoGarantia)
    {
        /*
            * se valida que la retención establecida en el subcontrato sea mayor a 0 para que el fondo de garantía pueda ser generado
            * */
        $subcontrato = Subcontrato::find($fondoGarantia->id_subcontrato);
        if(!(float) $subcontrato->retencion>0){
            throw New \Exception('La retención de fondo de garantía establecida en el subcontrato no es mayor a 0, el fondo de garantía no puede generarse');
        }

        /*
         * se valida que no exista un fondo de garantía registrado previamente para el subcontrato
         * */
        $fondo_previo = Subcontrato::find($fondoGarantia->id_subcontrato)->fondo_garantia;
        if($fondo_previo){
            throw New \Exception('El subcontrato selecciondo ya tiene un fondo de garantía generado');
        }

        $fondoGarantia->created_at = date('Y-m-d h:i:s');
        $fondoGarantia->usuario_registra = $subcontrato->usuario_registra;
    }

    public function created(FondoGarantia $fondoGarantia)
    {
        $fondoGarantia->generaMovimientoRegistro();
    }

    public function updating(FondoGarantia $fondoGarantia)
    {
        /*
        * se valida que el saldo del fondo de garantía no sea menor a 0 al momento de actualizarlo
        * */
        if($fondoGarantia->saldo<0)
        {
            throw New \Exception('El saldo del fondo de garantía no puede ser menor a 0');
        }
        /*
         * se valida que el saldo del fondo de garantía no sea mayor al subtotal del subcontrato al momento de actualizarlo
         * */
        if($fondoGarantia->saldo > $fondoGarantia->subcontrato->subtotal)
        {
            throw New \Exception('El saldo del fondo de garantía no puede ser mayor al subtotal del subcontrato');
        }
    }
}