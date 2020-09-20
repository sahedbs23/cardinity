<?php

namespace App\Repositories;

use App\Repositories\Contracts\SessionRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

abstract class AbstractSessionRepository implements SessionRepository
{
    /**
     * Name of the session key
     *
     * @var string
     */
    protected $sessionKey;

    protected $session;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setSessionKey();
    }

    /**
     * Instantiate Model
     *
     * @throws \Exception
     */
    public function setSessionKey()
    {
        //check if the sessionKey exists
        if (empty($this->sessionKey)) {
            throw new \Exception('Session key not defined');
        }
    }

    /**
     * Get Model instance
     *
     * @return array
     */
    public function getSession()
    {
        return Session::get($this->sessionKey);
    }

    /**
     * @inheritdoc
     */
    public function find(int $id = 0)
    {
        if (!$id){
            return $this->getSession();
        }
        $carts =  $this->getSession();
        return isset($carts[$id]) ? $carts[$id] : null;
    }

    /**
     * @inheritdoc
     */
    public function save($id, $data)
    {
        $carts =  $this->getSession();
        $carts[$id] = $data;
        Session::put($this->sessionKey,$carts);
    }

    /**
     * @inheritdoc
     */
    public function update($id, $data)
    {
        $this->save($id,$data);
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id,$deleteAll = false)
    {
        if ($deleteAll){
            Session::put($this->sessionKey);
            return true;
        }
        $carts =  $this->getSession();
        if (isset($carts[$id])){
            unset($carts[$id]);
            Session::put($this->sessionKey,$carts);
            return true;
        }
        return false;
    }



}
