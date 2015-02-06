<?php namespace Search;

interface DocumentIndex {

    public function __construct(Tokenizer $tokenizer);

    public function addDocument(Document $document);

    public function search($query);

    public function size();

    public function clear();
}