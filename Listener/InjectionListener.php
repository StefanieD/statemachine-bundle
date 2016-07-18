<?php
namespace SDrost\StateMachineBundle\Listener;

use SDrost\StateMachineBundle\Listener\Doctrine\InjectionListener as BaseInjectionListener;
use Doctrine\Common\Util\ClassUtils;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use SDrost\StateMachineBundle\Entity\Stateful;

/**
 * Injects the state machines into stateful entities when they are loaded by
 * Doctrine or JMSSerializer.
 */
class InjectionListener extends BaseInjectionListener
{
    public function onPostDeserialize(ObjectEvent $event)
    {
        $entity = $event->getObject();

        if (!$entity instanceof Stateful) {
            return;
        }

        $this->injectStateMachine($entity);
    }
}
