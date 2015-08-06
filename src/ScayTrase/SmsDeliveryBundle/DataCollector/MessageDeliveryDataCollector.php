<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.07.2014
 * Time: 15:24
 */

namespace ScayTrase\SmsDeliveryBundle\DataCollector;

use ScayTrase\SmsDeliveryBundle\Service\MessageDeliveryService;
use ScayTrase\SmsDeliveryBundle\Spool\Package;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class MessageDeliveryDataCollector extends DataCollector
{
    /** @var  MessageDeliveryService */
    private $sender;

    /**
     * MessageDeliveryDataCollector constructor.
     * @param MessageDeliveryService $sender
     */
    public function __construct(MessageDeliveryService $sender)
    {
        $this->sender = $sender;
    }

    /**
     * Collects data for the given Request and Response.
     *
     * @param Request $request A Request instance
     * @param Response $response A Response instance
     * @param \Exception $exception An Exception instance
     *
     * @api
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = $this->sender->getProfile();
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     *
     * @api
     */
    public function getName()
    {
        return 'sms_delivery.data_collector';
    }

    /** @return Package[] */
    public function getData()
    {
        return $this->data;
    }
}
