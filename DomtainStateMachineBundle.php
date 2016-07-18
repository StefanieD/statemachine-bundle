<?php
namespace Domtain\StateMachineBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Domtain\StateMachineBundle\DependencyInjection\Compiler\LoaderCompilerPass;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class DomtainStateMachineBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new LoaderCompilerPass());
    }
}
