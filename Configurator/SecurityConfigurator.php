<?php

namespace Librinfo\SecurityBundle\Configurator;

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
class SecurityConfigurator
{
    private $roleHierarchy = array();

    private static $instance;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * SecurityConfigurator constructor.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * loadSecurityYml
     *
     * @param string $path
     */
    public function loadSecurityYml($path)
    {
        if (!file_exists($path))
            return;

        $configYml = Yaml::parse(
            file_get_contents($path)
        );

        if (array_key_exists('librinfo.security', $configYml))
        {
            if (array_key_exists('method_access_control', $configYml['librinfo.security']))
            {
                $currentSecurityParameters = $this->container->getParameter('security.access.method_access_control');
                $bundleSecurityParameters = $configYml['librinfo.security']['method_access_control'];

                $this->container->setParameter(
                    'security.access.method_access_control',
                    array_merge($currentSecurityParameters, $bundleSecurityParameters)
                );
            }

            if (array_key_exists('security.role_hierarchy.roles', $configYml['librinfo.security']))
            {
                $currentSecurityRoleHierarchy = $this->container->getParameter('security.role_hierarchy.roles');

                $bundleSecurityRoleHierarchyGenerate = $this->generateRoleHierarchy($configYml['librinfo.security']['security.role_hierarchy.roles']);

                $this->container->setParameter(
                    'security.role_hierarchy.roles',
                    array_merge($currentSecurityRoleHierarchy, $bundleSecurityRoleHierarchyGenerate)
                );
            }
        }
    }

    /**
     * generateRoleHierarchy
     *
     * Generate Role Hierarchy structure by yml configuration
     *
     * @param mixed  $params
     * @param string $parent
     *
     * @return array
     */
    private function generateRoleHierarchy($params, $parent = "")
    {
        if (is_array($params))
        {
            foreach ($params as $key => $child)
            {
                if ($parent != "" && is_array($child))
                {
                    foreach ($child as $subkey => $subchild)
                    {
                        $this->roleHierarchy[$parent][] = $subkey;
                        if (is_array($child))
                        {
                            $this->generateRoleHierarchy($subchild, $subkey);
                        }
                    }
                }
                elseif (is_array($child))
                {
                    $this->generateRoleHierarchy($child, $key);
                }
                elseif (!isset($this->roleHierarchy[$parent]) || !in_array($child, $this->roleHierarchy[$parent]))
                {
                    $this->roleHierarchy[$parent][] = $child;
                }
            }
        }
        return $this->roleHierarchy;
    }

    /**
     * getInstance
     *
     * @param $container
     *
     * @return SecurityConfigurator
     *
     */
    public static function getInstance($container)
    {
        if (self::$instance == null)
        {
            self::$instance = new self($container);
        }

        return self::$instance;
    }
}
