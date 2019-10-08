<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\Subscriber;

use SensioLabs\Deptrac\AstRunner\Event\AstFileAnalyzedEvent;
use SensioLabs\Deptrac\AstRunner\Event\AstFileSyntaxErrorEvent;
use SensioLabs\Deptrac\AstRunner\Event\PostCreateAstMapEvent;
use SensioLabs\Deptrac\AstRunner\Event\PreCreateAstMapEvent;
use SensioLabs\Deptrac\Dependency\Event\PostEmitEvent;
use SensioLabs\Deptrac\Dependency\Event\PostFlattenEvent;
use SensioLabs\Deptrac\Dependency\Event\PreEmitEvent;
use SensioLabs\Deptrac\Dependency\Event\PreFlattenEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConsoleSubscriber implements EventSubscriberInterface
{
    private $output;

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
            PreEmitEvent::class => 'onPreDependencyEmit',
            PostEmitEvent::class => 'onPostDependencyEmit',
            PreFlattenEvent::class => 'onPreDependencyFlatten',
            PostFlattenEvent::class => 'onPostDependencyFlatten',
        ];
    }

    public function onPreCreateAstMapEvent(PreCreateAstMapEvent $preCreateAstMapEvent): void
    {
        $this->output->writeln(
            sprintf(
                'Start to create an AstMap for <info>%u</info> Files.',
                $preCreateAstMapEvent->getExpectedFileCount()
            ),
            OutputInterface::VERBOSITY_VERBOSE
        );
    }

    public function onPostCreateAstMapEvent(PostCreateAstMapEvent $postCreateAstMapEvent): void
    {
        $this->output->writeln('AstMap created.', OutputInterface::VERBOSITY_VERBOSE);
    }

    public function onAstFileAnalyzedEvent(AstFileAnalyzedEvent $analyzedEvent): void
    {
        $this->output->writeln(
            sprintf('Parsing File %s', $analyzedEvent->getFile()->getPathname()),
            OutputInterface::VERBOSITY_VERBOSE
        );
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
        $this->output->writeln(
            sprintf('start emitting dependencies <info>"%s"</info>', $event->getEmitterName()),
            OutputInterface::VERBOSITY_VERBOSE
        );
    }

    public function onPostDependencyEmit(PostEmitEvent $event): void
    {
        $this->output->writeln('<info>end emitting dependencies</info>', OutputInterface::VERBOSITY_VERBOSE);
    }

    public function onPreDependencyFlatten(PreFlattenEvent $event): void
    {
        $this->output->writeln('<info>start flatten dependencies</info>', OutputInterface::VERBOSITY_VERBOSE);
    }

    public function onPostDependencyFlatten(PostFlattenEvent $event): void
    {
        $this->output->writeln('<info>end flatten dependencies</info>', OutputInterface::VERBOSITY_VERBOSE);
    }
}
