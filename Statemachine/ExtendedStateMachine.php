<?php

namespace Domtain\StateMachineBundle\StateMachine;

use Finite\StateMachine\StateMachine;

/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class ExtendedStateMachine extends StateMachine
{
    use StateMachineExtension;
}
