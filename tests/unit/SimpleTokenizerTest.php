<?php namespace Search;


class SimpleTokenizerTest extends \PHPUnit_Framework_TestCase {
    function testTokenizer() {
        $tokenizer = new SimpleTokenizer();
        $tokens = $tokenizer->tokenize("Test string to tokenize. Why!?, To make sure it works! ... ");

        $this->assertContains('test', $tokens);
        $this->assertContains('string', $tokens);
        $this->assertContains('to', $tokens);
        $this->assertContains('tokenize', $tokens);
        $this->assertContains('why', $tokens);
        $this->assertContains('works', $tokens);
    }
}
