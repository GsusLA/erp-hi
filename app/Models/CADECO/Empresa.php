<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 03:35 PM
 */

namespace App\Models\CADECO;

use App\Models\CADECO\Contabilidad\CuentaEmpresa;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.empresas';
    protected $primaryKey = 'id_empresa';

    public $timestamps = false;

    public function cuentaEmpresa()
    {
        return $this->hasOne(CuentaEmpresa::class, 'id_empresa');
    }
}