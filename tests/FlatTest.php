<?php

use Petersuhm\Flat\Flat;

class FlatTest extends PHPUnit_Framework_TestCase {

    public function testIsInitializable()
    {
        $flat = new Flat();

        $this->assertInstanceOf('\Petersuhm\Flat\Flat', $flat);
    }
}