<?php


namespace App\Models\CADECO\Inventarios;


use App\Models\CADECO\Almacen;
use App\Models\CADECO\Material;
use Illuminate\Database\Eloquent\Model;

class Marbete extends  Model
{
    protected $connection = 'cadeco';
    protected $table = 'Inventarios.marbetes';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_inventario_fisico',
        'id_almacen',
        'id_material',
        'saldo',
        'folio'
    ];

    public function inventarios(){
        return $this->hasMany(Almacen::class,'id_almacen','id_almacen');
    }

    public function materiales(){
        return $this->hasMany(Material::class,'id_material','id_material');
    }

}