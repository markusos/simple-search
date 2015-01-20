<?php namespace Search;

class SimpleTokenizer implements Tokenizer {
    public function tokenize($string) {
        return array_filter(preg_split("/[\\s\\.,?!]+/", strtolower($string)));
    }
}