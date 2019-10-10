<?php

declare(strict_types=1);

namespace SensioLabs\Deptrac\OutputFormatter;

use SensioLabs\Deptrac\DependencyContext;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;
use Symfony\Component\Console\Output\OutputInterface;

final class XMLOutputFormatter implements OutputFormatterInterface
{
    private static $argument_dump_xml = 'dump-xml';

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
            OutputFormatterOption::newValueOption(static::$argument_dump_xml, 'path to a dumped xml file', './deptrac-report.xml'),
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
        DependencyContext $dependencyContext,
        OutputInterface $output,
        OutputFormatterInput $outputFormatterInput
    ): void {
        $xml = $this->createXml($dependencyContext);

        if ($dumpXmlPath = $outputFormatterInput->getOption(static::$argument_dump_xml)) {
            file_put_contents($dumpXmlPath, $xml);
            $output->writeln('<info>XML Report dumped to '.realpath($dumpXmlPath).'</info>');
        }
    }

    /**
     * @throws \Exception
     */
    private function createXml(DependencyContext $dependencyContext): string
    {
        if (!class_exists(\DOMDocument::class)) {
            throw new \Exception('Unable to create xml file (php-xml needs to be installed)');
        }

        $xmlDoc = new \DOMDocument('1.0', 'UTF-8');
        $xmlDoc->formatOutput = true;

        $rootEntry = $xmlDoc->createElement('entries');

        foreach ($dependencyContext->getViolations() as $rule) {
            $this->addRule('violation', $rootEntry, $xmlDoc, $rule);
        }

        foreach ($dependencyContext->getViolations() as $rule) {
            $this->addRule('skipped_violation', $rootEntry, $xmlDoc, $rule);
        }

        $xmlDoc->appendChild($rootEntry);

        return $xmlDoc->saveXML();
    }

    private function addRule(string $type, \DOMElement $rootEntry, \DOMDocument $xmlDoc, RulesetViolation $rule): void
    {
        $entry = $xmlDoc->createElement('entry');
        $entry->appendChild(new \DOMAttr('type', $type));

        $entry->appendChild($xmlDoc->createElement('LayerA', (string) $rule->getLayerA()));
        $entry->appendChild($xmlDoc->createElement('LayerB', (string) $rule->getLayerB()));
        $entry->appendChild($xmlDoc->createElement('ClassA', (string) $rule->getDependency()->getClassA()));
        $entry->appendChild($xmlDoc->createElement('ClassB', (string) $rule->getDependency()->getClassB()));
        $entry->appendChild($xmlDoc->createElement('ClassALine', (string) $rule->getDependency()->getClassALine()));

        $rootEntry->appendChild($entry);
    }
}
