<?php
namespace SDrost\StateMachineBundle\Services;

use Alom\Graphviz\Digraph;
use Finite\StateMachine\StateMachineInterface as StateMachine;
use Finite\State\StateInterface as State;

class Graphviz
{
    /**
     * @var string direction for graph
     */
    protected $printDir;

    /**
     * @var bool
     */
    protected $printCallbacks = false;

    /**
     * Returns the dot representation of a state machine.
     *
     * @param StateMachine $stateMachine The state machine to dump.
     *
     * @return string
     */
    public function render(StateMachine $stateMachine)
    {
        $graph = new Digraph('G');
        $graph->set('rankdir', $this->printDir);
        $graph->set('ranksep', 1.5); // distance between node levels
        $graph->set('nodesep', 0.5); // distance between nodes same hierarchie
        $graph->set('ratio', 2);
        $graph->set('overlap', false);

        $this->addStates($graph, $stateMachine);
        $this->addTransitions($graph, $stateMachine);

        $graph->end();

        return $graph->render();
    }

    private function addStates(Digraph $graph, StateMachine $stateMachine)
    {
        foreach ($stateMachine->getStates() as $stateName) {
            $state = $stateMachine->getState($stateName);

            $graph->beginNode(
                $stateName,
                $this->getStateAttributes($state)
            )->end();
        }
    }

    private function addTransitions(Digraph $graph, StateMachine $stateMachine)
    {
        foreach ($stateMachine->getStates() as $stateName) {
            $state = $stateMachine->getState($stateName);

            foreach ($state->getTransitions() as $transitionName) {
                $transition = $stateMachine->getTransition($transitionName);
                $label = ($this->printCallbacks) ?
                    $transitionName . $this->getTransitionCallbacks($stateMachine, $transition) :
                    $transitionName;

                $graph->beginEdge(
                    array($stateName, $transition->getState()),
                    $this->getEdgeAttributes($label, $stateName, $transition->getState())
                )->end();
            }
        }
    }

    private function getTransitionCallbacks($stateMachine, $transition)
    {
        $callbacks = array_merge(
            $stateMachine->getCallbacksOfTransition($transition->getName()),
            $stateMachine->getCallbacksToState($transition->getState()),
            $stateMachine->getCallbacksFromState($transition->getState())
        );
        $callbackString = "";
        foreach ($callbacks as $callbackName) {
            $callbackString .= "\r [" . $callbackName . "] ";
        }

        return $callbackString;
    }

    private function getEdgeAttributes($label, $fromState, $toState)
    {
        $options = array(
            'label' => $label,
            'fontname' => 'Verdana',
            'fontsize' => 12
        );

        if ($fromState === $toState) {
            $options = array_merge($options, array(
                'color' => 'dimgray',
                'fontcolor' => 'dimgray'
            ));
            $options['fontsize'] = 11;
        }

        return $options;
    }

    private function getStateAttributes(State $state)
    {
        return array_merge(
            array(
                'shape' => $this->getStateShape($state),
                'label' => $state->getName(),
                'fontname' => 'Verdana'
            ),
            $this->getStateColorProperties($state)
        );
    }

    private function getStateShape(State $state)
    {
        switch ($state->getType()) {
            case State::TYPE_INITIAL:
                return 'doublecircle';
            case State::TYPE_FINAL:
                return 'circle';
            default:
                return 'circle';
        }
    }

    private function getStateColorProperties(State $state)
    {
        switch ($state->getType()) {
            case State::TYPE_INITIAL:
                return array(
                    'style' => 'filled',
                    'fillcolor' => 'darkolivegreen1'
                );
            case State::TYPE_FINAL:
                return array(
                    'style' => 'filled',
                    'fillcolor' => 'aliceblue'
                );
            default:
                return array(
                    'style' => 'filled',
                    'fillcolor' => 'beige'
                );
        }
    }

    public function setPrintDirection($dir)
    {
        $this->printDir = $dir;

        return $this;
    }

    public function setPrintCallbacks($value)
    {
        $this->printCallbacks = $value;

        return $this;
    }
}
