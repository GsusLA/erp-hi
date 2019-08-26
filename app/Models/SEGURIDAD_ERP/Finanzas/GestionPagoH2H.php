<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;


use App\Facades\Context;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesa;
use App\Models\SEGURIDAD_ERP\Proyecto;
use Illuminate\Database\Eloquent\Model;

class GestionPagoH2H extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Finanzas.gestion_pagos_h2h';
    protected $fillable = [
        'id_distribucion_remesa',
        'id_distribucion_remesa_layout',
        'nombre_archivo',
    ];

    protected static function boot()
    {
        parent::boot();

        // Global Scope para proyecto
        self::addGlobalScope(function ($query) {
            return $query->where('estado', '=',1);
        });

        static::creating(function ($model) {
            $model->id_usuario =  auth()->id();
            $model->id_proyecto = Proyecto::query()->where('base_datos', '=', Context::getDatabase())->first()->getKey();
            $model->id_obra = Context::getIdObra();
        });
    }

    public function estatus(){
        return $this->belongsTo(CtgEstadoGestionPago::class, 'estado', 'id');
    }

    public function proyecto(){
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id');
    }

    public function dispersion_remesa(){
        return $this->belongsTo(DistribucionRecursoRemesa::class,'id_distribucion_remesa', 'id');
    }

}
