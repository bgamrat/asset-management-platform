<?php

namespace LegacyBridgeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Legacy\AppBundle\DependencyInjection\Security\Factory\WsseFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LegacyBridgeBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }
}
