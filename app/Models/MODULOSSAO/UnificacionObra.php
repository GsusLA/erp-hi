<?php


namespace App\Models\MODULOSSAO;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class UnificacionObra extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'UnificacionProyectoObra';
    public $timestamps = false;

    protected static  function  boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        self::addGlobalScope(function ($query){
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }
}