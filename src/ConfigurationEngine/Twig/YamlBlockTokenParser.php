<?php

namespace SensioLabs\Deptrac\ConfigurationEngine\Twig;

use Twig_Token;
use Twig_TokenParser;

class YamlBlockTokenParser extends Twig_TokenParser
{

    public function parse(Twig_Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $name = $stream->expect(Twig_Token::NAME_TYPE)->getValue();

        $arguments = $this->parser->getExpressionParser()->parseArguments(true, true);

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        $this->parser->pushLocalScope();

        /** @var \Twig_Node_Text */
        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);

        $this->parser->popLocalScope();

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new YamlNode($name, $body, $arguments, $token->getLine(), $this->getTag());
    }

    public function decideBlockEnd(Twig_Token $token)
    {
        return $token->test('endyaml');
    }

    public function getTag()
    {
        return 'yaml';
    }

}
