<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 13.08.2015
 * Time: 8:37
 */

namespace ScayTrase\SmsDeliveryBundle\Tests;

use ScayTrase\SmsDeliveryBundle\DataCollector\MessageDeliveryDataCollector;
use ScayTrase\SmsDeliveryBundle\DependencyInjection\Compiler\SpoolCompilerPass;
use ScayTrase\SmsDeliveryBundle\DependencyInjection\Compiler\TransportCompilerPass;
use ScayTrase\SmsDeliveryBundle\DependencyInjection\SmsDeliveryExtension;
use ScayTrase\SmsDeliveryBundle\Service\MessageDeliveryService;
use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;
use ScayTrase\SmsDeliveryBundle\Spool\DisabledSpool;
use ScayTrase\SmsDeliveryBundle\Transport\DummyTransport;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array configurations array
     */
    public function configurationProvider()
    {
        return array(
            'missing_configuration' => array(null),
            'empty_configuration' => array(array()),
            'disable_delivery_configuration' => array(array('disable_delivery' => true)),
            'delivery_recipient_configuration' => array(array('delivery_recipient' => '1234567890')),
            'full_configuration' => array(array('disable_delivery' => true, 'delivery_recipient' => '1234567890')),
        );
    }

    /**
     * @dataProvider configurationProvider
     * @param $config
     */
    public function testBundleConfiguration($config)
    {
        $container = $this->buildContainer($config);

        self::assertTrue($container->hasParameter('sms_delivery.spool'));
        self::assertTrue($container->hasParameter('sms_delivery.transport'));
        self::assertTrue($container->hasParameter('sms_delivery.disable_delivery'));
        self::assertTrue($container->hasParameter('sms_delivery.delivery_recipient'));
    }

    /**
     * @param array|null $config
     * @return ContainerBuilder
     */
    private function buildContainer(array $config = null)
    {
        $extension = new SmsDeliveryExtension();
        $container = new ContainerBuilder();
        $container->addCompilerPass(new TransportCompilerPass());
        $container->addCompilerPass(new SpoolCompilerPass());
        $extension->load(array((array)$config), $container);
        $container->compile();
        return $container;
    }

    public function testBundleConfigurationValues()
    {
        $container = $this->buildContainer(array('disable_delivery' => true, 'delivery_recipient' => '1234567890'));

        self::assertEquals(true, $container->getParameter('sms_delivery.disable_delivery'));
        self::assertEquals('1234567890', $container->getParameter('sms_delivery.delivery_recipient'));
    }

    public function testDefaultRecipientDelivery()
    {
        $container = $this->buildContainer(array('delivery_recipient' => '1234567890'));

        /** @var MessageDeliveryService|\PHPUnit_Framework_MockObject_MockObject $sender */
        $sender = $container->get('sms_delivery.sender');

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');
        $message->expects(self::once())->method('setRecipient');

        $sender->spoolMessage($message);
    }

    public function testPublicService()
    {
        $container = $this->buildContainer();

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        /** @var MessageDeliveryService $sender */
        $sender = $container->get('sms_delivery.sender');

        self::assertFalse($sender->spoolMessage($message));
    }

    public function testDataCollector()
    {
        $transport = new DummyTransport();

        /** @var MessageDeliveryService $sender */
        $sender = new MessageDeliveryService($transport, new DisabledSpool);
        $collector = new MessageDeliveryDataCollector($sender);

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        self::assertTrue($sender->spoolMessage($message));
        $collector->collect(new Request(), new Response());
        self::assertEquals(1, count($collector->getData()));
    }
}
