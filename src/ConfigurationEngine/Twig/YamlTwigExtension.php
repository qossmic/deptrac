<?php 

namespace SensioLabs\Deptrac\ConfigurationEngine\Twig;

use Twig_Environment;

class YamlTwigExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'deptrac_yaml';
    }

    public function getTokenParsers()
    {
        return [
            new YamlBlockTokenParser()
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('yaml', function(Twig_Environment $env, $context, $blockName) {

                if (!isset($context['yaml_'.$blockName])) {
                    throw new \LogicException(sprintf('there is no yaml block called "%s"', $blockName));
                }

                return json_encode('!!deptrac_'.($context['yaml_'.$blockName]()));
            }, array('needs_context' => true, 'needs_environment' => true, 'is_safe' => array('html'))),
             new \Twig_SimpleFunction('collectors', function($layerName) {
                return json_encode('!!collectors_'.$layerName);
            }, array('is_safe' => array('html')))
        ];
    }


}
