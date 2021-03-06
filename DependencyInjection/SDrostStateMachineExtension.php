<?php
namespace SDrost\StateMachineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class SDrostStateMachineExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // parse the configuration
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        // load the services
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('factories.yml');
        $loader->load('listeners.yml');

        $bundles = $container->getParameter('kernel.bundles');
        if (isset($bundles['TwigBundle'])) {
            $loader->load('twig.yml');
        }

        // register state machine loaders
        $this->registerStateMachines($config['state_machines'], $container);
        // set visualization properties
        $this->setVisualizationProperties($config['visualization'], $container);

        // disable useless listeners
        if (!$config['auto_injection']) {
            $container->removeDefinition('sdrost.state_machine.listener.injection');
        }
        if (!$config['auto_validation']) {
            $container->removeDefinition('sdrost.state_machine.listener.persistence');
        }

        // set the kphoen.state_machine service as "not shared" (or scope: prototype)
        $stateMachineDefinition = $container->getDefinition('sdrost.state_machine');
        if (method_exists($stateMachineDefinition, 'setShared')) {
            $stateMachineDefinition->setShared(false);
        } else {
            $stateMachineDefinition->setScope(ContainerInterface::SCOPE_PROTOTYPE);
        }
    }

    protected function registerStateMachines(array $machines, ContainerBuilder $container)
    {
        $persistenceListenerDef = $container->getDefinition('sdrost.state_machine.listener.persistence');

        foreach ($machines as $name => $config) {
            $container
                ->setDefinition('sdrost.state_machine.loader.'.$name, new DefinitionDecorator('sdrost.state_machine.array_loader'))
                ->replaceArgument(0, $config)
                ->addTag('state_machine.loader', array('state_machine' => $name))
            ;

            $persistenceListenerDef->addMethodCall('registerClass', array($config['class'], $config['property']));
        }
    }

    protected function setVisualizationProperties(array $properties, ContainerBuilder $container)
    {
        $graphvizServiceDef = $container->getDefinition('sdrost.state_machine.graphviz');
        $graphvizServiceDef->addMethodCall('setPrintDirection', array($properties['print_dir']));
        $graphvizServiceDef->addMethodCall('setPrintCallbacks', array($properties['print_callbacks']));
    }
}
