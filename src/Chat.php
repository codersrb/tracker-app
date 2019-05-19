<?php

namespace Radar;

use Psr\Http\Message\RequestInterface;
use Radar\Traits\WsTrait;
use Ratchet\Http\HttpServerInterface;
use Ratchet\ConnectionInterface;

/**
 * Class Chat
 * @package MyApp
 */
class Chat implements HttpServerInterface
{
    use WsTrait;

    /**
     * @var \SplObjectStorage
     */
    private $clients;
    /**
     * @var UserService
     */
    private $users;

    /**
     * Chat constructor.
     */
    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->users = new UserService();
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    /**
     * @param ConnectionInterface $from
     * @param string $msg Input data
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg);
        switch ($data->action) {
            case 'login' :
                $data = $this->users->loginAction($data->userName, $data->geo, $from);
                $this->sendToSelf($data, $from);

                if($data['status'] == 'success') {
                    $data = $this->users->getUsersAction();
                    $this->sendToAll($data);
                }
                break;
        }
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        $this->users->logoutAction($conn);

        $data = $this->users->getUsersAction();
        $this->sendToAll($data);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}