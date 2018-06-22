<?php

namespace SensioLabs\Deptrac\OutputFormatter;

use Fhaculty\Graph\Vertex;
use SensioLabs\AstRunner\AstMap;
use SensioLabs\Deptrac\ClassNameLayerResolverInterface;
use SensioLabs\Deptrac\Dependency\Result;
use SensioLabs\Deptrac\DependencyContext;
use SensioLabs\Deptrac\DependencyResult\DependencyInterface;
use SensioLabs\Deptrac\DependencyResult\InheritDependency;
use SensioLabs\Deptrac\RulesetEngine\RulesetViolation;
use Symfony\Component\Console\Output\OutputInterface;

final class JUnitOutputFormatter implements OutputFormatterInterface
{
    private static $argument_dump_xml = 'dump-xml';

    public function getName(): string
    {
        return 'junit';
    }

    /**
     * @return OutputFormatterOption[]
     */
    public function configureOptions(): array
    {
        return [
            OutputFormatterOption::newValueOption(static::$argument_dump_xml, 'path to a dumped xml file', ''),
        ];
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function finish(
        DependencyContext $dependencyContext,
        OutputInterface $output,
        OutputFormatterInput $outputFormatterInput
    ) {

        $xml = $this->createXml($dependencyContext);

        if ($dumpXmlPath = $outputFormatterInput->getOption(static::$argument_dump_xml) ?: './junit-report.xml') {
            file_put_contents($dumpXmlPath, $xml);
            $output->writeln('<info>JUnit Report dumped to ' . realpath($dumpXmlPath) . '</info>');
        }
    }

    /**
     * @param DependencyContext $dependencyContext
     * @return string
     * @throws \Exception
     */
    private function createXml(DependencyContext $dependencyContext)
    {
        if (!class_exists(\DomDocument::class)) {
            throw new \Exception('Unable to create xml file (php-xml needs to be installed)');
        }

        $xmlDoc = new \DOMDocument('1.0', "UTF-8");
        $xmlDoc->formatOutput = true;

        $this->addTestSuites($dependencyContext, $xmlDoc);

        return $xmlDoc->saveXML();
    }

    private function addTestSuites(DependencyContext $dependencyContext, \DOMDocument $xmlDoc)
    {
        $testSuites = $xmlDoc->createElement('testsuites');

        $xmlDoc->appendChild($testSuites);

        $this->addTestSuite($dependencyContext, $xmlDoc, $testSuites);
    }

    private function addTestSuite(DependencyContext $dependencyContext, \DOMDocument $xmlDoc, \DOMElement $testSuites)
    {
        $layers = $dependencyContext->getClassNameLayerResolver()->getLayers();

        $layerIndex = 0;
        foreach ($layers as $layer) {
            $violationsByLayer = $dependencyContext->getViolationsByLayerName($layer);
            if (count($violationsByLayer) === 0) {
                continue;
            }

            $violationsByClassName = [];
            foreach ($violationsByLayer as $violation) {
                $violationsByClassName[$violation->getDependency()->getClassA()][] = $violation;
            }

            $testSuite = $xmlDoc->createElement('testsuite');
            $testSuite->appendChild(new \DOMAttr('id', ++$layerIndex));
            $testSuite->appendChild(new \DOMAttr('package', ''));
            $testSuite->appendChild(new \DOMAttr('name', $layer));
            $testSuite->appendChild(new \DOMAttr('hostname', 'localhost'));
            $testSuite->appendChild(new \DOMAttr('tests', count($violationsByClassName)));
            $testSuite->appendChild(new \DOMAttr('failures', count($violationsByLayer)));
            $testSuite->appendChild(new \DOMAttr('errors', 0));
            $testSuite->appendChild(new \DOMAttr('time', 0));

            $testSuites->appendChild($testSuite);

            $this->addTestCase($layer, $violationsByClassName, $xmlDoc, $testSuite);
        }
    }

    private function addTestCase(string $layer, array $violationsByClassName, \DOMDocument $xmlDoc, \DOMElement $testSuite)
    {
        foreach ($violationsByClassName as $className => $violations) {
            $testCase = $xmlDoc->createElement('testcase');
            $testCase->appendChild(new \DOMAttr('name', $layer . ' - ' . $className));
            $testCase->appendChild(new \DOMAttr('classname', $className));
            $testCase->appendChild(new \DOMAttr('time', 0));

            foreach ($violations as $violation) {
                $this->addFailure($violation, $xmlDoc, $testCase);
            }

            $testSuite->appendChild($testCase);
        }
    }

    private function addFailure(RulesetViolation $violation, \DOMDocument $xmlDoc, \DOMElement $testCase)
    {
        $dependency = $violation->getDependency();

        $message = sprintf(
            '%s:%s must not depend on %s (%s on %s)',
            $dependency->getClassA(),
            $dependency->getClassALine(),
            $dependency->getClassB(),
            $violation->getLayerA(),
            $violation->getLayerB()
        );

        $error = $xmlDoc->createElement('failure');
        $error->appendChild(new \DOMAttr('message', $message));
        $error->appendChild(new \DOMAttr('type', 'WARNING'));

        $testCase->appendChild($error);
    }
}
