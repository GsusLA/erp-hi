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
        $api->post('getContext', 'App\Http\Controllers\v1\AuthController@getContext');
        $api->post('refresh', 'App\Http\Controllers\v1\AuthController@refresh');
        $api->get('obras', 'App\Http\Controllers\v1\AuthController@obras');
    });

    /**
     * DBO
     */
    $api->group(['middleware' => 'api'], function ($api) {
        // CUENTAS
        $api->group(['prefix' => 'cuenta'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\CuentaController@index');
        });
    });

    /**
     * CONTABILIDAD
     */
    $api->group(['middleware' => 'api', 'prefix' => 'contabilidad'], function ($api) {
        //CUENTAS DE ALMACÉN
        $api->group(['prefix' => 'cuenta-almacen'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaAlmacenController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaAlmacenController@find')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaAlmacenController@update')->where(['id' => '[0-9]+']);
        });

        //CUENTAS DE EMPRESA
        $api->group(['prefix' => 'cuenta-empresa'], function ($api){
           $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaEmpresaController@paginate');
           $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaEmpresaController@find')->where(['id' => '[0-9]+']);
           $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaEmpresaController@update')->where(['id' => '[0-9]+']);
        });

        //CUENTAS DE FONDO
        $api->group(['prefix' => 'cuenta-fondo'], function ($api){
            $api->get('paginate','App\Http\Controllers\v1\CADECO\Contabilidad\CuentaFondoController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaFondoController@find')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaFondoController@update')->where(['id' => '[0-9]+']);
        });

        //CUENTAS GENERALES
        $api->group(['prefix' => 'cuenta-general'], function ($api){
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaGeneralController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaGeneralController@find')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\CuentaGeneralController@update')->where(['id' => '[0-9]+']);
        });

        //ESTATUS PREPÓLIZA
        $api->group(['prefix' => 'estatus-prepoliza'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\EstatusPrepolizaController@index');
        });

        //PÓLIZAS
        $api->group(['prefix' => 'poliza'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@find')->where(['id' => '[0-9]+']);
            $api->patch('{id}', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@update')->where(['id' => '[0-9]+']);
            $api->patch('{id}/omitir', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@omitir')->where(['id' => '[0-9]+']);
            $api->patch('{id}/validar', 'App\Http\Controllers\v1\CADECO\Contabilidad\PolizaController@validar')->where(['id' => '[0-9]+']);
        });

        //TIPOS CUENTA CONTABLE
        $api->group(['prefix' => 'tipo-cuenta-contable'], function($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoCuentaContableController@index');
        });

        //TIPOS PÓLIZA CONTPAQ
        $api->group(['prefix' => 'tipo-poliza-contpaq'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Contabilidad\TipoPolizaContpaqController@index');
        });
    });

    /**
     * TESORERIA
     */
    $api->group(['middleware' => 'api', 'prefix' => 'tesoreria'], function ($api) {
        //MOVIMIENTOS BANCARIOS
        $api->group(['prefix' => 'movimiento-bancario'], function ($api) {
            $api->get('paginate', 'App\Http\Controllers\v1\CADECO\Tesoreria\MovimientoBancarioController@paginate');
            $api->get('{id}', 'App\Http\Controllers\v1\CADECO\Tesoreria\MovimientoBancarioController@find');
            $api->delete('{id}', 'App\Http\Controllers\v1\CADECO\Tesoreria\MovimientoBancarioController@destroy');
        });

        //TIPOS MOVIMIENTO
        $api->group(['prefix' => 'tipo-movimiento'], function ($api) {
            $api->get('/', 'App\Http\Controllers\v1\CADECO\Tesoreria\TipoMovimientoController@index');
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
});