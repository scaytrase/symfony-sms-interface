<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 21.07.2015
 * Time: 16:26
 */

namespace ScayTrase\SmsDeliveryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class TransportCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sms_delivery.sender')) {
            return;
        }

        $sender = $container->getDefinition('sms_delivery.sender');

        $transport = $container->getDefinition($container->getParameter('sms_delivery.transport'));
        if (
            $container->hasParameter('sms_delivery.delivery_recipient') &&
            $container->getParameter('sms_delivery.delivery_recipient') !== null
        ) {
            $overrideTransport = new Definition('ScayTrase\SmsDeliveryBundle\Transport\OverrideRecipientTransport');
            $overrideTransport->setArguments(
                array(
                    $transport,
                    $container->getParameter('sms_delivery.delivery_recipient')
                )
            );
            $sender->replaceArgument(0, $overrideTransport);
        } else {
            $sender->replaceArgument(0, $transport);
        }
    }
}
