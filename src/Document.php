<?php namespace Search;

class Document {

    private $title;
    private $content;
    private $path;

    function __construct($title, $content, $path)
    {
        $this->title = $title;
        $this->content = $content;
        $this->path = $path;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getPath()
    {
        return $this->path;
    }
}