<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 05:54 PM
 */

namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class EntradaEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.entradas_eliminadas';
    protected $primaryKey = 'id_transaccion';

    protected $fillable = [
        'id_transaccion',
        'id_antecedente',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'id_obra',
        'id_empresa',
        'id_sucursal',
        'id_moneda',
        'cumplimiento',
        'vencimiento',
        'opciones',
        'anticipo',
        'referencia',
        'comentario',
        'observaciones',
        'TipoLiberacion',
        'FechaHoraRegistro',
        'usuario_elimina',
        'motivo_eliminacion',
        'fecha_eliminacion'
    ];

    public $timestamps = false;
}