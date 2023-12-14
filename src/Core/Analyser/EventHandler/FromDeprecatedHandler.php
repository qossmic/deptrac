<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Analyser\EventHandler;

use Qossmic\Deptrac\Contract\Analyser\ProcessEvent;
use Qossmic\Deptrac\Core\Ast\AstMap\ClassLike\ClassLikeReference;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Allow deprecated code to depend on anything.
 *
 * @internal
 */
class FromDeprecatedHandler implements EventSubscriberInterface
{
    private bool $enabled;

    /**
     * @param array{skip_deprecated: bool, ...} $config
     */
    public function __construct(array $config)
    {
        $this->enabled = $config['skip_deprecated'];
    }

    public function invoke(ProcessEvent $event): void
    {
        if (!$this->enabled) {
            return;
        }

        $ref = $event->dependerReference;

        if ($ref instanceof ClassLikeReference && ($ref->hasTag('@deprecated') ?? false)) {
            $event->stopPropagation();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            ProcessEvent::class => ['invoke', 1],
        ];
    }
}
