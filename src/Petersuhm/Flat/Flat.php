<?php namespace Petersuhm\Flat;

use \Michelf\MarkdownExtra;

class Flat {

    public function posts()
    {
        $posts = array();
        $files = glob('../content/*.md');

        foreach ($files as $file)
        {
            $source = file_get_contents($file);
            $content = explode("\n\n", $source, 2);
            $meta = json_decode($content[0], true);

            $parser = new MarkdownExtra;

            $post = new Post;
            $post->title = $meta['title'];
            $post->date = $meta['date'];
            $post->slug = basename($file, '.md');
            $post->excerpt = $meta['excerpt'];
            $post->body = $parser->transform($content[1]);

            array_push($posts, $post);
        }

        return $posts;
    }

    public function post($slug)
    {
        $file = '../content/' . $slug . '.md';

            $source = file_get_contents($file);
            $content = explode("\n\n", $source, 2);
            $meta = json_decode($content[0], true);

            $parser = new MarkdownExtra;

            $post = new Post();
            $post->title = $meta['title'];
            $post->date = $meta['date'];
            $post->slug = basename($file, '.md');
            $post->excerpt = $meta['excerpt'];
            $post->body = $parser->transform($content[1]);

        return $post;
    }
}