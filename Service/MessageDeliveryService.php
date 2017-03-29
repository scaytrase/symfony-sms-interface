<?php

namespace ScayTrase\SmsDeliveryBundle\Service;

use ScayTrase\SmsDeliveryBundle\ShortMessage;
use ScayTrase\SmsDeliveryBundle\Spool\InstantSpool;
use ScayTrase\SmsDeliveryBundle\Spool\Package;
use ScayTrase\SmsDeliveryBundle\Spool\SpoolInterface;
use ScayTrase\SmsDeliveryBundle\Transport\TransportInterface;

final class MessageDeliveryService
{
    /** @var  TransportInterface */
    private $transport;
    /** @var  Package[] */
    private $profile = [];
    /** @var SpoolInterface */
    private $spool;

    /**
     * @param TransportInterface $transport
     * @param SpoolInterface     $spool
     */
    public function __construct(TransportInterface $transport, SpoolInterface $spool = null)
    {
        $this->transport = $transport;
        $this->spool = $spool ?: new InstantSpool();
    }

    /**
     * @param ShortMessage $message
     *
     * @return boolean True if package was successfully spooled
     */
    public function spoolMessage(ShortMessage $message)
    {
        $package         = new Package($this->transport, $message);
        $this->profile[] = $package;

        return $this->spool->pushPackage($package);
    }

    /**
     * @return bool
     */
    public function flush()
    {
        return $this->getSpool()->flush();
    }

    /**
     * @return SpoolInterface
     */
    public function getSpool()
    {
        return $this->spool;
    }

    /**
     * @return Package[]
     */
    public function getProfile()
    {
        return $this->profile;
    }
}
