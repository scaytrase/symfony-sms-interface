<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 06.08.2015
 * Time: 17:49
 */

namespace ScayTrase\SmsDeliveryBundle\Spool;

use ScayTrase\SmsDeliveryBundle\Exception\DeliveryFailedException;

class InstantSpool implements SpoolInterface
{

    public function clear()
    {
    }

    public function flush()
    {
        return true;
    }

    /**
     * @param Package $package
     * @return bool
     */
    public function pushPackage(Package $package)
    {
        try {
            $result = $package->getTransport()->send($package->getMessage());
            $package->setStatus($result === true ? Package::STATUS_SUCCESS : Package::STATUS_FAIL);
            $package->setReason($result === true ? Package::REASON_OK : $result);
            return $result;
        } catch (DeliveryFailedException $e) {
            $package->setStatus(Package::STATUS_FAIL);
            $package->setReason($e->getMessage());
        }

        return false;
    }
}
