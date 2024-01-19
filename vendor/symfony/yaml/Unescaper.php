<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DEPTRAC_202401\Symfony\Component\Yaml;

use DEPTRAC_202401\Symfony\Component\Yaml\Exception\ParseException;
/**
 * Unescaper encapsulates unescaping rules for single and double-quoted
 * YAML strings.
 *
 * @author Matthew Lewinski <matthew@lewinski.org>
 *
 * @internal
 */
class Unescaper
{
    /**
     * Regex fragment that matches an escaped character in a double quoted string.
     */
    public const REGEX_ESCAPED_CHARACTER = '\\\\(x[0-9a-fA-F]{2}|u[0-9a-fA-F]{4}|U[0-9a-fA-F]{8}|.)';
    /**
     * Unescapes a single quoted string.
     *
     * @param string $value A single quoted string
     */
    public function unescapeSingleQuotedString(string $value) : string
    {
        return \str_replace('\'\'', '\'', $value);
    }
    /**
     * Unescapes a double quoted string.
     *
     * @param string $value A double quoted string
     */
    public function unescapeDoubleQuotedString(string $value) : string
    {
        $callback = fn($match) => $this->unescapeCharacter($match[0]);
        // evaluate the string
        return \preg_replace_callback('/' . self::REGEX_ESCAPED_CHARACTER . '/u', $callback, $value);
    }
    /**
     * Unescapes a character that was found in a double-quoted string.
     *
     * @param string $value An escaped character
     */
    private function unescapeCharacter(string $value) : string
    {
        return match ($value[1]) {
            '0' => "\x00",
            'a' => "\x07",
            'b' => "\x08",
            't' => "\t",
            "\t" => "\t",
            'n' => "\n",
            'v' => "\v",
            'f' => "\f",
            'r' => "\r",
            'e' => "\x1b",
            ' ' => ' ',
            '"' => '"',
            '/' => '/',
            '\\' => '\\',
            // U+0085 NEXT LINE
            'N' => "",
            // U+00A0 NO-BREAK SPACE
            '_' => " ",
            // U+2028 LINE SEPARATOR
            'L' => " ",
            // U+2029 PARAGRAPH SEPARATOR
            'P' => " ",
            'x' => self::utf8chr(\hexdec(\substr($value, 2, 2))),
            'u' => self::utf8chr(\hexdec(\substr($value, 2, 4))),
            'U' => self::utf8chr(\hexdec(\substr($value, 2, 8))),
            default => throw new ParseException(\sprintf('Found unknown escape character "%s".', $value)),
        };
    }
    /**
     * Get the UTF-8 character for the given code point.
     */
    private static function utf8chr(int $c) : string
    {
        if (0x80 > ($c %= 0x200000)) {
            return \chr($c);
        }
        if (0x800 > $c) {
            return \chr(0xc0 | $c >> 6) . \chr(0x80 | $c & 0x3f);
        }
        if (0x10000 > $c) {
            return \chr(0xe0 | $c >> 12) . \chr(0x80 | $c >> 6 & 0x3f) . \chr(0x80 | $c & 0x3f);
        }
        return \chr(0xf0 | $c >> 18) . \chr(0x80 | $c >> 12 & 0x3f) . \chr(0x80 | $c >> 6 & 0x3f) . \chr(0x80 | $c & 0x3f);
    }
}
