<?php namespace Search\Index;

use Search\Document;

class MemoryDocumentIndex implements DocumentIndex {

    private $index;

    function __construct()
    {
        $this->index = [];
    }

    public function addDocument(Document $document) {
        $uniqueTokens = array_unique($document->tokens);
        foreach($uniqueTokens as $token) {
            $this->addDocumentForToken($token, $document);
        }
    }

    private function addDocumentForToken($token, Document $document) {
        if (!isset($this->index[$token])) {
            $this->index[$token] = [$document->id => $document->id];
        }
        else {
            $this->index[$token][$document->id] = $document->id;
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

    public function clear() {
        $this->index = [];
    }

    /**
     * Get the number of indexed terms
     * @return integer Number of indexed terms
     */
    public function size()
    {
        return count($this->index);
    }
}