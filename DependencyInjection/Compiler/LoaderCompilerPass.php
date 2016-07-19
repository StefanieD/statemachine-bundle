<?php
namespace SDrost\StateMachineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class LoaderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('sdrost.state_machine.factory')) {
            return;
        }

        $definition = $container->getDefinition('sdrost.state_machine.factory');

        foreach ($container->findTaggedServiceIds('state_machine.loader') as $id => $attributes) {
            // get service classes in callbacks
            $loaderDefinition = $container->getDefinition($id);
            $loaderConfig = $loaderDefinition->getArgument(0);
            if (isset($loaderConfig['callbacks'])) {
                foreach (array('before', 'after') as $position) {
                    foreach ($loaderConfig['callbacks'][$position] as &$callback) {
                        if (
                            is_array($callback['do'])
                            && 0 === strpos($callback['do'][0], '@')
                            && $container->hasDefinition(substr($callback['do'][0], 1))
                        ) {
                            $callback['do'][0] = new Reference(substr($callback['do'][0], 1));
                        }
                    }
                }

                $loaderDefinition->replaceArgument(0, $loaderConfig);
            }

            $definition->addMethodCall('addNamedLoader', array($attributes[0]['state_machine'], new Reference($id)));

            // setLazy method wasn't available before 2.3, FiniteBundle requirement is ~2.1
            if (method_exists($definition, 'setLazy')) {
                $definition->setLazy(true);
            }
        }
    }
}
