<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap;

use Qossmic\Deptrac\Contract\Ast\FileOccurrence;
use Qossmic\Deptrac\Contract\Ast\TokenInterface;

/**
 * @psalm-immutable
 */
class DependencyToken
{
    public function __construct(
        public readonly TokenInterface $token,
        public readonly FileOccurrence $fileOccurrence,
        public readonly DependencyTokenType $type
    ) {
    }
}
