<?php namespace Search;

interface DocumentIndex {

    public function addDocument(Document $document);

    public function search($query);

    public function size();
}