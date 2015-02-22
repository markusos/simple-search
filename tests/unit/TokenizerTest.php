<?php namespace Search;


class TokenizerTest extends \PHPUnit_Framework_TestCase {
    function testSimpleTokenizer() {
        $tokenizer = new Tokenizer\SimpleTokenizer();
        $tokens = $tokenizer->tokenize("Test string to tokenize. Why!?, To make sure it works! ... ");

        $this->assertContains('test', $tokens);
        $this->assertContains('string', $tokens);
        $this->assertContains('to', $tokens);
        $this->assertContains('tokenize', $tokens);
        $this->assertContains('why', $tokens);
        $this->assertContains('works', $tokens);
    }

    function testSnowballTokenizer() {
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
}
