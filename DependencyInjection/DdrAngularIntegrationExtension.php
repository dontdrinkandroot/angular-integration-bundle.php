<?php

namespace Dontdrinkandroot\AngularIntegrationBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class DdrAngularIntegrationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $container->setParameter('ddr_angular_integration.name', $config['name']);
        $container->setParameter('ddr_angular_integration.short_name', $config['short_name']);
        $container->setParameter('ddr_angular_integration.theme_color', $config['theme_color']);
        $container->setParameter('ddr_angular_integration.background_color', $config['background_color']);
        $container->setParameter('ddr_angular_integration.package_manager', $config['package_manager']);
        $container->setParameter('ddr_angular_integration.hrefs.app', $config['hrefs']['app']);
        $container->setParameter('ddr_angular_integration.hrefs.api', $config['hrefs']['api']);
        $container->setParameter('ddr_angular_integration.directories.root', $config['directories']['root']);
        $container->setParameter('ddr_angular_integration.directories.src', $config['directories']['src']);
        $container->setParameter('ddr_angular_integration.external_styles', $config['external_styles']);
    }
}
