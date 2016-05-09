<?php

namespace SensioLabs\Deptrac\ConfigurationEngine;

interface ConfigurationEngineInterface
{

    public function supports($pathname);

    public function render($pathname);

}