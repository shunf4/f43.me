<?php

namespace AppBundle\Parser;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ParserCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('AppBundle\Parser\ParserChain')) {
            return;
        }

        $definition = $container->getDefinition(
            'AppBundle\Parser\ParserChain'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'feed.parser'
        );

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addParser',
                    [new Reference($id), $attributes['alias']]
                );
            }
        }
    }
}
