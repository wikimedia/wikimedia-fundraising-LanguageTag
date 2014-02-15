<?php
namespace Bcp47;

class UnderscoreAnomaly {
    static function preprocess(&$raw) {
        $raw = str_replace('_', '-', $raw);
    }
}
