<?php


namespace App\Models\CADECO;


class EstimacionPartida extends Item
{
    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub


    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'item_antecedente', 'id_concepto');
    }

    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto', 'id_concepto');
    }

    public function getEstimadoAnteriorAttribute()
    {
        return Item::where('item_antecedente', '=', $this->item_antecedente)->where("id_transaccion", '<', $this->id_transaccion)->get()->sum('cantidad');
    }

    public function getAncestrosAttribute(){

        $list=array();
        $size = strlen($this->contrato->nivel)/4;
        $first=4;

            for($i=0; $i<$size-1;$i++){
                $nivel=substr($this->contrato->nivel,0,$first);
               $result= $this->contrato->where('id_transaccion','=',$this->contrato->id_transaccion)->where('id_concepto','<', $this->item_antecedente)->where('nivel','LIKE',$nivel)->get();
                array_push($list,[$result[0]->descripcion, $result[0]->nivel]);
                $first+=4;
            }
            return $list;
    }


}
