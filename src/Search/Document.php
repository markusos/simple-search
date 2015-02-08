<?php namespace Search;

class Document {

    public $id;
    public $title;
    public $content;
    public $location;

    function __construct($title, $content, $location)
    {
        $this->title = $title;
        $this->content = $content;
        $this->location = $location;
    }
}