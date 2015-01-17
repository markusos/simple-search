<?php namespace Search;

class SimpleTokenizer implements Tokenizer {
    public function tokenize($string) {
        return preg_split("/\\s+/", $string);
    }
}