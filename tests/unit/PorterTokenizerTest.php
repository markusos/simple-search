<?php namespace Search;

class PorterTokenizerTest extends \PHPUnit_Framework_TestCase
{
    function testSimpleTokenizer()
    {
        $stopwords = ['in', 'the', 'is', 'a'];
        $tokenizer = new Tokenizer\PorterTokenizer($stopwords);
        $tokens = $tokenizer->tokenize('In computer engineering, computer architecture is the conceptual design and fundamental operational structure of a computing system.');

        $this->assertContains('comput', $tokens);
        $this->assertContains('engin', $tokens);
        $this->assertContains('in', $tokens);
        $this->assertContains('and', $tokens);
        $this->assertContains('system', $tokens);
        $this->assertContains('architectur', $tokens);

        $this->assertEquals('computer', $tokenizer->getWord('comput'));
    }
}
