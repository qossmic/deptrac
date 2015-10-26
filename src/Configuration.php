<?php

namespace DependencyTracker;

use DependencyTracker\Configuration\ConfigurationView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Configuration
{

    private $views;

    private $paths;

    private $exclude_files;

    public static function fromArray(array $arr)
    {
        $options = (new OptionsResolver())->setRequired([
            'views',
            'paths',
            'exclude_files'
        ])->resolve($arr);

        return new static(
            array_map(function($v) { return ConfigurationView::fromArray($v); }, $options['views']),
            $options['paths'],
            $options['exclude_files']
        );
    }

    /**
     * @param $views
     * @param $paths
     */
    public function __construct($views, $paths, $exclude_files)
    {
        $this->views = $views;
        $this->paths = $paths;
        $this->exclude_files = $exclude_files;
    }

    /**
     * @return ConfigurationView[]
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @return mixed
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * @return mixed
     */
    public function getExcludeFiles()
    {
        return $this->exclude_files;
    }

}
