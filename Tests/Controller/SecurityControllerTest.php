<?php

namespace Librinfo\SecurityBundle\Tests\Controller;

use Librinfo\SecurityBundle\Configurator\SecurityConfigurator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private $datafixtures;
    private $role_hierarchy;
    private $method_access_control;
    private $client;

    private function init(){
        $this->client = static::createClient();

        $this->datafixtures = $this->client->getContainer()->getParameter('librinfo.securitybundle.datafixtures');
        $this->method_access_control = $this->client->getContainer()->getParameter('security.access.method_access_control');
        $this->role_hierarchy = $this->client->getContainer()->getParameter('security.role_hierarchy.roles');
    }

    public function testIndex()
    {
        $this->init();

        /** @var \Librinfo\UserBundle\Entity\User $user */
        $user = $this->client->getContainer()->get('librinfo_core.services.authenticate')->authencicateUser($this->datafixtures['user']['username']);

        $this->assertTrue($user->hasRole($this->datafixtures['user']['role']));

        /** @var AdminMenu $twigExtension */
        $twigExtension = $this->client->getContainer()->get('twig')->getExtension('AdminMenu');
        $menuConfig = $this->client->getContainer()->getParameter('librinfo-core')['custom_menu'];

        $result = $twigExtension->showAdminMenu(
            $this->client->getContainer()->get('twig'),
            $menuConfig[0]['position']
        );

        $this->assertNotEmpty($result);
    }
}
