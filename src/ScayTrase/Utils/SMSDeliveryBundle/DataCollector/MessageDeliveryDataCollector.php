<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.07.2014
 * Time: 15:24
 */

namespace ScayTrase\Utils\SMSDeliveryBundle\DataCollector;


use ScayTrase\Utils\SMSDeliveryBundle\Service\MessageDeliveryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class MessageDeliveryDataCollector extends DataCollector
{

    /** @var MessageDeliveryService */
    private $service;

    /**
     * Constructor.
     *
     * @param MessageDeliveryService $service
     */
    public function __construct(MessageDeliveryService $service)
    {
        $this->service = $service;
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
        $this->data = $this->service->getCollectorData();
    }

    public function getRecords()
    {
        return $this->data['messages'];
    }

    public function getService()
    {
        return $this->data['service'];
    }

    /**
     * @return int
     */
    public function getMessageCount()
    {
        return count($this->data['messages']);
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
}