<?php


namespace App\LAYOUT;


use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaPartida;
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

    public function __construct($base, $idobra)
    {
        session()->put('db', $base);
        session()->put('id_obra', $idobra);
        config()->set('database.connections.cadeco.database', $base);
        $this->decode_entrada = config('app.env_variables.SANTANDER_H2H_DECODE_ENTRADA');
        $this->decode_salida = config('app.env_variables.SANTANDER_H2H_DECODE_SALIDA');
        $this->sftp_entrada = config('app.env_variables.SANTANDER_SFTP_ENTRADA');
        $this->sftp_salida = config('app.env_variables.SANTANDER_SFTP_SALIDA');
        $this->sftp_h2h_host = config('app.env_variables.SFTP_H2H_HOST');
        $this->sftp_h2h_port = config('app.env_variables.SFTP_H2H_PORT');
        $this->sftp_h2h_user = config('app.env_variables.SFTP_H2H_USER');
        $this->sftp_h2h_pass = config('app.env_variables.SFTP_H2H_PASS');
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
        dd('panda malo');

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
            if( substr($pagos[0], 33, 2) === '00'){ /** Archivo de salida sin errores, registrar pagos*/
                try{
                    DB::connection('cadeco')->beginTransaction();

                    for ($i = 1; $i < count($pagos) -1; $i++){
                        $id_documento = substr($pagos[$i], 228, 40);
                        $val_partida= substr($pagos[$i], 400, 2);
                        dd($id_documento, $val_partida, $dispersion->where('id_documento', '=', $id_documento)->first());

                        $dist_recurso_partida = DistribucionRecursoRemesaPartida::where('id_distribucion_recurso', '=', $dispersion->id)->where('id_documento', '=', $id_documento)->first()->partidaValidaEstado();

                        if($val_partida== 0){
                            $documento = Documento::with('tipoDocumento')->where('IDRemesa', '=', $dispersion->id_remesa)->where('IDDocumento', '=', $id_documento)->first();
                            $transaccion = Transaccion::find($documento->IDTransaccionCDC);
                            if($transaccion){

                            }

                            dd($transaccion);
                        }
                        dd($val_partida== 0);
                    }




                    /** @var  $dist_layout_registro, Actualizacion del registro del layout */
                    $dist_layout_registro = DistribucionRecursoRemesaLayout::where('id_distrubucion_recurso', '=', $dispersion->id)->first();
                    $dist_layout_registro->usuario_carga = auth()->id();
                    $dist_layout_registro->fecha_hora_carga = date('Y-m-d h:i:s');
                    $dist_layout_registro->folio_confirmacion_bancaria = 1;
                    $dist_layout_registro->save();

                    dd($dispersion,  $dispersion->id);
                    dd('stop polar');
                    DB::connection('cadeco')->commit();
                }catch (\Exception $e){
                    DB::connection('cadeco')->rollBack();
                    abort(400, $e->getMessage());
                    throw $e;
                }

            }else{ /** Archivo de salida con errores, se procede a registrar la causa de rechazo */
                // TODO Registrar causa de rechazo y cambio de estatus
            }
            dd('koala', substr($pagos[0], 33, 2));
        }
        dd(substr($pagos[0], 14, 1));
    }




}
