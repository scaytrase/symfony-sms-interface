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

class SpoolCompilerPass implements CompilerPassInterface
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

        if (
            $container->hasParameter('sms_delivery.disable_delivery') &&
            $container->getParameter('sms_delivery.disable_delivery') === true &&
            $container->hasDefinition('sms_delivery.spool.disabled')
        ) {
            $sender->replaceArgument(1, $container->getDefinition('sms_delivery.spool.disabled'));
        } else {
            $spool = $container->getDefinition($container->getParameter('sms_delivery.spool'));
            $sender->replaceArgument(1, $spool);
        }
    }
}
