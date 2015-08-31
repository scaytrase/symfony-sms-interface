<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 11.08.2015
 * Time: 14:59
 */

namespace ScayTrase\SmsDeliveryBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FlushCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('scaytrase:sms_delivery:flush');
        $this->setDescription('Flush the delivery service message spool');
    }


    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sender = $this->getContainer()->get('sms_delivery.sender');

        $sender->flush();
    }

}
