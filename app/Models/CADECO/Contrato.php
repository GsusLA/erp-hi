<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 11/03/2019
 * Time: 07:30 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'contratos';
    protected $primaryKey = 'id_concepto';
    protected $fillable = [
        'id_transaccion',
        'descripcion',
        'unidad',
        'cantidad_original',
        'cantidad_presupuestada',
        'cantidad_modificada',
        'estado',
        'clave',
        'id_marca',
        'id_modelo',
        'nivel'
    ];

    public $timestamps = false;
}