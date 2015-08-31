<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-08-06
 * Time: 22:48
 */

namespace ScayTrase\SmsDeliveryBundle\Spool;

class DisabledSpool implements SpoolInterface
{
    const REASON_DISABLED = 'Delivery is disabled';

    /**
     * @param Package $package
     * @return bool
     */
    public function pushPackage(Package $package)
    {
        $package->setStatus(Package::STATUS_DISABLED);
        $package->setReason(self::REASON_DISABLED);
        return true;
    }

    public function clear()
    {
        return;
    }

    /** @return bool */
    public function flush()
    {
        return true;
    }
}
