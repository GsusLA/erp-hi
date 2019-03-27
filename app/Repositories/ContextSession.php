<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 27/11/18
 * Time: 12:51 PM
 */

namespace App\Repositories;


use App\Contracts\Context;
use App\Models\CADECO\Obra;

class ContextSession implements Context
{

    private $auth;

    /**
     * ContextSession constructor.
     */
    public function __construct()
    {
        $this->auth = auth();
    }

    public function setContext(string $database, int $id_obra)
    {
        try {
            config()->set('database.connections.cadeco.database', $database);

            if(! $usuarioCadeco = $this->auth->user()->usuarioCadeco) {
                $obras = Obra::query()->whereNull('obras.id_obra');
            } else {
                if($usuarioCadeco->tieneAccesoATodasLasObras()) {
                    $obras = Obra::query();
                } else {
                    $obras = $usuarioCadeco->obras();
                }
            }

            if($obras->where('obras.id_obra', '=', $id_obra)->first()) {

                session()->put('db', $database);
                session()->put('id_obra', $id_obra);

                return $this->auth->claims(['db' => $database, 'obra' => $id_obra])->refresh();
            } else {
                abort('403', 'Forbidden');
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Devuelve el id de la obra sobre la que se está trabajando
     *
     * @return mixed
     */
    public function getIdObra()
    {
        return $this->auth->payload()->get('obra') != null ? $this->auth->payload()->get('obra') : (session()->get('id_obra') != null ? session()->get('id_obra') :  config()->get('app.id_obra'));
    }

    /**
     * Devuelve el nombre de la base de datos sobre la que se está trabajando
     *
     * @return mixed
     */
    public function getDatabase()
    {
        return $this->auth->payload()->get('db') != null ? $this->auth->payload()->get('db') : (session()->get('db') ? session()->get('db') : config()->get('database.connections.cadeco.database'));
    }

    /**
     * Nos dice si el contexto esta establecido
     *
     * @return bool
     */
    public function isEstablished(): bool
    {
        return $this->getDatabase() && $this->getIdObra();
    }
}