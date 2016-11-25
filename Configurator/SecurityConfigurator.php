<?php

namespace Librinfo\SecurityBundle\Configurator;

use Blast\CoreBundle\DependencyInjection\DefaultParameters;
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
    public function loadSecurityYml($path, $load_global = true)
    {
        if (!file_exists($path))
            return;

        $configYml = Yaml::parse(
            file_get_contents($path)
        );

        $env = ($this->container->getParameter('kernel.environment') == 'test') ? '_test':'';

        if ( is_array($configYml) && array_key_exists('librinfo.security', $configYml))
        {
            if (is_array($configYml['librinfo.security']) && array_key_exists('method_access_control', $configYml['librinfo.security']))
            {
                $currentSecurityParameters = $this->container->getParameter('security.access.method_access_control');
                $bundleSecurityParameters = $configYml['librinfo.security']['method_access_control'];

                $this->container->setParameter(
                    'security.access.method_access_control',
                    array_merge($currentSecurityParameters, $bundleSecurityParameters)
                );

                if($load_global)
                    $this->loadSecurityYml($this->container->getParameter('kernel.root_dir') . '/config/application_security'.$env.'.yml',false);
            }

            if (is_array($configYml['librinfo.security']) && array_key_exists('security.role_hierarchy.roles', $configYml['librinfo.security']))
            {
                $currentSecurityRoleHierarchy = $this->container->getParameter('security.role_hierarchy.roles');

                $bundleSecurityRoleHierarchyGenerate = $this->generateRoleHierarchy($configYml['librinfo.security']['security.role_hierarchy.roles']);

                $roleHierarchy = $this->filterUniqueRole(array_merge($currentSecurityRoleHierarchy, $bundleSecurityRoleHierarchyGenerate));

                $this->container->setParameter(
                    'security.role_hierarchy.roles',
                    $roleHierarchy
                );

                if($load_global)
                    $this->loadSecurityYml($this->container->getParameter('kernel.root_dir') . '/config/application_security'.$env.'.yml',false);
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
     * filterUniqueRole
     *
     * @param $array
     *
     * @return mixed
     *
     */
    private function filterUniqueRole($array)
    {
        $arrayRewrite = array();
        foreach ($array as $key => $parent)
        {
            foreach ($parent as $role)
            {
                if (!isset($arrayRewrite[$key]) || !in_array($role, $arrayRewrite[$key]))
                {
                    $arrayRewrite[$key][] = $role;
                }
            }
        }
        return $arrayRewrite;
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
