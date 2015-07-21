<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.07.2014
 * Time: 14:01
 */

namespace ScayTrase\SmsDeliveryBundle\Tests;

use ScayTrase\SmsDeliveryBundle\DependencyInjection\SmsDeliveryExtension;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageDeliveryServiceTest extends WebTestCase
{

    /**
     * @var SmsDeliveryExtension
     */
    private $extension;

    /**
     * Root name of the configuration
     *
     * @var string
     */
    private $root;

    public function setUp()
    {
        parent::setUp();

        $this->extension = $this->getExtension();
        $this->root = "sms_delivery";
    }

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
        $container = $this->getContainer();
        $this->extension->load(array($config), $container);

        $this->assertTrue($container->hasParameter($this->root . '.disable_delivery'));
        $this->assertTrue($container->hasParameter($this->root . '.delivery_recipient'));
    }

    public function testDisabledDelivery()
    {
        $this->extension->load(array(array('disable_delivery' => true)), $container = $this->getContainer());

        /** @var MessageDeliveryService|\PHPUnit_Framework_MockObject_MockObject $sender */
        $sender = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\MessageDeliveryService',
            array('sendMessage'), array($container));
        $sender->expects($this->never())->method('sendMessage');

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        $message->expects($this->never())->method('getBody');
        $message->expects($this->never())->method('setRecipient');
        $message->expects($this->never())->method('getRecipient');

        $this->assertTrue($sender->send($message));
    }

    public function testDefaultRecipientDelivery()
    {
        $this->extension->load(array(array('delivery_recipient' => '1234567890')), $container = $this->getContainer());

        /** @var MessageDeliveryService|\PHPUnit_Framework_MockObject_MockObject $sender */
        $sender = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\MessageDeliveryService',
            array('sendMessage'), array($container));
        $sender->expects($this->once())->method('sendMessage')->willReturn(true);

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        $message->expects($this->once())->method('setRecipient');

        $this->assertTrue($sender->send($message));
    }

    public function testDataCollector()
    {
        $this->extension->load(array(), $container = $this->getContainer());

        /** @var MessageDeliveryService|\PHPUnit_Framework_MockObject_MockObject $sender */
        $sender = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\MessageDeliveryService',
            array('sendMessage'), array($container));
        $sender->expects($this->once())->method('sendMessage')->willReturn(true);

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        $this->assertTrue($sender->send($message));

        $collector = new MessageDeliveryDataCollector($sender);
        $collector->collect(new Request(), new Response());
        $this->assertEquals(1, count($collector->getData()));
    }

    public function testPublicService()
    {
        $this->extension->load(array(), $container = $this->getContainer());

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        /** @var MessageDeliveryService $sender */
        $sender = $container->get('sms_delivery.sender');

        $this->assertFalse($sender->send($message));
    }

    public function testExceptionalSender()
    {
        $this->extension->load(array(), $container = $this->getContainer());

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        /** @var MessageDeliveryService|\PHPUnit_Framework_MockObject_MockObject $sender */
        $sender = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\MessageDeliveryService',
            array('sendMessage'), array($container));
        $sender->expects($this->once())->method('sendMessage')->willThrowException(new DeliveryFailedException('Test exception'));

        $this->assertFalse($sender->send($message));

        $collector = new MessageDeliveryDataCollector($sender);
        $collector->collect(new Request(), new Response());
        $this->assertEquals(1, count($collector->getData()));

        $this->assertEquals('Test exception' . PHP_EOL, $collector->getData()[0]['reason']);
    }

    protected function getExtension()
    {
        return new SmsDeliveryExtension();
    }

    private function getContainer()
    {
        $container = new ContainerBuilder();

        return $container;
    }
}