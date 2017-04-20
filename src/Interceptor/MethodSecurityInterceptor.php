<?php

namespace Librinfo\SecurityBundle\Interceptor;

use CG\Proxy\MethodInvocation;
use JMS\SecurityExtraBundle\Security\Authorization\Interception\MethodSecurityInterceptor as MSI;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MethodSecurityInterceptor extends MSI
{
    public function intercept(MethodInvocation $method)
    {

        try
        {
            return parent::intercept($method);
        }
        catch (AccessDeniedException $e)
        {
            $parent = new \ReflectionClass($this);
            $parent->getParentClass()
                ->getProperty('metadataFactory')
                ->setAccessible(true);
            $property = $parent->getParentClass()
                ->getProperty('metadataFactory');
            $property->setAccessible(true);

            // retrieve metadata factory of parent class
            // (it's a private attribute, so using ReflectionClass in order to get it)
            $metaDataFactory = $property
                ->getValue($this)
                ->getMetadataForClass($method->reflection->class);

            // Checking type of class (Controller or Service)
            if (substr(key($metaDataFactory->methodMetadata), -6) == "Action")
                throw $e; // We are on a controller action, so throwing Exception
            else
                return null; // We are on a service, so returning NULL to avoid blocking of all UI
        }
    }
}
