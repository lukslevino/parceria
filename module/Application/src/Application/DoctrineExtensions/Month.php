<?php

namespace Application\DoctrineExtensions;

use Doctrine\ORM\Query\Lexer,
    Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * @author Andréia Bohner <andreiabohner@gmail.com>
 */
class Month extends FunctionNode
{
    private $date;

    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf(
            'EXTRACT(MONTH FROM %s)',
            $sqlWalker->walkArithmeticPrimary($this->date));
    }

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->date = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
