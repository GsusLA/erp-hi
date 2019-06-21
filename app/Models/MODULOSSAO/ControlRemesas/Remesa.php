<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/05/2019
 * Time: 06:33 PM
 */

namespace App\Models\MODULOSSAO\ControlRemesas;


use App\Models\IGH\Usuario;
use App\Models\MODULOSSAO\Proyectos\Proyecto;
use Illuminate\Database\Eloquent\Model;

class Remesa extends Model
{

    protected $connection = 'modulosao';
    protected $table = 'ControlRemesas.Remesas';
    protected $primaryKey = 'IDRemesa';
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        self::addGlobalScope(function ($query){
            $id_proyecto = Usuario::getProyectoModuloSAO();
            return $query->where('IDProyecto', '=',$id_proyecto);
        });
    }

    public function documento()
    {
        return $this->hasMany(Documento::class, 'IDRemesa', 'IDRemesa');
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'IDProyecto', 'IDProyecto');
    }

    public function remesaLiberada()
    {
        return $this->belongsTo(RemesaLiberada::class, 'IDRemesa', 'IDRemesa');
    }

    public function scopeLiberada($query)
    {
        return $query->has('remesaLiberada');
    }

    public function getTipoAttibute(){
        if($this->IDTipoRemesa == 1){
            return 'Ordinaria';
        }
        if($this->IDTipoRemesa == 2){
            return 'Extraordinaria';
        }
        return null;
    }
}