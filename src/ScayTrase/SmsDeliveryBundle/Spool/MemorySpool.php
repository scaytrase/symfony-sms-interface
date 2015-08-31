<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-08-06
 * Time: 22:41
 */

namespace ScayTrase\SmsDeliveryBundle\Spool;

use ScayTrase\SmsDeliveryBundle\Exception\DeliveryFailedException;

class MemorySpool implements SpoolInterface
{
    /** @var  Package[] */
    private $queue = array();

    public function clear()
    {
        $this->queue = array();
    }

    /** @return bool */
    public function flush()
    {
        $state = true;



        foreach ($this->queue as $package) {
            try {
                $result = $package->getTransport()->send($package->getMessage());
                $package->setStatus($result === true ? Package::STATUS_SUCCESS : Package::STATUS_FAIL);
                $package->setReason($result === true ? Package::REASON_OK : $result);
            } catch (DeliveryFailedException $e) {
                $package->setStatus(Package::STATUS_FAIL);
                $package->setReason($e->getMessage());
                $state = false;
            }
        }

        return $state;
    }

    /**
     * @param Package $package
     * @return bool
     */
    public function pushPackage(Package $package)
    {
        $this->queue[] = $package;

        return true;
    }
}
