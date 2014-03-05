<?php
namespace Bcp47;

use \PHPUnit_Framework_TestCase;

class LanguageTagLookupTest extends PHPUnit_Framework_TestCase {
    function testLookup() {
        $testTuples = array(
            /* (raw preferred lang priority list, array available langs, string result) */
            array('zh-classical', array('zh', 'lzh'), 'lzh'),
            array('fr, en', array('fr-US', 'en'), 'en'),
            array('fr, gr', array('en'), ''),
        );

        foreach ($testTuples as $row) {
            list($preferred, $available, $expected) = $row;
            $result = LanguageTag::lookupBestLang(
                LanguageTag::fromRaw($preferred),
                LanguageTag::fromRaw(implode(", ", $available))
            )->getCanonical();
            $this->assertSame($expected, $result);
        }
    }
}
