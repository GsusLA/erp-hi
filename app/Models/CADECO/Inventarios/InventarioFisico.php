<?php


namespace App\Models\CADECO\Inventarios;


use App\Facades\Context;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class InventarioFisico extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Inventarios.inventario_fisico';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    public function getNumeroFolioFormatAttribute()
    {
        return 'T '. str_pad($this->folio, 3,"0",STR_PAD_LEFT);
    }

    public static function calcularFolio()
    {
        $est = InventarioFisico::query()->orderBy('folio', 'DESC')->first();
        return $est ? $est->folio + 1 : 1;
    }

    public function marbetes(){
        return $this->hasMany(Marbete::class, 'id_inventario_fisico','id');
    }

    public function validar(){

        if(InventarioFisico::query()->where('estado', '=',0)->first() != null){
            abort(400,'Existen inventarios físicos no finalizados');
        }
        return true;
    }

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'usuario_inicia', 'idusuario');
    }

    public function getFechaHoraInicioFormatAttribute()
    {
        $date = date_create($this->fecha_hora_inicio);
        return date_format($date,"d-m-Y h:i");

    }

    public function getCantidadMarbetesAttribute()
    {
        return count($this->marbetes);

    }

    public function getEstadoFormatAttribute()
    {
        if($this->estado == 0){
            return 'Abierto';
        }else{
            return 'Cerrado';
        }

    }

}