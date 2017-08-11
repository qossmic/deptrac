<?php

namespace SensioLabs\Deptrac\DependencyResult;

interface DependencyInterface
{
    /**
     * @return string
     */
    public function getClassA();

    /**
     * @return string
     */
    public function getClassALine();

    /**
     * @return string
     */
    public function getClassB();
}
