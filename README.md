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

### General configuration

You should have this kind of configuration in ```app/config.security.yml``` :

```
# app/config/security.yml

security:
    encoders:
        FOS\UserBundle\Model\UserInterface: bcrypt

    providers:
        fos_userbundle:
            id: fos_user.user_provider.username_email

    firewalls:
        dev:
            pattern:  ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            pattern: ^/
            form_login:
                provider: fos_userbundle
                csrf_provider: security.csrf.token_manager # Use form.csrf_provider instead for Symfony <2.4

            logout:       true
            anonymous:    ~

    access_control:
        - { path: ^/(css|images|js), role: IS_AUTHENTICATED_ANONYMOUSLY } # allow assets for anonymous users
        - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/, roles: IS_AUTHENTICATED_FULLY }

jms_security_extra:
    secure_all_services: false
    enable_iddqd_attribute: false
    expressions: true
    voters:
        disable_authenticated: false
        disable_role:          false
        disable_acl:           true
    method_access_control: {}
```

### Default configuration

Default security configuration is set in ```LibrinfoSecurityBundle/Resources/config/security.yml```.
```
# LibrinfoSecurityBundle/Resources/config/security.yml
librinfo.security:
    # Checking Controllers and/or Services access
    method_access_control:
        'FOSUserBundle:SecurityController:loginAction$': 'isAnonymous()'
        'SonataAdminBundle:Core:dashboard$': 'hasRole("ROLE_USER")'
        'Librinfo\\UIBundle\\Twig\\Extension\\AdminMenu::showAdminMenu$': 'hasRole("ROLE_CRM_MANAGER")'
    # Defining custom Roles hierarchy (as a tree)
    security.role_hierarchy.roles:
        ROLE_SUPER_ADMIN:
            - ROLE_CRM_MANAGER:
                - ROLE_CRM_CONTACT_MANAGER:
                    - ROLE_CRM_CONTACT_VIEWER:
                        - ROLE_USER
                - ROLE_CRM_ORGANISM_MANAGER:
                    - ROLE_CRM_ORGANISM_VIEWER:
                        - ROLE_USER
                - ROLE_CRM_ADMIN:
                    - ROLE_USER
```

### Defining custom role hierarchy and custom access control rules

You can define your own role hierarchy and your own access control rules
by creating new configuration file ```app/config/application_security.yml```.

It's very important to prefix all your roles with « ROLE_ ». It's because Symfony's RoleVoter use this prefix by default.
* see [RoleVoter::supportsAttribute](https://github.com/symfony/symfony/blob/2.8/src/Symfony/Component/Security/Core/Authorization/Voter/RoleVoter.php)

In this file you can define your custom access control logic under ```method_access_control:``` key.
You can define your custom role hierarchy under ```security.role_hierarchy.roles:``` key.

```
librinfo.security:
    # ...
    security.role_hierarchy.roles:
        ROLE_SUPER_ADMIN:
            - ROLE_TEST_ADMIN:
                - ROLE_TEST_SUB_ADMIN:
                    - ROLE_USER
                - ROLE_TEST_SUB_USER:
                    - ROLE_TEST_SUB_SUB_USER:
                        - ROLE_USER
                - ROLE_TEST_SUB_OTHER:
                    - ROLE_TEST_SUB_SUB_OTHER:
                        - ROLE_TEST_SUB_SUB_SUB_OTHER:
                            - ROLE_USER
    method_access_control:
            'Librinfo\\UIBundle\\Twig\\Extension\\AdminMenu::showAdminMenu$': 'hasRole("ROLE_CRM_MANAGER")'
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