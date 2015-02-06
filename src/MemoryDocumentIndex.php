<?php namespace Search;

class MemoryDocumentIndex implements DocumentIndex {

    private $index;
    private $tokenizer;
    private $size;

    function __construct(Tokenizer $tokenizer)
    {
        $this->index = [];
        $this->tokenizer = $tokenizer;
        $this->size = 0;
    }

    public function addDocument(Document $document) {
        $document->id = $this->size;
        $content = $document->content;
        $tokens = array_unique($this->tokenizer->tokenize($content));
        foreach($tokens as $token) {
            $this->addDocumentForToken($token, $document);
        }
        $this->size += 1;
    }

    private function addDocumentForToken($token, Document $document) {
        if (!isset($this->index[$token])) {
            $this->index[$token] = [$document->id => $document];
        }
        else {
            $this->index[$token][$document->id] = $document;
        }
    }

    public function search($query) {
        if (!isset($this->index[$query])) {
            return [];
        }
        else {
            return $this->index[$query];
        }
    }

    public function size() {
        return $this->size;
    }

    public function clear() {
        $this->size = 0;
        $this->index = [];
    }
}