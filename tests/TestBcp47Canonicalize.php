<?php
namespace Bcp47;

use \PHPUnit_Framework_TestCase;

class TestBcp47Canonicalize extends PHPUnit_Framework_TestCase {
    function setUp() {
        parent::setUp();
        $this->parser = new Bcp47();
    }

    function testCanonicalize() {
        $testPairs = array(
            'zh-classical' => 'lzh',
            'zh-hant' => 'zh-Hant',
            'en-fr' => 'en-FR',
            'en-X-rational' => 'en-x-rational',
        );

        foreach ($testPairs as $raw => $expected) {
            $this->assertSame($expected, $this->parser->canonicalize($raw));
        }
    }
}
