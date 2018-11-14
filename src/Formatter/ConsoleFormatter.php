<?php

namespace SensioLabs\Deptrac\Formatter;

use SensioLabs\AstRunner\Event\AstFileAnalyzedEvent;
use SensioLabs\AstRunner\Event\AstFileSyntaxErrorEvent;
use SensioLabs\AstRunner\Event\PostCreateAstMapEvent;
use SensioLabs\AstRunner\Event\PreCreateAstMapEvent;
use SensioLabs\Deptrac\Dependency\Events as DependencyEvents;
use SensioLabs\Deptrac\Dependency\PreEmitEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ConsoleFormatter
{
    protected $dispatcher;
    protected $output;

    public function __construct(EventDispatcherInterface $dispatcher, OutputInterface $output)
    {
        $this->dispatcher = $dispatcher;
        $this->output = $output;

        $dispatcher->addListener(PreCreateAstMapEvent::class, [$this, 'onPreCreateAstMapEvent']);
        $dispatcher->addListener(PostCreateAstMapEvent::class, [$this, 'onPostCreateAstMapEvent']);
        $dispatcher->addListener(AstFileAnalyzedEvent::class, [$this, 'onAstFileAnalyzedEvent']);
        $dispatcher->addListener(AstFileSyntaxErrorEvent::class, [$this, 'onAstFileSyntaxErrorEvent']);
        $dispatcher->addListener(DependencyEvents::PRE_EMIT, [$this, 'onPreDependencyEmit']);
        $dispatcher->addListener(DependencyEvents::POST_EMIT, [$this, 'onPostDependencyEmit']);
        $dispatcher->addListener(DependencyEvents::PRE_FLATTEN, [$this, 'onPreDependencyFlatten']);
        $dispatcher->addListener(DependencyEvents::POST_FLATTEN, [$this, 'onPostDependencyFlatten']);
    }

    public function onPreCreateAstMapEvent(PreCreateAstMapEvent $preCreateAstMapEvent): void
    {
        $this->output->writeln(sprintf(
            'Start to create an AstMap for <info>%u</info> Files.',
            $preCreateAstMapEvent->getExpectedFileCount()
        ));
    }

    public function onPostCreateAstMapEvent(PostCreateAstMapEvent $postCreateAstMapEvent): void
    {
        $this->output->writeln("\nAstMap created.");
    }

    public function onAstFileAnalyzedEvent(AstFileAnalyzedEvent $analyzedEvent): void
    {
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->output->writeln(sprintf('Parsing File %s', $analyzedEvent->getFile()->getPathname()));
        } else {
            $this->output->write('.');
        }
    }

    public function onAstFileSyntaxErrorEvent(AstFileSyntaxErrorEvent $astFileSyntaxErrorEvent): void
    {
        $this->output->writeln(sprintf(
            "\nSyntax Error on File %s\n<error>%s</error>\n",
            $astFileSyntaxErrorEvent->getFile()->getPathname(),
            $astFileSyntaxErrorEvent->getSyntaxError()
        ));
    }

    public function onPreDependencyEmit(PreEmitEvent $event): void
    {
        $this->output->writeln(sprintf('start emitting dependencies <info>"%s"</info>', $event->getEmitterName()));
    }

    public function onPostDependencyEmit(): void
    {
        $this->output->writeln('<info>end emitting dependencies</info>');
    }

    public function onPreDependencyFlatten(): void
    {
        $this->output->writeln('<info>start flatten dependencies</info>');
    }

    public function onPostDependencyFlatten(): void
    {
        $this->output->writeln('<info>end flatten dependencies</info>');
    }
}
