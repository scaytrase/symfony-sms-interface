<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 12.08.2015
 * Time: 9:37
 */

namespace ScayTrase\SmsDeliveryBundle\Tests;

use ScayTrase\SmsDeliveryBundle\Service\MessageDeliveryService;
use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;
use ScayTrase\SmsDeliveryBundle\Spool\MemorySpool;
use ScayTrase\SmsDeliveryBundle\Spool\Package;
use ScayTrase\SmsDeliveryBundle\Tests\fixtures\KernelForTest;
use ScayTrase\SmsDeliveryBundle\Transport\TransportInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MemorySpoolTest extends \PHPUnit_Framework_TestCase
{
    public function testSpoolingAndFlushing()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|TransportInterface $transport */
        $transport = $this->getMock('ScayTrase\SmsDeliveryBundle\Transport\TransportInterface');
        $transport->expects(self::any())->method('send')->willReturn(true);
        $spool = new MemorySpool();

        $sender = new MessageDeliveryService($transport, $spool);

        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        self::assertTrue($sender->spoolMessage($message));

        $profile = $sender->getProfile();
        self::assertCount(1, $profile);
        self::assertEquals(Package::STATUS_SPOOLED, $profile[0]->getStatus());

        self::assertTrue($sender->getSpool()->flush());
        self::assertCount(1, $profile);
        self::assertEquals(Package::STATUS_SUCCESS, $profile[0]->getStatus());
    }

    public function testKernelEvents()
    {
        /** @var ShortMessageInterface|\PHPUnit_Framework_MockObject_MockObject $message */
        $message = $this->getMock('ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface');

        /** @var \PHPUnit_Framework_MockObject_MockObject|TransportInterface $transport */
        $transport = $this->getMock('ScayTrase\SmsDeliveryBundle\Transport\TransportInterface');
        $transport->expects(self::any())->method('send')->willReturn(true);

        $kernel = new KernelForTest('test', true, __DIR__ . '/fixtures/config/memory_spool.yml');
        $kernel->boot();

        $container = $kernel->getContainer();
        $sender = $container->get('sms_delivery.sender');

        $sender->spoolMessage($message);

        $profile = $sender->getProfile();
        self::assertCount(1, $profile);
        self::assertEquals(Package::STATUS_SPOOLED, $profile[0]->getStatus());

        $kernel->terminate(new Request(), new Response());

        self::assertCount(1, $profile);
        self::assertEquals(Package::STATUS_FAIL, $profile[0]->getStatus());
    }
}
