<?php
namespace Radar\Traits;

use Ratchet\ConnectionInterface;

/**
 * Trait WsTrait
 * @package Radar\Traits
 */
trait WsTrait
{
    /**
     * Send a message to all
     *
     * @param array $data
     */
    public function sendToAll(array $data)
    {
        /** @var ConnectionInterface $client */
        foreach($this->clients as $client) {
            $client->send(
                $this->prepare($data)
            );
        }
    }

    /**
     * Send a message to self/client provided
     * @param array $data
     * @param ConnectionInterface $self
     *
     * @return ConnectionInterface
     */
    public function sendToSelf(array $data, ConnectionInterface $self)
    {
        return $self->send(
            $this->prepare($data)
        );
    }

    /**
     * Prepare json response
     *
     * @param array $data
     * @return string
     */
    public function prepare(array $data)
    {
        return json_encode($data);
    }
}