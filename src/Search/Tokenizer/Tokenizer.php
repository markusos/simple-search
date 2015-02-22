<?php namespace Search\Tokenizer;

/**
 * Interface Tokenizer
 * @package Search
 */
interface Tokenizer {
    /**
     * Tokenize the given string
     * @param $string String to tokenize
     * @return array Array of tokens
     */
    public function tokenize($string);
}