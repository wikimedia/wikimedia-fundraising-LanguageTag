<?php
namespace LanguageTag;

use \PHPUnit_Framework_TestCase;

class LanguageTagCanonicalizeTest extends PHPUnit_Framework_TestCase {
    function testCanonicalize() {
        $testPairs = array(
            'zh-classical' => 'lzh',
            'zh-hant' => 'zh-Hant',
            'en-fr' => 'en-FR',
            'en-X-rational' => 'en-x-rational',
        );

        foreach ($testPairs as $raw => $expected) {
            $this->tag = LanguageTag::fromRaw($raw);
            $this->assertSame($expected, $this->tag->getCanonical());
        }
    }
}
