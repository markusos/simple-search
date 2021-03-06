<?php namespace Search;

class SnowballTokenizerTest extends \PHPUnit_Framework_TestCase
{

    function testSnowballTokenizer()
    {
        $tokenizer = new Tokenizer\SnowballTokenizer();
        $tokens = $tokenizer->tokenize('In computer engineering, computer architecture is the conceptual design and fundamental operational structure of a computing system.');

        $this->assertContains('comput', $tokens);
        $this->assertContains('engin', $tokens);
        $this->assertContains('in', $tokens);
        $this->assertContains('and', $tokens);
        $this->assertContains('system', $tokens);
        $this->assertContains('architectur', $tokens);

        $this->assertEquals('computer', $tokenizer->getWord('comput'));
    }

    function testSnowballTokenizerStopWords()
    {
        $tokenizer = new Tokenizer\SnowballTokenizer();
        $tokenizer->setStopWords(['computer']);
        $this->assertContains('computer', $tokenizer->tokenize('In computer engineering'));
    }

}