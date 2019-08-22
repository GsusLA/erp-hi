<?php


namespace App\LAYOUT;

use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaLayout;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\Storage;

class DistribucionRecursoRemesa
{
    protected $data = array();
    protected $id;
    protected $remesa;
    protected $linea;
    private $cifrado_entrada;
    private $cifrado_salida;
    private $decode_entrada;
    private $decode_salida;

    public function __construct($id)
    {
        $this->id = $id;
        $this->remesa = \App\Models\CADECO\Finanzas\DistribucionRecursoRemesa::with('partida')->where('id', '=', $id)->first();
        if($this->remesa->estado != 1){echo "Layout de distribucion de remesa no disponible.". PHP_EOL . "Estado: " . $this->remesa->estatus->descripcion ;die;}
        if($this->remesa->remesaLayout){echo "Layout de distribucion de remesa pagada previamente." ;die;}
        $this->cifrado_entrada = config('app.env_variables.SANTANDER_H2H_CIFRADO_ENTRADA');
        $this->cifrado_salida = config('app.env_variables.SANTANDER_H2H_CIFRADO_SALIDA');
        $this->decode_entrada = config('app.env_variables.SANTANDER_H2H_DECODE_ENTRADA');
        $this->decode_salida = config('app.env_variables.SANTANDER_H2H_DECODE_SALIDA');
        $this->sftp_entrada = config('app.env_variables.SANTANDER_SFTP_ENTRADA');
        $this->sftp_salida = config('app.env_variables.SANTANDER_SFTP_SALIDA');
        $this->linea = 2;
    }

    function create(){
        $this->encabezado();
        $this->detalle();
        $this->sumario();
        $date = now();
        $file_nombre = $this->getFileName($date);

        $a = "";
        foreach ($this->data as $dat){$a .= $dat . "\n";}
        Storage::disk('h2h_in')->put($file_nombre.'.in', $a);

        $this->enviarRepositorioFtp($file_nombre.'.in');

        $reg_layout = DistribucionRecursoRemesaLayout::where('id_distrubucion_recurso', '=', $this->id)->first();


        dd('stop panda');
        if($reg_layout){
            $reg_layout->contador_descarga = $reg_layout->contador_descarga + 1;
            $reg_layout->save();

            $this->remesa->estado = 2;
            $this->remesa->save();

        }else{
            $reg_layout = new DistribucionRecursoRemesaLayout();
            $reg_layout->id_distrubucion_recurso =$this->id;
            $reg_layout->usuario_descarga = auth()->id();
            $reg_layout->contador_descarga = 1;
            $reg_layout->fecha_hora_descarga = date('Y-m-d h:i:s');
            $reg_layout->nombre_archivo = $file_nombre;
            $reg_layout->save();

            $this->remesa->estado = 2;
            $this->remesa->save();
        }

          return Storage::disk('h2h_in')->download($file_nombre.'.in');
    }

    function getFileName($date){
        $file_nombre = 'tran' . $date->format('dmYHi') .'_' . 'nemonico';
        $path = "layouts/files/$file_nombre.in";
        if(file_exists($path)){
            $this->getFileName($date->add(new DateInterval('PT1M')));
        }
        return 'tran' . $date->format('dmYHi') .'_' . 'nemonico';
    }

    function encabezado(){
        /** @var  $tipo_registro, Fijo: 01  = Registro Encabezado de Bloque */
        $tipo_registro = '01';
        /** @var  $numero_secuencial, Número del registro con incremento ascendente. Fijo: 0000001 */
        $numero_secuencial = '0000001';
        /** @var  $codigo_operacion, Valor Fijo: 60  */
        $codigo_operacion = '60';
        /** @var  $numero_banco, Valor Fijo: 014 (Banco Santander México) */
        $numero_banco = '014';
        /** @var  $sentido, Valor Fijo: E de Entrada a Santander (en los archivos de respuesta se informara S de Salida de Santander) */
        $sentido = 'E';
        /** @var  $servicio, Valor Fijo: 2 */
        $servicio = '2';
        /** @var  $numero_bloque, "Número que identifica a un bloque de transacciones.
        Para el archivo de entrada el formato es el siguiente:
        DD = Día del mes en que es generada la información por el cliente
        NNNNN = Número consecutivo ascendente del 00001 al 99999 que corresponde al bloque de información preparado durante ese día."
         */
        $numero_bloque = date('d') . '00001';
        /** @var  $fecha_presentacion, Fecha de Envío del archivo a Santander en formato de AAAAMMDD (debe ser en día hábil bancario) */
        $fecha_presentacion = date('Ymd');
        /** @var  $codigo_divisa, Identifica el tipo de divisa en la cual se debe operar la transacción. */
        $codigo_divisa = '01';
        /** @var  $causa_rechazo_boque, Valor Fijo:00 para los archivos de Entrada a Santander (este valor puede cambiar en la Respuesta en caso de
         * Rechazo Total del Archivo, si el formato del archivo es correcto, el valor en la respuesta seguirá siendo 00)
         */
        $causa_rechazo_boque = '00';
        /** @var  $modalidad, Indica si la operación es para aplicación en mismo día, o en fecha programada (1 mismo día, 2 programado) */
        $modalidad = '1';
        /** @var  $uso_futuro, Disponible para uso futuro, se debe rellenar con espacios */
        $uso_futuro = ' '; for($i = 1;$i<40;$i++){$uso_futuro .=' ';}
        /** @var  $reservado, Disponible para uso futuro, se debe rellenar con espacios */
        $reservado = ' ';  for($i = 1;$i<406;$i++){$reservado .=' ';}


        $this->data[] = $tipo_registro .
            $numero_secuencial .
            $codigo_operacion .
            $numero_banco .
            $sentido .
            $servicio .
            $numero_bloque .
            $fecha_presentacion .
            $codigo_divisa .
            $causa_rechazo_boque .
            $modalidad .
            $uso_futuro .
            $reservado;
    }

    function detalle(){
        foreach ($this->remesa->partida as $key => $partida){
            if($partida->estado == 1) {
                $razon_social_abono = strlen($partida->cuentaAbono->empresa->razon_social) > 40 ? substr($partida->cuentaAbono->empresa->razon_social, 0, 40) :$partida->cuentaAbono->empresa->razon_social;
                $razon_social_cargo = strlen($partida->cuentaCargo->empresa->razon_social) > 40 ? substr($partida->cuentaCargo->empresa->razon_social, 0, 40) :$partida->cuentaCargo->empresa->razon_social;
                $monto = explode('.', number_format($partida->documento->MontoTotal, '2', '.', ''));
                $concepto = strlen($partida->documento->Concepto) > 40 ? substr($partida->documento->Concepto, 1, 40) :$partida->documento->Concepto;
                $descripcion_referencia = strlen($partida->documento->Concepto) > 30 ? substr($partida->documento->Concepto, 1, 30) :$partida->documento->Concepto;
                $tipo_operacion         = $partida->cuentaAbono->tipo_cuenta == 1? '98':'01';
                $tipo_cuenta_receptor   = $partida->cuentaAbono->tipo_cuenta == 1? '01':'40';


                $this->data[] =
                    '02' . /** Detalle del Archivo. Valor Fijo: 02 */
                    str_pad($this->linea, 7, 0, STR_PAD_LEFT) .
                    '60' .
                    str_pad($partida->documento->IDMoneda, 2, 0, STR_PAD_LEFT) .
                    date('Ymd') .
                    '014' .
                    str_pad($partida->cuentaAbono->banco->ctg_banco->clave, 3, 0, STR_PAD_LEFT) .
                    str_pad($monto[0] . $monto[1], 15, 0, STR_PAD_LEFT) .
                    str_pad('', 16, ' ', STR_PAD_LEFT) .
                    $tipo_operacion .
                    date('Ymd') .
                    '01' .
                    str_pad($partida->cuentaCargo->numero, 20, 0, STR_PAD_LEFT) .
                    str_pad(strtoupper($this->elimina_caracteres_especiales($razon_social_cargo)), 40, ' ', STR_PAD_RIGHT) .
                    str_pad(strtoupper($this->elimina_caracteres_especiales($partida->cuentaCargo->empresa->rfc)), '18', ' ', STR_PAD_RIGHT) .
                    $tipo_cuenta_receptor .
                    str_pad($partida->cuentaAbono->cuenta_clabe, 20, 0, STR_PAD_LEFT) .
                    str_pad(strtoupper($this->elimina_caracteres_especiales( $razon_social_abono)), '40', ' ', STR_PAD_RIGHT) .
                    str_pad(strtoupper($this->elimina_caracteres_especiales($partida->cuentaAbono->empresa->rfc)), '18', ' ', STR_PAD_RIGHT) .
                    str_pad($partida->documento->IDDocumento, 40, ' ', STR_PAD_RIGHT) .
                    str_pad(strtoupper($this->elimina_caracteres_especiales($partida->cuentaCargo->empresa->razon_social)), 40, ' ', STR_PAD_RIGHT) .
                    str_pad(0, 15, 0, STR_PAD_RIGHT) .
                    str_pad(1, 7, 0, STR_PAD_LEFT) .
                    str_pad(strtoupper($this->elimina_caracteres_especiales($concepto)), '40', ' ', STR_PAD_RIGHT) .
                    str_pad('', 30, ' ', STR_PAD_LEFT) .
                    '00' .
                    date('Ymd') .
                    str_pad('', 12, ' ', STR_PAD_LEFT) .
                    str_pad($this->remesa->id, 30, ' ', STR_PAD_RIGHT) .
                    str_pad(strtoupper($this->elimina_caracteres_especiales($descripcion_referencia)), 30, ' ', STR_PAD_RIGHT);
                $this->linea++;
            }
        }
    }

    function sumario(){
        $tipo_registro = '09';
        $numero_secuencia = str_pad($this->linea, 7, 0, STR_PAD_LEFT);
        $codigo_operacion = '60';
        $numero_bloque = date('d') . '00001';
        $numero_operaciones = str_pad($this->linea - 2, 7, 0, STR_PAD_LEFT);
        $monto_total = explode('.', number_format($this->remesa->monto_distribuido, '2', '.', ''));
        $importe_operaciones = str_pad($monto_total[0]. $monto_total[1], 18, 0, STR_PAD_LEFT);

        $this->data[] = $tipo_registro .
            $numero_secuencia .
            $codigo_operacion .
            $numero_bloque .
            $numero_operaciones .
            $importe_operaciones .
            str_pad('', 40, ' ') .
            str_pad('', 399, ' ');
    }

    function enviarRepositorioFtp($file){
        $path = Storage::disk('h2h_in')->getDriver()->getAdapter()->getPathPrefix();
        $cmd = str_replace('/', '\\', "copy " . $path . $file . " " . $this->cifrado_entrada . "\\" . $file);
        exec($cmd , $salida, $resp);
        if($resp != 0){echo 'Fallo copia a repositorio1';die;}
        $cmd = str_replace('/', '\\', "copy " . $this->cifrado_entrada . "\\" . $file . " " . $this->cifrado_salida . "\\" . $file );
        exec($cmd , $salida, $resp);
        if($resp != 0){echo 'Fallo copia a repositorio2';die;}

        $sftp = new SFTPConnection("172.50.32.48", 22);
        $sftp->login("ftpuser1", "12345");

        $sftp->uploadFile($this->cifrado_salida . "\\" . $file, $this->sftp_entrada . $file);

        dd('pardo ok', $sftp);

    }

    function elimina_caracteres_especiales($string){
        //echo $string;
        //$string = trim($string);
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );
        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string    );
        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );
        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );
        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );
        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C'),
            $string
        );
        $string = str_replace(
            array('&'),
            array('y'),
            $string
        );
        //     //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array("\\", "¨", "º", "-", "~",
                "#", "@", "|", "!", "\"",
                "·", "$", "%", "&", "/",
                "(", ")", "?", "'", "¡",
                "¿", "[", "^", "`", "]",
                "+", "}", "{", "¨", "´",
                ">", "<", ";", ",", ":",
                "."),
            '',
            $string
        );
        return $string;

    }
}
