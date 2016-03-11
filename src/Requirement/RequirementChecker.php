<?php

namespace SensioLabs\Deptrac\Requirement;

class RequirementChecker
{
    const REQUIRED_PHP_VERSION = '5.5.9';

    /** @var Requirement[] */
    private $requirements = [];

    /**
     * Constructor that initializes the requirements.
     */
    public function __construct()
    {
        /* mandatory requirements follow */

        $installedPhpVersion = phpversion();

        $this->addRequirement(
            version_compare($installedPhpVersion, self::REQUIRED_PHP_VERSION, '>='),
            sprintf('PHP version must be at least %s (%s installed)', self::REQUIRED_PHP_VERSION, $installedPhpVersion),
            sprintf('You are running PHP version "<strong>%s</strong>", but Deptrac needs at least PHP "<strong>%s</strong>" to run.
                Before using Deptrac, upgrade your PHP installation, preferably to the latest version.',
                $installedPhpVersion, self::REQUIRED_PHP_VERSION),
            sprintf('Install PHP %s or newer (installed version is %s)', self::REQUIRED_PHP_VERSION, $installedPhpVersion)
        );

        /* optional recommendations follow */

    }

    /**
     * Adds a Requirement.
     *
     * @param Requirement $requirement A Requirement instance
     */
    public function add(Requirement $requirement)
    {
        $this->requirements[] = $requirement;
    }

    /**
     * Adds a mandatory requirement.
     *
     * @param bool        $fulfilled   Whether the requirement is fulfilled
     * @param string      $testMessage The message for testing the requirement
     * @param string      $helpHtml    The help text formatted in HTML for resolving the problem
     * @param string|null $helpText    The help text (when null, it will be inferred from $helpHtml, i.e. stripped from HTML tags)
     */
    public function addRequirement($fulfilled, $testMessage, $helpHtml, $helpText = null)
    {
        $this->add(new Requirement($fulfilled, $testMessage, $helpHtml, $helpText, false));
    }

    /**
     * Adds an optional recommendation.
     *
     * @param bool        $fulfilled   Whether the recommendation is fulfilled
     * @param string      $testMessage The message for testing the recommendation
     * @param string      $helpHtml    The help text formatted in HTML for resolving the problem
     * @param string|null $helpText    The help text (when null, it will be inferred from $helpHtml, i.e. stripped from HTML tags)
     */
    public function addRecommendation($fulfilled, $testMessage, $helpHtml, $helpText = null)
    {
        $this->add(new Requirement($fulfilled, $testMessage, $helpHtml, $helpText, true));
    }

    /**
     * Returns all mandatory requirements.
     *
     * @return array Array of Requirement instances
     */
    public function getRequirements()
    {
        return array_values(array_map(function (Requirement $req) {
            return $req;
        }, array_filter($this->requirements, function (Requirement $req) {
            return !$req->isOptional();
        })));
    }

    /**
     * Returns the mandatory requirements that were not met.
     *
     * @return array Array of Requirement instances
     */
    public function getFailedRequirements()
    {
        return array_values(array_map(function (Requirement $req) {
            return $req;
        }, array_filter($this->requirements, function (Requirement $req) {
            return !$req->isFulfilled() && !$req->isOptional();
        })));
    }

    /**
     * Returns all optional recommendations.
     *
     * @return array Array of Requirement instances
     */
    public function getRecommendations()
    {
        return array_values(array_map(function (Requirement $req) {
            return $req;
        }, array_filter($this->requirements, function (Requirement $req) {
            return $req->isOptional();
        })));
    }

    /**
     * Returns the recommendations that were not met.
     *
     * @return array Array of Requirement instances
     */
    public function getFailedRecommendations()
    {
        return array_values(array_map(function (Requirement $req) {
            return $req;
        }, array_filter($this->requirements, function (Requirement $req) {
            return !$req->isFulfilled() && $req->isOptional();
        })));
    }
}
