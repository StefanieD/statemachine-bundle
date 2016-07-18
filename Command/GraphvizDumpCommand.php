<?php
namespace SDrost\StateMachineBundle\Command;

use SDrost\StateMachineBundle\Services\Graphviz;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GraphvizDumpCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('state-machine:dump:graphviz')
            ->setDescription('Generate a graphiz dump of a state machine')
            ->addArgument('state-machine', InputArgument::REQUIRED, 'The state machine to dump');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stateMachineName = $input->getArgument('state-machine');
        $stateMachine = $this->getStateMachine($stateMachineName);
        $visualisation = new Graphviz();
        $tmpFileName = $stateMachineName . '.dot';
        $pictureName = $stateMachineName . '.png';
        $rootDir = $this->getContainer()->get('kernel')->getRootDir();
        $path = $rootDir . '/files/statemachine/';

        $handle = fopen($path . $tmpFileName, "a");
        fwrite($handle, $visualisation->render($stateMachine));
        fclose($handle);

        exec('dot -Tpng ' . $path . $tmpFileName . ' -o ' . $path . $pictureName);
        unlink($path . $tmpFileName);
    }

    private function getStateMachine($name)
    {
        $factory = $this->getContainer()->get('sdrost.state_machine.factory');

        return $factory->getNamed($name);
    }
}
