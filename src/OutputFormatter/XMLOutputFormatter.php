<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\RulesetEngine\Context;
use SensioLabs\Deptrac\RulesetEngine\SkippedViolation;
use SensioLabs\Deptrac\RulesetEngine\Violation;
use Symfony\Component\Console\Output\OutputInterface;

final class XMLOutputFormatter implements OutputFormatterInterface
{
    private const DUMP_XML = 'dump-xml';

    public function getName(): string
    {
        return 'xml';
    }

    /**
     * @return OutputFormatterOption[]
     */
    public function configureOptions(): array
    {
        return [
            OutputFormatterOption::newValueOption(self::DUMP_XML, 'path to a dumped xml file', './deptrac-report.xml'),
        ];
    }

    public function enabledByDefault(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function finish(
        Context $dependencyContext,
        OutputInterface $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $xml = $this->createXml($dependencyContext);

        if ($dumpXmlPath = $outputFormatterInput->getOption(self::DUMP_XML)) {
            file_put_contents($dumpXmlPath, $xml);
            $output->writeln('<info>XML Report dumped to '.realpath($dumpXmlPath).'</info>');
        }
    }

    /**
     * @throws \Exception
     */
    private function createXml(Context $dependencyContext): string
    {
        if (!class_exists(\DOMDocument::class)) {
            throw new \Exception('Unable to create xml file (php-xml needs to be installed)');
        }

        $xmlDoc = new \DOMDocument('1.0', 'UTF-8');
        $xmlDoc->formatOutput = true;

        $rootEntry = $xmlDoc->createElement('entries');

        foreach ($dependencyContext->violations() as $rule) {
            $this->addRule('violation', $rootEntry, $xmlDoc, $rule);
        }

        foreach ($dependencyContext->skippedViolations() as $rule) {
            $this->addRule('skipped_violation', $rootEntry, $xmlDoc, $rule);
        }

        $xmlDoc->appendChild($rootEntry);

        return (string) $xmlDoc->saveXML();
    }

    /**
     * @param Violation|SkippedViolation $rule
     */
    private function addRule(string $type, \DOMElement $rootEntry, \DOMDocument $xmlDoc, $rule): void
    {
        $entry = $xmlDoc->createElement('entry');
        $entry->appendChild(new \DOMAttr('type', $type));

        $entry->appendChild($xmlDoc->createElement('LayerA', $rule->getLayerA()));
        $entry->appendChild($xmlDoc->createElement('LayerB', $rule->getLayerB()));

        $dependency = $rule->getDependency();
        $entry->appendChild($xmlDoc->createElement('ClassA', $dependency->getClassLikeNameA()->toString()));
        $entry->appendChild($xmlDoc->createElement('ClassB', $dependency->getClassLikeNameB()->toString()));

        $fileOccurrence = $dependency->getFileOccurrence();
        $occurrence = $xmlDoc->createElement('occurrence');
        $occurrence->setAttribute('file', $fileOccurrence->getFilenpath());
        $occurrence->setAttribute('line', (string) $fileOccurrence->getLine());
        $entry->appendChild($occurrence);

        $rootEntry->appendChild($entry);
    }
}
