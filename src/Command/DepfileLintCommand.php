<?php 

namespace SensioLabs\Deptrac\Command;

use SensioLabs\Deptrac\ConfigurationEngine\ConfigurationEngineInterface;
use SensioLabs\Deptrac\OutputFormatter\Graphviz\Graphs;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class DepfileLintCommand extends Command
{

    /** @var ConfigurationEngineInterface */
    private $configurationEngine;

    /**
     * @param ConfigurationEngineInterface $configurationEngine
     */
    public function __construct(ConfigurationEngineInterface $configurationEngine)
    {
        $this->configurationEngine = $configurationEngine;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('lint');

        $this->getDefinition()->setArguments([
            new InputArgument('depfile', InputArgument::OPTIONAL, 'Path to the depfile', getcwd().'/depfile.yml'),
        ]);
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {

        $g = new Graphs('', [], [], true);
        $output->writeln($g->render());
        $output->writeln("------");

        $a = $this->configurationEngine->render(
            $input->getArgument('depfile', getcwd().'/depfile.yml')
        );

        try {
            $output->write(Yaml::dump($a, PHP_INT_MAX));
        } catch(ParseException $e) {
            $output->write($a);
            throw $e;
        }
    }
}
