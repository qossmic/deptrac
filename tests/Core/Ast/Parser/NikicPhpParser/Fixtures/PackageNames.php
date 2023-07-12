<?php

namespace Tests\Qossmic\Deptrac\Core\Ast\Parser\NikicPhpParser\Fixtures;

/**
 * @package PackageA
 */
class PackageAClass
{
}

class NoPackageClass
{
}

/**
 * @package PackageB
 */
function packageBFunction(): void
{
}

function noPackageFunction(): void
{
}
