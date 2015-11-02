# SymfonyLibrinfoSecurityBundle
Managing security rules and access controls


The "libre-informatique" bundles
--------------------------------

```php
    // ...
    public function registerBundles()
    {
        $bundles = array(
            // ...

            // The libre-informatique bundles
            new Librinfo\SecurityBundle\LibrinfoSecurityBundle(),

            // your personal bundles
        );
    }
```


Managing roles by configuration in YAML file.
Here's an example:
```
# app/config/parameters.yml
parameters:
    librinfo.security:
        method_access_control:
            'FOSUserBundle:SecurityController:loginAction$': 'isAnonymous()'
            'SonataAdminBundle:Core:dashboard$': 'hasRole("ROLE_USER")'
            'Librinfo\\UIBundle\\Twig\\Extension\\AdminMenu::showAdminMenu$': 'hasRole("CRM_MANAGER")'
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

Managing access controls for limit access to user at default function.
Here's an example:
```
method_access_control:
    'FOSUserBundle:SecurityController:loginAction$': 'isAnonymous()'
    'SonataAdminBundle:Core:dashboard$': 'hasRole("ROLE_USER")'
    'Librinfo\\UIBundle\\Twig\\Extension\\AdminMenu::showAdminMenu$': 'hasRole("CRM_MANAGER")'
```


If you change your config.yml don't forget to clear:cache.
