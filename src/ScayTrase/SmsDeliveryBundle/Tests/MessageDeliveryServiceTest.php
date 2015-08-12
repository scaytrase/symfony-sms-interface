<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.07.2014
 * Time: 14:01
 */

namespace ScayTrase\SmsDeliveryBundle\Tests;

use ScayTrase\SmsDeliveryBundle\DataCollector\MessageDeliveryDataCollector;
use ScayTrase\SmsDeliveryBundle\DependencyInjection\Compiler\TransportCompilerPass;
use ScayTrase\SmsDeliveryBundle\DependencyInjection\SmsDeliveryExtension;
use ScayTrase\SmsDeliveryBundle\Service\MessageDeliveryService;
use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;
use ScayTrase\SmsDeliveryBundle\Transport\DummyTransport;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageDeliveryServiceTest extends \PHPUnit_Framework_TestCase
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

    public function testDisabledDelivery()
    {
        $transport = new DummyTransport();
        /** @var MessageDeliveryService $sender */
        $sender = new MessageDeliveryService($transport, null, true);

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        $message->expects(self::never())->method('getBody');
        $message->expects(self::never())->method('setRecipient');
        $message->expects(self::never())->method('getRecipient');

        self::assertTrue($sender->spoolMessage($message));
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

    public function testDataCollector()
    {
        $transport = new DummyTransport();

        /** @var MessageDeliveryService $sender */
        $sender = new MessageDeliveryService($transport, null, true, null);
        $collector = new MessageDeliveryDataCollector($sender);

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        self::assertTrue($sender->spoolMessage($message));
        $collector->collect(new Request(), new Response());
        self::assertEquals(1, count($collector->getData()));
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

    public function testExceptionalSender()
    {
        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        $transport = new DummyTransport();
        $sender = new MessageDeliveryService($transport, null, false, null);
        $collector = new MessageDeliveryDataCollector($sender);


        self::assertFalse($sender->spoolMessage($message));
        $collector->collect(new Request(), new Response());
        self::assertEquals(1, count($collector->getData()));

        $data = $collector->getData();
        self::assertEquals('Sending not configured', $data[0]->getReason());
    }
}
