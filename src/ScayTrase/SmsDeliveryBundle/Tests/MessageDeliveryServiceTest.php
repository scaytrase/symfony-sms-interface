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
use ScayTrase\SmsDeliveryBundle\Exception\InvalidRecipientDeliveryException;
use ScayTrase\SmsDeliveryBundle\Service\MessageDeliveryService;
use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;
use ScayTrase\SmsDeliveryBundle\Transport\DummyTransport;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageDeliveryServiceTest extends WebTestCase
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

        $this->assertTrue($container->hasParameter('sms_delivery.transport'));
        $this->assertTrue($container->hasParameter('sms_delivery.disable_delivery'));
        $this->assertTrue($container->hasParameter('sms_delivery.delivery_recipient'));
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

        $this->assertEquals(true, $container->getParameter('sms_delivery.disable_delivery'));
        $this->assertEquals('1234567890', $container->getParameter('sms_delivery.delivery_recipient'));
    }

    public function testDisabledDelivery()
    {
        $transport = new DummyTransport(false);
        /** @var MessageDeliveryService $sender */
        $sender = new MessageDeliveryService($transport, true);

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        $message->expects($this->never())->method('getBody');
        $message->expects($this->never())->method('setRecipient');
        $message->expects($this->never())->method('getRecipient');

        $this->assertTrue($sender->send($message));
    }

    public function testDefaultRecipientDelivery()
    {
        $container = $this->buildContainer(array('delivery_recipient' => '1234567890'));

        /** @var MessageDeliveryService|\PHPUnit_Framework_MockObject_MockObject $sender */
        $sender = $container->get('sms_delivery.sender');

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');
        $message->expects($this->once())->method('setRecipient');

        $sender->send($message);
    }

    public function testDataCollector()
    {
        $transport = new DummyTransport();

        /** @var MessageDeliveryService $sender */
        $sender = new MessageDeliveryService($transport, true, null);
        $collector = new MessageDeliveryDataCollector($sender);

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        $this->assertTrue($sender->send($message));
        $collector->collect(new Request(), new Response());
        $this->assertEquals(1, count($collector->getData()));
    }

    public function testPublicService()
    {
        $container = $this->buildContainer();

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        /** @var MessageDeliveryService $sender */
        $sender = $container->get('sms_delivery.sender');

        $this->assertFalse($sender->send($message));
    }

    public function testExceptionalSender()
    {
        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        $transport = new DummyTransport();
        $sender = new MessageDeliveryService($transport, false, null);
        $collector = new MessageDeliveryDataCollector($sender);


        $this->assertFalse($sender->send($message));
        $collector->collect(new Request(), new Response());
        $this->assertEquals(1, count($collector->getData()));

        $this->assertEquals('Sending not configured', $collector->getData()[0]['reason']);
    }
}
