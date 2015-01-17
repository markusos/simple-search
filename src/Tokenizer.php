<?php namespace Search;

interface Tokenizer {
    public function tokenize($string);
}