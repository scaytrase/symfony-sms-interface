<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.07.2014
 * Time: 14:01
 */

namespace ScayTrase\SmsDeliveryBundle\Tests;

use ScayTrase\SmsDeliveryBundle\Service\MessageDeliveryService;
use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;
use ScayTrase\SmsDeliveryBundle\Spool\DisabledSpool;
use ScayTrase\SmsDeliveryBundle\Transport\DummyTransport;

class MessageDeliveryServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testDisabledDelivery()
    {
        $transport = new DummyTransport();
        /** @var MessageDeliveryService $sender */
        $sender = new MessageDeliveryService($transport, new DisabledSpool);

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        $message->expects(self::never())->method('getBody');
        $message->expects(self::never())->method('setRecipient');
        $message->expects(self::never())->method('getRecipient');

        self::assertTrue($sender->spoolMessage($message));
    }

    public function testExceptionalSender()
    {
        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        $transport = new DummyTransport();
        $sender = new MessageDeliveryService($transport);

        self::assertFalse($sender->spoolMessage($message));
        $profile = $sender->getProfile();
        self::assertCount(1, $profile);
        self::assertEquals('Sending not configured', $profile[0]->getReason());
    }
}
