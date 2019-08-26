<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;


use Illuminate\Database\Eloquent\Model;

class CtgEstadoGestionPago extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Finanzas.ctg_estado_gestion_pagos';
    public $timestamps = false;
}
