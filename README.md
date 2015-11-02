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
parameters:
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

### Defining custom access control rules

If you want to define your own access control rules to limit user access on specifics controllers and/or services,
you can define them into your ```app/config/security.yml```, under the key :
```
 jms_security_extra:
    method_access_control:
```

#### Example :

```
# app/config/security.yml
jms_security_extra:
    method_access_control:
        'FOSUserBundle:SecurityController:loginAction$': 'isAnonymous()'
        'SonataAdminBundle:Core:dashboard$': 'hasRole("ROLE_ADMIN)'
        'Librinfo\\UIBundle\\Twig\\Extension\\AdminMenu::showAdminMenu$': 'hasRole("ROLE_SUPER_ADMIN")'
```

#### Notes :

* Your custom rules will override default values defined in this bundle.
* Don't forget to clear the cache in order to see your changes applied.

### Defining custom role hierarchy

You can define your own role hierarchy by adding into ```app/config/parameters.yml``` this structure :

```
paramters:
    # your parameters
    # ...
    librinfo_security.security.role_hierarchy.roles:
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
```

#### Notes :

* Your custom rules will override default values defined in this bundle.
* Don't forget to clear the cache in order to see your changes applied.
* Your custom hierarchy will not be merged with bundle's default,
 it will override the whole configuration.
