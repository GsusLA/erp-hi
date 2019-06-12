<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['middleware' => 'api', 'prefix' => 'auth'], function ($api) {
        $api->post('login', 'App\Http\Controllers\v1\AuthController@login');
        $api->post('logout', 'App\Http\Controllers\v1\AuthController@logout');
        $api->post('setContext', 'App\Http\Controllers\v1\AuthController@setContext');
      /*  $api->get('obras', 'App\Http\Controllers\v1\AuthController@obras');*/
        $api->get('obras/paginate', 'App\Http\Controllers\v1\CADECO\ObraController@authPaginate');
        $api->get('obras/por-usuario/{id_usuario}', 'App\Http\Controllers\v1\CADECO\ObraController@porUsuario')->where(['id_usuario' => '[0-9]+']);
    });

    /**
     * DBO
     */
    $api->group(['middleware' => 'api'], function ($api) {
        // ALMACENES
        $api->group(['prefix' => 'almacen'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\AlmacenController@index');
        });

        // CONCEPTOS
        $api->group(['prefix' => 'concepto'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\ConceptoController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\ConceptoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\ConceptoController@show')->where(['id' => '[0-9]+']);
        });

        // COSTOS
        $api->group(['prefix' => 'costo'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\CostoController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\CostoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\CostoController@show')->where(['id' => '[0-9]+']);
        });

        // CUENTAS
        $api->group(['prefix' => 'cuenta'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\CuentaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\CuentaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\CuentaController@show')->where(['id' => '[0-9]+']);

        });

        // EMPRESAS
        $api->group(['prefix' => 'empresa'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\EmpresaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\EmpresaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\EmpresaController@show')->where(['id' => '[0-9]+']);
        });

        // FONDOS
        $api->group(['prefix' =>  'fondo'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\FondoController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\FondoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\FondoController@show')->where(['id' => '[0-9]+']);
        });

        // MATERIALES
        $api->group(['prefix' => 'material'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\MaterialController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\MaterialController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\MaterialController@show')->where(['id' => '[0-9]+']);
        });

        // MONEDA
        $api->group(['prefix' => 'moneda'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\MonedaController@index');
        });

        // OBRA
        $api->group(['prefix' => 'obra'], function ($api) {
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\ObraController@show');
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\ObraController@update');
        });
    });

    /**
     * CHARTS
     */
    $api->group(['middleware' => 'api', 'prefix' => 'chart'], function ($api) {
        $api->get('avance-cuentas-contables', 'App\Http\Controllers\v1\ChartController@avanceCuentasContables');
        $api->get('prepolizas-semanal', 'App\Http\Controllers\v1\ChartController@prepolizasSemanal');
        $api->get('prepolizas-acumulado', 'App\Http\Controllers\v1\ChartController@polizasDoughnut');
    });

    /**
     * CONTABILIDAD
     */
    $api->group(['middleware' => 'api', 'prefix' => 'contabilidad'], function ($api) {
        //CIERRES DE PERIODO
        $api->group(['prefix' => 'cierre-periodo'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\CierreController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CierreController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CierreController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}/abrir', 'App\Http\Controllers\v1\CADECO\Contabilidad\CierreController@abrir')->where(['id' => '[0-9]+']);
            $api->patch('{id}/cerrar', 'App\Http\Controllers\v1\CADECO\Contabilidad\CierreController@cerrar')->where(['id' => '[0-9]+']);
        });

        //CUENTAS DE ALMACÉN
        $api->group(['prefix' => 'cuenta-almacen'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaAlmacenController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaAlmacenController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaAlmacenController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaAlmacenController@update')->where(['id' => '[0-9]+']);
        });

        //CUENTAS DE BANCO
        $api->group(['prefix' => 'cuenta-banco'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaBancoController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaBancoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaBancoController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaBancoController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaBancoController@destroy')->where(['id' => '[0-9]+']);

        });

        //CUENTAS DE CONCEPTO
        $api->group(['prefix' => 'cuenta-concepto'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaConceptoController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaConceptoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaConceptoController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaConceptoController@update')->where(['id' => '[0-9]+']);
        });

        //CUENTAS DE COSTO
        $api->group(['prefix' => 'cuenta-costo'], function ($api){
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaCostoController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaCostoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaCostoController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaCostoController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaCostoController@destroy')->where(['id' => '[0-9]+']);
        });

        //CUENTAS DE EMPRESA
        $api->group(['prefix' => 'cuenta-empresa'], function ($api){
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaEmpresaController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaEmpresaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaEmpresaController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaEmpresaController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaEmpresaController@destroy')->where(['id' => '[0-9]+']);
        });

        //CUENTAS DE FONDO
        $api->group(['prefix' => 'cuenta-fondo'], function ($api){
            $api->post('/','App\Http\Controllers\v1\CADECO\Contabilidad\CuentaFondoController@store');
            $api->get('paginate','App\Http\Controllers\v1\CADECO\Contabilidad\CuentaFondoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaFondoController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaFondoController@update')->where(['id' => '[0-9]+']);
        });

        //CUENTAS GENERALES
        $api->group(['prefix' => 'cuenta-general'], function ($api){
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaGeneralController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaGeneralController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaGeneralController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaGeneralController@update')->where(['id' => '[0-9]+']);
        });

        //CUENTAS DE MATERIALES
        $api->group(['prefix' => 'cuenta-material'], function ($api){
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaMaterialController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaMaterialController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaMaterialController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaMaterialController@update')->where(['id' => '[0-9]+']);
        });

        // DATOS CONTABLES
        $api->group(['prefix' => 'datos-contables'], function ($api){
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\DatosContablesController@update')->where(['id' => '[0-9]+']);
        });

        //ESTATUS PREPÓLIZA
        $api->group(['prefix' => 'estatus-prepoliza'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\EstatusPrepolizaController@index');
        });

        //NATURALEZA PÓLIZA
        $api->group(['prefix' => 'naturaleza-poliza'], function($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\NaturalezaPolizaController@index');
        });

        //PÓLIZAS
        $api->group(['prefix' => 'poliza'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@update')->where(['id' => '[0-9]+']);
            $api->patch('{id}/omitir', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@omitir')->where(['id' => '[0-9]+']);
            $api->patch('{id}/validar', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@validar')->where(['id' => '[0-9]+']);
        });

        //TIPOS CUENTA CONTABLE
        $api->group(['prefix' => 'tipo-cuenta-contable'], function($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoCuentaContableController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoCuentaContableController@paginate');
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoCuentaContableController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoCuentaContableController@show')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoCuentaContableController@destroy')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoCuentaContableController@update')->where(['id' => '[0-9]+']);
        });

        //TIPOS CUENTA EMPRESA
        $api->group(['prefix' => 'tipo-cuenta-empresa'], function($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoCuentaEmpresaController@index');
        });

        //TIPOS CUENTA MATERIAL
        $api->group(['prefix' => 'tipo-cuenta-material'], function($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoCuentaMaterialController@index');
        });

        //TIPOS PÓLIZA CONTPAQ
        $api->group(['prefix' => 'tipo-poliza-contpaq'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoPolizaContpaqController@index');
        });

        //TRANSACCIÓN INTERFÁZ
        $api->group(['prefix' => 'transaccion-interfaz'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\TransaccionInterfazController@index');
        });
    });

    /**
     * COMPRAS
     */
    $api->group(['middleware' => 'api', 'prefix' => 'compras'], function ($api) {

        /**
         * ORDEN DE COMPRA
         */
        $api->group(['prefix' => 'orden-compra'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Compras\OrdenCompraController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Compras\OrdenCompraController@show')->where(['id' => '[0-9]+']);
        });
    });

    /**
     * CONTRATOS
     */
    $api->group(['middleware' => 'api', 'prefix' => 'contratos'], function ($api) {

        /**
         * ESTIMACIÓN
         */
        $api->group(['prefix' => 'estimacion'], function ($api) {
            /**
             * FORMATO ORDEN DE PAGO DE ESTIMACION
             */
                $api->get('{id}/formato-orden-pago', 'App\Http\Controllers\v1\CADECO\Contratos\EstimacionController@pdfOrdenPago')->where(['id' => '[0-9]+']);
        });


        /**
         * SUBCONTRATO
         */
        $api->group(['prefix' => 'subcontrato'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\SubcontratoController@show')->where(['id' => '[0-9]+']);
        });
        //FONDO DE GARANTÍA
        $api->group(['prefix' => 'fondo-garantia'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contratos\FondoGarantiaController@index');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contratos\FondoGarantiaController@paginate');
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Contratos\FondoGarantiaController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\FondoGarantiaController@show')->where(['id' => '[0-9]+']);
            $api->post('{id}/ajustar_saldo', 'App\Http\Controllers\v1\CADECO\Contratos\FondoGarantiaController@ajustarSaldo')->where(['id' => '[0-9]+']);
            //SOLICITUD DE MOVIMIENTO
            $api->group(['prefix' => 'solicitud-movimiento'], function ($api) {
                $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudMovimientoFondoGarantiaController@paginate');
                $api->post('/', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudMovimientoFondoGarantiaController@store');
                $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudMovimientoFondoGarantiaController@show')->where(['id' => '[0-9]+']);
                $api->patch('{id}/autorizar', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudMovimientoFondoGarantiaController@autorizar')->where(['id' => '[0-9]+']);
                $api->patch('{id}/rechazar', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudMovimientoFondoGarantiaController@rechazar')->where(['id' => '[0-9]+']);
                $api->patch('{id}/cancelar', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudMovimientoFondoGarantiaController@cancelar')->where(['id' => '[0-9]+']);
                $api->patch('{id}/revertir-autorizacion', 'App\Http\Controllers\v1\CADECO\Contratos\SolicitudMovimientoFondoGarantiaController@revertirAutorizacion')->where(['id' => '[0-9]+']);
            });
        });
    });

    /**
     * FINANZAS
     */
    $api->group(['middleware' => 'api', 'prefix' => 'finanzas'], function ($api) {

        /**
         * SOLICITUD DE PAGO ANTICIPADO
         */
        $api->group(['prefix' => 'solicitud-pago-anticipado'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudPagoAnticipadoController@paginate');
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudPagoAnticipadoController@store');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudPagoAnticipadoController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}/cancelar', 'App\Http\Controllers\v1\CADECO\Finanzas\SolicitudPagoAnticipadoController@cancelar')->where(['id' => '[0-9]+']);


        });
    });

    /**
     * PERSONALIZADO
     */
    $api->group(['middleware' => 'api', 'prefix' => 'seguridad'], function($api){

        //ESQUEMA PERSONALIZADO
        $api->group(['prefix' => 'rol'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Seguridad\RolController@store');
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Seguridad\RolController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Seguridad\RolController@show')->where(['id' => '[0-9]+']);
            $api->get('por-usuario/{user_id}', 'App\Http\Controllers\v1\CADECO\Seguridad\RolController@porUsuario')->where(['user_id' => '[0-9]+']);
            $api->post('asignacion-masiva', 'App\Http\Controllers\v1\CADECO\Seguridad\RolController@asignacionPersonalizada');
            $api->post('desasignacion-masiva', 'App\Http\Controllers\v1\CADECO\Seguridad\RolController@desasignacionPersonalizada');
            $api->post('asignacion-permisos', 'App\Http\Controllers\v1\CADECO\Seguridad\RolController@asignacionPermisos');
            //$api->post('crear-rol', 'App\Http\Controllers\v1\CADECO\Seguridad\RolController@crearRol');
        });
    });

      /**
     * TESORERIA
     */
    $api->group(['middleware' => 'api', 'prefix' => 'tesoreria'], function ($api) {
        //MOVIMIENTOS BANCARIOS
        $api->group(['prefix' => 'movimiento-bancario'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Tesoreria\MovimientoBancarioController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Tesoreria\MovimientoBancarioController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Tesoreria\MovimientoBancarioController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Tesoreria\MovimientoBancarioController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Tesoreria\MovimientoBancarioController@destroy')->where(['id' => '[0-9]+']);
        });

        //TRASPASO ENTRE CUENTAS
        $api->group(['prefix' => 'traspaso-entre-cuentas'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\CADECO\Tesoreria\TraspasoEntreCuentasController@store');
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Tesoreria\TraspasoEntreCuentasController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Tesoreria\TraspasoEntreCuentasController@show')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Tesoreria\TraspasoEntreCuentasController@update')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Tesoreria\TraspasoEntreCuentasController@destroy')->where(['id' => '[0-9]+']);
        });

        //TIPOS MOVIMIENTO
        $api->group(['prefix' => 'tipo-movimiento'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Tesoreria\TipoMovimientoController@index');
        });
    });


    /** SEGURIDAD ERP */
    $api->group(['middleware' => 'api', 'prefix' => 'SEGURIDAD_ERP'], function ($api) {
        $api->group(['prefix' => 'permiso'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PermisoController@index');
            $api->get('por-usuario/{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\PermisoController@porUsuario')->where(['id' => '[0-9]+']);
        });

        $api->group(['prefix' => 'rol'], function ($api) {
            $api->post('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\RolController@store');
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\RolController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\RolController@show')->where(['id' => '[0-9]+']);
            $api->delete('{id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\RolController@destroy')->where(['id' => '[0-9]+']);
            $api->get('por-usuario/{user_id}', 'App\Http\Controllers\v1\SEGURIDAD_ERP\RolController@porUsuario')->where(['user_id' => '[0-9]+']);
            $api->post('asignacion-masiva', 'App\Http\Controllers\v1\SEGURIDAD_ERP\RolController@asignacionMasiva');
            $api->post('desasignacion-masiva', 'App\Http\Controllers\v1\SEGURIDAD_ERP\RolController@desasignacionMasiva');
            $api->post('asignacion-permisos', 'App\Http\Controllers\v1\SEGURIDAD_ERP\RolController@asignacionPermisos');
        });

        $api->group(['prefix' => 'sistema'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\SistemaController@index');
            $api->get('sistemas-obra', 'App\Http\Controllers\v1\SEGURIDAD_ERP\SistemaController@porObra');
            $api->post('asignacion-sistemas', 'App\Http\Controllers\v1\SEGURIDAD_ERP\SistemaController@asignacionSistemas');
        });

        $api->group(['prefix' => 'tipo-proyecto'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\SEGURIDAD_ERP\TipoProyectoController@index');
        });
    });

    /** IGH */
    $api->group(['middleware' => 'api', 'prefix' => 'IGH'], function ($api) {
        $api->group(['prefix' => 'usuario'], function ($api) {
            $api->get('currentUser', 'App\Http\Controllers\v1\IGH\UsuarioController@currentUser');
            $api->get('/', 'App\Http\Controllers\v1\IGH\UsuarioController@index');
            $api->get('{id}', 'App\Http\Controllers\v1\IGH\UsuarioController@show')->where(['id' => '[0-9]+']);
        });

        $api->group(['prefix' => 'menu'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\IGH\MenuController@index');
        });
    });
});