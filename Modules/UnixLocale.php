<?php
namespace Bcp47\Modules;

class UnixLocale {
    function preprocess(&$raw) {
        $raw = str_replace('_', '-', $raw);
    }
}
