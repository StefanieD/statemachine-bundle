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
    protected $toStateCallbacks = array();

    /**
     * The state callbacks.
     *
     * @var array
     */
    protected $fromStateCallbacks = array();

    /**
     * {@inheritdoc}
     */
    public function addTransitionCallback($callback, $methodName, $specs)
    {
        if (!$callback instanceof CallbackInterface) {
            $callback = new CallbackBuilderFactory($callback);
        }

        foreach ($specs['on'] as $transition) {
            $this->transitionCallbacks[$transition][] = $methodName;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addToStateCallback($callback, $methodName, $specs)
    {
        if (!$callback instanceof CallbackInterface) {
            $callback = new CallbackBuilderFactory($callback);
        }

        foreach ($specs['to'] as $state) {
            $this->toStateCallbacks[$state][] = $methodName;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addFromStateCallback($callback, $methodName, $specs)
    {
        if (!$callback instanceof CallbackInterface) {
            $callback = new CallbackBuilderFactory($callback);
        }

        foreach ($specs['from'] as $state) {
            $this->fromStateCallbacks[$state][] = $methodName;
        }
    }

    public function getCallbacksOfTransition($transition)
    {
        return (isset($this->transitionCallbacks[$transition])) ? $this->transitionCallbacks[$transition] : array();
    }

    public function getCallbacksToState($state)
    {
        return (isset($this->toStateCallbacks[$state])) ? $this->toStateCallbacks[$state] : array();
    }

    public function getCallbacksFromState($state)
    {
        return (isset($this->fromStateCallbacks[$state])) ? $this->fromStateCallbacks[$state] : array();
    }

    public function getTransitionCallbacks()
    {
        return $this->transitionCallbacks;
    }

    public function getToStateCallbacks()
    {
        return $this->toStateCallbacks;
    }

    public function getFromStateCallbacks()
    {
        return $this->fromStateCallbacks;
    }
}
