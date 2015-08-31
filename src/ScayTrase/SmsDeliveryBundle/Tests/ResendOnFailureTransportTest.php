<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-08-05
 * Time: 22:38
 */

namespace ScayTrase\SmsDeliveryBundle\Tests;

use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;
use ScayTrase\SmsDeliveryBundle\Transport\ResendOnFailureTransport;
use ScayTrase\SmsDeliveryBundle\Transport\TransportInterface;

class ResendOnFailureTransportTest extends \PHPUnit_Framework_TestCase
{
    public function testResending()
    {
        /** @var TransportInterface|\PHPUnit_Framework_MockObject_MockObject $transport */
        $transport = $this->getMock('ScayTrase\SmsDeliveryBundle\Transport\TransportInterface');
        $transport->expects(self::at(0))->method('send')->willReturn(false);
        $transport->expects(self::at(1))->method('send')->willReturn(false);
        $transport->expects(self::at(2))->method('send')->willReturn(false);
        $transport->expects(self::at(3))->method('send')->willReturn(false);
        $transport->expects(self::at(4))->method('send')->willReturn(true);

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        $chain = new ResendOnFailureTransport($transport,0,2);
        self::assertFalse($chain->send($message));

        $chain = new ResendOnFailureTransport($transport,0,3);
        self::assertTrue($chain->send($message));
    }
}
