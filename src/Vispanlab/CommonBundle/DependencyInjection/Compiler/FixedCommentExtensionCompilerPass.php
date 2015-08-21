<?php

namespace Vispanlab\CommonBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FixedCommentExtensionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('fos_comment.twig.comment_extension');
        $definition->setClass('Vispanlab\CommonBundle\Extension\FixedCommentExtension');
    }
}