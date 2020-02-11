<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */
namespace MailPoetVendor\Doctrine\ORM\Query\AST\Functions;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Doctrine\ORM\Query\Lexer;
/**
 * "LOCATE" "(" StringPrimary "," StringPrimary ["," SimpleArithmeticExpression]")"
 *
 * 
 * @link    www.doctrine-project.org
 * @since   2.0
 * @author  Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author  Jonathan Wage <jonwage@gmail.com>
 * @author  Roman Borschel <roman@code-factory.org>
 * @author  Benjamin Eberlei <kontakt@beberlei.de>
 */
class LocateFunction extends \MailPoetVendor\Doctrine\ORM\Query\AST\Functions\FunctionNode
{
    public $firstStringPrimary;
    public $secondStringPrimary;
    /**
     * @var \MailPoetVendor\Doctrine\ORM\Query\AST\SimpleArithmeticExpression|bool
     */
    public $simpleArithmeticExpression = \false;
    /**
     * @override
     */
    public function getSql(\MailPoetVendor\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return $sqlWalker->getConnection()->getDatabasePlatform()->getLocateExpression(
            $sqlWalker->walkStringPrimary($this->secondStringPrimary),
            // its the other way around in platform
            $sqlWalker->walkStringPrimary($this->firstStringPrimary),
            $this->simpleArithmeticExpression ? $sqlWalker->walkSimpleArithmeticExpression($this->simpleArithmeticExpression) : \false
        );
    }
    /**
     * @override
     */
    public function parse(\MailPoetVendor\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(\MailPoetVendor\Doctrine\ORM\Query\Lexer::T_IDENTIFIER);
        $parser->match(\MailPoetVendor\Doctrine\ORM\Query\Lexer::T_OPEN_PARENTHESIS);
        $this->firstStringPrimary = $parser->StringPrimary();
        $parser->match(\MailPoetVendor\Doctrine\ORM\Query\Lexer::T_COMMA);
        $this->secondStringPrimary = $parser->StringPrimary();
        $lexer = $parser->getLexer();
        if ($lexer->isNextToken(\MailPoetVendor\Doctrine\ORM\Query\Lexer::T_COMMA)) {
            $parser->match(\MailPoetVendor\Doctrine\ORM\Query\Lexer::T_COMMA);
            $this->simpleArithmeticExpression = $parser->SimpleArithmeticExpression();
        }
        $parser->match(\MailPoetVendor\Doctrine\ORM\Query\Lexer::T_CLOSE_PARENTHESIS);
    }
}
