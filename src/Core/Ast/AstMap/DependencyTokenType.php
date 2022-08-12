<?php

declare(strict_types=1);

namespace Qossmic\Deptrac\Core\Ast\AstMap;

enum DependencyTokenType: string
{
    case USE = 'use';
    case RETURN_TYPE = 'returntype';
    case PARAMETER = 'parameter';
    case NEW = 'new';
    case STATIC_PROPERTY = 'static_property';
    case STATIC_METHOD = 'static_method';
    case INSTANCEOF = 'instanceof';
    case CATCH = 'catch';
    case VARIABLE = 'variable';
    case THROW = 'throw';
    case CONST = 'const';
    case ANONYMOUS_CLASS_EXTENDS = 'anonymous_class_extends';
    case ANONYMOUS_CLASS_IMPLEMENTS = 'anonymous_class_implements';
    case ANONYMOUS_CLASS_TRAIT = 'anonymous_class_trait';
    case ATTRIBUTE = 'attribute';
    case SUPERGLOBAL_VARIABLE = 'superglobal_variable';
    case UNRESOLVED_FUNCTION_CALL = 'unresolved_function_call';
}
