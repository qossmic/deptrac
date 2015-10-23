<?php

namespace DependencyTracker;

use DependencyTracker\Configuration\ConfigurationView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Configuration
{

    private $views;

    private $paths;

    public static function fromArray(array $arr)
    {
        $options = (new OptionsResolver())->setRequired([
            'views',
            'paths'
        ])->resolve($arr);

        return new static(
            array_map(function($v) { return ConfigurationView::fromArray($v); }, $options['views']),
            $options['paths']
        );
    }

    /**
     * @param $views
     * @param $paths
     */
    public function __construct($views, $paths)
    {
        $this->views = $views;
        $this->paths = $paths;
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




}
