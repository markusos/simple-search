<?php namespace Search\Tokenizer;

trait TokenizeTrait {

    private function toTokens($string) {
        return array_filter(preg_split("/[\\s\\.,?!;:()\\]\\[\\{\\}\\-\\_]+/", strtolower($string)));
    }

    private function encode($tokens) {
        return array_map(function($token) {
            return utf8_encode($token);
        }, $tokens);
    }
}