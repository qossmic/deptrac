<?php 

namespace examples\Sublayers1\Controller;



use examples\Sublayers1\Services\Domain\OrderService;

class ControllerA
{

    public function foo() {
        new OrderService();
    }

}
