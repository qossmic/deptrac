<?php

namespace SensioLabs\Deptrac\ConfigurationEngine;

use SensioLabs\Deptrac\ConfigurationEngine\Twig\YamlTwigExtension;
use Symfony\Component\Yaml\Yaml;
use Twig_Loader_Chain;

class TwigConfigurationEngine implements ConfigurationEngineInterface
{
    /** @var ConfigurationProviderInterface */
    private $configurationProvider;

    /**
     * @param ConfigurationProviderInterface $configurationProvider
     */
    public function __construct(ConfigurationProviderInterface $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    public function supports($pathname)
    {
        return true;
    }


    public function requiresParsingStep(array $yamlAsArray, $loopfor)
    {
        $requires = false;

        array_walk_recursive($yamlAsArray, function($item, $key) use (&$requires, $loopfor)  {
            $requires = $requires || substr($item, 0, strlen($loopfor)) == $loopfor;
        });

        return $requires;
    }

    public function render($pathname)
    {


        $twig = new \Twig_Environment(
            new Twig_Loader_Chain(array(
                new \Twig_Loader_Array([
                    'base' => $this->configurationProvider->provide($pathname)
                ]),
                new \Twig_Loader_Filesystem([
                    '@root' => dirname($pathname)
                ])
            ))
        );

        $twig->addExtension(new YamlTwigExtension());

        $renderedYml = $twig->render(
            'base',
            array_merge([
                '__FILE__' => $pathname,
                '__DIR__' => dirname($pathname)
            ]
        ));

        $yamlAsArray = Yaml::parse($renderedYml);

        while ($this->requiresParsingStep($yamlAsArray, '!!deptrac_')) {
            array_walk_recursive($yamlAsArray, function(&$item, &$key) {
                $loopfor = '!!deptrac_';
                if (substr($item, 0, strlen($loopfor)) == $loopfor) {
                    $str = substr($item, strlen($loopfor));

                    $item = Yaml::parse($str);
                }
            });
        }

        return $yamlAsArray;
    }
}
