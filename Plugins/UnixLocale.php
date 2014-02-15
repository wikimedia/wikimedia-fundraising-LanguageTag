<?php
namespace Bcp47;

class UnixLocale {
    static function preprocess(&$raw) {
        $raw = str_replace('_', '-', $raw);
    }
}
