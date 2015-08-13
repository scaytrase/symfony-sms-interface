<?php

namespace ScayTrase\SmsDeliveryBundle;

use ScayTrase\SmsDeliveryBundle\DependencyInjection\Compiler\SpoolCompilerPass;
use ScayTrase\SmsDeliveryBundle\DependencyInjection\Compiler\TransportCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SmsDeliveryBundle extends Bundle
{
    /**
     * Builds the bundle.
     *
     * It is only ever called once when the cache is empty.
     *
     * This method can be overridden to register compilation passes,
     * other extensions, ...
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new TransportCompilerPass());
        $container->addCompilerPass(new SpoolCompilerPass());
    }

}

