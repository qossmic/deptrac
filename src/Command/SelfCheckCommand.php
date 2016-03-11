<?php


namespace SensioLabs\Deptrac\Command;

use SensioLabs\Deptrac\Requirement\RequirementChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SelfCheckCommand extends Command
{
    /** @var RequirementChecker */
    private $requirementChecker;

    /**
     * @param RequirementChecker $requirementChecker
     */
    public function __construct(RequirementChecker $requirementChecker)
    {
        parent::__construct();
        $this->requirementChecker = $requirementChecker;
    }

    protected function configure()
    {
        $this->setName('self-check');
        $this->setAliases(['selfcheck']);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $majorProblems = $this->requirementChecker->getFailedRequirements();
        $minorProblems = $this->requirementChecker->getFailedRecommendations();

        if (!count($majorProblems) && !count($minorProblems)) {
            $output->writeln('<info>All checks passed successfully. Your system is ready to run Deptrac.</info>');
            return 0;
        }

        if (count($majorProblems)) {
            $output->writeln('<error>Major problems have been detected and must be fixed before continuing:</error>');

            foreach ($majorProblems as $problem) {
                $output->writeln('- '.$problem->getHelpText());
            }
        }

        if (count($minorProblems)) {
            if (count($majorProblems)) {
                $output->writeln('<comment>Additionally, to enhance your Deptrac experience, it’s recommended that you fix the following:</comment>');
            } else {
                $output->writeln('<comment>To enhance your Deptrac experience, it’s recommended that you fix the following:</comment>');
            }

            foreach ($minorProblems as $problem) {
                $output->writeln('- '.$problem->getHelpText());
            }
        }

        return count($majorProblems);
    }
}
