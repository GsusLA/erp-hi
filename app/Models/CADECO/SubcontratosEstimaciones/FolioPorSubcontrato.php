<?php


namespace App\Models\CADECO\SubcontratosEstimaciones;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class FolioPorSubcontrato extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosEstimaciones.FolioPorSubcontrato';
    protected $primaryKey = 'IDSubcontrato';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'IDSubcontrato',
        'UltimoFolio'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('IDObra', '=', Context::getIdObra());
        });

        self::creating(function($model) {
            $model->IDObra = Context::getIdObra();
        });
    }
}