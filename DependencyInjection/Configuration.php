<?php

namespace Dontdrinkandroot\AngularIntegrationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ddr_angular_integration');
        $rootNode = $treeBuilder->getRootNode();

        // @formatter:off
        $rootNode->children()
            ->scalarNode('name')->isRequired()->end()
            ->scalarNode('short_name')->isRequired()->end()
            ->scalarNode('theme_color')->defaultValue('#3f51b5')->end()
            ->scalarNode('background_color')->defaultValue('#3f51b5')->end()
            ->scalarNode('package_manager')->defaultValue('yarn')->end()

            ->arrayNode('hrefs')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('app')->defaultValue('http://localhost:8000/app/')->end()
                    ->scalarNode('api')->defaultValue('http://localhost:8000/api/')->end()
                ->end()
            ->end()

            ->arrayNode('directories')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('root')->defaultValue('%kernel.root_dir%/../')->end()
                    ->scalarNode('src')->defaultValue('%kernel.root_dir%/../ngsrc/')->end()
                ->end()
            ->end()

            ->arrayNode('external_styles')
                ->prototype('scalar')->end()
            ->end()
        ->end();
        // @formatter:on

        return $treeBuilder;
    }
}
