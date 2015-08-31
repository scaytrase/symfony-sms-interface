<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.07.2014
 * Time: 14:15
 */

namespace ScayTrase\SmsDeliveryBundle\Service;

use ScayTrase\SmsDeliveryBundle\Spool\InstantSpool;
use ScayTrase\SmsDeliveryBundle\Spool\Package;
use ScayTrase\SmsDeliveryBundle\Spool\SpoolInterface;
use ScayTrase\SmsDeliveryBundle\Transport\TransportInterface;

/**
 * Class MessageDeliveryService
 * @package ScayTrase\Utils\SMSDeliveryBundle\Service
 * Basic sender service class. Implement sendMessage() method with actual 3rd-party SMS sending API interaction.
 */
class MessageDeliveryService
{
    /** @var  TransportInterface */
    protected $transport;
    /** @var  Package[] */
    private $profile = array();
    /** @var SpoolInterface */
    private $spool;

    /**
     * @param TransportInterface $transport
     * @param SpoolInterface $spool
     */
    public function __construct(TransportInterface $transport, SpoolInterface $spool = null)
    {
        $this->transport = $transport;
        $this->spool = $spool;
        if (!$this->spool) {
            $this->spool = new InstantSpool();
        }
    }

    /**
     * @param ShortMessageInterface $message
     * @return boolean True if package was successfully spooled
     */
    public function spoolMessage(ShortMessageInterface $message)
    {
        $package = new Package($this->transport, $message);
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
