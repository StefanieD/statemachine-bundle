<?php
namespace SDrost\StateMachineBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use SDrost\StateMachineBundle\DependencyInjection\Compiler\LoaderCompilerPass;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class SDrostStateMachineBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new LoaderCompilerPass());
    }
}
