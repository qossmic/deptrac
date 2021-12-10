<?php

namespace Qossmic\Deptrac\Console\Command;

use function is_string;
use Qossmic\Deptrac\AstRunner\AstMap\ClassLikeName;
use Qossmic\Deptrac\AstRunner\AstMap\FileName;
use Qossmic\Deptrac\AstRunner\AstMap\FunctionName;
use Qossmic\Deptrac\Exception\Console\InvalidArgumentException;
use Qossmic\Deptrac\Exception\Console\InvalidTokenException;

class DebugTokenOptions
{
    private string $configurationFile;

    /**
     * @var ClassLikeName|FileName|FunctionName
     */
    private $token;

    /**
     * @param mixed $configurationFile
     */
    public function __construct($configurationFile, string $tokenName, string $tokenType)
    {
        if (!is_string($configurationFile)) {
            throw InvalidArgumentException::invalidDepfileType($configurationFile);
        }

        $this->configurationFile = $configurationFile;

        switch ($tokenType) {
            case 'class-like':
                $this->token = ClassLikeName::fromFQCN($tokenName);
                break;
            case 'function':
                $this->token = FunctionName::fromFQCN($tokenName);
                break;
            case 'file':
                $this->token = new FileName($tokenName);
                break;
            default:
                throw InvalidTokenException::invalidTokenType($tokenType, ['class-like', 'function', 'file']);
        }
    }

    public function getConfigurationFile(): string
    {
        return $this->configurationFile;
    }

    /**
     * @return ClassLikeName|FileName|FunctionName
     */
    public function getToken()
    {
        return $this->token;
    }
}
