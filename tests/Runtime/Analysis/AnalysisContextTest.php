<?php

declare(strict_types=1);

namespace Tests\Qossmic\Deptrac\Runtime\Analysis;

use PHPUnit\Framework\TestCase;
use Qossmic\Deptrac\Exception\Runtime\AnalysisContextException;
use Qossmic\Deptrac\Runtime\Analysis\AnalysisContext;

final class AnalysisContextTest extends TestCase
{
    public function testCanBeCreatedWithTokens(): void
    {
        $context = new AnalysisContext([AnalysisContext::CLASS_TOKEN, AnalysisContext::USE_TOKEN]);

        self::assertSame([AnalysisContext::CLASS_TOKEN, AnalysisContext::USE_TOKEN], $context->getTypes());
    }

    public function testIgnoresDuplicateEntries(): void
    {
        $context = new AnalysisContext([
            AnalysisContext::CLASS_TOKEN,
            AnalysisContext::CLASS_TOKEN,
            AnalysisContext::USE_TOKEN,
        ]);

        self::assertSame([AnalysisContext::CLASS_TOKEN, AnalysisContext::USE_TOKEN], $context->getTypes());
    }

    public function testFailsWithInvalidTypes(): void
    {
        $this->expectException(AnalysisContextException::class);
        $this->expectExceptionMessage('Your analysis context contains invalid types: "foo", "bar". Supported types: "class", "class_superglobal", "use", "file", "function", "function_superglobal".');

        new AnalysisContext([AnalysisContext::CLASS_TOKEN, 'foo', 'bar']);
    }

    public function testFailsWithEmptyArray(): void
    {
        $this->expectException(AnalysisContextException::class);
        $this->expectExceptionMessage('You must provide at least one type to be analysed. Supported types: "class", "class_superglobal", "use", "file", "function", "function_superglobal".');

        new AnalysisContext([]);
    }
}
