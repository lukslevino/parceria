<?php

namespace Application\DoctrineExtensions;

use Doctrine\ORM\Query\Lexer,
    Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * @author Cédric Bertolini <bertolini.cedric@me.com>
 */
class ToChar extends FunctionNode
{
    private $datetime;
    private $fmt;

    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf(
            'TO_CHAR(%s, %s)',
            $sqlWalker->walkArithmeticPrimary($this->datetime),
            $sqlWalker->walkArithmeticPrimary($this->fmt));
    }

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->datetime = $parser->ArithmeticExpression();
        $parser->match(Lexer::T_COMMA);
        $this->fmt = $parser->StringExpression();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
