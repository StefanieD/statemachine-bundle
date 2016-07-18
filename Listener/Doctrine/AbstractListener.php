<?php
namespace SDrost\StateMachineBundle\Listener\Doctrine;

use Doctrine\Common\EventSubscriber;
use Finite\Factory\FactoryInterface;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
abstract class AbstractListener implements EventSubscriber
{
    protected $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Checks if the given entity supports state machines
     *
     * @param  \ReflectionClass $reflClass
     * @return boolean
     */
    protected function isEntitySupported(\ReflectionClass $reflClass)
    {
        return $reflClass->implementsInterface('\SDrost\StateMachineBundle\Entity\Stateful');
    }

    protected function injectStateMachine($entity)
    {
        if ($entity->getStateMachine() === null) {
            $stateMachine = $this->factory->get($entity);
            $entity->setStateMachine($stateMachine);
        }
    }
}
