<?php 

namespace DependencyTracker\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    protected function configure()
    {
        $this->setName('init');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = getcwd().'/depfile.yml';

        if (is_file($file)) {
            $output->writeln('<error>depfile already exists</error>');
            return 1;
        }

        $config = <<<EOF
paths:
  - ./src
exclude_files:
  - .*test.*
layers:
  -
    name: LayerA
    collectors:
      -
        type: className
        args:
          regex: .*Acme\\LayerA.*
  -
    name: LayerB
    collectors:
      -
        type: className
        args:
          regex: .*Acme\\LayerB.*
ruleset:
  LayerA:
    - LayerB
EOF;

        file_put_contents($file, $config);
        $output->writeln("depfile <info>dumped.</info>");
    }
}
