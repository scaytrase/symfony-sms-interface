<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-08-05
 * Time: 21:53
 */

namespace ScayTrase\SmsDeliveryBundle\Transport;

use Psr\Log\LoggerInterface;
use ScayTrase\SmsDeliveryBundle\Exception\DeliveryFailedException;
use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;

/**
 * Class ChainedTransport
 * @package ScayTrase\SmsDeliveryBundle\Transport
 *
 * This transport is a meta-transport. It iterates internal list of transports until success or end of list
 */
class ChainedTransport implements TransportInterface
{
    /**
     * @var TransportInterface[][]
     */
    private $transports = [];

    /** @var  LoggerInterface */
    private $logger;

    /**
     * ChainedTransport constructor.
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @return TransportInterface[][]
     */
    public function getTransports()
    {
        return $this->transports;
    }

    /**
     * @param TransportInterface[][] $transports
     */
    public function setTransports($transports)
    {
        $this->transports = $transports;
    }

    /**
     * @param TransportInterface $transport
     * @param int $priority
     */
    public function addTransport(TransportInterface $transport, $priority = 255)
    {
        $this->transports[$priority][] = $transport;
    }

    /**
     * @param ShortMessageInterface $message
     * @return boolean
     *
     * @throws DeliveryFailedException
     */
    public function send(ShortMessageInterface $message)
    {
        foreach ($this->transports as $priority => $transports) {
            foreach ($transports as $transport) {
                $result = false;
                try {
                    $result = $transport->send($message);
                } catch (DeliveryFailedException $exception) {
                    if ($this->logger) {
                        $this->logger->warning($exception->getMessage());
                    }
                }

                if ($result === true) {
                    return true;
                }
            }
        }

        if ($this->logger) {
            $this->logger->error(
                'All transports failed or empty transport list',
                array('count' => count($this->transports, COUNT_RECURSIVE))
            );
        }

        return false;
    }
}
