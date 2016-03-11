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

        $dispatcher->addListener(PreCreateAstMapEvent::class, array($this, 'onPreCreateAstMapEvent'));
        $dispatcher->addListener(PostCreateAstMapEvent::class, array($this, 'onPostCreateAstMapEvent'));
        $dispatcher->addListener(AstFileAnalyzedEvent::class, array($this, 'onAstFileAnalyzedEvent'));
        $dispatcher->addListener(AstFileSyntaxErrorEvent::class, array($this, 'onAstFileSyntaxErrorEvent'));
    }

    public function onPreCreateAstMapEvent(PreCreateAstMapEvent $preCreateAstMapEvent)
    {
        $this->output->writeln(sprintf(
            'Start to create an AstMap for <info>%s</info> Files.',
            $preCreateAstMapEvent->getExpectedFileCount()
        ));
    }

    public function onPostCreateAstMapEvent(PostCreateAstMapEvent $postCreateAstMapEvent)
    {
        $this->output->writeln('AstMap created.');
    }

    public function onAstFileAnalyzedEvent(AstFileAnalyzedEvent $analyzedEvent)
    {
        $this->output->writeln(sprintf('Parsing File %s', $analyzedEvent->getFile()->getRelativePathname()));
    }

    public function onAstFileSyntaxErrorEvent(AstFileSyntaxErrorEvent $astFileSyntaxErrorEvent)
    {
        $this->output->writeln(sprintf(
            "Syntax Error on File %s\n<error>%s</error>\n",
            $astFileSyntaxErrorEvent->getFile()->getRelativePathname(),
            $astFileSyntaxErrorEvent->getSyntaxError()
        ));
    }
}
