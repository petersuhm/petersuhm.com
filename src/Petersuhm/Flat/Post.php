<?php namespace Petersuhm\Flat;

class Post {

    public $title;
    public $date;
    public $slug;
    public $excerpt;
    public $body;

    public function __construct($title = '', $date = '', $slug = '', $excerpt = '', $body = '')
    {
        $this->title = $title;
        $this->date = $date;
        $this->slug = $slug;
        $this->excerpt = $excerpt;
        $this->body = $body;
    }
}