<?php

namespace ScayTrase\SmsDeliveryBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sms_delivery');

        $disable_delivery = (new BooleanNodeDefinition('disable_delivery'));
        $delivery_recipient = (new ScalarNodeDefinition('delivery_recipient'));
        $transport = (new ScalarNodeDefinition('transport'));
        $spool = (new ScalarNodeDefinition('spool'));

        $rootNode
            ->children()
                ->append($spool->defaultValue('sms_delivery.spool.instant')->info('Sender message spool'))
                ->append($transport->defaultValue('sms_delivery.transport.dummy')->info('Sender transport service'))
                ->append($disable_delivery->defaultFalse()->info('Disables actual delivery for testing purposes'))
                ->append($delivery_recipient->defaultNull()->info('Recipient for messages for testing purposes'))
            ->end();

        return $treeBuilder;
    }
}
