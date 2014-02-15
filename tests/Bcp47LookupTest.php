<?php
namespace Bcp47;

use \PHPUnit_Framework_TestCase;

class TestBcp47Lookup extends PHPUnit_Framework_TestCase {
    function setUp() {
        parent::setUp();
        $this->looker = new Bcp47();
    }

    function testLookup() {
        $testTuples = array(
            /* (raw preferred lang priority list, array available langs, string result) */
            array('zh-classical', array('zh', 'lzh'), 'lzh'),
            array('fr, en', array('fr-US', 'en'), 'en'),
            array('fr, gr', array('en'), ''),
        );

        foreach ($testTuples as $row) {
            $this->assertSame($row[2], $this->looker->lookupBestLang($row[0], $row[1]));
        }
    }
}
