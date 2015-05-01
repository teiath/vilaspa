<?php

namespace Vispanlab\UserBundle;

use Vispanlab\UserBundle\Security\Authentication\Factory\TEIFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class VispanlabUserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new TEIFactory());
    }

    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
