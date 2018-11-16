<?php

namespace SensioLabs\Deptrac\Subscriber;

use SensioLabs\Deptrac\AstRunner\Event\AstFileAnalyzedEvent;
use SensioLabs\Deptrac\AstRunner\Event\AstFileSyntaxErrorEvent;
use SensioLabs\Deptrac\AstRunner\Event\PostCreateAstMapEvent;
use SensioLabs\Deptrac\AstRunner\Event\PreCreateAstMapEvent;
use SensioLabs\Deptrac\Dependency\Events as DependencyEvents;
use SensioLabs\Deptrac\Dependency\PreEmitEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConsoleSubscriber implements EventSubscriberInterface
{
    protected $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PreCreateAstMapEvent::class => 'onPreCreateAstMapEvent',
            PostCreateAstMapEvent::class => 'onPostCreateAstMapEvent',
            AstFileAnalyzedEvent::class => 'onAstFileAnalyzedEvent',
            AstFileSyntaxErrorEvent::class => 'onAstFileSyntaxErrorEvent',
            DependencyEvents::PRE_EMIT => 'onPreDependencyEmit',
            DependencyEvents::POST_EMIT => 'onPostDependencyEmit',
            DependencyEvents::PRE_FLATTEN => 'onPreDependencyFlatten',
            DependencyEvents::POST_FLATTEN => 'onPostDependencyFlatten',
        ];
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
