<?php
declare(strict_types=1);
/**
 * @copyright 2017 Hostnet B.V.
 */

namespace Hostnet\Component\CssSniff\Sniff;

use Hostnet\Component\CssSniff\File;
use Hostnet\Component\CssSniff\SniffInterface;
use Yannickl88\Component\CSS\Token;

final class ColorSniff implements SniffInterface
{
    private const CSS_RULES = [
        'color',
        'background',
        'border',
        'border-bottom',
        'border-left',
        'border-right',
        'border-top',
        'box-shadow',
    ];

    public function register(): array
    {
        return [
            Token::T_WORD,
        ];
    }

    public function process(File $file, int $stack_ptr): void
    {
        $token = $file->get($stack_ptr);

        if (in_array($token->chars, self::CSS_RULES, true) || 1 === preg_match('/-color$/', $token->chars)) {
            $t = $file->findNext(Token::T_WORD, $stack_ptr + 1);

            if (null !== $t && $t->chars[0] === '#' && 1 !== preg_match('/^#[0-9a-f]{6}$/', $t->chars)) {
                $file->addViolation(
                    self::class,
                    'Colors should always be 6 characters hex values.',
                    $t->lines[0],
                    $t->offsets[0],
                    $t->offsets[0] + strlen($t->chars)
                );
            }
        }
    }
}
