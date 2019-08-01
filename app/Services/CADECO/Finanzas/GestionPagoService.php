<?php


namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Cuenta;
use App\Models\CADECO\Finanzas\CuentaBancariaProveedor;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesa;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaPartida;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
use App\Repositories\Repository;
use function GuzzleHttp\Psr7\get_message_body_summary;
use mysql_xdevapi\Exception;
use NumberFormatter;

class GestionPagoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * DistribucionRecursoRemesaService constructor.
     * @param Repository $repository
     */
    public function __construct(DistribucionRecursoRemesa $model)
    {
        $this->repository = new Repository($model);
    }

    public function validarBitacora($bitacora){
//        $nombre = $bitacora->getClientOriginalName();
//        if(pathinfo($nombre, PATHINFO_EXTENSION) != 'txt'){
//            abort(400, 'Archivo no valido.');
//        }

        $registros_bitacora = array();
        $doctos_repetidos = [];
        foreach ($this->getTxtData($bitacora) as $key => $pago){

            $c_cargo = Cuenta::query()->where('numero', $pago['cuenta_abono'])->first();
            if(!$c_cargo){
                if(strlen($pago['concepto']) > 10 && is_numeric(substr($pago['concepto'], 1, 9))){
                    $documento = Documento::query()->where('IDDocumento', '=', substr($pago['concepto'], 1, 9))->first();
                    $registros_bitacora[] = array(
                        'id_documento' => $documento->IDDocumento,
                        'id_transaccion' => $documento->transaccion? $documento->transaccion->id_transaccion:null,
                        'estado' => $documento->partidas->estatus,
                        'pagable' => $documento->partidas->pagable,
                        'concepto' => $documento->Concepto,
                        'beneficiario' => $documento->Destinatario,
                        'monto' => $pago['monto'],
                        'cuenta_cargo' => ['numero'=> $documento->partidas->cuentaCargo->numero, 'abreviatura'=> $documento->partidas->cuentaCargo->abreviatura, 'nombre' => $documento->partidas->cuentaCargo->empresa->razon_social],
                        'cuenta_abono' => ['numero'=> $documento->partidas->cuentaAbono->cuenta_clabe, 'abreviatura'=> $documento->partidas->cuentaAbono->complemento->nombre_corto, 'nombre' => $documento->partidas->cuentaAbono->banco->razon_social],
                        'referencia' => $pago['referencia'],
                    );
                }else{
                    $documentos = Documento::query()->where('MontoTotalSolicitado', '=', $pago['monto'])->get();
                    if(count($documentos) == 1){
                        $registros_bitacora[] = array(
                            'id_documento' => $documentos[0]->IDDocumento,
                            'id_transaccion' => $documentos[0]->transaccion? $documentos[0]->transaccion->id_transaccion:null,
                            'estado' => $documentos[0]->partidas->estatus,
                            'pagable' => $documentos[0]->partidas->pagable,
                            'concepto' => $documentos[0]->Concepto,
                            'beneficiario' => $documentos[0]->Destinatario,
                            'monto' => $pago['monto'],
                            'cuenta_cargo' => ['numero'=> $documentos[0]->partidas->cuentaCargo->numero, 'abreviatura'=> $documentos[0]->partidas->cuentaCargo->abreviatura, 'nombre' => $documentos[0]->partidas->cuentaCargo->empresa->razon_social],
                            'cuenta_abono' => ['numero'=> $documentos[0]->partidas->cuentaAbono->cuenta_clabe, 'abreviatura'=> $documentos[0]->partidas->cuentaAbono->complemento->nombre_corto, 'nombre' => $documentos[0]->partidas->cuentaAbono->banco->razon_social],
                            'referencia' => $pago['referencia'],
                        );
                    }else {
                        $cta_abono = CuentaBancariaProveedor::query()->where('cuenta_clabe', $pago['cuenta_abono'])->first();
                        $cta_cargo = Cuenta::query()->where('numero', $pago['cuenta_cargo'])->first();

                        if(!$cta_abono || !$cta_cargo){
                            $registros_bitacora[] = array(
                                'id_documento' => '',
                                'id_transaccion' => '',
                                'estado' => ['id' => 0, 'estado' => -3, 'descripcion' => 'Inválido'],
                                'pagable' => false,
                                'concepto' => $pago['concepto'],
                                'beneficiario' => '',
                                'monto' => $pago['monto'],
                                'cuenta_cargo' => $cta_cargo?['numero' => $cta_cargo->numero, 'abreviatura' => $cta_cargo->abreviatura, 'nombre' => $cta_cargo->empresa->razon_social]:
                                                    ['numero' => $pago['cuenta_cargo'], 'abreviatura' => $pago['cuenta_cargo'], 'nombre' => ''],
                                'cuenta_abono' => $cta_abono?['numero' => $cta_abono->cuenta_clabe, 'abreviatura' => $cta_abono->complemento->nombre_corto, 'nombre' => $cta_abono->banco->razon_social]:
                                                    ['numero' => $pago['cuenta_abono'], 'abreviatura' => $pago['cuenta_abono'], 'nombre' => ''],
                                'referencia' => $pago['referencia'],
                            );
                        }else {
                            $dist_part = DistribucionRecursoRemesaPartida::query()->whereIn('id_documento', $documentos->pluck('IDDocumento'))
                                ->where('id_cuenta_abono', '=', $cta_abono->id)
                                ->where('id_cuenta_cargo', '=', $cta_cargo->id_cuenta)
                                ->whereNotIn('id_documento', array_values($doctos_repetidos))->get();


                            if (count($dist_part) > 1) {
                                $doctos_repetidos[$dist_part[0]->id_documento] = $dist_part[0]->id_documento;
                                $registros_bitacora[] = array(
                                    'id_documento' => $dist_part[0]->documento->IDDocumento,
                                    'id_transaccion' => $dist_part[0]->documento->transaccion ? $documentos[0]->transaccion->id_transaccion : null,
                                    'estado' => $dist_part[0]->documento->partidas->estatus,
                                    'pagable' => $dist_part[0]->documento->partidas->pagable,
                                    'concepto' => $dist_part[0]->documento->Concepto,
                                    'beneficiario' => $dist_part[0]->documento->Destinatario,
                                    'monto' => $pago['monto'],
                                    'cuenta_cargo' => ['numero' => $dist_part[0]->documento->partidas->cuentaCargo->numero, 'abreviatura' => $dist_part[0]->documento->partidas->cuentaCargo->abreviatura, 'nombre' => $dist_part[0]->documento->partidas->cuentaCargo->empresa->razon_social],
                                    'cuenta_abono' => ['numero' => $dist_part[0]->documento->partidas->cuentaAbono->cuenta_clabe, 'abreviatura' => $dist_part[0]->documento->partidas->cuentaAbono->complemento->nombre_corto, 'nombre' => $dist_part[0]->documento->partidas->cuentaAbono->banco->razon_social],
                                    'referencia' => $pago['referencia'],
                                );
                            }
                            if (count($dist_part) == 1) {
                                $registros_bitacora[] = array(
                                    'id_documento' => $dist_part[0]->documento->IDDocumento,
                                    'id_transaccion' => $dist_part[0]->documento->transaccion ? $documentos[0]->transaccion->id_transaccion : null,
                                    'estado' => $dist_part[0]->documento->partidas->estatus,
                                    'pagable' => $dist_part[0]->documento->partidas->pagable,
                                    'concepto' => $dist_part[0]->documento->Concepto,
                                    'beneficiario' => $dist_part[0]->documento->Destinatario,
                                    'monto' => $pago['monto'],
                                    'cuenta_cargo' => ['numero' => $dist_part[0]->documento->partidas->cuentaCargo->numero, 'abreviatura' => $dist_part[0]->documento->partidas->cuentaCargo->abreviatura, 'nombre' => $dist_part[0]->documento->partidas->cuentaCargo->empresa->razon_social],
                                    'cuenta_abono' => ['numero' => $dist_part[0]->documento->partidas->cuentaAbono->cuenta_clabe, 'abreviatura' => $dist_part[0]->documento->partidas->cuentaAbono->complemento->nombre_corto, 'nombre' => $dist_part[0]->documento->partidas->cuentaAbono->banco->razon_social],
                                    'referencia' => $pago['referencia'],
                                );
                            }
                        }
                    }
                }
            }
        }
        return $registros_bitacora;
    }

    public function getTxtData($file){
        try{
            $myfile = fopen($file, "r") or die("Unable to open file!");
            $content = array();
            while(!feof($myfile)) {
                $linea = explode(";",fgets($myfile));
                if(count($linea) > 1 && $linea[8] == 'Aceptado' && $linea[4] > 1) {
                    $content[] = array(
                        "fecha" => $linea[0],
                        "hora" => $linea[1],
                        "concepto" => str_replace('  ', '', $linea[2]),
                        "cuenta_cargo" =>  str_replace(' ', '', $linea[3]),
                        "cuenta_abono" =>  str_replace(' ', '', $linea[4]),
                        "monto" => $this->getAmount($linea[5]),
                        "referencia" => $linea[6],
                        "usuario" => $linea[7],
                        "estatus" => $linea[8],
                        "origen" => $linea[9]
                    );
                }
            }
            fclose($myfile);
            return $content;
        }catch (\Exception $e){
            throw New \Exception('Error al procesar el archivo: ' . $e->getMessage());
        }
        return [];
    }

    public function getAmount($money)
    {
        $cleanString = preg_replace('/([^0-9\.,])/i', '', $money);
        $onlyNumbersString = preg_replace('/([^0-9])/i', '', $money);

        $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;

        $stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
        $removedThousendSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '',  $stringWithCommaOrDot);

        return (float) str_replace(',', '.', $removedThousendSeparator);
    }

}
