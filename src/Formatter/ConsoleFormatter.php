<?php

namespace SensioLabs\Deptrac\Formatter;

use SensioLabs\AstRunner\Event\AstFileAnalyzedEvent;
use SensioLabs\AstRunner\Event\AstFileSyntaxErrorEvent;
use SensioLabs\AstRunner\Event\PostCreateAstMapEvent;
use SensioLabs\AstRunner\Event\PreCreateAstMapEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ConsoleFormatter
{
    /** @var EventDispatcherInterface */
    protected $dispatcher;

    /** @var OutputInterface */
    protected $output;

    /**
     * ConsoleFormatter constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        OutputInterface $output
    ) {
        $this->dispatcher = $dispatcher;
        $this->output = $output;

        $dispatcher->addListener(PreCreateAstMapEvent::class, [$this, 'onPreCreateAstMapEvent']);
        $dispatcher->addListener(PostCreateAstMapEvent::class, [$this, 'onPostCreateAstMapEvent']);
        $dispatcher->addListener(AstFileAnalyzedEvent::class, [$this, 'onAstFileAnalyzedEvent']);
        $dispatcher->addListener(AstFileSyntaxErrorEvent::class, [$this, 'onAstFileSyntaxErrorEvent']);
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
