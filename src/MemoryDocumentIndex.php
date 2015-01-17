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
        $content = $document->getContent();
        $tokens = array_unique($this->tokenizer->tokenize($content));
        foreach($tokens as $token) {
            $this->addDocumentForToken($token, $document);
        }
        $this->size += 1;
    }

    public function addDocumentForToken($token, Document $document) {
        if (!isset($this->index[$token])) {
            $this->index[$token] = [$document];
        }
        else {
            $this->index[$token][] = $document;
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
}