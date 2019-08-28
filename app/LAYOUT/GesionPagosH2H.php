<?php


namespace App\LAYOUT;

use App\Models\CADECO\OrdenPago;
use App\Models\CADECO\Pago;
use App\Models\CADECO\PagoACuenta;
use App\Models\CADECO\PagoVario;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GesionPagosH2H
{
    protected $sftp_h2h_host;
    protected $sftp_h2h_port;
    protected $sftp_h2h_user;
    protected $sftp_h2h_pass;
    protected $decode_entrada;
    protected $decode_salida;
    protected $sftp;

    public function __construct($base, $idobra, $id_usuario)
    {
        session()->put('db', $base);
        session()->put('id_obra', $idobra);
        config()->set('database.connections.cadeco.database', $base);
        auth()->loginUsingId($id_usuario, false);
        $this->decode_entrada   = config('app.env_variables.SANTANDER_H2H_DECODE_ENTRADA');
        $this->decode_salida    = config('app.env_variables.SANTANDER_H2H_DECODE_SALIDA');
        $this->sftp_entrada     = config('app.env_variables.SANTANDER_SFTP_ENTRADA');
        $this->sftp_salida      = config('app.env_variables.SANTANDER_SFTP_SALIDA');
        $this->sftp_h2h_host    = config('app.env_variables.SFTP_H2H_HOST');
        $this->sftp_h2h_port    = config('app.env_variables.SFTP_H2H_PORT');
        $this->sftp_h2h_user    = config('app.env_variables.SFTP_H2H_USER');
        $this->sftp_h2h_pass    = config('app.env_variables.SFTP_H2H_PASS');
        $this->sftp_connect();
    }

    public function  sftp_connect(){
        try{
            $this->sftp = new SFTPConnection($this->sftp_h2h_host, $this->sftp_h2h_port);
            $this->sftp->login($this->sftp_h2h_user, $this->sftp_h2h_pass);
        }catch (\Exception $e){
            throw new Exception("Could not open connection." . $e);
        }
    }

    public function buscar_respuesta_h2h($data){
        if($archivo = $this->sftp->getFile($this->sftp_salida.$data->nombre_archivo.'.out')){
            $this->decodificar_archivo($archivo, $data->nombre_archivo);
            $this->procesar_pagos($data->dispersion_remesa,$this->getOutData($data->nombre_archivo));
        }
    }

    public function decodificar_archivo($archivo, $nombre){
        Storage::disk('cifrado')->put('/'.$nombre.'.out', $archivo);
        $cmd = str_replace('/', '\\', "copy " . $this->decode_entrada .'/'. $nombre.'.out' . " " . $this->decode_salida . "\\" . $nombre.'.out');
        exec($cmd, $salida, $resp);
        if ($resp != 0) {
            echo 'Fallo envío a repositorio de salida.';
            die;
        }

        Storage::disk('h2h_out')->put($nombre.'.out', fopen($this->decode_salida. "\\" . $nombre.'.out', 'r'));
    }

    public function getOutData($docFile){
        $archivo_decod = fopen(Storage::disk('h2h_out')->path($docFile.'.out'), 'r');
        $content = array();
        while(!feof($archivo_decod)) {
            $linea = str_replace("\n","",fgets($archivo_decod));
            $content[] = $linea;
        }
        return $content;
    }

    /**
     * @param $dispersion , Dispersión de recursos de remesa que se va a procesar
     * @param $pagos , archivo de salida con las respuestas de las operaciones de la dispersión de recursos de remesa
     * @throws Exception
     */
    public function procesar_pagos($dispersion, $pagos){
        if(substr($pagos[0], 14, 1) == 'S'){  /** Valida sentido de archivo de respuesta bancario H2H, 'S' indica que es archivo de salida ***/
            /** Valida el estado del archivo de pagos, si la respuesta es diferente a '00' entonces el archivo fue rechazado por el banco,
             * y el código de rechaso es el indicado en estos mismos caraceres
             */
            try{
                DB::connection('cadeco')->beginTransaction();
                $codigo_aceptacion_dispersion = substr($pagos[0], 33, 2);
                if( $codigo_aceptacion_dispersion === '00' && $dispersion->estado == 2){ /** Archivo de salida sin errores, registrar pagos*/
                    for ($i = 1; $i < count($pagos) -2; $i++){
                        $id_documento = str_replace(' ', '', substr($pagos[$i], 228, 40));
                        $codigo_aceptacion_partida = substr($pagos[$i], 400, 2);
                        $pago= substr($pagos[$i], 1, 20);
                        $pago_remesa = null;
                        $partida = $dispersion->partida->first(function ($value, $key) use($id_documento) {
                            if ($value->id_documento == $id_documento)
                                return $value;
                        });

                        if($codigo_aceptacion_partida== 0){
                            $data = array(
                                //"id_cuenta" => $partida_remesa->id_cuenta_cargo,
                                "id_empresa" => $partida->documento->IDDestinatario,
                                "id_moneda" => $partida->documento->IDMoneda,
                                "monto" => -1 * abs($partida->documento->MontoTotalSolicitado),
                                //"saldo" => -1 * abs($partida_remesa->documento->MontoTotalSolicitado),
                                "referencia" => $pago,
                                //"destino" => $partida_remesa->documento->Destinatario,
                                //"observaciones" => $partida_remesa->documento->Observaciones
                            );
                            if ($partida->documento->transaccion) {
                                $transaccion = $partida->documento->transaccion;
                                switch ($partida->documento->transaccion->tipo_transaccion) {
                                    case 65:
                                        // se registra un pago
                                        $data["id_antecedente"] = $transaccion->id_antecedente;
                                        $data["id_referente"] = $transaccion->id_transaccion;
                                        unset($data["referencia"]);
                                        $o_pago = OrdenPago::query()->create($data);
                                        $o_pago = OrdenPago::query()->where('id_transaccion', '=', $o_pago->id_transaccion)->first();
                                        unset($data["id_antecedente"]);
                                        unset($data["id_referente"]);
                                        $data["numero_folio"] = $o_pago->numero_folio;
                                        $data["referencia"] = $pago;
                                        $data["estado"] = 2;
                                        $data["id_cuenta"] = $partida->id_cuenta_cargo;
                                        $data["destino"] = $partida->documento->Destinatario;
                                        $data["observaciones"] = $partida->documento->Observaciones;
                                        $pago_remesa = Pago::query()->create($data);

                                        break;
                                    case 72:
                                        if ($partida->documento->IDTipoDocumento == 12) {
                                            unset($data["id_empresa"]);
                                            $data["id_antecedente"] = $transaccion->id_transaccion;
                                            $data["id_referente"] = $transaccion->id_referente;
                                            $data["estado"] = 1;
                                            $data["id_cuenta"] = $partida->id_cuenta_cargo;
                                            $data["saldo"] = -1 * abs($partida->documento->MontoTotalSolicitado);
                                            $data["destino"] = $partida->documento->Destinatario;
                                            $data["observaciones"] = $partida->documento->Observaciones;
                                            $pago_remesa = PagoVario::query()->create($data);


                                        } else {
                                            $data["id_cuenta"] = $partida->id_cuenta_cargo;
                                            $data["saldo"] = -1 * abs($partida->documento->MontoTotalSolicitado);
                                            $data["destino"] = $partida->documento->Destinatario;
                                            $data["observaciones"] = $partida->documento->Observaciones;

                                            $pago_remesa = PagoACuenta::query()->create($data);
                                        }
                                        break;
                                    default:
                                        $data["id_cuenta"] = $partida->id_cuenta_cargo;
                                        $data["saldo"] = -1 * abs($partida->documento->MontoTotalSolicitado);
                                        $data["destino"] = $partida->documento->Destinatario;
                                        $data["observaciones"] = $partida->documento->Observaciones;

                                        $pago_remesa = PagoACuenta::query()->create($data);
                                        break;
                                }
                                $transaccion->estado = 2;
                                $transaccion->save();

                            } else {
                                $data["id_cuenta"] = $partida->id_cuenta_cargo;
                                $data["saldo"] = -1 * abs($partida->documento->MontoTotalSolicitado);
                                $data["destino"] = $partida->documento->Destinatario;
                                $data["observaciones"] = $partida->documento->Observaciones;

                                $pago_remesa = PagoACuenta::query()->create($data);
                            }

                            $partida->estado = 2;
                            $partida->id_transaccion_pago = $pago_remesa->id_transaccion;
                            $partida->folio_partida_bancaria = $pago;

                        }else{  /** Registrar motivo de rechazo de la partida y actualizar estado en la dispersión */
                            $partida->estado = -2; //// registrar estado de rechazo bancario
                        }

                        $partida->clave_aceptacion = $codigo_aceptacion_partida;  /// registrar id de motivo de rechazo
                        $partida->save();
                    }

                    $dispersion->estado = 3;

                    $dispersion->remesaLayout->usuario_carga = auth()->id();
                    $dispersion->remesaLayout->fecha_hora_carga = date('Y-m-d');
                    $dispersion->remesaLayout->folio_confirmacion_bancaria = date('Y-m-d');
                    $dispersion->remesaLayout->save();

                    $dispersion->gestionPagoH2H->estado = 2;
                    $dispersion->gestionPagoH2H->save();


                }else{ /** Archivo de salida con errores, se procede a registrar la causa de rechazo */
                    // TODO Registrar causa de rechazo y cambio de estatus
                    $dispersion->estado = -2;
                    $dispersion->partida()->update(['estado' => -2, 'clave_aceptacion' => $codigo_aceptacion_dispersion])->save();
                }
                $dispersion->clave_aceptacion = $codigo_aceptacion_dispersion;
                $dispersion->save();
                DB::connection('cadeco')->commit();
            }catch (\Exception $e){
                DB::connection('cadeco')->rollBack();
                $dispersion->gestionPagoH2H->estado = -2;
                $dispersion->gestionPagoH2H->save();
            }
        }
    }




}
