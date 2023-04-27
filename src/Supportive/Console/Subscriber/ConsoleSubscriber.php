<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Supportive\Console\Subscriber;

use Qossmic\Deptrac\Contract\Ast\AstFileAnalysedEvent;
use Qossmic\Deptrac\Contract\Ast\AstFileSyntaxErrorEvent;
use Qossmic\Deptrac\Contract\Ast\PostCreateAstMapEvent;
use Qossmic\Deptrac\Contract\Ast\PreCreateAstMapEvent;
use Qossmic\Deptrac\Contract\Dependency\PostEmitEvent;
use Qossmic\Deptrac\Contract\Dependency\PostFlattenEvent;
use Qossmic\Deptrac\Contract\Dependency\PreEmitEvent;
use Qossmic\Deptrac\Contract\Dependency\PreFlattenEvent;
use Qossmic\Deptrac\Contract\OutputFormatter\OutputInterface;
use Qossmic\Deptrac\Supportive\Time\Stopwatch;
use Qossmic\Deptrac\Supportive\Time\StopwatchException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use function sprintf;

class ConsoleSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly OutputInterface $output,
        private readonly Stopwatch $stopwatch,
    ) {
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

    /** @throws StopwatchException */
    public function onPreCreateAstMapEvent(PreCreateAstMapEvent $preCreateAstMapEvent): void
    {
        if ($this->output->isVerbose()) {
            $this->stopwatch->start('ast');

            $this->output->writeLineFormatted(
                sprintf(
                    'Start to create an AstMap for <info>%u</info> Files.',
                    $preCreateAstMapEvent->expectedFileCount
                )
            );
        }
    }

    /** @throws StopwatchException */
    public function onPostCreateAstMapEvent(PostCreateAstMapEvent $postCreateAstMapEvent): void
    {
        if ($this->output->isVerbose()) {
            $period = $this->stopwatch->stop('ast');

            $this->output->writeLineFormatted(
                sprintf(
                    'AstMap created in %01.2f sec.',
                    $period->toSeconds(),
                )
            );
        }
    }

    public function onAstFileAnalysedEvent(AstFileAnalysedEvent $analysedEvent): void
    {
        if ($this->output->isVerbose()) {
            $this->output->writeLineFormatted(sprintf('Parsing File %s', $analysedEvent->file));
        }
    }

    public function onAstFileSyntaxErrorEvent(AstFileSyntaxErrorEvent $astFileSyntaxErrorEvent): void
    {
        $this->output->writeLineFormatted(sprintf(
            "\nSyntax Error on File %s\n<error>%s</error>\n",
            $astFileSyntaxErrorEvent->file,
            $astFileSyntaxErrorEvent->syntaxError
        ));
    }

    /** @throws StopwatchException */
    public function onPreDependencyEmit(PreEmitEvent $event): void
    {
        if ($this->output->isVerbose()) {
            $this->stopwatch->start('deps');

            $this->output->writeLineFormatted(
                sprintf('start emitting dependencies <info>"%s"</info>', $event->emitterName)
            );
        }
    }

    /** @throws StopwatchException */
    public function onPostDependencyEmit(PostEmitEvent $event): void
    {
        if ($this->output->isVerbose()) {
            $period = $this->stopwatch->stop('deps');

            $this->output->writeLineFormatted(
                sprintf(
                    '<info>Dependencies emitted in %01.2f sec.</info>',
                    $period->toSeconds(),
                )
            );
        }
    }

    /** @throws StopwatchException */
    public function onPreDependencyFlatten(PreFlattenEvent $event): void
    {
        if ($this->output->isVerbose()) {
            $this->stopwatch->start('flatten');

            $this->output->writeLineFormatted('<info>start flatten dependencies</info>');
        }
    }

    /** @throws StopwatchException */
    public function onPostDependencyFlatten(PostFlattenEvent $event): void
    {
        if ($this->output->isVerbose()) {
            $period = $this->stopwatch->stop('flatten');

            $this->output->writeLineFormatted(
                sprintf(
                    '<info>Dependencies flattened in %01.f sec.</info>',
                    $period->toSeconds(),
                )
            );
        }
    }
}
