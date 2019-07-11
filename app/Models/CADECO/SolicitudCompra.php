<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Transaccion;
use App\Models\IGH\Usuario;

class SolicitudCompra extends Transaccion
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->where('tipo_transaccion', '=', 17);
        });
    }

    public $searchable = [
        'numero_folio',
        'observaciones',
        'fecha'
    ];

    public function getRegistroAttribute()
    {
        $comentario = explode('|', $this->comentario);
        return $comentario[1];
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'registro', 'usuario');
    }
}