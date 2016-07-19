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
                    array($stateName, $transition->getState()), array('label' => $transitionName))
                    ->end();
            }
        }
    }

    private function getStateAttributes(State $state)
    {
        return array(
            'shape' => $this->getStateShape($state),
            'label' => $state->getName(),
        );
    }

    private function getStateShape(State $state)
    {
        switch ($state->getType()) {
            case State::TYPE_INITIAL:
                return 'doublecircle';
            case State::TYPE_FINAL:
                return 'rect';
            default:
                return 'circle';
        }
    }
}
