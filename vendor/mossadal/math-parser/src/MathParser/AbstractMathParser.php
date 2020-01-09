<<<<<<< HEAD
<?php

namespace MathParser;

/*
* @package     Parsing
* @author      Frank Wikström <frank@mossadal.se>
* @author      Horse Luke <horseluke@126.com>
* @copyright   2015 Frank Wikström
* @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
*
*/

use MathParser\Lexing\Lexer;
use MathParser\Parsing\Parser;

abstract class AbstractMathParser
{
    
    /** StdMathLexer $lexer instance of Lexer used for tokenizing */
    protected $lexer;
    /** Parser $parser instance of Parsed used for the actual parsing */
    protected $parser;
    /** Token[] $tokens list of tokens generated by the Lexer */
    protected $tokens;
    /** Node $tree AST generated by the parser */
    protected $tree;
    
    public function setSimplifying($flag)
    {
        $this->parser->setSimplifying($flag);
    }
    
    /**
     * Return Letex
     * @retval Lexer
     */
    public function getLexer()
    {
        return $this->lexer;
    }
    
    /**
     * Return Parser
     * @retval Parser
     */
    public function getParser()
    {
        return $this->parser;
    }
    
    /**
     * Return the token list for the last parsed expression.
     * @retval Token[]
     */
    public function getTokenList()
    {
        return $this->tokens;
    }
    
    /**
     * Return the AST of the last parsed expression.
     * @retval Node
     */
    public function getTree()
    {
        return $this->tree;
    }
    
    /**
     * Replace Letex
     */
    public function replaceLexer(Lexer $lexer)
    {
        $this->lexer = $lexer;
    }
    
    /**
     * Replace Parser
     */
    public function replaceParser(Parser $parser)
    {
        $this->parser = $parser;
    }
    
    abstract public function parse($text);
    
=======
<?php

namespace MathParser;

/*
* @package     Parsing
* @author      Frank Wikström <frank@mossadal.se>
* @author      Horse Luke <horseluke@126.com>
* @copyright   2015 Frank Wikström
* @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
*
*/

use MathParser\Lexing\Lexer;
use MathParser\Parsing\Parser;

abstract class AbstractMathParser
{
    
    /** StdMathLexer $lexer instance of Lexer used for tokenizing */
    protected $lexer;
    /** Parser $parser instance of Parsed used for the actual parsing */
    protected $parser;
    /** Token[] $tokens list of tokens generated by the Lexer */
    protected $tokens;
    /** Node $tree AST generated by the parser */
    protected $tree;
    
    public function setSimplifying($flag)
    {
        $this->parser->setSimplifying($flag);
    }
    
    /**
     * Return Letex
     * @retval Lexer
     */
    public function getLexer()
    {
        return $this->lexer;
    }
    
    /**
     * Return Parser
     * @retval Parser
     */
    public function getParser()
    {
        return $this->parser;
    }
    
    /**
     * Return the token list for the last parsed expression.
     * @retval Token[]
     */
    public function getTokenList()
    {
        return $this->tokens;
    }
    
    /**
     * Return the AST of the last parsed expression.
     * @retval Node
     */
    public function getTree()
    {
        return $this->tree;
    }
    
    /**
     * Replace Letex
     */
    public function replaceLexer(Lexer $lexer)
    {
        $this->lexer = $lexer;
    }
    
    /**
     * Replace Parser
     */
    public function replaceParser(Parser $parser)
    {
        $this->parser = $parser;
    }
    
    abstract public function parse($text);
    
>>>>>>> feature/3239
}