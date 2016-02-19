<?php 

namespace exmaples\MyNamespace\Controllers;

use exmaples\MyNamespace\Service\SomeService;

class SomeController
{
    /** SomeController constructor.*/
    public function __construct(SomeService $service)
    {
    }
}
