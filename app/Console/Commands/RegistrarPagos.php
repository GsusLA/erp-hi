<?php

namespace App\Console\Commands;

use App\Contracts\Context;
use App\Http\Controllers\v1\AuthController;
use App\LAYOUT\DistribucionRecursoRemesa;
use App\LAYOUT\GesionPagosH2H;
use App\LAYOUT\SFTPConnection;
use App\Models\SEGURIDAD_ERP\Finanzas\GestionPagoH2H;
use App\Services\AuthService;
use Illuminate\Console\Command;

class RegistrarPagos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'registrar:pagos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Registrar pagos desde respuesta H2H Santander';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    protected $context;
    public function __construct(Context $context)
    {
        parent::__construct();
        $this->context = $context;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $pagos_h2h = GestionPagoH2H::query()->get();
        foreach ($pagos_h2h as $pago){
            $gestion = new GesionPagosH2H($pago->proyecto->base_datos, $pago->id_obra, $pago->id_usuario);
            $gestion->buscar_respuesta_h2h($pago);
        }
    }
}
