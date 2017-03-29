<?php

namespace ScayTrase\SmsDeliveryBundle\Transport;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ScayTrase\SmsDeliveryBundle\Exception\DeliveryFailedException;
use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;

/**
 * This transport is a meta-transport. It iterates internal list of transports until success or end of list
 */
final class ChainedTransport implements TransportInterface
{
    /**
     * @var TransportInterface[][]
     */
    private $transports = [];

    /** @var  LoggerInterface */
    private $logger;

    /**
     * ChainedTransport constructor.
     *
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @param TransportInterface $transport
     * @param int                $priority
     */
    public function addTransport(TransportInterface $transport, $priority = 255)
    {
        $this->transports[$priority][] = $transport;
    }

    /**
     * @param ShortMessageInterface $message
     *
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
                    $this->logger->warning($exception->getMessage());
                }

                if ($result === true) {
                    return true;
                }
            }
        }

        $this->logger->error(
            'All transports failed or empty transport list',
            ['count' => count($this->transports, COUNT_RECURSIVE)]
        );

        return false;
    }
}
