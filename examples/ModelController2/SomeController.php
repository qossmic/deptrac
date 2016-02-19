<?php 

namespace exmaples\MyNamespace\Controllers;

use exmaples\MyNamespace\Models\SomeModel;

class SomeController
{
    public function foo(SomeModel $m) {
        return $m;
    }
}
