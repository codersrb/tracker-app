<?php

namespace Radar;

use Ratchet\ConnectionInterface;

/**
 * Class UserService
 * @package Radar
 */
class UserService
{
    /**
     * @var array $users App users
     */
    private $users = [];

    /**
     * login to app
     *
     * @param string $userName
     * @param ConnectionInterface $client
     * @param \stdClass $geo
     *
     * @return array
     */
    public function loginAction(string $userName, \stdClass $geo, ConnectionInterface &$client) : array
    {
        if($this->isUserNameExists($userName)) {
            $data =  [
                'action' => 'login',
                'status' => 'err',
                'message' => 'Username already exists.'
            ];
        } else {
            $this->users[$client->resourceId] = [
                'userName' => $userName,
                'geo' => $geo,
                'online' => true
            ];
            $data = [
                'action' => 'login',
                'status' => 'success',
                'message' => 'Login Successful',
                'data' => $userName
            ];
        }
        return $data;
    }

    /**
     * logout from app
     *
     * @param ConnectionInterface $client
     */
    public function logoutAction(ConnectionInterface &$client)
    {
        if(array_key_exists($client->resourceId, $this->users)) {
            $this->users[$client->resourceId]['online'] = false;
        }
    }

    /**
     * Get Active Users
     *
     * @return array
     */
    public function getUsersAction() : array
    {
        return [
            'action' => 'getUsers',
            'status' => 'success',
            'data' => $this->users
        ];
    }

    /**
     * Check if userName exists or not
     *
     * @param string $userName
     * @return bool
     */
    private function isUserNameExists(string $userName) : bool
    {
        foreach($this->users as $eachUser) {
            if($eachUser['userName'] == $userName) {
                return true;
            }
        }
        return false;
    }
}