<?php


namespace App\Models\CADECO\Contratos;


use Illuminate\Database\Eloquent\Model;

class AreaSubcontratante extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Contratos.cp_areas_subcontratantes';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_area_subcontratante'
    ];

    public function actualiza($id, $id_area){
        $contrato = AreaSubcontratante::find($id);
        $contrato->id_area_subcontratante = $id_area;
        $contrato->timestamp_registro = date('Y-m-d h:i:s');
        $contrato->id_usuario = auth()->id();
        $contrato->save();
        return $contrato;
    }

}