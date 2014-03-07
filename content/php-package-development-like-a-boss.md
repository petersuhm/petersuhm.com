{
    "title": "PHP Package Development Like a Boss",
    "date": "2014-03-07",
    "excerpt": "In this article, we will look into the nuts and bolts of building a PHP package for Packagist. We will go through all the steps one by one and we will be using a test-driven approach with PHPUnit. PHP has been accused for many things during the years, but the community has really stepped up its game recently. With dependency management tools like Composer, we are able to build packages that can be easily used by others. We will be building a package ourselves, and our package will use and depend on other packages. We will build a small utlility for making a flat file CMS. Let's call it \"Guru\"."
}

In this article, we will look into the nuts and bolts of building a PHP package for [Packagist](https://packagist.org/). We will go through all the steps one by one and we will be using a test-driven approach with PHPUnit. PHP has been accused for many things during the years, but the community has really stepped up its game recently. With dependency management tools like Composer, we are able to build packages that can be easily used by others. We will be building a package ourselves, and our package will use and depend on other packages. We will build a small utlility for making a flat file CMS. Let's call it "Guru".

I assume that you have basic knowledge about object-oriented PHP and test-driven development. Otherwise, I encourage you to go through [this guide](http://www.phptherightway.com/). I also assume that you have the following things installed and configured:

* A working PHP setup (min. 5.3)
* [PHPUnit](http://phpunit.de/)
* [Composer](http://getcomposer.org/)

Let's get started.

## Table of contents

* [What's a package?](#whats-a-package)
* [The Guru package](#the-guru-package)
* [Building the foundation](#building-the-foundation)
* [The posts](#the-posts)
* [Building the Guru](#building-the-guru)
    * [What we need](#what-we-need)
    * [Configuring Guru](#configuring-guru)
    * [Getting all posts](#getting-all-posts)
    * [Getting a post from a slug](#getting-a-post-from-a-slug)
* [Going public](#going-public)
    * [Tagging releases](#tagging-releases)
    * [Publishing on Packagist](#publishing-on-packagist)
* [Wrapping it up](#wrapping-it-up)

## What's a package? {#whats-a-package}

A package is a collection of PHP code that is packed together in a way so that it can be easily used for other projects. It's a great idea to split up a complex application into smaller, more manageable chunks. If you include a `composer.json` file in your package, you are able to put in on Packagist and have other people use it. Composer is a dependency management tool for PHP applications. If you don't know it, should read about it [here](https://getcomposer.org/doc/). Packagist is an online repository for Composer packages. You can read more about it [here](https://packagist.org/about).

Packages are great, since you can build whole web applications just by putting together other people's packages in your own way. You can also do your own open source contributions, by publishing parts of your own applications as packages, and have the community help you improve it. Win-win.

## The Guru package {#the-guru-package}

Guru is the PHP package we will be building for this tutorial. It's a flat file content management system, which basically means that it takes flat text files and turn them into web content. Files, in our case, are in the [markdown](http://daringfireball.net/projects/markdown/) format.

You could use Guru together with an awesome PHP framework, such as [Laravel](http://laravel.com/) or [Slim](http://slimframework.com/), in order to build a blog. (Actually, you shouldn't do this, since the code here is only meant as an example. It's not yet ready for production, but please do play along here!)

## Building the foundation {#building-the-foundation}

In this section, we will setup the basic file skeleton that we need for our project. Our package is made up by the files located in the `guru` directory. Files outside this directory (such as `index.php`) are only used for development, and will not be published together with the package.

Go ahead and create the following files and directories:

```
|-- packages/
|   `-- guru/
|       |-- src/
|       |   `-- Petersuhm/
|       |       `-- Guru/
|       |           `-- Guru.php
|       |-- tests/
|       |   `-- GuruTest.php
|       |-- .gitignore
|       |-- composer.json
|       `-- phpunit.xml
`-- public/
    `-- index.php

```

The first file we will work on is our `composer.json` file. This file holds important information about our package and its (upcoming) dependencies. It should look something like this:

```json
{
    "name": "petersuhm/guru",
    "description": "Flat file CMS package for PHP.",
    "authors": [
        {
            "name": "Peter Suhm",
            "email": "peter@suhm.dk"
        }
    ],
    "require": {
        "php": ">=5.3.0"
    },
    "autoload": {
        "psr-0": {
            "Petersuhm\\Guru": "src/"
        }
    },
    "minimum-stability": "dev"
}
```

Now run `composer install`, and Composer will create a vendor directory and the necessary files for autoloading.

If you use Git for version control, you should make a `.gitignore` file and fill in the following:

```
/vendor
composer.phar
composer.lock
```

Next file is `Guru.php`, which contains our `Guru` class. This class will be the backbone of our package and be responsible for turning our markdown files into posts. For now, the file should look like this:

```php
# packages/guru/src/Petersuhm/Guru/Guru.php
<?php namespace Petersuhm\Guru;

class Guru {}
```

Since we will be usen a test-driven approach, obviously we need a test for every class we make. So go ahead, and fill in the first test for our `Guru` class:

```php
# packages/guru/tests/GuruTest.php
<?php

use Petersuhm\Guru\Guru;

class GuruTest extends PHPUnit_Framework_TestCase {

    public function testIsInitializable()
    {
        $guru = new Guru();

        $this->assertInstanceOf('\Petersuhm\Guru\Guru', $guru);
    }
}
```

If you try and run `phpunit tests/` from the `guru` directory, you will see that I doesn't work. This is because we need to tell PHPUnit how to autoload our classes. We can do this in a `phpunit.xml` file. At the same time, we might as well add some extra configuration to enable colors and to tell PHPUnit where our testsuite is, so we only need to run `phpunit` in order to run it. Here is the content of our `phpunit.xml` file:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit colors="true"
         bootstrap="./vendor/autoload.php"
>
    <testsuites>
        <testsuite name="Guru Test Suite">
            <directory suffix=".php">./tests/</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

If you run `phpunit` from the guru directory, you should see a green/passing test. This means that our foundation is working. So far, so good.

## The posts {#the-posts}

Our posts will be in the markdown format, including some meta data. We want them to look similar to this:

```
title: Hello World!
date: 2014-12-24
-------
# Hello World!

This is my **first** post.
```

_Hint: This is because we will be using Dayle Rees' [Kurenai](https://github.com/daylerees/kurenai) package later on._

Okay, so we need to represent this in a class. We will call it `Post`. Let's do it the TDD way and create a test:

```php
# packages/guru/tests/PostTest.php
<?php

use Petersuhm\Guru\Post;

class PostTest extends PHPUnit_Framework_TestCase {

    public function testIsInitializable()
    {
        $post = new Post();

        $this->assertInstanceOf('\Petersuhm\Guru\Post', $post);
    }
}
```

This will, of course, fail, since we don't have a `Post` class. I will not go through _all_ my TDD iterations here, so just go ahead and make the file, and see that you get a green test:

```php
# packages/guru/src/Petersuhm/Guru/Post.php
<?php namespace Petersuhm\Guru;

class Post {}
```

Now we need to add some fields to our posts. In addition to the `title`, `date` and `body`, we also need a `slug`, which will be the unique identifier of a post. Again, we start with the test:

```php
# packages/guru/tests/PostTest.php
...
public function testInstantiatesPost()
{
    $title = 'First post';
    $date = '2014-12-24';
    $slug = 'first-post';
    $body = '<h1>First post</h1><p>This is my first post.</p>';

    $post = new Post($title, $date, $slug, $body);

    $this->assertEquals($post->title, $title);
    $this->assertEquals($post->date, $date);
    $this->assertEquals($post->slug, $slug);
    $this->assertEquals($post->body, $body);
}
```

And to get a nice green passing test, add the fields to the `Post` class:

```php
# packages/guru/src/Petersuhm/Guru/Post.php
<?php namespace Petersuhm\Guru;

class Post {

    public $title;
    public $date;
    public $slug;
    public $body;

    public function __construct($title = '', $date = '', $slug = '', $body = '')
    {
        $this->title = $title;
        $this->date = $date;
        $this->slug = $slug;
        $this->body = $body;
    }
}
```

Now we have a neat little class to represent our posts. It will come in handy later.

## Building the Guru {#building-the-guru}

In this section, we will build our Guru class. We will implement three methods in it: `config()`, `posts()` and `post()`. `posts()` will return all markdown files as `Post` objects and `post()` will return a single `Post` object, when given a post slug.

### What we need {#what-we-need}

First of all, we need to add some dependencies to our `composer.json` file. We will need [Mockery](https://github.com/padraic/mockery) in our tests, and we will need [Kurenai](https://github.com/daylerees/kurenai) to parse our markdown files. Add the dependencies now, so our file look like this:

```json
{
    "name": "petersuhm/guru",
    "description": "Flat file CMS package for PHP.",
    "authors": [
        {
            "name": "Peter Suhm",
            "email": "peter@suhm.dk"
        }
    ],
    "require": {
        "php": ">=5.3.0",
        "daylerees/kurenai": "dev-master"
    },
    "require-dev": {
        "mockery/mockery": "dev-master"
    },
    "autoload": {
        "psr-0": {
            "Petersuhm\\Guru": "src/"
        }
    },
    "minimum-stability": "dev"
}
```

Now, we need to run `composer update`, in order to fetch the new dependencies.

We will also need a couple of fixtures for our tests. We will make two test files: `tests/fixtures/first-post.md` and `tests/fixtures/second-post.md`. They should look like this:

```
title: First post
date: 2014-12-24
-------
# First post
```
```
title: Second post
date: 2014-12-25
-------
# Second post
```

This will be useful when we test that our Guru works correctly with the file system.

### Configuring Guru {#configuring-guru}

When using the Guru package, we want to able to do something like this:

```php
$guru = new \Petersuhm\Guru\Guru();
$guru->config(array(
    'content_dir' => '../content',
    'content_ext' => '.md'
));

$posts = $guru->posts();
// or
$post = $guru->post('some-post');
```

First things first, we need a `config()` method that can take and array as input. Let´s write the test:

```php
# packages/guru/tests/GuruTest.php
...
public function testConfig()
{
    $settings = array('key' => 'value');
    $guru = new Guru();

    $guru->config($settings);

    $this->assertEquals($guru->settings, $settings);
}
```

Next step is to get a green test, so let´s implement the necessary code:

```php
# packages/guru/src/Petersuhm/Guru/Guru.php
...
public $settings = array();

public function config($settings)
{
    $this->settings = array_merge($this->settings, $settings);
}
```

And it's green.

### Getting all posts {#getting-all-posts}

Okay, things are about to get serious. Can you feel it?

Let's start by looking at the constructor. In order to ensure that our class is testable, we will inject our dependencies through the constructor. We need two things: a `\Kurenai\DocumentParser` instance and a post resolver. The post resolver will be used to resolve every `Post` object we might need. We will implement this using a [closure](http://php.net/closures). This is how the constuctor should look:

```php
# packages/guru/src/Petersuhm/Guru/Guru.php
...
use Kurenai\DocumentParser;

public function __construct(DocumentParser $parser = null, $postResolver = null)
{
    if ($parser === null)
        $this->parser = new DocumentParser;
    else
        $this->parser = $parser;

    if ($postResolver === null)
        $this->postResolver = function() { return new Post; };
    else
        $this->postResolver = $postResolver;
}
```

This way, we are able to inject mocks and stubs into our class, should it be necessary.

Speaking of which. Let's look at some testing. Since we will be using Mockery, we need to change our `GuruTest` a tiny bit. We also need to setup the dependencies that we will inject into the `Guru` class. For the `DocumentParser`, we will use a mock, so that we can set expectations, and for the post resolver, we will instantiate an object from a simple `PostStub` class that we will declare. We need to add the following code to our test:

```php
# packages/guru/tests/GuruTest.php
...
use Petersuhm\Guru\Guru;
use Petersuhm\Guru\Post;
use Mockery as m;

class GuruTest extends PHPUnit_Framework_TestCase {

    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        $this->parser = m::mock('\Kurenai\DocumentParser');
        $this->postResolver = function () { return new PostStub(); };
    }

    public function testIsInitializable()
...
class PostStub {}
```

Now, we are ready to write the test for our `posts()` method. Here is what we will be doing: First, we will fetch the two post fixtures that we already made. We also instantiates two `PostStub` objects, which we will compare to the ones returned by `posts()` (remember that the post resolver for our tests will make a new `PostStub` instance). Next, we loop over the files, which serves two purposes. First we setup our two `PostStub` instances, and second we make sure that our `DocumentParser`'s `parse()` method is called with the content of the two files and returns a mocked instance of the `Document` class. We also set some expectations for the mocked `Document`s that matches the values of the `PostStub` instances. Finally, we instantiate the `Guru` class and test that the `posts()` method is in fact returning the two posts and that they matches the stubs. The test should look like this:

```php
# packages/guru/tests/GuruTest.php
...
public function testPosts()
{
    $directory = __DIR__ . '/fixtures';
    $files = array(
        $directory . '/first-post.md',
        $directory . '/second-post.md'
    );
    $posts = array(new PostStub, new PostStub);

    for ($i = 0; $i < 2; $i++)
    {
        $posts[$i]->title = 'A title';
        $posts[$i]->date = '2014-12-24';
        $posts[$i]->slug = basename($files[$i], '.md');
        $posts[$i]->body = '<h1>Some content</h1>';

        $document = m::mock('\Kurenai\Document');
        $document->shouldReceive('get')->with('title')->andReturn('A title');
        $document->shouldReceive('get')->with('date')->andReturn('2014-12-24');
        $document->shouldReceive('getHtmlContent')->andReturn('<h1>Some content</h1>');

        $source = file_get_contents($files[$i]);
        $this->parser->shouldReceive('parse')->with($source)->andReturn($document);
    }

    $guru = new Guru($this->parser, $this->postResolver);
    $guru->config(array(
        'content_dir' => $directory,
        'content_ext' => '.md'
    ));

    $this->assertEquals($guru->posts(), array($posts[0], $posts[1]));
}
```

In order to get a passing test, we need to actually implement the `posts()` method. We will use the built-in [glob](http://php.net/glob) function, to scan the content directory for markdown files. For each file, we will run the content through the parser and turn it into a `Post` object. Finally, we will return all the posts in an array:

```php
# packages/guru/src/Petersuhm/Guru/Guru.php
...
public function posts()
{
    $posts = array();

    $pattern = $this->settings['content_dir'] . '/*' . $this->settings['content_ext'];
    $files = glob($pattern);

    foreach ($files as $file)
    {
        $slug = basename($file, '.md');
        $source = file_get_contents($file);
        $document = $this->parser->parse($source);

        $post = call_user_func($this->postResolver);
        $post->title = $document->get('title');
        $post->date = $document->get('date');
        $post->slug = $slug;
        $post->body = $document->getHtmlContent();

        array_push($posts, $post);
    }

    return $posts;
}
```

Our Guru is taking shape.

### Getting a post from a slug {#getting-a-post-from-a-slug}

We also need to be able to get a single post from the Guru. We will do this by calling a `post()` method with a post slug. We start with the test, which is basically the same as the one for `posts()`, just without the loop:

```php
# packages/guru/tests/GuruTest.php
...
public function testPost()
{
    $directory = __DIR__ . '/fixtures';
    $file = $directory . '/second-post.md';

    $post = new PostStub();
    $post->title = 'A title';
    $post->date = '2014-12-24';
    $post->slug = 'second-post';
    $post->body = '<h1>Some content</h1>';

    $document = m::mock('\Kurenai\Document');
    $document->shouldReceive('get')->with('title')->andReturn('A title');
    $document->shouldReceive('get')->with('date')->andReturn('2014-12-24');
    $document->shouldReceive('getHtmlContent')->andReturn('<h1>Some content</h1>');

    $source = file_get_contents($file);
    $this->parser->shouldReceive('parse')->with($source)->andReturn($document);

    $guru = new Guru($this->parser, $this->postResolver);
    $guru->config(array(
        'content_dir' => $directory,
        'content_ext' => '.md'
    ));

    $this->assertEquals($guru->post('second-post'), $post);
}
```

The implementation looks similar to `posts()`, but this time we need to fetch the filename from the slug:

```php
# packages/guru/src/Petersuhm/Guru/Guru.php
...
public function post($slug)
{
    $file = $this->settings['content_dir'] . '/' . $slug . $this->settings['content_ext'];
    $source = file_get_contents($file);
    $document = $this->parser->parse($source);

    $post = call_user_func($this->postResolver);
    $post->title = $document->get('title');
    $post->date = $document->get('date');
    $post->slug = $slug;
    $post->body = $document->getHtmlContent();

    return $post;
}
```

That's it. We now have a working Guru. At the moment though, it doesn't care much about security or error handling, but (for now) that is out of the scope of this tutorial.

So far, we didn't test our Guru outside of the testing environment. If we want to see that it actually works, we can do something like this in the `index.php` file:

```php
# public/index.php
<?php

require "../packages/guru/vendor/autoload.php";

$guru = new \Petersuhm\Guru\Guru();
$guru->config(array(
    'content_dir' => '../content',
    'content_ext' => '.md'
));

$posts = $guru->posts();

foreach ($posts as $post)
{
    var_dump($guru->post($post->slug));
}
```

Normally, we don't need to require the autoload file for a package, but since we aren´t using Composer in the root project, we need to require it manually. If we fetched the package from Packagist, Composer would take care of this. In order to see anything, you need to make a `content` directory and put some markdown files in it. You can reuse the ones you made for testing in `packages/guru/tests/fixtures`.

## Going public {#going-public}

At this point, we are ready to release the first version of our Guru. We will put in on [Packagist](https://packagist.org), so others can use it.

### Tagging releases {#tagging-releases}

Before we go public, we should make a formal release, so people know which version of Guru they are using. We will do this with [Git tagging](http://git-scm.com/book/en/Git-Basics-Tagging), which works perfectly with both Github and Packagist. So in this section, I assume you use Git. Otherwise, you have to figure out how to do this somewhere else. Let's tag our first release. We will call this version `v0.0.1-alpha`, since it is not really stable or ready for production (yet). With Git, this is easy:

```bash
$ git tag "v0.0.1-alpha" -m "Guru is out!!!"
$ git push --tags
```

You can do this on Github as well, if you prefer to use their interface. Easy, right?

### Publishing on Packagist {#publishing-on-packagist}

Publishing a package on Packagist is super easy. Basically, all you have to do is to have the `composer.json` file present in your package's root directory (which we already have) and to put your package in a version control repository (like [Github](https://github.com/) or [BitBucket](https://bitbucket.org/)). When this is done, you can submit it on Packagist. I will not go trough the details here, but your can read more on their [website](https://packagist.org/about).

## Wrapping it up {#wrapping-it-up}

It's a great feeling. Our package is finally on Packagist - ready for others to incorporate in their projects, but this is not the end. From now on, we have to keep making Guru better. Maybe we will get pull requests on Github from people who wants to help us improve the code. In our case, we should probably work on security and error handling, before we release next time. We should probably also include a file describing the license of our package. How about [MIT](http://opensource.org/licenses/MIT).

Great open source projects, released trough Composer and Packagist, are leading a new era of modern PHP development. If you want to share parts of your code, it has never been easier. Thinking about putting your code in packages, even if you don't open source them and publish them, is a great way to design better applications. By structuring your code into more manageable chunks, your applications will become less complex and easier to maintain. It will also be easier for you to reuse your code in other projects. So go ahead. Make an awesome package and publish it for the World to use it!

&copy; 2014 Copyright Peter Suhm