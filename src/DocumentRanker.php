<?php
namespace Search;


interface DocumentRanker {

    function __construct(DocumentIndex $index, Tokenizer $tokenizer);

    public function rank(Document $document, $query);

    public function findKeywords($content);

}