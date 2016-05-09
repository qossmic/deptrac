<?php

namespace SensioLabs\Deptrac\ConfigurationEngine;

interface ConfigurationProviderInterface
{

    public function supports($filepath);

    public function provide($filepath);

}