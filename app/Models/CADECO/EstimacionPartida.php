<?php


namespace App\Models\CADECO;


class EstimacionPartida extends Item
{
    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'item_antecedente', 'id_concepto');
    }

    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto', 'id_concepto');
    }

    public function getEstimadoAnteriorAttribute($id)
    {
       return Item::where('item_antecedente', '=', $this->item_antecedente)->where("id_transaccion", '<', $id)
           ->where("id_antecedente", '=', $this->id_antecedente)
           ->where('id_concepto', '!=', null)->get()->sum('cantidad');
    }

    public function getAncestrosAttribute(){

        $list=array();
        $size = strlen($this->contrato->nivel)/4;
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
