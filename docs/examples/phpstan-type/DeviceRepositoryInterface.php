<?php declare(strict_types=1);

namespace Acme;

use SomeVendor\DeviceRepositoryFindByFilter;

/**
 * @phpstan-type DeviceRepositoryFindByFilter array{
 *     ids?: int[]|string[],
 *     accountId?: int|string,
 *     activated?: boolean,
 * }
 */
interface DeviceRepositoryInterface
{
}
