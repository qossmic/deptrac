<?php 

namespace SensioLabs\Deptrac\ConfigurationEngine\Twig;

use Twig_Compiler;
use Twig_Node;
use Twig_NodeInterface;

class YamlNode extends Twig_Node
{
    const VARARGS_NAME = 'varargs';

    public function __construct($name, Twig_NodeInterface $body, Twig_NodeInterface $arguments, $line, $tag = null)
    {
        parent::__construct(array('body' => $body, 'arguments' => $arguments), array('name' => 'yaml_'.$name), $line, $tag);
    }

    public function compile(Twig_Compiler $compiler)
    {


        $compiler->write('$context[\''.$this->getAttribute('name').'\'] = function(');

        $count = count($this->getNode('arguments'));
        $pos = 0;
        foreach ($this->getNode('arguments') as $name => $default) {
            $compiler
                ->raw('$__'.$name.'__ = ')
                ->subcompile($default)
            ;

            if (++$pos < $count) {
                $compiler->raw(', ');
            }
        }

        if (PHP_VERSION_ID >= 50600) {
            if ($count) {
                $compiler->raw(', ');
            }

            $compiler->raw('...$__varargs__');
        }

        $compiler->write(') use ($context) {'."\n")
        ->write("ob_start();\n")
        ;




        $compiler
            ->write("\$context = array_merge(\$context, \$this->env->mergeGlobals(array(\n")
            ->indent()
        ;

        foreach ($this->getNode('arguments') as $name => $default) {
            $compiler
                ->addIndentation()
                ->string($name)
                ->raw(' => $__'.$name.'__')
                ->raw(",\n")
            ;
        }

        $compiler
            ->addIndentation()
            ->string(self::VARARGS_NAME)
            ->raw(' => ')
        ;

        if (PHP_VERSION_ID >= 50600) {
            $compiler->raw("\$__varargs__,\n");
        } else {
            $compiler
                ->raw('func_num_args() > ')
                ->repr($count)
                ->raw(' ? array_slice(func_get_args(), ')
                ->repr($count)
                ->raw(") : array(),\n")
            ;
        }
        $compiler->write(")));\n\n");


        $compiler->write("try {\n")
            ->indent()
                ->subcompile($this->getNode('body'))
            ->outdent()
                ->write("} catch (Exception \$e) {\n")
            ->indent()
                ->write("ob_end_clean();\n\n")
                ->write("throw \$e;\n")
            ->outdent()
            ->write("}\n")

            ->write("return ob_get_clean();\n")
            ->write("};\n\n")

        ;
    }
}
