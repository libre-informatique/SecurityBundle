<?php

namespace Librinfo\SecurityBundle\DependencyInjection;

use Librinfo\CoreBundle\DependencyInjection\DefaultParameters;
use Librinfo\SecurityBundle\Configurator\SecurityConfigurator;
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
    private $roleHierarchy = array();

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        //$loader->load('security.yml');

        if($container->getParameter("kernel.environment") == "test")
        {
            $loader->load('datafixtures.yml');
        }

        $configSonataAdmin = Yaml::parse(
            file_get_contents(__DIR__ . '/../Resources/config/bundles/jms_security_extra.yml')
        );

        $env = ($container->getParameter('kernel.environment') == 'test') ? '_test':'';
        SecurityConfigurator::getInstance($container)->loadSecurityYml(__DIR__ . '/../Resources/config/security'.$env.'.yml');

        DefaultParameters::getInstance($container)
            ->defineDefaultConfiguration(
                $configSonataAdmin['default']
            );
    }
}
