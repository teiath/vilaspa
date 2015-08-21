<?php

namespace Vispanlab\CommonBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vispanlab\CommonBundle\DependencyInjection\Compiler\FixedCommentExtensionCompilerPass;

class VispanlabCommonBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new FixedCommentExtensionCompilerPass());
    }
}
