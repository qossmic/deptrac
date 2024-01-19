<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\DependencyInjection\Compiler;

use DEPTRAC_202401\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
/**
 * Compiler Pass Configuration.
 *
 * This class has a default configuration embedded.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class PassConfig
{
    public const TYPE_AFTER_REMOVING = 'afterRemoving';
    public const TYPE_BEFORE_OPTIMIZATION = 'beforeOptimization';
    public const TYPE_BEFORE_REMOVING = 'beforeRemoving';
    public const TYPE_OPTIMIZE = 'optimization';
    public const TYPE_REMOVE = 'removing';
    private MergeExtensionConfigurationPass $mergePass;
    private array $afterRemovingPasses;
    private array $beforeOptimizationPasses;
    private array $beforeRemovingPasses = [];
    private array $optimizationPasses;
    private array $removingPasses;
    public function __construct()
    {
        $this->mergePass = new MergeExtensionConfigurationPass();
        $this->beforeOptimizationPasses = [100 => [new ResolveClassPass(), new RegisterAutoconfigureAttributesPass(), new AutowireAsDecoratorPass(), new AttributeAutoconfigurationPass(), new ResolveInstanceofConditionalsPass(), new RegisterEnvVarProcessorsPass()], -1000 => [new ExtensionCompilerPass()]];
        $this->optimizationPasses = [[new AutoAliasServicePass(), new ValidateEnvPlaceholdersPass(), new ResolveDecoratorStackPass(), new ResolveChildDefinitionsPass(), new RegisterServiceSubscribersPass(), new ResolveParameterPlaceHoldersPass(\false, \false), new ResolveFactoryClassPass(), new ResolveNamedArgumentsPass(), new AutowireRequiredMethodsPass(), new AutowireRequiredPropertiesPass(), new ResolveBindingsPass(), new ServiceLocatorTagPass(), new DecoratorServicePass(), new CheckDefinitionValidityPass(), new AutowirePass(\false), new ServiceLocatorTagPass(), new ResolveTaggedIteratorArgumentPass(), new ResolveServiceSubscribersPass(), new ResolveReferencesToAliasesPass(), new ResolveInvalidReferencesPass(), new AnalyzeServiceReferencesPass(\true), new CheckCircularReferencesPass(), new CheckReferenceValidityPass(), new CheckArgumentsValidityPass(\false)]];
        $this->removingPasses = [[new RemovePrivateAliasesPass(), new ReplaceAliasByActualDefinitionPass(), new RemoveAbstractDefinitionsPass(), new RemoveUnusedDefinitionsPass(), new AnalyzeServiceReferencesPass(), new CheckExceptionOnInvalidReferenceBehaviorPass(), new InlineServiceDefinitionsPass(new AnalyzeServiceReferencesPass()), new AnalyzeServiceReferencesPass(), new DefinitionErrorExceptionPass()]];
        $this->afterRemovingPasses = [
            0 => [new ResolveHotPathPass(), new ResolveNoPreloadPass(), new AliasDeprecatedPublicServicesPass()],
            // Let build parameters be available as late as possible
            -2048 => [new RemoveBuildParametersPass()],
        ];
    }
    /**
     * Returns all passes in order to be processed.
     *
     * @return CompilerPassInterface[]
     */
    public function getPasses() : array
    {
        return \array_merge([$this->mergePass], $this->getBeforeOptimizationPasses(), $this->getOptimizationPasses(), $this->getBeforeRemovingPasses(), $this->getRemovingPasses(), $this->getAfterRemovingPasses());
    }
    /**
     * Adds a pass.
     *
     * @return void
     *
     * @throws InvalidArgumentException when a pass type doesn't exist
     */
    public function addPass(CompilerPassInterface $pass, string $type = self::TYPE_BEFORE_OPTIMIZATION, int $priority = 0)
    {
        $property = $type . 'Passes';
        if (!isset($this->{$property})) {
            throw new InvalidArgumentException(\sprintf('Invalid type "%s".', $type));
        }
        $passes =& $this->{$property};
        if (!isset($passes[$priority])) {
            $passes[$priority] = [];
        }
        $passes[$priority][] = $pass;
    }
    /**
     * Gets all passes for the AfterRemoving pass.
     *
     * @return CompilerPassInterface[]
     */
    public function getAfterRemovingPasses() : array
    {
        return $this->sortPasses($this->afterRemovingPasses);
    }
    /**
     * Gets all passes for the BeforeOptimization pass.
     *
     * @return CompilerPassInterface[]
     */
    public function getBeforeOptimizationPasses() : array
    {
        return $this->sortPasses($this->beforeOptimizationPasses);
    }
    /**
     * Gets all passes for the BeforeRemoving pass.
     *
     * @return CompilerPassInterface[]
     */
    public function getBeforeRemovingPasses() : array
    {
        return $this->sortPasses($this->beforeRemovingPasses);
    }
    /**
     * Gets all passes for the Optimization pass.
     *
     * @return CompilerPassInterface[]
     */
    public function getOptimizationPasses() : array
    {
        return $this->sortPasses($this->optimizationPasses);
    }
    /**
     * Gets all passes for the Removing pass.
     *
     * @return CompilerPassInterface[]
     */
    public function getRemovingPasses() : array
    {
        return $this->sortPasses($this->removingPasses);
    }
    /**
     * Gets the Merge pass.
     */
    public function getMergePass() : CompilerPassInterface
    {
        return $this->mergePass;
    }
    /**
     * @return void
     */
    public function setMergePass(CompilerPassInterface $pass)
    {
        $this->mergePass = $pass;
    }
    /**
     * Sets the AfterRemoving passes.
     *
     * @param CompilerPassInterface[] $passes
     *
     * @return void
     */
    public function setAfterRemovingPasses(array $passes)
    {
        $this->afterRemovingPasses = [$passes];
    }
    /**
     * Sets the BeforeOptimization passes.
     *
     * @param CompilerPassInterface[] $passes
     *
     * @return void
     */
    public function setBeforeOptimizationPasses(array $passes)
    {
        $this->beforeOptimizationPasses = [$passes];
    }
    /**
     * Sets the BeforeRemoving passes.
     *
     * @param CompilerPassInterface[] $passes
     *
     * @return void
     */
    public function setBeforeRemovingPasses(array $passes)
    {
        $this->beforeRemovingPasses = [$passes];
    }
    /**
     * Sets the Optimization passes.
     *
     * @param CompilerPassInterface[] $passes
     *
     * @return void
     */
    public function setOptimizationPasses(array $passes)
    {
        $this->optimizationPasses = [$passes];
    }
    /**
     * Sets the Removing passes.
     *
     * @param CompilerPassInterface[] $passes
     *
     * @return void
     */
    public function setRemovingPasses(array $passes)
    {
        $this->removingPasses = [$passes];
    }
    /**
     * Sort passes by priority.
     *
     * @param array $passes CompilerPassInterface instances with their priority as key
     *
     * @return CompilerPassInterface[]
     */
    private function sortPasses(array $passes) : array
    {
        if (0 === \count($passes)) {
            return [];
        }
        \krsort($passes);
        // Flatten the array
        return \array_merge(...$passes);
    }
}
