<?php namespace Search;

use Guzzle\Common\Exception\RuntimeException;

class Document {

    private $id;
    private $title;
    private $content;
    private $path;

    function __construct($title, $content, $path)
    {
        $this->title = $title;
        $this->content = $content;
        $this->path = $path;
    }

    public function getId()
    {
        if (isset($this->id)) {
            return $this->id;
        }
        else {
            throw new RuntimeException('trying to access id before indexed');
        }
    }

    public function setId($id)
    {
        $this->id = $id;
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