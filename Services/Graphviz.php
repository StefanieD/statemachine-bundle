<?php
namespace SDrost\StateMachineBundle\Services;

use Alom\Graphviz\Digraph;
use Finite\StateMachine\StateMachineInterface as StateMachine;
use Finite\State\StateInterface as State;

class Graphviz
{
    protected $printDir;

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

        $this->addStates($graph, $stateMachine);
        $this->addTransitions($graph, $stateMachine);

        $graph->end();

        return $graph->render();
    }

    public function setPrintDirection($dir)
    {
        $this->printDir = $dir;

        return $this;
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

                $graph->beginEdge(
                    array($stateName, $transition->getState()), array('label' => $transitionName /*. $this->getTransitionCallbacks($transition)*/))
                    ->end();
            }
        }
    }

    private function getTransitionCallbacks($transition)
    {
        $callbackString = "";
        for($i=0; $i<=2; $i++) {
            $callbackString .= "\n[callback" . $i . "]";
        }

        return $callbackString;
    }

    private function getStateAttributes(State $state)
    {
        return array_merge(array(
            'shape' => $this->getStateShape($state),
            'label' => $state->getName()),
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
                return 'rect';
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
}
