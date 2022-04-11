<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Subscriber;

use Qossmic\Deptrac\Console\Output;
use Qossmic\Deptrac\Event\AstRunner\AstFileAnalysedEvent;
use Qossmic\Deptrac\Event\AstRunner\AstFileSyntaxErrorEvent;
use Qossmic\Deptrac\Event\AstRunner\PostCreateAstMapEvent;
use Qossmic\Deptrac\Event\AstRunner\PreCreateAstMapEvent;
use Qossmic\Deptrac\Event\Dependency\PostEmitEvent;
use Qossmic\Deptrac\Event\Dependency\PostFlattenEvent;
use Qossmic\Deptrac\Event\Dependency\PreEmitEvent;
use Qossmic\Deptrac\Event\Dependency\PreFlattenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConsoleSubscriber implements EventSubscriberInterface
{
    private Output $output;

    public function __construct(Output $output)
    {
        $this->output = $output;
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PreCreateAstMapEvent::class => 'onPreCreateAstMapEvent',
            PostCreateAstMapEvent::class => 'onPostCreateAstMapEvent',
            AstFileAnalysedEvent::class => 'onAstFileAnalysedEvent',
            AstFileSyntaxErrorEvent::class => 'onAstFileSyntaxErrorEvent',
            PreEmitEvent::class => 'onPreDependencyEmit',
            PostEmitEvent::class => 'onPostDependencyEmit',
            PreFlattenEvent::class => 'onPreDependencyFlatten',
            PostFlattenEvent::class => 'onPostDependencyFlatten',
        ];
    }

    public function onPreCreateAstMapEvent(PreCreateAstMapEvent $preCreateAstMapEvent): void
    {
        if ($this->output->isVerbose()) {
            $this->output->writeLineFormatted(
                sprintf(
                    'Start to create an AstMap for <info>%u</info> Files.',
                    $preCreateAstMapEvent->getExpectedFileCount()
                )
            );
        }
    }

    public function onPostCreateAstMapEvent(PostCreateAstMapEvent $postCreateAstMapEvent): void
    {
        if ($this->output->isVerbose()) {
            $this->output->writeLineFormatted('AstMap created.');
        }
    }

    public function onAstFileAnalysedEvent(AstFileAnalysedEvent $analysedEvent): void
    {
        if ($this->output->isVerbose()) {
            $this->output->writeLineFormatted(sprintf('Parsing File %s', $analysedEvent->getFile()));
        }
    }

    public function onAstFileSyntaxErrorEvent(AstFileSyntaxErrorEvent $astFileSyntaxErrorEvent): void
    {
        $this->output->writeLineFormatted(sprintf(
            "\nSyntax Error on File %s\n<error>%s</error>\n",
            $astFileSyntaxErrorEvent->getFile(),
            $astFileSyntaxErrorEvent->getSyntaxError()
        ));
    }

    public function onPreDependencyEmit(PreEmitEvent $event): void
    {
        if ($this->output->isVerbose()) {
            $this->output->writeLineFormatted(
                sprintf('start emitting dependencies <info>"%s"</info>', $event->getEmitterName())
            );
        }
    }

    public function onPostDependencyEmit(PostEmitEvent $event): void
    {
        if ($this->output->isVerbose()) {
            $this->output->writeLineFormatted('<info>end emitting dependencies</info>');
        }
    }

    public function onPreDependencyFlatten(PreFlattenEvent $event): void
    {
        if ($this->output->isVerbose()) {
            $this->output->writeLineFormatted('<info>start flatten dependencies</info>');
        }
    }

    public function onPostDependencyFlatten(PostFlattenEvent $event): void
    {
        if ($this->output->isVerbose()) {
            $this->output->writeLineFormatted('<info>end flatten dependencies</info>');
        }
    }
}
