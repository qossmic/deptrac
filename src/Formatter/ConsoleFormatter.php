<?php

namespace SensioLabs\Deptrac\Formatter;

use SensioLabs\AstRunner\Event\AstFileAnalyzedEvent;
use SensioLabs\AstRunner\Event\AstFileSyntaxErrorEvent;
use SensioLabs\AstRunner\Event\PostCreateAstMapEvent;
use SensioLabs\AstRunner\Event\PreCreateAstMapEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConsoleFormatter implements EventSubscriberInterface
{
    /** @var OutputInterface */
    protected $output;

    /**
     * ConsoleFormatter constructor.
     *
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output) {
        $this->output = $output;
    }

    public static function getSubscribedEvents()
    {
        return array(
            PreCreateAstMapEvent::class => 'onPreCreateAstMapEvent',
            PostCreateAstMapEvent::class => 'onPostCreateAstMapEvent',
            AstFileAnalyzedEvent::class => 'onAstFileAnalyzedEvent',
            AstFileSyntaxErrorEvent::class => 'onAstFileSyntaxErrorEvent',
        );
    }

    public function onPreCreateAstMapEvent(PreCreateAstMapEvent $preCreateAstMapEvent)
    {
        $this->output->writeln(sprintf(
            'Start to create an AstMap for <info>%u</info> Files.',
            $preCreateAstMapEvent->getExpectedFileCount()
        ));
    }

    public function onPostCreateAstMapEvent(PostCreateAstMapEvent $postCreateAstMapEvent)
    {
        $this->output->writeln("\nAstMap created.");
    }

    public function onAstFileAnalyzedEvent(AstFileAnalyzedEvent $analyzedEvent)
    {
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->output->writeln(sprintf('Parsing File %s', $analyzedEvent->getFile()->getRelativePathname()));
        } else {
            $this->output->write('.');
        }
    }

    public function onAstFileSyntaxErrorEvent(AstFileSyntaxErrorEvent $astFileSyntaxErrorEvent)
    {
        $this->output->writeln(sprintf(
            "\nSyntax Error on File %s\n<error>%s</error>\n",
            $astFileSyntaxErrorEvent->getFile()->getRelativePathname(),
            $astFileSyntaxErrorEvent->getSyntaxError()
        ));
    }
}
