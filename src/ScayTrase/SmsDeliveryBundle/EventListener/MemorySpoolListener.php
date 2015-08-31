<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 12.08.2015
 * Time: 10:31
 */

namespace ScayTrase\SmsDeliveryBundle\EventListener;

use Psr\Log\LoggerInterface;
use ScayTrase\SmsDeliveryBundle\Service\MessageDeliveryService;
use ScayTrase\SmsDeliveryBundle\Spool\MemorySpool;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class MemorySpoolListener implements EventSubscriberInterface
{
    /** @var MessageDeliveryService */
    private $sender;
    /** @var LoggerInterface */
    private $logger;

    /**
     * Constructor.
     *
     * @param MessageDeliveryService $sender
     * @param LoggerInterface $logger A LoggerInterface instance
     */
    public function __construct(MessageDeliveryService $sender, LoggerInterface $logger = null)
    {
        $this->sender = $sender;
        $this->logger = $logger;
    }

    /** {@inheritdoc} */
    public static function getSubscribedEvents()
    {
        $listeners = array(KernelEvents::TERMINATE => 'onTerminate');

        if (class_exists('Symfony\Component\Console\ConsoleEvents')) {
            $listeners[ConsoleEvents::TERMINATE] = 'onTerminate';
        }

        return $listeners;
    }

    public function onTerminate()
    {
        $spool = $this->sender->getSpool();

        if ($spool instanceof MemorySpool) {
            try {
                $spool->flush();
            } catch (\Exception $exception) {
                if (null !== $this->logger) {
                    $this->logger->error(sprintf('Exception occurred while flushing message queue: %s',
                        $exception->getMessage()));
                }
            }
        }
    }
}
