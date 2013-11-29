<?php

namespace Widget\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html//cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('widget');
        $this->addFormConfig($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    protected function addFormConfig(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
            ->arrayNode('form')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('templating')
            ->defaultValue("WidgetBundle:Form:fields.html.twig")
            ->end()
            ->end()
            ->end();
    }
}
