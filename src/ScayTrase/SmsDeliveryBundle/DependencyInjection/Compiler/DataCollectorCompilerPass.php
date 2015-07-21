<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 21.07.2015
 * Time: 16:30
 */

namespace ScayTrase\SmsDeliveryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DataCollectorCompilerPass implements CompilerPassInterface
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
        if ($container->hasDefinition('profiler') && $container->hasDefinition('data_collector.sms_delivery.data_collector')) {
            $container
                ->getDefinition('sms_delivery.sender')
                ->addArgument(
                    $container->getDefinition('data_collector.sms_delivery.data_collector')
                );
        }
    }
}
