<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-08-05
 * Time: 22:23
 */

namespace ScayTrase\SmsDeliveryBundle\Tests;

use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;
use ScayTrase\SmsDeliveryBundle\Transport\ChainedTransport;
use ScayTrase\SmsDeliveryBundle\Transport\TransportInterface;

class ChainedTransportTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccessfulChain()
    {
        /** @var TransportInterface|\PHPUnit_Framework_MockObject_MockObject $transport */
        $transport = $this->getMock('ScayTrase\SmsDeliveryBundle\Transport\TransportInterface');
        $transport->expects(self::at(0))->method('send')->willReturn(false);
        $transport->expects(self::at(1))->method('send')->willReturn(false);
        $transport->expects(self::at(2))->method('send')->willReturn(true);

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        $chain = new ChainedTransport();
        $chain->addTransport($transport);
        $chain->addTransport($transport);
        $chain->addTransport($transport);

        self::assertTrue($chain->send($message));
    }

    public function testFailedChain()
    {
        /** @var TransportInterface|\PHPUnit_Framework_MockObject_MockObject $transport */
        $transport = $this->getMock('ScayTrase\SmsDeliveryBundle\Transport\TransportInterface');
        $transport->expects(self::at(0))->method('send')->willReturn(false);
        $transport->expects(self::at(1))->method('send')->willReturn(false);

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        $chain = new ChainedTransport();
        $chain->addTransport($transport);
        $chain->addTransport($transport);

        self::assertFalse($chain->send($message));
    }
}
