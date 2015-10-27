<?php

namespace DependencyTracker;

use DependencyTracker\Configuration\ConfigurationView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Configuration
{

    private $views;

    private $paths;

    private $exclude_files;

    private $formatter;

    public static function fromArray(array $arr)
    {
        $options = (new OptionsResolver())->setRequired([
            'views',
            'paths',
            'exclude_files'
        ])->setDefaults([
            'formatter' => 'graphviz'
        ])
        ->resolve($arr);

        return new static(
            array_map(function($v) { return ConfigurationView::fromArray($v); }, $options['views']),
            $options['paths'],
            $options['exclude_files'],
            $options['formatter']
        );
    }

    /**
     * @param $views
     * @param $paths
     */
    public function __construct($views, $paths, $exclude_files, $formatter)
    {
        $this->views = $views;
        $this->paths = $paths;
        $this->exclude_files = $exclude_files;
        $this->formatter = $formatter;
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

    /**
     * @return mixed
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

}
