# SymfonyLibrinfoSecurityBundle
Managing security rules and access controls

## The "libre-informatique" bundles

```php
    // ...
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Librinfo\SecurityBundle\LibrinfoSecurityBundle(),
            // ...
        );
    }
```

## Configuration

### Default configuration

Default security configuration is set in ```LibrinfoSecurityBundle/Resources/config/security.yml```.
```
# LibrinfoSecurityBundle/Resources/config/security.yml
librinfo.security:
    # Checking Controllers and/or Services access
    method_access_control:
        'FOSUserBundle:SecurityController:loginAction$': 'isAnonymous()'
        'SonataAdminBundle:Core:dashboard$': 'hasRole("ROLE_USER")'
        'Librinfo\\UIBundle\\Twig\\Extension\\AdminMenu::showAdminMenu$': 'hasRole("CRM_MANAGER")'
    # Defining custom Roles hierarchy (as a tree)
    security.role_hierarchy.roles:
        ROLE_SUPER_ADMIN:
            - CRM_MANAGER:
                - CRM_CONTACT_MANAGER:
                    - CRM_CONTACT_VIEWER:
                        - ROLE_USER:
                            - ROLE_SONATA_ADMIN
                - CRM_ORGANISM_MANAGER:
                    - CRM_ORGANISM_VIEWER:
                        - ROLE_USER:
                            - ROLE_SONATA_ADMIN
                - CRM_ADMIN:
                    - ROLE_USER:
                        - ROLE_SONATA_ADMIN
```

### Defining custom role hierarchy and custom access control rules

You can define your own role hierarchy and your own access control rules
by creating new configuration file ```app/config/application_security.yml```.

In this file you can define your custom access control logic under ```method_access_control:``` key.
You can define your custom role hierarchy under ```security.role_hierarchy.roles:``` key.

```
librinfo.security:
    # ...
    security.role_hierarchy.roles:
        ROLE_SUPER_ADMIN:
            - TEST_ADMIN:
                - TEST_SUB_ADMIN:
                    - ROLE_USER
                - TEST_SUB_USER:
                    - TEST_SUB_SUB_USER:
                        - ROLE_USER
                - TEST_SUB_OTHER:
                    - TEST_SUB_SUB_OTHER:
                        - TEST_SUB_SUB_SUB_OTHER:
                            - ROLE_USER
    method_access_control:
            'Librinfo\\UIBundle\\Twig\\Extension\\AdminMenu::showAdminMenu$': 'hasRole("CRM_MANAGER")'
```

#### Notes :

* Your custom rules will be merged with default values defined in ```LibrinfoSecurityBundle``` bundle and others ones.
* Do not forget to clear the cache in order to see your changes applied.
* Your custom hierarchy will be merged with ```LibrinfoSecurityBundle``` bundle's defaults
and other bundles using this configuration system.

### Defining custom rules and roles within a bundle

You can define custom rules into any bundle in your src directory.

There are few step to achieve that :
* create your own ```security.yml``` into ```<YOURBUNDLE DIR>/Resources/config/security.yml```
* add your rules as described in [Defining custom role hierarchy and custom access control rules](#Defining custom role hierarchy and custom access control rules)
* add into ```<YOUR BUNDLEDIR>/DependencyInjection/<YOURBUNDLE>Extension.php``` this code :
```
public function load(array $configs, ContainerBuilder $container)
{
    // ...
    SecurityConfigurator::getInstance($container)->loadSecurityYml(__DIR__ . '/../Resources/config/security.yml');
    // ...
}
```