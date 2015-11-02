<?php

namespace Librinfo\SecurityBundle\DependencyInjection;

use Librinfo\CoreBundle\DependencyInjection\DefaultParameters;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class LibrinfoSecurityExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('security.yml');

        $currentSecurityParamters = $container->getParameter('security.access.method_access_control');
        $bundleSecurityParameters = $container->getParameter('librinfo.security')['method_access_control'];

        $container->setParameter(
            'security.access.method_access_control',
            array_merge($bundleSecurityParameters,$currentSecurityParamters)
        );
    }
}
