<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14/01/19
 * Time: 08:56 AM
 */

namespace App\Models\CADECO\SubcontratosFG;

use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\Transaccion;
use Illuminate\Database\Eloquent\Model;

class FondoGarantia extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosFG.fondos_garantia';
    protected $primaryKey = 'id_subcontrato';
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        self::creating(function ($fondo) {

            $subcontrato = Subcontrato::find($fondo->id_subcontrato);
            if(!(float) $subcontrato->retencion>0){
                throw New \Exception('La retención de fondo de garantía establecida en el subcontrato no es mayor a 0, el fondo de garantía no puede generarse');
            }
            $fondo->created_at = date('Y-m-d h:i:s');
        });

    }

    public function subcontrato()
    {
        return $this->hasOne(Transaccion::class, "id_subcontrato");
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoFondoGarantia::class,"id_fondo_garantia");

    }

    public function solicitudes()
    {
        return $this->hasMany(SolicitudMovimientoFondoGarantia::class,"id_fondo_garantia");

    }

}