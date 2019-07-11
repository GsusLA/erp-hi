<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 01/07/2019
 * Time: 10:35 AM
 */


namespace App\PDF;


use App\Models\CADECO\Almacen;
use App\Models\CADECO\OrdenCompraPartida;
use App\Models\CADECO\Compras\OrdenCompraPartidaComplemento;
use App\Models\CADECO\SolicitudCompra;
use App\Models\CADECO\Concepto;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Entrega;
use App\Models\CADECO\Item;
use App\Models\CADECO\Material;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Sucursal;
use Carbon\Carbon;
use App\Facades\Context;
use App\Models\CADECO\OrdenCompra;
use App\Models\CADECO\Moneda;
use App\Models\CADECO\Cambio;
use Ghidev\Fpdf\Rotation;




class OrdenCompraFormato extends Rotation
{

    protected $obra;
    protected $ordenCompra;
    protected $objeto_ordenCompra;

    private $encola = '',
        $archivo='',
        $clausulado = '',
        $con_fianza = 0,
        $tipo_orden = null,
        $encabezado_pdf,
        $conFirmaDAF = false,
        $id_tipo_fianza = 0,
        $folio_sao,
        $sin_texto,
        $NuevoClausulado = 0,
        $dim=0,
        $dim_aux=0,
        $obs_item='',
        $id_antecedente;


    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;
    const MAX_WIDTH = 225;
    const MAX_HEIGHT = 180;

    /**
     * OrdenCompraFormato constructor.
     * @param $ordenCompra
     */
    public function __construct($ordenCompra)
    {
        parent::__construct('P', 'cm', 'A4');

        $this->obra = Obra::find(Context::getIdObra());

        $this->id_oc=$ordenCompra;
        if(!(empty($this->ordenCompra[0]->complemento->fecha_entrega))){
            $this->fecha_entrega= $this->ordenCompra[0]->complemento->fecha_entrega;
            $this->domicilio=$this->ordenCompra[0]->complemento->domicilio_entrega;
            $this->plazo=$this->ordenCompra[0]->complemento->plazos_entrega_ejecucion;
            $this->condiciones=$this->ordenCompra[0]->complemento->otras_condiciones;
            $this->descuento=$this->complemento[0]->descuento;
        }else{
            $this->fecha_entrega='';
            $this->domicilio='';
            $this->plazo='';
            $this->condiciones='';
            $this->descuento='';
        }

        $this->ordenCompra = OrdenCompra::with('solicitud','partidas','complemento','solicitud')->where('id_transaccion', '=', $ordenCompra)->get();
        $this->fecha = substr($this->ordenCompra[0]->fecha, 0, 10);
        $this->id_antecedente=$this->ordenCompra[0]->id_antecedente;

        $this->sucursal=Sucursal::where('id_sucursal','=',$this->ordenCompra[0]->id_sucursal )->get();
        $this->sucursal_direccion=$this->sucursal[0]->direccion;
        $this->id_empresa=$this->sucursal[0]->id_empresa;
        $this->empresa=Empresa::where('id_empresa','=',$this->id_empresa)->get();
        $this->empresa_nombre=$this->empresa[0]->razon_social;
        $this->empresa_rfc=$this->empresa[0]->rfc;

        $versiones = OrdenCompra::where('id_transaccion', '=', $this->ordenCompra[0]->id_transaccion)->count();
        $this->folio_sao = $this->ordenCompra[0]->numero_folio_format;
        $this->requisicion_folio_sao =str_pad($this->ordenCompra[0]->solicitud->numero_folio_format, 5, '0', STR_PAD_LEFT);
        $this->obra_nombre = $this->obra->nombre;



        // @ TODO clausulado provisional hasta definir estructura nueva -Gsus-
        $this->NuevoClausulado = 0;
        $this->archivo='';


        if (strtotime($this->fecha) >= '2019-04-08' and Context::getDatabase() <> "SAO1814_TERMINAL_NAICM") {
            $this->NuevoClausulado = 1;
            $this->archivo = 'Clausulado_2019.jpg';
        } // fin if comparación de fecha
        else {
            if (Context::getDatabase() == "SAO_CORP") {
                $this->conFirmaDAF = true;
            } else {
                $this->conFirmaDAF = false;
            }
            switch (Context::getDatabase()) {
                case "SAO1814_SPM_MOBILIARIO":
                    $this->archivo = $this->ordenCompra[0]->complemento->con_fianza == 0 ? 'ClausuladoHSPMSF.jpg' : 'ClausuladoHSPM.jpg';
                    break;
                case "SAO1814_MUSEO_BARROCO":
                    $this->archivo = $this->ordenCompra[0]->complemento->con_fianza == 0 ? 'ClausuladoMIBSF.jpg' : 'ClausuladoMIB.jpg';
                    break;
                case "SAO1814_HOTEL_DREAMS_PM":
                    if (Context::getId() == 1) {
                        switch ($this->ordenCompra[0]->complemento->con_fianza) {
                            case 0:
                                $this->archivo = "ClausuladoDreamsSF_COD.jpg";
                                break;
                            case 1:
                                $this->archivo = "ClausuladoDreams_COD.jpg";
                                break;
                            case 2:
                                $this->archivo = "ClausuladoDreams3F_COD.jpg";
                                break;
                            case 3:
                                $this->archivo = "ClausuladoDreams2FSVO_COD.jpg";
                                break;
                            case 4:
                                $this->archivo = "ClausuladoDreamsFA_COD.jpg";
                                break;
                            case 5:
                                $this->archivo = "ClausuladoDreamsPagare_COD.jpg";
                                break;
                            case 6:
                                $this->archivo = "ClausuladoDreamsPagYFBC_COD.jpg";
                                break;
                        }
                    } else {
                        switch ($this->ordenCompra[0]->complemento->con_fianza) {
                            case 0:
                                $this->archivo = "ClausuladoDreamsSF.jpg";
                                break;
                            case 1:
                                $this->archivo = "ClausuladoDreams.jpg";
                                break;
                            case 2:
                                $this->archivo = "ClausuladoDreams3F.jpg";
                                break;
                            case 3:
                                $this->archivo = "ClausuladoDreams2FSVO.jpg";
                                break;
                            case 4:
                                $this->archivo = "ClausuladoDreamsFA.jpg";
                                break;
                            case 5:
                                $this->archivo = "ClausuladoDreamsPagare.jpg";
                                break;
                            case 6:
                                $this->archivo = "ClausuladoDreamsPagYFBC.jpg";
                                break;
                            case 7:
                                $this->archivo = "ClausuladoDreamsEspecialCintas.jpg";
                                break;
                        }
                    }
                    break;
                case "SAO1814_TERMINAL_NAICM":
                    $this->archivo = "Clausulado_ctvm.jpg";
                    break;
                case "SAO1814_TUNEL_DRENAJE_PRO":
                    $this->archivo = "Clausulado_tunel_drenaje_pro.jpg";
                    break;
                default:
                    $this->archivo = "Clausulado.jpg";
            }
        }

        $this->clausulado_page=public_path('pdf/clausulados/'.$this->archivo);
        $this->SetAutoPageBreak(true, 3);
        $this->sin_texto=public_path('pdf/clausulados/SinTexto.jpg');


//        $this->clausulado_page = $this->setSourceFile(public_path('pdf/clausulados/MDD.pdf'));
//        $this->clausulado_page = $this->setSourceFile(public_path('pdf/clausulados/' . $this->archivo));
//
//        $this->SetAutoPageBreak(true, 3);
//       $this->clausulado=$this->importPage($this->clausulado_page );
//
//        $this->setSourceFile(public_path('pdf/clausulados/SinTexto.pdf'));
//        $this->sin_texto =  $this->importPage(1);

    }

    function Header()
    {
        $residuo = $this->PageNo() % 2;

        if ($residuo > 0) {
            $postTitle = .7;


            $this->Cell(11.5);
            $x_f = $this->GetX();
            $y_f = $this->GetY();


            $this->SetTextColor('0,0,0');
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(4.5, .7, utf8_decode('NÚMERO '), 'LT', 0, 'L');
            $this->Cell(3.5, .7, $this->folio_sao, 'RT', 0, 'L');
            $this->Ln(.7);
            $y_f = $this->GetY();

            $this->SetFont('Arial', 'B', 24);
            $this->Cell(11.5, $postTitle, 'ORDEN DE COMPRA ', 0, 0, 'C', 0);
            $this->Ln();



            $this->SetY($y_f);
            $this->SetX($x_f);
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(4.5, .7, 'FECHA ', 'L', 0, 'L');
            $this->Cell(3.5, .7, date("d-m-Y", strtotime($this->fecha)) . ' ', 'R', 0, 'L');
            $this->Ln(.7);

            $this->Cell(11.5);
            $this->Cell(4.5, .7, 'SOLICITUD ', 'LB', 0, 'L');
            $this->Cell(3.5, .7, $this->requisicion_folio_sao . ' ', 'RB', 1, 'L');
            $this->Ln(.5);

            $this->SetFont('Arial', 'B', 13);
            $this->SetWidths([19.5]);
            $this->SetRounds(['1234']);
            $this->SetRadius([0.2]);
            $this->SetFills(['255,255,255']);
            $this->SetTextColors(['0,0,0']);
            $this->SetHeights([0.7]);
            $this->SetRounds(['1234']);
            $this->SetRadius([0.2]);
            $this->SetAligns("C");

            $this->Row([utf8_decode($this->obra_nombre . '  ' . " ")]);
            $this->Ln(.5);
            $this->SetFont('Arial', '', 10);
            $this->Cell(9.5, .7, 'Proveedor', 0, 0, 'L');
            $this->Cell(.5);
            $this->Cell(9.5, .7, 'Cliente (Facturar a)', 0, 0, 'L');
            $this->Ln(.8);
            $y_inicial = $this->getY();
            $x_inicial = $this->getX();
            $this->MultiCell(9.5, .5,
                "empresa" . '
' . "sucrus" . '
' . "dsad", '', 'L');
            $y_final_1 = $this->getY();
            $this->setY($y_inicial);
            $this->setX($x_inicial + 10);
            $this->MultiCell(9.5, .5,
                utf8_decode("hora") . '
' . "dir factura" . '
' . "obra rfc", '', 'L');
            $y_final_2 = $this->getY();

            if ($y_final_1 > $y_final_2)
                $y_alto = $y_final_1;

            else
                $y_alto = $y_final_2;

            $alto = abs($y_inicial - $y_alto) + 1;
            $this->SetWidths([9.5]);
            $this->SetRounds(['1234']);
            $this->SetRadius([0.2]);
            $this->SetFills(['255,255,255']);
            $this->SetTextColors(['0,0,0']);
            $this->SetHeights([$alto]);
            $this->SetStyles(['DF']);
            $this->SetAligns("L");
            $this->SetFont('Arial', '', 10);
            $this->setY($y_inicial);
            $this->Row([""]);
            $this->setY($y_inicial);
            $this->setX($x_inicial);
            $this->MultiCell(9.5, .5,
                "" . $this->empresa_nombre.'
' . utf8_decode(strtoupper($this->sucursal_direccion)) . '
' . $this->empresa_rfc, '', 'L');

            $this->setY($y_inicial);
            $this->setX($x_inicial + 10);
            $this->Row([""]);

            $this->setY($y_inicial);
            $this->setX($x_inicial + 10);
            $this->MultiCell(9.5, .5,
                utf8_decode($this->obra->cliente) . '
' . $this->obra->direccion . '
' . $this->obra->rfc, '', 'L');

            $this->setY($y_alto);
            $this->Ln(.5);

            $this->SetFont('Arial', '', 6);
            $this->SetHeights([0.8]);

            // Cuadro partidas
            if ($this->encola == "partida") {
                $this->SetFillColor(180, 180, 180);
                $this->SetWidths([0.5, 1.5, 1.5, 2, 7, 2, 1, 2, 2]);
                $this->SetStyles(['DF', 'DF', 'DF', 'DF', 'DF', 'FD', 'FD', 'DF']);
                $this->SetRounds(['1', '', '', '', '', '', '', '', '2']);
                $this->SetRadius([0.2, 0, 0, 0, 0, 0, 0, 0, 0.2]);
                $this->SetFills(['180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180', '180,180,180']);
                $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
                $this->SetHeights([0.4]);
                $this->SetAligns(['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C']);
                $this->Row(["#", "Cantidad", "Unidad", "No. Parte", utf8_decode("Descripción"), "Precio", "% Descto.", "Precio Neto", "Importe"]);
                $this->SetRounds(['', '', '', '', '', '', '', '', '']);
                $this->SetFills(['255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255', '255,255,255']);
                $this->SetAligns(['C', 'R', 'C', 'L', 'L', 'R', 'R', 'R', 'R']);
                $this->SetTextColors(['0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0', '0,0,0']);
            } else if ($this->encola == "observaciones_partida") {
                $this->SetRadius([0]);
                $this->SetTextColors(['150,150,150']);
                $this->SetWidths([19.5]);
                $this->SetAligns(['J']);
            }
//            } else if ($this->encola == "centro_costo") {
//                $this->SetRadius([0]);
//                $this->SetTextColors(['0,0,0']);
//                $this->SetWidths([19.5]);
//                $this->SetAligns(['J']);
            else if ($this->encola == "observaciones_encabezado") {
                $this->SetWidths([19.5]);
                $this->SetRounds(['12']);
                $this->SetRadius([0.2]);
                $this->SetFills(['180,180,180']);
                $this->SetTextColors(['0,0,0']);
                $this->SetHeights([0.5]);
                $this->SetFont('Arial', '', 9);
                $this->SetAligns(['C']);
            } else if ($this->encola == "observaciones") {
                $this->SetRounds(['34']);
                $this->SetRadius([0.2]);
                $this->SetAligns(['J']);
                $this->SetStyles(['DF']);
                $this->SetFills(['255,255,255']);
                $this->SetTextColors(['0,0,0']);
                $this->SetHeights([0.5]);
                $this->SetFont('Arial', '', 9);
                $this->SetWidths([19.5]);
            }
        } else {
            if ($this->NuevoClausulado == 1) {

//                $this->SetTextColor('0,0,0');
//                $this->SetFont('Arial', 'B', 10);
//
//                $this->Cell(10);
//                $this->CellFitScale(4.6, .7, utf8_decode('OBRA: '), '', 0, 'R');
//                $this->CellFitScale(5, .7,   $this->obra_nombre  . ' ', 'LRT', 0, 'L');
//                $this->Ln(.5);
//
//
//                $this->Cell(10);
//                $this->CellFitScale(4.6, .7, utf8_decode('FECHA: '), '', 0, 'R');
//                $this->CellFitScale(5, .7, date("d-m-Y", strtotime($this->fecha)) . ' ', 'LR', 0, 'L');
//                $this->Ln(.5);
//
//                $this->Cell(10);
//                $this->CellFitScale(4.6, .5, utf8_decode('NÚMERO: '), '', 0, 'R');
//                $this->CellFitScale(5, .5,  $this->folio_sao . ' ', 'LRB', 0, 'L');
                $this->Ln(.5);


                // -----------------------   pie
                $this->Ln(19.5);

                $this->SetFont('Arial', 'B', 4);
                $this->image($this->sin_texto, 0, 5, 21);
//                $this->Cell(10);
//                $this->Cell(4.6, .5, utf8_decode('"EL CLIENTE"'), 'LT', 0, 'C');
//                $this->Cell(5, .5, utf8_decode('"EL PROVEEDOR"'), 'LRT', 0, 'C');
                $this->Ln(.4);


//                $this->Cell(10);
//                $this->CellFitScale(4.6, 1.6, '   ____________________________________________   ', 'LT', 0, 'C');
//                $this->CellFitScale(5, 1.6, '   ____________________________________________   ', 'LTR', 0, 'C');
//                $this->Ln(1);
//
//
//                $this->Cell(10);
//                $this->CellFitScale(4.6, .4, utf8_decode($this->ordenCompra[0]->obra->facturar), 'LT', 0, 'C');
//                $this->CellFitScale(5, .4, utf8_decode($this->ordenCompra[0]->empresa->razon_social), 'LTR', 0, 'C');
//                $this->Ln(.4);
//
//
//                $this->Cell(10);
//                $this->Cell(4.6, .2, 'APODERADO LEGAL, FACTOR o', 'LTR', 0, 'C');
//                $this->Cell(5, .2, 'APODERADO LEGAL, FACTOR o', 'RT', 0, 'C');
//                $this->Ln(.2);
//
//                $this->Cell(10);
//
//                $this->Cell(4.6, .2, 'DEPENDIENTE', 'LB', 0, 'C');
//                $this->Cell(5, .2, 'DEPENDIENTE', 'LRB', 0, 'C');
                $this->Ln(.2);
//                $this->Ln(.7);
//                $this->Ln(.8);
//                $this->Ln(.8);
//                $this->Ln(.8);
//                $x_f = $this->GetX();
//                $y_f = $this->GetY();
//                $this->Cell(20,10);
//                $this->SetTextColor('0,0,0');
//                $this->SetFont('Arial', 'B', 14);
//                $this->Cell(4.5, .7, utf8_decode('NÚMERO '), 'LT', 0, 'L');
//                $this->Cell(3.5, .7, $this->folio_sao, 'RT', 0, 'L');


            }else if ($this->NuevoClausulado==2){
                $this->SetTextColor('0,0,0');
                $this->SetFont('Arial', 'B', 10);

                $this->Cell(11.5);
                $this->Cell(3, .7, utf8_decode('NÚMERO OC: '), 'LT', 0, 'L');
                $this->Cell(5, .7, $this->folio_sao . ' ', 'RT', 0, 'L');
                $this->Ln(.5);

                $this->Cell(11.5);
                $this->Cell(3, .7, utf8_decode('OBRA: '), 'L', 0, 'L');
                $this->Cell(5, .7, $this->obra_nombre  . ' ', 'R', 0, 'L');
                $this->Ln(.5);

                $this->SetFont('Arial', 'B', 10);
                $this->Cell(11.5);
                $this->Cell(3, .5, 'FECHA: ', 'L', 0, 'L');
                $this->Cell(5, .5, date("d-m-Y", strtotime($this->fecha))  . ' ', 'R', 0, 'L');
                $this->Ln(.5);

                $this->SetFont('Arial', 'B', 10);
                $this->Cell(11.5);
                $this->Cell(3, .5, 'SOLICITUD: ', 'L', 0, 'L');
                $this->Cell(5, .5, $this->requisicion_folio_sao   . ' ', 'R', 0, 'L');
                $this->Ln(.5);

                $this->Cell(11.5);
                $this->Cell(4.5, .5, 'TOTAL: ', 'LB', 0, 'L');
                $this->Cell(3.5, .5, "$ " . number_format($this->ordenCompra[0]->monto, 2, '.', ','), 'RB', 1, 'L');
                $this->Ln(.5);
            }
            else {
                if (\Ghi\Core\Facades\Context::getDatabaseName() == "SAO1814_TERMINAL_NAICM") {
                    $this->SetTextColor('0,0,0');
                    $this->SetFont('Arial', 'B', 10);

                    $this->Cell(11.5);
                    $this->Cell(3, .7, utf8_decode('NO. OC: '), 'LT', 0, 'L');
                    $this->Cell(5, .7, $this->ordenCompra[0]->numero_folio . ' ', 'RT', 0, 'L');
                    $this->Ln(.5);

                    $this->Cell(11.5);
                    $this->Cell(3, .7, utf8_decode('OBRA: '), 'L', 0, 'L');
                    $this->Cell(5, .7, $this->obra_nombre  . ' ', 'R', 0, 'L');
                    $this->Ln(.5);

                    $this->SetFont('Arial', 'B', 10);
                    $this->Cell(11.5);
                    $this->Cell(3, .5, 'FECHA: ', 'L', 0, 'L');
                    $this->Cell(5, .5, date("d-m-Y", strtotime($this->fecha))  . ' ', 'R', 0, 'L');
                    $this->Ln(.5);

                    $this->SetFont('Arial', 'B', 10);
                    $this->Cell(11.5);
                    $this->Cell(3, .5, 'NO. SOLICITUD: ', 'L', 0, 'L');
                    $this->Cell(5, .5, $this->folio_sao  . ' ', 'R', 0, 'L');
                    $this->Ln(.5);

                    $this->Cell(11.5);
                    $this->Cell(4.5, .5, 'TOTAL: ', 'LB', 0, 'L');
                    $this->Cell(3.5, .5, "$ " . number_format($this->ordenCompra[0]->monto, 2, '.', ','), 'RB', 1, 'L');
                    $this->Ln(.5);
                } else {
                    $this->SetTextColor('0,0,0');
                    $this->SetFont('Arial', 'B', 14);

                    $this->Cell(11.5);
                    $this->Cell(4.5, .7, utf8_decode('NÚMERO OC '), 'LT', 0, 'L');
                    $this->Cell(3.5, .7, $this->ordenCompra[0]->numero_folio . ' ', 'RT', 0, 'L');
                    $this->Ln(.7);


                    $this->SetFont('Arial', 'B', 10);
                    $this->Cell(11.5);
                    $this->Cell(4.5, .5, 'FECHA ', 'L', 0, 'L');
                    $this->Cell(3.5, .5, date("d-m-Y", strtotime($this->fecha)) . ' ', 'R', 0, 'L');
                    $this->Ln(.5);

                    $this->SetFont('Arial', 'B', 10);
                    $this->Cell(11.5);
                    $this->Cell(4.5, .5, 'SOLICITUD ', 'L', 0, 'L');
                    $this->Cell(3.5, .5, $this->folio_sao  . ' ', 'R', 0, 'L');
                    $this->Ln(.5);

                    $this->Cell(11.5);
                    $this->Cell(4.5, .5, 'TOTAL ', 'LB', 0, 'L');
                    $this->Cell(3.5, .5, "$ " . number_format($this->ordenCompra[0]->monto, 2, '.', ','), 'RB', 1, 'L');
                    $this->Ln(.5);
                }
            }
        }
        $this->y_subtotal = $this->GetY();
    }

    public function totales()
    {
        if($this->dim_aux==1){
            $this->Ln(.8);
        }

        // Declara variables a usar.
        $id_costo="";
        $id_costo = $this->ordenCompra[0]->id_costo;
        if(empty($id_costo)){
            $id_costo='';
        }
        $total = $this->ordenCompra[0]->monto;
        $moneda = Moneda::where('id_moneda', '=', $this->ordenCompra[0]->id_moneda)->first()->nombre;
        $cambio = Cambio::where('id_moneda', '=', $this->ordenCompra[0]->id_moneda)->whereDate('fecha', '=', Carbon::now())->first();
        //dd($this->ordenCompra->id_moneda, $cambio);
        $tipo_cambio = $this->ordenCompra[0]->id_moneda != 1 ? $cambio->cambio : 1;
        $total_pesos = ($total * $tipo_cambio);
        $anticipo_monto = $this->ordenCompra[0]->anticipo_monto;
        $iva = $this->ordenCompra[0]->impuesto;
        $subtotal = $total - $iva;
        $anticipo_pactado_monetario = $total * $this->ordenCompra[0]->porcentaje_anticipo_pactado / 100;

        // @TODO usar tabla cotizacion_complemento
        //$descuento = RQCTOCCotizaciones::where('idtransaccion_sao', '=', $this->ordenCompra->id_referente)->first()->descuento;
        //$anticipo = RQCTOCCotizaciones::where('idtransaccion_sao', '=', $this->ordenCompra->id_referente)->first()->anticipo;

//        $descuento = $this->ordenCompra->cotizacion->complemento->descuento;
//        $anticipo = $this->ordenCompra->cotizacion->complemento->anticipo;

        $descuento= 0;
        // Subtotal antes del descuento global.
        $subtotal_antes_descuento = (100 * $subtotal) / (100 - (float) $descuento);
        $descuento_monetario = $subtotal_antes_descuento - $subtotal;
        //$anticipo_monetario = $total * $this->ordenCompra->anticipo / 100;

//        if (!is_null($this->ordenCompra[0]->complemento->id_forma_pago_credito))
//        {
//            $forma_pago_txt = is_null($this->ordenCompra[0]->complemento->id_forma_pago_credito) ? '' : FormaPagoCredito::where('id',  '=', $this->ordenCompra->complemento->id_forma_pago_credito)->first()->descripcion;
//            $tipo_credito_txt = false;
//        }
//
//        else
//        {
//            $forma_pago_txt = is_null($this->ordenCompra->complemento->id_forma_pago) ? '' : utf8_decode(FormaPago::where('id',  '=', $this->ordenCompra->complemento->id_forma_pago)->first()->descripcion);
//            $tipo_credito_txt = is_null($this->ordenCompra->complemento->id_tipo_credito) ? '' : TipoCredito::where('id', '=', $this->ordenCompra->complemento->id_tipo_credito)->first()->descripcion;
//        }
        $this->encola="";
        $this->y_subtotal = $this->GetY();
        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial', 'B', 9);
        $this->CellFitScale(17.5, .5, 'Subtotal Antes Descuento:', 0, 0,'R');
        $this->CellFitScale(2, .5, number_format($subtotal_antes_descuento, 2, '.', ','), 1, 0,'R');
        $this->Ln(.5);
        $this->CellFitScale(17.5, .5, 'Descuento Global ('. $descuento .'%):', 0, 0,'R');
        $this->CellFitScale(2, .5, number_format($descuento_monetario, 2, '.', ','), 1, 0,'R');
        $this->Ln(.5);

        $this->CellFitScale(17.5, .5, 'Subtotal:', 0, 0,'R');
        $this->CellFitScale(2, .5, number_format($subtotal, 2, '.', ','), 1, 0,'R');
        $this->Ln(.5);
        $this->CellFitScale(17.5, .5, 'IVA:', 0, 0,'R');
        $this->CellFitScale(2, .5, number_format($iva, 2, '.', ','), 1, 0,'R');
        $this->Ln(.5);
        $this->CellFitScale(17.5, .5, 'Total:', 0, 0,'R');
        $this->CellFitScale(2, .5, number_format($total, 2, '.', ','), 1, 0,'R');
        $this->Ln(.5);
        $this->CellFitScale(17.5, .5, 'Moneda:', 0, 0,'R');
        $this->CellFitScale(2, .5, $moneda, 1, 0,'R');
        $this->Ln(.5);

        $this->setY($this->y_subtotal+0.3);

        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial', 'B', 9);
        $this->CellFitScale(4, .5, 'Anticipo ('. "anticipo" .' %): ', 0, 0,'L');
        $this->SetFont('Arial', '', 9);
        $this->CellFitScale(2, .5, number_format($anticipo_monto, 2, '.', ','), 1, 0,'R');
        $this->Ln(.7);

        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial', 'B', 9);
        $this->CellFitScale(4, .5, 'Pago en Parcialidades ('. "porcentaje ant pactado" .' %): ', 0, 0,'L');
        $this->SetFont('Arial', '', 9);
        $this->CellFitScale(2, .5, number_format($anticipo_pactado_monetario, 2, '.', ','), 1, 0,'R');
        $this->Ln(.7);

        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial', 'B', 9);
        $this->CellFitScale(4, .5, 'Fecha de entrega: ', 0, 0,'L');
        $this->SetFont('Arial', '', 9);

        $this->CellFitScale(2, .5, (is_null($this->fecha_entrega) ? '' : date("d-m-Y", strtotime($this->fecha_entrega))), 1, 0,'R');
        $this->Ln(.7);
        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial', 'B', 9);
        $this->CellFitScale(4, .5, 'Forma de Pago:', 0, 0,'L');
        $this->SetFont('Arial', '', 9);
        $this->CellFitScale(5, .5, utf8_decode(""), 1, 0,'L');
        $this->Ln(.7);

//        if($tipo_credito_txt)
//        {
//            $this->SetTextColor(0,0,0);
//            $this->SetFont('Arial', 'B', 9);
//            $this->CellFitScale(4, .5, utf8_decode('Tipo de Crédito:'), 0, 0,'L');
//            $this->SetFont('Arial', '', 9);
//            $this->CellFitScale(5, .5, utf8_decode($tipo_credito_txt), 1, 0,'L');
//            $this->Ln(.7);
//        }

        $this->Ln(0.4);
        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial', 'B', 9);
        $this->CellFitScale(4, .5, 'Domicilio Entrega:', 0, 0,'L');

        $this->SetFont('Arial', '', 9);

        $this->MultiCell(15.5, .5, utf8_decode($this->domicilio), 1, 'J');
        $this->Ln(.2);

        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial', 'B', 7);

        $this->CellFitScale(4, .5, utf8_decode('Plazos de entrega / ejecución:'), 0, 0,'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(15.5, .5, utf8_decode($this->plazo), 1, 'J');
        $this->Ln(.2);
        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial', 'B', 9);

        $this->CellFitScale(4, .5, 'Otras Condiciones:', 0, 0,'L');
        $this->SetFont('Arial', '', 9);
        $tipo_gasto = 'NO REGISTRADO';
        $this->MultiCell(15.5, .5, utf8_decode($tipo_gasto), 1, 'J');

        // Tipo de gasto para pista  id_costo
        if (in_array(Context::getDatabase(), ["SAO1814_PISTA_AEROPUERTO", "SAO1814_DEV_PISTA_AEROPUERTO"]))
        {
            $tipo_gasto = 'NO REGISTRADO';
//            $tipos_gasto = Costo::where('id_costo', '=', $this->ordenCompra[0]->id_costo)->first();
//
//            if (!is_null($tipos_gasto))
//                $tipo_gasto = $tipos_gasto->descripcion;

            $this->Ln(.2);
            $this->SetTextColor(0,0,0);
            $this->SetFont('Arial', 'B', 9);
            $this->CellFitScale(4, .5, 'Tipo de Gasto:', 0, 0,'L');
            $this->SetFont('Arial', '', 9);
            $this->MultiCell(15.5, .5, utf8_decode($tipo_gasto), 1, 'J');
        }

        $this->Ln(.7);
        $this->SetWidths([19.5]);
        $this->SetRounds(['12']);
        $this->SetRadius([0.2]);
        $this->SetFills(['180,180,180']);
        $this->SetTextColors(['0,0,0']);
        $this->SetHeights([0.5]);
        $this->SetFont('Arial', '', 9);
        $this->SetAligns(['C']);
        $this->encola="observaciones_encabezado";
        $this->Row(["Observaciones"]);
        $this->SetRounds(['34']);
        $this->SetRadius([0.2]);
        $this->SetAligns(['J']);
        $this->SetStyles(['DF']);
        $this->SetFills(['255,255,255']);
        $this->SetTextColors(['0,0,0']);
        $this->SetHeights([0.5]);
        $this->SetFont('Arial', '', 9);
        $this->SetWidths([19.5]);
        $this->encola="observaciones";

        $this->Row([utf8_decode($this->ordenCompra[0]->observaciones)]);

        $this->NuevoClausulado=2;
        $this->AddPage();

//        $this->SetFont('Arial', 'B', 12);
//        $this->Cell(165);
//        $this->Cell(60,10, 'Principal');
//        $this->Cell(60,10, 'Anotador');
//        $this->Ln(5);
//        $this->Cell(165);
//        $this->Cell(60,10, 'Auxiliar');
//        $this->Cell(60,10, 'Cronometrador');
//        $this->Ln(5);
//        $this->Cell(10);
//        $this->Cell(10,10,'Encuentro');
//        $this->Cell(40);
//        $this->Cell(25,10, 'Fecha');
//        $this->Cell(15,10, 'Hora');
//        $this->Cell(30,10, 'Categoria');
//        $this->Cell(25,10, 'Compet.');
        $this->image($this->clausulado_page, 0, 3.1, 21);

    }



    public function partidas($partidas = [])
    {

        $this->Ln(.8);
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(180,180,180);
        $this->SetWidths([0.5,1.5,1.5,2.5,6.5,2,1,2,2]);
        $this->SetStyles(['DF','DF','DF','DF','DF','FD','FD','DF']);
        $this->SetRounds(['1','','','','','','','','2']);
        $this->SetRadius([0.2,0,0,0,0,0,0,0,0.2]);
        $this->SetFills(['180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180','180,180,180']);
        $this->SetTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);
        $this->SetHeights([0.4]);
        $this->SetAligns(['C','C','C','C','C','C','C','C','C']);
        $this->Row(["#","Cantidad", "Unidad", "No. Parte", utf8_decode("Descripción"), "Precio", "% Descto.","Precio Neto", "Importe"]);

        $this->item_ante=Item::where('id_transaccion','=',$this->id_antecedente)->get();

        $count_partidas = count($partidas);

        $ac = 0;

        foreach ( $partidas as $i=> $p)
        {

            $this->destino_item=Entrega::where('id_item','=',$this->item_ante[$i]->id_item)->get();


            if(!empty($this->destino_item[0]->id_concepto)){
                $item_arr= Concepto::where('id_concepto','=',$this->destino_item[0]->id_concepto)->where('id_obra','=',$this->ordenCompra[0]->obra->id_obra)->get();

                $this->obs_item=$item_arr[0]->descripcion;

            }else{
                if(!empty($this->destino_item[0]->id_almacen)){
                    $item_arr= Almacen::where('id_almacen','=',$this->destino_item[0]->id_almacen)->where('id_obra','=',$this->ordenCompra[0]->obra->id_obra)->get();
                    $this->obs_item=$item_arr[0]->descripcion;
                }else{
                    $this->obs_item='';
                }
            }


            $partida_comp=OrdenCompraPartidaComplemento::where('id_item','=', $p->id_item)->get();

            $this->material=Material::where('id_material','=',$p->id_material)->get();

            $ac++;
            $this->SetWidths([0.5,1.5,1.5,2.5,6.5,2,1,2,2]);
            $this->encola="partida";
            $this->SetRounds(['','','','','','','','','']);
            $this->SetFills(['255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255','255,255,255']);
            $this->SetAligns(['C','R','C','L','L','R','R','R','R']);
            $this->SetTextColors(['0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0','0,0,0']);

            $this->complemento=OrdenCompraPartidaComplemento::where("id_item","=",$this->material[0]->id_item)->get();

            if(!in_array(Context::getDatabase(), ['SAO1814_TERMINAL_NAICM']))
                if($count_partidas == ($i+1) && empty($partida_comp[0]->observaciones));
            {
                $this->SetRounds(['','','','','','','','','']);
                $this->SetRadius([0,0,0,0,0,0,0,0,0]);
            }

            $precio = !empty($p->descuento) ? $p->precio_material : $p->precio_unitario;
            $precio_neto = !empty($p->descuento) ? ($p->precio_material - ($p->descuento / 100)) : $p->precio_material;

            $this->Row([$ac,
                number_format($p->cantidad,2, '.', ','),
                $this->material[0]->unidad,
                $this->material[0]->numero_parte,
                utf8_decode($this->material[0]->descripcion),
                number_format($precio,2, '.', ','),
                (!empty($p->complemento->descuento) ? number_format($p->complemento->descuento,2, '.', ',') : '-'),
                number_format(round($precio_neto),2, '.', ','),
                number_format(round($precio_neto) * $p->cantidad,2, '.', ',')
            ]);

            // Centro de costo
            $this->encola="centro_costo";

            if($count_partidas == ($i+1) && (!is_null($p->complemento) && $p->complemento->observaciones ==''))
            {
                $this->SetRounds(['4','','','','','','','','3']);
                $this->SetRadius([0.2,0,0,0,0,0,0,0,0]);
            }


            $this->SetWidths([19.5]);
            $this->SetAligns(['L']);


            if(!empty( $this->obs_item)){
                $this->Row([utf8_decode($this->obs_item)]);
            }




            if(!empty($p->complemento->observaciones))
            {
                $this->SetTextColors(['150,150,150']);
                $this->SetWidths([19.5]);
                $this->SetAligns(['J']);
                if($count_partidas == ($i+1))
                {
                    $this->SetRounds(['4']);
                    $this->SetRadius([0.2]);
                }

                $this->encola="observaciones_partida";
                $this->Row([html_entity_decode(mb_convert_encoding($p->complemento->observaciones, 'HTML-ENTITIES', 'UTF-8'))]);
            }
        }

        $this->dim = $this->GetY();
        if($this->dim>19.8) {
            $this->AddPage();
            $this->dim_aux=1;
        }


    }



    public function Footer()
    {
        $residuo = $this->PageNo() % 2;

        $this->SetTextColor('0,0,0');

        // Firmas.
        if ($residuo > 0) {
            if (Context::getDatabase() == "SAO1814" && $this->ordenCompra[0]->obra->id_obra == 41) {
                $this->SetY(-4.5);
                $this->SetFont('Arial', '', 6);
                $this->SetFillColor(180, 180, 180);
                $this->Cell(3.92, .4, utf8_decode(''), 0, 0, 'C');
                $this->Cell(3.92, .4, utf8_decode('Elaboró'), 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, utf8_decode('Revisó'), 'TRLB', 0, 'C', 1);
                $this->Cell(7.84, .4, utf8_decode('Autorizó'), 'TRLB', 0, 'C', 1);
                $this->Ln();
                $this->Cell(3.92, .4, utf8_decode('Proveedor'), 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, 'Jefe Compras', 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, 'Gerente Administrativo', 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, utf8_decode('Control de Costos'), 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, 'Director de proyecto', 'TRLB', 0, 'C', 1);
                $this->Ln();

                $this->Cell(3.92, 1.2, '', 'TRLB', 0, 'C');
                $this->Cell(3.92, 1.2, '', 'TRLB', 0, 'C');
                $this->Cell(3.92, 1.2, '', 'TRLB', 0, 'C');
                $this->Cell(3.92, 1.2, '', 'TRLB', 0, 'C');
                $this->Cell(3.92, 1.2, '', 'TRLB', 0, 'C');
                $this->Ln();

                $this->Cell(3.92, .4, '', 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, 'LIC. BRENDA ELIZABETH ESQUIVEL ESPINOZA', 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, 'C.P. ROGELIO HERNANDEZ BELTRAN', 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, 'ING. JUAN CARLOS MARTINEZ ANTUNA', 'TRLB', 0, 'C', 1);
                $this->Cell(3.92, .4, 'ING. PEDRO ALFONSO MIRANDA REYES', 'TRLB', 0, 'C', 1);

            } else if (Context::getDatabase() == "SAO1814_SPM_MOBILIARIO" && $this->ordenCompra[0]->obra->id_obra == 1) {
                $this->SetY(-3.5);
                $this->SetFont('Arial', '', 6);
                $this->SetFillColor(180, 180, 180);
                $this->CellFitScale(6.53, .4, utf8_decode('Proveedor'), 'TRLB', 0, 'C', 1);
                $this->CellFitScale(6.53, .4, utf8_decode('Gerente de Procuración'), 'TRLB', 0, 'C', 1);
                $this->CellFitScale(6.53, .4, utf8_decode('Facturar a:'), 'TRLB', 0, 'C', 1);
                $this->Ln();
                $this->CellFitScale(6.53, .4, utf8_decode($this->ordenCompra[0]->empresa->razon_social), 'TRLB', 0, 'C', 1);
                $this->CellFitScale(6.53, .4, '', 'TRLB', 0, 'C', 1);
                $this->CellFitScale(6.53, .4, utf8_decode($this->ordenCompra[0]->obra->facturar), 'TRLB', 0, 'C', 1);
                $this->Ln();

                $this->CellFitScale(6.53, 1.2, '', 'TRLB', 0, 'C');
                $this->CellFitScale(6.53, 1.2, '', 'TRLB', 0, 'C');
                $this->CellFitScale(6.53, 1.2, '', 'TRLB', 0, 'C');
                $this->Ln();

                $this->CellFitScale(6.53, .4, '', 'TRLB', 0, 'C', 1);

                // Harcodeo intensifies!!!
                if ($this->ordenCompra->id_transaccion >= 42544)
                    $this->CellFitScale(6.53, .4, utf8_decode('SANDRA MOSQUEDA ALVARADO'), 'TRLB', 0, 'C', 1);

                else
                    $this->CellFitScale(6.53, .4, utf8_decode('LIC. KARLA HAYDE LÓPEZ-NIETO FÉLIX-DÍAZ'), 'TRLB', 0, 'C', 1);


                $this->CellFitScale(6.53, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'TRLB', 0, 'C', 1);


            } else if (Context::getDatabase() == "SAO1814_MUSEO_BARROCO") {
                $this->SetY(-3.5);
                $this->SetFont('Arial', '', 6);
                $this->SetFillColor(180, 180, 180);

                $this->CellFitScale(4.89, .4, utf8_decode('Proveedor'), 'TRLB', 0, 'C', 1);
                $this->CellFitScale(4.89, .4, utf8_decode('Gerente de Procuración'), 'TRLB', 0, 'C', 1);
                $this->CellFitScale(9.78, .4, utf8_decode('Facturar a:'), 'TRLB', 0, 'C', 1);
                $this->Ln();
                $this->CellFitScale(4.89, .4, utf8_decode($this->ordenCompra->empresa->razon_social), 'TRLB', 0, 'C', 1);
                $this->CellFitScale(4.89, .4, '', 'TRLB', 0, 'C', 1);
                $this->CellFitScale(9.78, .4, utf8_decode($this->ordenCompra[0]->obra->facturar), 'TRLB', 0, 'C', 1);
                $this->Ln();
                $this->CellFitScale(4.89, 1.2, '', 'TRLB', 0, 'C');
                $this->CellFitScale(4.89, 1.2, '', 'TRLB', 0, 'C');
                $this->CellFitScale(4.89, 1.2, '', 'TRLB', 0, 'C');
                $this->CellFitScale(4.89, 1.2, '', 'TRLB', 0, 'C');
                $this->Ln();
                $this->CellFitScale(4.89, .4, '', 'TRLB', 0, 'C', 1);

                if ($this->con_fianza == 0)
                    $this->CellFitScale(4.89, .4, utf8_decode(''), 'TRLB', 0, 'C', 1);

                else
                    $this->CellFitScale(4.89, .4, utf8_decode(''), 'TRLB', 0, 'C', 1);

                $this->CellFitScale(4.89, .4, utf8_decode('ING. LUIS HUMBERTO ESPINOSA HERNÁNDEZ'), 'TRLB', 0, 'C', 1);
                $this->CellFitScale(4.89, .4, utf8_decode('LIC. FERNANDO GONZÁLEZ ORTÍZ'), 'TRLB', 0, 'C', 1);

            } else if (Context::getDatabase() == "SAO1814_TERMINAL_NAICM") {
                $this->SetY(-3.5);
                $this->SetFont('Arial', 'B', 5);
                $this->SetY(-2.7);
                $this->Cell(2);
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0, 'C');
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0, 'C');
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0, 'C');
                $this->CellFitScale(4, .4, ('Vo.Bo.'), 1, 0, 'C');
                $this->Ln(.4);
                $this->Cell(2);
                $this->CellFitScale(4, .8, '', 1, 0, 'C');
                $this->CellFitScale(4, .8, '', 1, 0, 'C');
                $this->CellFitScale(4, .8, '', 1, 0, 'C');
                $this->CellFitScale(4, .8, '', 1, 0, 'C');
                $this->Ln(.8);
                $this->Cell(2);
                $this->CellFitScale(4, .3, utf8_decode('Gerente / Director de Área Solicitante'), 1, 0, 'C');
                $this->CellFitScale(4, .3, utf8_decode('Jefe de Compras'), 1, 0, 'C');
                $this->CellFitScale(4, .3, utf8_decode('Gerente / Director de Procuración'), 1, 0, 'C');
                $this->CellFitScale(4, .3, ('Director General'), 1, 0, 'C');

            } else if (Context::getDatabase() == "SAO1814_TUNEL_DRENAJE_PRO") {
                $this->SetY(-2.7);
                $this->Cell(4.5);
                $this->CellFitScale(5, .5, utf8_decode('Jefe de Compras'), 1, 0, 'C');
                $this->CellFitScale(5, .5, utf8_decode('Gerente Administrativo'), 1, 0, 'C');
                $this->CellFitScale(5, .5, utf8_decode('Gerente de Proyecto'), 1, 0, 'C');
                $this->Ln(.5);
                $this->Cell(4.5);
                $this->CellFitScale(5, 1.2, ' ', 1, 0, 'R');
                $this->CellFitScale(5, 1.2, ' ', 1, 0, 'R');
                $this->CellFitScale(5, 1.2, ' ', 1, 0, 'R');

            } else {

                if ($this->conFirmaDAF) {
                    $this->SetY(-3.7);
                    $this->Cell(19, .5, utf8_decode("Orden de Compra no válida sin la firma de la Dirección de Administración y Finanzas."), 0, 1, "L");
                    $this->Ln(0.3);
                    $y_idaf = $this->GetY();
                    $this->CellFitScale(4.9, .5, ('Proveedor'), 1, 0, 'C');
                    $this->CellFitScale(4.9, .5, ('Jefe Compras'), 1, 0, 'C');
                    $this->CellFitScale(4.9, .5, utf8_decode('Dirección de Administración y Finanzas'), 1, 0, 'C');
                    $this->Ln(.5);
                    $this->CellFitScale(4.9, 1.2, ' ', 1, 0, 'R');
                    $this->CellFitScale(4.9, 1.2, ' ', 1, 0, 'R');
                    $this->CellFitScale(4.9, 1.2, ' ', 1, 0, 'R');
                    $this->setY($y_idaf);
                    $this->Cell(14.7);
                    $this->CellFitScale(4.9, .5, utf8_decode('Aprobó'), 1, 0, 'C');
                    $this->Ln(.5);
                    $this->Cell(14.7);
                    $this->CellFitScale(4.9, 1.2, ' ', 1, 0, 'R');

                } else {
                    $this->SetY(-2.7);
                    $this->Cell(4.5);
                    $this->CellFitScale(5, .5, utf8_decode('Proveedor'), 1, 0, 'C');
                    $this->CellFitScale(5, .5, utf8_decode('Jefe Compras'), 1, 0, 'C');
                    $this->CellFitScale(5, .5, utf8_decode('Aprobó'), 1, 0, 'C');
                    $this->Ln(.5);
                    $this->Cell(4.5);
                    $this->CellFitScale(5, 1.2, ' ', 1, 0, 'R');
                    $this->CellFitScale(5, 1.2, ' ', 1, 0, 'R');
                    $this->CellFitScale(5, 1.2, ' ', 1, 0, 'R');
                }

            }
        }
        $this->SetY(-0.8);
        $this->SetFont('Arial', 'B', 8);

        if ($residuo > 0)
            $this->Cell(10, .3, utf8_decode('Términos y condiciones adicionales al reverso.'), 0, 1, 'L');

        else
            $this->Cell(10, .3, (''), 0, 1, 'L');

        $this->SetFont('Arial', 'BI', 6);
        $this->Cell(10, .3, utf8_decode('Formato generado desde el módulo de ordenes de compra. Fecha de registro: ' . date("d-m-Y", strtotime($this->fecha))), 0, 0, 'L');
        $this->Cell(9.5, .3, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'R');

    }


    public function agregaPagina()
    {
        $this->AddPageSH($this->CurOrientation);
        $this->useTemplatePDF($this->sin_texto, 0, -0.5, 22);
        $this->AddPage($this->CurOrientation);

    }


    function create()
    {
        $this->SetMargins(1, .5, 2);
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetAutoPageBreak(true, 4);
        // Partidas.
        $this->partidas($this->ordenCompra[0]->partidas);

        $a= $this->PageNo()%2;

        if($a==0){
            $this->AddPage();
            $this->totales();
        }
        else{
            $this->totales();
        }

        try {
            $this->Output('I', 'Formato - Orden de Compra.pdf', 1);
        } catch (\Exception $ex) {
            dd("error", $ex);
        }
        exit;
    }





}