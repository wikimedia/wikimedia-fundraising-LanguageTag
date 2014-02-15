<?php
namespace Bcp47;

use \Goatherd_LanguageTag_Parser;
use \Locale;

class Bcp47 {
     const SPLIT_PRIORITY_LIST_REGEX = '/[, ]+/';

 // This function adapted from http://alloyui.com/versions/1.0.x/api/intl-base.js.html
   /**
    * Returns the language among those available that
    * best matches the preferred language list, using the lookup
    * algorithm of BCP 47: https://tools.ietf.org/html/rfc4647
    * If none of the available languages meets the user's preferences,
    * then "" is returned.
    * FIXME: Extended language ranges are not supported.
    *
    * @param {String[] | String} preferredLanguages The list of preferred languages
    * by tag, in descending order of preference.  A string array or a comma-separated list.
    * @param {String[]} availableLanguages The list of languages
    * that the application supports, represented as BCP 47 language
    * tags.
    *
    * @return {String} The available language that best matches the
    * preferred language list, or "".
    */
    function lookupBestLang($preferredLanguages, $availableLanguages) {

        // Canonicalize inputs
        foreach ($availableLanguages as &$avail) {
            $avail = $this->canonicalize($avail);
        }

        if (is_string($preferredLanguages)) {
            $preferredLanguages = $this->splitPriorityList($preferredLanguages);
        }

        foreach ($preferredLanguages as $language) {
            // Skip meaningless bits
            if (!$language or $language === '*') {
                continue;
            }

            $language = $this->canonicalize($language);

            // Check the fallback sequence for one language
            while ($language) {
                if (in_array($language, $availableLanguages)) {
                    return $language;
                }
                $parts = explode('-', $language);
                if (count($parts) > 1) {
                    // Drop the most specific (rightmost) variation
                    array_pop($parts);

                    // Drop private-use subtags preceded by a single-character subtag
                    if (count($parts) > 1
                        and strlen($parts[0]) >= 2
                        and strlen(reset(array_slice($parts, -2, 1))) == 1
                    ) {
                        $parts = array_slice($parts, 0, -2);
                    }
                    $language = implode('-', $parts);
                } else {
                    // nothing available for this language
                    break;
                }
            }
        }

        return '';
    }

    /**
     * @param string $raw
     * @return array
     */
    function parse($raw) {
        $parser = new Goatherd_LanguageTag_Parser();
        $parsed = $parser->parse($raw);

        return $parsed;
    }

    protected function splitPriorityList($raw) {
        return preg_split(self::SPLIT_PRIORITY_LIST_REGEX, $raw, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Convert a language tag to the canonical form
     *
     * Note, you will lose language extension information.
     *
     * @param string $raw a single language tag or a language priority list
     * @return string
     */
    function canonicalize($raw) {
        if (preg_match(Bcp47::SPLIT_PRIORITY_LIST_REGEX, $raw)) {
            $tagList = $this->splitPriorityList($raw);
            $recursedlyCanonicalized = array_map(function ($tag) {
                return $this->canonicalize($tag);
            }, $tagList);
            return implode(', ', $recursedlyCanonicalized);
        }
        // TODO: DI or config magic
        //UnixLocale::preprocess($raw);
        //MediaWikiLocale::preprocess($raw);

        $parsed = $this->parse($raw);

        $canonical = array();
        if (isset($parsed['language'])) {
            $canonical[] = strtolower($parsed['language']);
        }
        if (isset($parsed['script'])) {
            $canonical[] = ucfirst($parsed['script']);
        }
        if (isset($parsed['region'])) {
            $canonical[] = strtoupper($parsed['region']);
        }
        if (isset($parsed['variants'])) {
            foreach ($parsed['variants'] as $subtag => $variant) {
                if ($subtag === $variant or is_integer($subtag)) {
                    $canonical[] = strtolower($variant);
                } else {
                    $canonical[] = strtolower($subtag);
                    $canonical[] = strtolower($variant);
                }
            }
        }
        if (isset($parsed['extension'])) {
            asort($parsed['extension']);
            foreach ($parsed['extension'] as $subtag => $extension) {
                if ($subtag === $extension or is_integer($subtag)) {
                    $canonical[] = strtolower($extension);
                } else {
                    $canonical[] = strtolower($subtag);
                    $canonical[] = strtolower($extension);
                }
            }
        }
        if (isset($parsed['privateuse'])) {
            foreach ($parsed['privateuse'] as $subtag => $privateuse) {
                if ($subtag === $privateuse or is_integer($subtag)) {
                    $canonical[] = 'x';
                    $canonical[] = strtolower($privateuse);
                } else {
                    $canonical[] = strtolower($subtag);
                    $canonical[] = strtolower($privateuse);
                }
            }
        }

        return implode('-', $canonical);
    }
}

/**
 * TODO: real dependency injection
 */
class PluginManager {
    static $singleton;

    protected $plugins;

    static protected function get() {
        if (!PluginManager::$singleton) {
            PluginManager::$singleton = new PluginManager();
        }
        return PluginManager::$singleton;
    }

    function runHook($name, $args) {
        foreach (PluginManager::$plugins as $plugin) {
            $func = array($plugin, $name);
            if (is_callable($func)) {
                $func($args);
            }
        }
    }
}
