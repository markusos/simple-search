<?php namespace Search;

class Document {

    public $id;
    public $title;
    public $content;
    public $tokens;

    function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }
}