<?php

declare(strict_types=1);

return [
    'prefix' => null,                       // string|null
    'finders' => [],                        // Finder[]
    'patchers' => [],                       // callable[]
    'files-whitelist' => [],                // string[]
    'whitelist' => [],                      // string[]
    'whitelist-global-constants' => true,   // bool
    'whitelist-global-classes' => true,     // bool
    'whitelist-global-functions' => true,   // bool
];
