<?php

namespace SDrost\StateMachineBundle\StateMachine;

use Finite\Event\Callback\CallbackBuilderFactory;
use Finite\Event\Callback\CallbackInterface;
use Finite\StateMachine\StateMachine;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class ExtendedStateMachine extends StateMachine
{
    use StateMachineTrait;

    /**
     * The transition callbacks.
     *
     * @var array
     */
    protected $transitionCallbacks = array();

    /**
     * The state callbacks.
     *
     * @var array
     */
    protected $stateCallbacks = array();


    public function addCallback($callback, $callbackName, $specs)
    {
        if (isset($specs['on'][0])) {
            $this->addTransitionCallback($callback, $specs['on'][0], $callbackName);
        }
        if (isset($specs['from'][0])) {
            $this->addStateCallback($callback, $specs['from'][0], $callbackName);
        }
        if (isset($specs['to'][0])) {
            $this->addStateCallback($callback, $specs['to'][0], $callbackName);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addTransitionCallback($callback, $transition, $method)
    {
        if (!$callback instanceof CallbackInterface) {
            $callback = new CallbackBuilderFactory($callback);
        }

        $this->transitionCallbacks[$transition][] = $method;
    }

    /**
     * {@inheritdoc}
     */
    public function addStateCallback($callback, $state, $method)
    {
        if (!$callback instanceof CallbackInterface) {
            $callback = new CallbackBuilderFactory($callback);
        }

        $this->stateCallbacks[$state][] = $method;
    }

    public function getCallbacksOfTransition($transition)
    {
        return (isset($this->transitionCallbacks[$transition])) ? $this->transitionCallbacks[$transition] : array();
    }

    public function getCallbacksOfState($state)
    {
        return (isset($this->stateCallbacks[$state])) ? $this->stateCallbacks[$state] : array();
    }

    public function getTransitionCallbacks()
    {
        return $this->transitionCallbacks;
    }

    public function getStateCallbacks()
    {
        return $this->stateCallbacks;
    }
}
