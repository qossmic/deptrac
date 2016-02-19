<?php

namespace exmaples\MyNamespace\Service;

use exmaples\MyNamespace\Repository\SomeRepository;

class SomeService
{

    public function __construct(SomeRepository $someRepository)
    {
    }
}
