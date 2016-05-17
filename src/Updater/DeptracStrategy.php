<?php

namespace SensioLabs\Deptrac\Updater;

use Humbug\SelfUpdate\Strategy\ShaStrategy;

class DeptracStrategy extends ShaStrategy
{
    public function __construct()
    {
        $this->setPharUrl('http://get.sensiolabs.de/deptrac.phar');
        $this->setVersionUrl('http://get.sensiolabs.de/deptrac.version');
    }
}
