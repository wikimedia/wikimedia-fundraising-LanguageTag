<?php
namespace LanguageTag\Modules;

class UnixLocale {
    function preprocess(&$raw) {
        $raw = str_replace('_', '-', $raw);
    }
}
