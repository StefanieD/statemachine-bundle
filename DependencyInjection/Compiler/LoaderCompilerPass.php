<?php
namespace SDrost\StateMachineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class LoaderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('domtain.state_machine.factory')) {
            return;
        }

        $definition = $container->getDefinition('domtain.state_machine.factory');

        foreach ($container->findTaggedServiceIds('state_machine.loader') as $id => $attributes) {
            $definition->addMethodCall('addNamedLoader', array($attributes[0]['state_machine'], new Reference($id)));

            // setLazy method wasn't available before 2.3, FiniteBundle requirement is ~2.1
            if (method_exists($definition, 'setLazy')) {
                $definition->setLazy(true);
            }
        }
    }
}
