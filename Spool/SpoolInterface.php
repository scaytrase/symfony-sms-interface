<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 06.08.2015
 * Time: 17:49
 */

namespace ScayTrase\SmsDeliveryBundle\Spool;

interface SpoolInterface
{
    /**
     * @param Package $package
     * @return bool
     */
    public function pushPackage(Package $package);

    public function clear();

    /** @return bool */
    public function flush();
}
