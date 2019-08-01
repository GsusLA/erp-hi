<?php


namespace App\Models\CADECO;


class EmpresaFondoFijo extends Empresa
{
    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        self::addGlobalScope(function ($query){
            return $query->where('tipo_empresa', '=', 32);
        });

        self::creating(function ($model){
            $model->tipo_empresa = 32;
            $model->UsuarioRegistro = auth()->id();
            $model->razon_social= strtoupper($model->razon_social);
        });
    }
}