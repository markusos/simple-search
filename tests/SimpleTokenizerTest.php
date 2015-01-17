<?php
/**
 * Created by PhpStorm.
 * User: markus
 * Date: 17/01/15
 * Time: 14:08
 */

namespace Search;


class SimpleTokenizerTest extends \PHPUnit_Framework_TestCase {
    function testTokenizer() {
        $tokenizer = new SimpleTokenizer();
        $tokens = $tokenizer->tokenize("test string to tokenize");

        $this->assertContains('test', $tokens);
        $this->assertContains('string', $tokens);
        $this->assertContains('to', $tokens);
        $this->assertContains('tokenize', $tokens);
    }
}
